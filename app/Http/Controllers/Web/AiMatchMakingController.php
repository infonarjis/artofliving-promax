<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CountryMaster;
use App\Models\DailyBestMatch;
use App\Models\EducationMaster;
use App\Models\ExpressInterest;
use App\Models\MaritalStatusMaster;
use App\Models\MotherTongueMaster;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\ReligionMaster;
use App\Models\ShortlistProfile;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AiMatchMakingController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index()
    {
        $authUser = auth()->guard('web')->user();
        $currentLanguage = App::getLocale();

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.search', [
            'authUser' => $authUser,
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),
            'topCountries' => $topCountries,
            'allCountries' => $allCountries,
            'educationList' => EducationMaster::getDropdown($currentLanguage),
        ]);
    }

    public function getMatches(Request $request)
    {
        $min_match_percentage = $request->min_match_percentage ?? 60;

        $canVideoCall = false;
        $canVoiceCall = false;

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        ## Check Active Plan
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'    => $memberId,
            'current_plan' => 'Yes',
        ])
            ->whereDate('plan_expiry_date', '>=', $today)
            ->first();

        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        ## Base Query (WITHOUT pagination)
        $baseQuery = Register::query()->active();

        $this->applySearchFilters($baseQuery, $request);

        $baseQuery->where('gender', '!=', $authUser->gender);
        $baseQuery->common($authUser);

        ## Prepare the match engine once (explicit preference + learned behavior)
        $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
        $this->matchService->setPreference($partnerPref);

        ## Step 1 — Score candidates in memory-safe chunks (instead of loading the
        ## whole candidate set at once). We keep percent/breakdown maps so Step 3
        ## (display) never has to recompute the same score — calculated exactly once.
        ## Scoring is MUTUAL: forward ("would I like them") is blended with reverse
        ## ("would they like me", judged against their own stated preference) via
        ## a harmonic mean, so one-sided matches don't dominate the results.
        $filteredIds = [];
        $percentMap = [];
        $breakdownMap = [];

        (clone $baseQuery)
            ->select([
                'id',
                'gender',
                'marital_status',
                'height',
                'religion',
                'caste',
                'education_level',
                'occupation',
                'mother_tongue',
                'manglik',
                'birthdate',
                'country_id',
                'state_id',
                'city',
            ])
            ->chunkById(500, function ($profiles) use (&$filteredIds, &$percentMap, &$breakdownMap, $min_match_percentage, $memberId, $authUser) {
                // Batch-load this chunk's candidates' partner preferences in one
                // query instead of one query per candidate (avoids N+1).
                $chunkIds = $profiles->pluck('id');
                $candidatePrefs = RegisterPartner::whereIn('member_id', $chunkIds)
                    ->get()
                    ->keyBy('member_id');

                foreach ($profiles as $profile) {
                    $result = $this->matchService->mutualPercent(
                        $profile,
                        $authUser,
                        $candidatePrefs->get($profile->id),
                        $memberId,
                        $profile->id
                    );

                    // Filter on the WEAKER of the two directions, not the
                    // harmonic-mean blend. Harmonic mean sits between its two
                    // inputs, so a mutual score of 91% can be made of e.g.
                    // forward=97 / reverse=86 — the blend clears a 91%
                    // threshold even though "them -> you" individually
                    // doesn't, which is confusing since the UI shows both
                    // numbers on the card. min(forward, reverse) >= threshold
                    // guarantees mutual >= threshold too (harmonic mean is
                    // always >= the smaller input), so every number shown on
                    // the card — ring and breakdown alike — honors the filter.
                    if ($result['mutual'] >= $min_match_percentage) {
                    // if (min($result['forward'], $result['reverse']) >= $min_match_percentage) {
                        $filteredIds[] = $profile->id;
                        $percentMap[$profile->id] = $result['mutual'];
                        $breakdownMap[$profile->id] = $result;
                    }
                }
            });

        ## Rank by mutual match % (best first) before applying pagination/ordering below.
        arsort($percentMap);
        $filteredIds = array_keys($percentMap);

        ## If no profiles match, avoid empty whereIn crash
        if (empty($filteredIds)) {
            $filteredIds = [0];
        }

        ## Step 2 — Final Query WITH pagination, ordered by match % (falls back to id DESC)
        $query = Register::query()->active()
            ->whereIn('id', $filteredIds)
            ->with([
                'payments',
                'motherTongueData',
                'maritalStatusData',
                'religionData',
            ]);

        $query->select([
            'id',
            'gender',
            'fullname',
            'matri_id',
            'marital_status',
            'height',
            'religion',
            'education_level',
            'birthdate',
            'country_id',
            'state_id',
            'city',
            'last_activity',
            'photo1',
            'photo1_status',
            'photo2',
            'photo2_status',
            'photo3',
            'photo3_status',
            'photo4',
            'photo4_status',
            'photo_visibility',
            'plan_status',
            'plan_name'
        ]);

        if (count($filteredIds) > 1) {
            $query->orderByRaw('FIELD(id, ' . implode(',', $filteredIds) . ')');
        } else {
            $query->orderBy('id', 'DESC');
        }

        $resultArr = $query->paginate(5)->appends($request->all());

        ## Step 3 — Enrich results with flags + reuse the precomputed match % (no recompute)

        $receiverIds = $resultArr->pluck('id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestIds = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {

            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            $interestIds = ExpressInterest::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

            foreach ($resultArr as $item) {
                $item->hasPhotoRequestAccess = in_array($item->id, $acceptedRequests);
                $item->is_shortlisted        = in_array($item->id, $shortlistedIds);
                $item->is_interest           = in_array($item->id, $interestIds);
                $item->isBlocked             = isset($blockedMap[$item->id]);
                $item->matchPercent          = $percentMap[$item->id] ?? $this->matchService->percent($item, $memberId);
                $item->matchForward          = $breakdownMap[$item->id]['forward'] ?? null;
                $item->matchReverse          = $breakdownMap[$item->id]['reverse'] ?? null;
            }
        }

        ## Ajax Pagination :
        if ($request->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.ajax_result', compact('resultArr', 'canVideoCall', 'canVoiceCall', 'currentPlan'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.index', [
                'resultArr' => $resultArr,
                'request' => $request,
                'canVideoCall' => $canVideoCall,
                'canVoiceCall' => $canVoiceCall,
                'currentPlan' => $currentPlan
            ])
        )->header('Vary', 'X-Requested-With');
    }

    /**
     * "Best Matches Today" — served from the precomputed cache table filled
     * nightly by GenerateBestMatches / ComputeBestMatchesForMember, so this
     * page loads instantly instead of scoring the whole pool live.
     * Falls back to a capped live computation for members the nightly job
     * hasn't covered yet (e.g. brand-new sign-ups).
     */
    public function bestMatchesToday(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;
        $today = Carbon::today()->toDateString();

        $cached = DailyBestMatch::where('member_id', $memberId)
            ->where('computed_date', $today)
            ->orderBy('rank')
            ->with(['matchedMember' => function ($q) {
                $q->select([
                    'id',
                    'matri_id',
                    'gender',
                    'height',
                    'religion',
                    'education_level',
                    'birthdate',
                    'country_id',
                    'state_id',
                    'city',
                    'photo1',
                    'photo1_status',
                    'photo_visibility',
                    'plan_status',
                    'plan_name',
                ]);
            }])
            ->limit(10)
            ->get();

        if ($cached->isEmpty()) {
            return $this->computeBestMatchesLive($memberId, $authUser);
        }

        ## Enrich with flags + reuse the precomputed match % (no recompute).
        ## Flags go on the *profile* (matchedMember), not the DailyBestMatch
        ## pivot row — match_card.blade.php reads is_shortlisted / is_interest /
        ## hasPhotoRequestAccess / matchPercent off the profile object.
        $receiverIds = $cached->pluck('matched_member_id')->toArray();

        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

        // Fetch all interests in one query
        $interests = ExpressInterest::active()
            ->where(function ($query) use ($authUser, $receiverIds) {
                $query->where(function ($q) use ($authUser, $receiverIds) {
                    $q->where('sender_member_id', $authUser->id)
                        ->whereIn('receiver_member_id', $receiverIds);
                })
                    ->orWhere(function ($q) use ($authUser, $receiverIds) {
                        $q->whereIn('sender_member_id', $receiverIds)
                            ->where('receiver_member_id', $authUser->id);
                    });
            })
            ->get();

        // Create lookup maps
        $sentInterests = $interests
            ->where('sender_member_id', $authUser->id)
            ->keyBy('receiver_member_id');

        $receivedInterests = $interests
            ->where('receiver_member_id', $authUser->id)
            ->keyBy('sender_member_id');

        $acceptedRequestMap = array_fill_keys($acceptedRequests, true);
        $shortlistedMap = array_fill_keys($shortlistedIds, true);
        $maxReminders = _getConstant('express_interest.max_reminders');

        $bestMatches = $cached
            ->map(function ($row) use ($acceptedRequests, $shortlistedIds, $blockedMap, $sentInterests, $receivedInterests, $maxReminders) {
                $profile = $row->matchedMember;
                if (!$profile) {
                    return null; // profile deactivated/deleted since last night's run
                }

                $profile->matchPercent          = $row->match_percent;
                $profile->matchForward          = $row->score_breakdown['forward'] ?? null;
                $profile->matchReverse          = $row->score_breakdown['reverse'] ?? null;
                $profile->hasPhotoRequestAccess = in_array($profile->id, $acceptedRequests);
                $profile->is_shortlisted        = in_array($profile->id, $shortlistedIds);
                $profile->isBlocked             = isset($blockedMap[$profile->id]);

                // Express Interest
                $received = $receivedInterests->get($profile->id);
                $sent = $sentInterests->get($profile->id);

                if ($received) {
                    $profile->interest_state  = 'received';
                    $profile->interest_status = $received->receiver_response;
                    $profile->interest_id     = $received->id;
                } elseif ($sent) {
                    $profile->interest_state      = 'sent';
                    $profile->interest_status     = $sent->receiver_response;
                    $profile->interest_id         = $sent->id;
                    $profile->reminder_count      = (int) $sent->reminder_count;
                    $profile->can_send_reminder   = $sent->receiver_response === 'Pending'
                        && $sent->reminder_count < $maxReminders;
                } else {
                    $profile->interest_state    = 'none';
                    $profile->interest_status   = null;
                    $profile->interest_id       = null;
                    $profile->reminder_count    = 0;
                    $profile->can_send_reminder = false;
                }

                return $profile;
            })
            ->filter(fn($profile) => $profile && !$profile->isBlocked) // drop blocks since caching
            ->values();

        if ($bestMatches->isEmpty()) {
            return $this->computeBestMatchesLive($memberId, $authUser);
        }

        $canVideoCall = false;
        $canVoiceCall = false;
        $currentPlan = [];
        ## Check Plan Exists:
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $memberId,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.best_matches', [
            'bestMatches' => $bestMatches,
            'generatedAt' => optional($cached->first())->created_at,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall,
            'currentPlan' => $currentPlan,
        ]);
    }

    /**
     * Cold-start fallback: cheap, capped live computation for members the
     * nightly cache hasn't run for yet. Not meant to replace the nightly
     * job at scale — it caps the pool at 1000 candidates to stay fast.
     */
    protected function computeBestMatchesLive(int $memberId, $authUser)
    {
        $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
        $this->matchService->setPreference($partnerPref);

        $scored = [];

        Register::query()->active()
            ->where('gender', '!=', $authUser->gender)
            ->common($authUser)
            ->select([
                'id',
                'matri_id',
                'gender',
                'marital_status',
                'height',
                'religion',
                'caste',
                'education_level',
                'occupation',
                'mother_tongue',
                'manglik',
                'birthdate',
                'country_id',
                'state_id',
                'city',
                'photo1',
                'photo1_status',
                'photo_visibility',
                'plan_status',
                'plan_name',
            ])
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->chunkById(5, function ($profiles) use (&$scored, $memberId, $authUser) {
                $chunkIds = $profiles->pluck('id');
                $candidatePrefs = RegisterPartner::whereIn('member_id', $chunkIds)->get()->keyBy('member_id');

                foreach ($profiles as $profile) {
                    $result = $this->matchService->mutualPercent(
                        $profile,
                        $authUser,
                        $candidatePrefs->get($profile->id),
                        $memberId,
                        $profile->id
                    );
                    $profile->matchPercent = $result['mutual'];
                    $profile->matchForward = $result['forward'];
                    $profile->matchReverse = $result['reverse'];
                    $scored[] = $profile;
                }
            });

        usort($scored, fn($a, $b) => $b->matchPercent <=> $a->matchPercent);
        $bestMatches = collect(array_slice($scored, 0, 20));

        ## Enrich with flags — same pattern as getMatches() Step 3. Run these
        ## lookups only against the final top-20, not the full scored pool,
        ## since that's all that's actually rendered.
        $receiverIds = $bestMatches->pluck('id')->toArray();

        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $interestIds = ExpressInterest::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

        $bestMatches->each(function ($item) use ($acceptedRequests, $shortlistedIds, $interestIds, $blockedMap) {
            $item->hasPhotoRequestAccess = in_array($item->id, $acceptedRequests);
            $item->is_shortlisted        = in_array($item->id, $shortlistedIds);
            $item->is_interest           = in_array($item->id, $interestIds);
            $item->isBlocked             = isset($blockedMap[$item->id]);
        });

        $canVideoCall = false;
        $canVoiceCall = false;
        $currentPlan = [];
        ## Check Plan Exists:
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $memberId,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.best_matches', [
            'bestMatches' => $bestMatches,
            'generatedAt' => null,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall,
            'currentPlan' => $currentPlan,
        ]);
    }

    /**
     * Records that the authenticated member viewed a profile — feeds the
     * behavioral learning engine. If profile detail pages are rendered from
     * a different controller in your app, just call
     * $behaviorService->trackView($memberId, $profileId) from there instead
     * of duplicating this route.
     */
    // public function viewProfile(Request $request, int $id)
    // {
    //     $authUser = auth()->guard('web')->user();
    //     $memberId = $authUser->id;
    //     $profile = Register::active()->findOrFail($id);

    //     $this->behaviorService->trackView($memberId, $profile->id);

    //     $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
    //     $this->matchService->setPreference($partnerPref);

    //     $candidatePref = RegisterPartner::where('member_id', $profile->id)->first();
    //     $match = $this->matchService->mutualPercent($profile, $authUser, $candidatePref, $memberId, $profile->id);

    //     return view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiMatchMaking.profile_detail', [
    //         'profile' => $profile,
    //         'matchPercent' => $match['mutual'],
    //         'matchForward' => $match['forward'],
    //         'matchReverse' => $match['reverse'],
    //     ]);
    // }

    private function applySearchFilters($query, $request)
    {
        if (!auth()->guard('web')->check()) {
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
        }

        ## Age :
        if ($request->filled('part_frm_age') && $request->filled('part_to_age')) {
            $minAge = (int) $request->part_frm_age;
            $maxAge = (int) $request->part_to_age;

            $fromDate = Carbon::now()->subYears($maxAge)->startOfDay();
            $toDate   = Carbon::now()->subYears($minAge)->endOfDay();

            $query->whereBetween('birthdate', [$fromDate, $toDate]);
        }

        // HEIGHT
        if ($request->filled('part_height') && $request->filled('part_height_to')) {
            $query->whereBetween('height', [
                $request->part_height,
                $request->part_height_to
            ]);
        }

        if ($request->filled('religion') && $request->filled('religion') != 'Does Not Matter') {
            $query->whereIn('religion', $request->religion);
        }

        if ($request->filled('caste') && $request->filled('caste') != 'Does Not Matter') {
            $query->whereIn('caste', $request->caste);
        }

        ## Common Filter Handler
        $filters = [
            'religion',
            'caste',
            'marital_status',
            'mother_tongue',
            'country_id',
            'state_id',
            'city',
            'education_level',
            'occupation',
            'employee_in',
            'income',
            'designation_level',
            'diet',
            'smoke',
            'drink',
            'body_type',
            'complexion',
            'star',
            'manglik'
        ];

        foreach ($filters as $field) {
            if ($request->filled($field)) {
                $values = (array) $request->$field;
                $values = array_filter($values, function ($val) {
                    return $val !== 'Does Not Matter';
                });
                if (!empty($values)) {
                    $query->whereIn($field, $values);
                }
            }
        }

        ## With Photo Search :
        if ($request->filled('photo_search') && $request->photo_search == 'Yes') {
            $query->photoVisible();
        }

        // KEYWORD
        if ($request->filled('keyword_search')) {
            $keyword = $request->keyword_search;
            $query->where(function ($q) use ($keyword) {
                $q->where('matri_id', 'LIKE', "%$keyword%")
                    ->orWhereHas('religionData', function ($rq) use ($keyword) {
                        $rq->where('religion_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('casteData', function ($rq) use ($keyword) {
                        $rq->where('caste_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('countryData', function ($rq) use ($keyword) {
                        $rq->where('country_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('stateData', function ($rq) use ($keyword) {
                        $rq->where('state_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('cityData', function ($rq) use ($keyword) {
                        $rq->where('city_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('motherTongueData', function ($rq) use ($keyword) {
                        $rq->where('mtongue_name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('occupationData', function ($rq) use ($keyword) {
                        $rq->where('occupation_name', 'LIKE', "%$keyword%");
                    });
            });
        }

        // ID SEARCH
        if ($request->filled('id_search')) {
            $query->where('matri_id', $request->id_search);
        }

        return $query;
    }
}

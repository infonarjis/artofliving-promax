<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CountryMaster;
use App\Models\DailyBestMatch;
use App\Models\EducationMaster;
use App\Models\ExpressInterest;
use App\Models\MaritalStatusMaster;
use App\Models\MotherTongue;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\Religion;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiMatchMakingController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $limit = (int) $request->input('limit', 10);
        $page  = (int) $request->input('page', 1);

        $min_match_percentage = (int) $request->input('min_match_percentage', 60);

        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

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

        // =====================================================
        // Step 2 — Final Query WITH pagination
        // Ordered by match % order
        // Falls back to id DESC
        // =====================================================

        // Clean and validate IDs
        $filteredIds = collect($filteredIds)
            ->filter(function ($id) {
                return is_numeric($id) && (int) $id > 0;
            })
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->toArray();


        // If no valid IDs are available
        if (empty($filteredIds)) {
            return ApiResponseService::success(
                _getLangApi($request, 'msg_data_get_success'),
                [
                    'resultCount' => 0,
                    'resultList'  => [],
                ]
            );
        }
        // Pagination values
        $page = max(1, (int) $request->get('page', 1));
        $limit = max(1, (int) $request->get('limit', 10));
        // Build query
        $query = Register::query()
            ->active()
            ->whereIn('id', $filteredIds)
            ->with([
                'payments',
                'motherTongueData',
                'maritalStatusData',
                'religionData',
            ]);
        // Select required columns
        $query->select(ApiCommonActionModel::relation('select'));
        // Preserve match percentage order
        if (count($filteredIds) > 1) {
            $ids = implode(',', array_map('intval', $filteredIds));
            $query->orderByRaw("FIELD(id, {$ids})");
        } else {
            $query->orderBy('id', 'DESC');
        }
        // Total count
        $resultCount = count($filteredIds);
        // Pagination
        $offset = ($page - 1) * $limit;
        $resultList = $query->offset($offset)->limit($limit)->get();

        // Response
        // return ApiResponseService::success(
        //     _getLangApi($request, 'msg_data_get_success'),
        //     [
        //         'resultCount' => $resultCount,
        //         'resultList'  => $resultList,
        //     ]
        // );

        ## Step 3 — Enrich results with flags + reuse the precomputed match % (no recompute)

        $receiverIds = $resultList->pluck('id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestMap = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {

            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            ## Express Interests (both directions, ONE query for the whole page) :
            $interestRows = ExpressInterest::active()
                ->where(function ($q) use ($memberId, $receiverIds) {
                    $q->where('sender_member_id', $memberId)
                        ->whereIn('receiver_member_id', $receiverIds);
                })
                ->orWhere(function ($q) use ($memberId, $receiverIds) {
                    $q->whereIn('sender_member_id', $receiverIds)
                        ->where('receiver_member_id', $memberId);
                })
                ->get();
            $maxReminders = _getConstant('express_interest.max_reminders');
            foreach ($receiverIds as $receiverId) {
                $received = $interestRows->first(fn($i) => $i->sender_member_id == $receiverId && $i->receiver_member_id == $memberId);
                $sent     = $interestRows->first(fn($i) => $i->sender_member_id == $memberId && $i->receiver_member_id == $receiverId);

                if ($received) {
                    // The other member sent us the interest.
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'received',
                        'interest_status' => $received->receiver_response,
                        'interest_id'     => $received->id,
                    ];
                } elseif ($sent) {
                    $usedReminders = (int) $sent->reminder_count;
                    $interestMap[$receiverId] = [
                        'interest_state'            => 'sent',
                        'interest_status'           => $sent->receiver_response,
                        'interest_id'               => $sent->id,
                        'total_reminder_count'      => $usedReminders,
                        'pending_reminder_count'    => max(0, $maxReminders - $usedReminders),
                        'can_send_reminder'         => $sent->receiver_response === 'Pending'
                            && $sent->reminder_count < $maxReminders,
                    ];
                } else {
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'none',
                        'interest_status' => null,
                    ];
                }
            }

            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

            foreach ($resultList as $item) {
                $item->matchPercent     = $percentMap[$item->id] ?? $this->matchService->percent($item, $memberId);
                // $item->matchForward  = $breakdownMap[$item->id]['forward'] ?? null;
                // $item->matchReverse  = $breakdownMap[$item->id]['reverse'] ?? null;
            }
        }

        $resultArr = ApiCommonActionModel::commonResponse(
            $resultList,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService
        );

        return ApiResponseService::success(
            _getLangApi($request, 'msg_data_get_success'),
            [
                'resultCount' => $resultCount,
                'resultList'  => $resultArr,
            ]
        );
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
        $validator = Validator::make($request->all(), [
            'page'  => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
        ]);

        $limit = (int) $request->input('limit', 10);
        $page  = (int) $request->input('page', 1);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;
        $today = Carbon::today()->toDateString();

        $cached = DailyBestMatch::where('member_id', $memberId)
            ->where('computed_date', $today)
            ->orderBy('rank')
            ->with(['matchedMember' => function ($q) {
                $q->select(ApiCommonActionModel::relation('select'));
            }]);

        // Total Count
        $resultCount = $cached->count();
        $resultList = $cached->forPage($page, $limit)->get();

        if ($resultList->isEmpty()) {
            return $this->computeBestMatchesLive($request, $memberId, $authUser);
        }

        ## Enrich with flags + reuse the precomputed match % (no recompute).
        ## Flags go on the *profile* (matchedMember), not the DailyBestMatch
        ## pivot row — match_card.blade.php reads is_shortlisted / is_interest /
        ## hasPhotoRequestAccess / matchPercent off the profile object.
        $receiverIds = $resultList->pluck('matched_member_id')->toArray();

        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $receiverIds)
            ->pluck('receiver_member_id')
            ->toArray();

        ## Express Interests (both directions, ONE query for the whole page) :
        $interestMap = [];
        $interestRows = ExpressInterest::active()
            ->where(function ($q) use ($memberId, $receiverIds) {
                $q->where('sender_member_id', $memberId)
                    ->whereIn('receiver_member_id', $receiverIds);
            })
            ->orWhere(function ($q) use ($memberId, $receiverIds) {
                $q->whereIn('sender_member_id', $receiverIds)
                    ->where('receiver_member_id', $memberId);
            })
            ->get();
        $maxReminders = _getConstant('express_interest.max_reminders');
        foreach ($receiverIds as $receiverId) {
            $received = $interestRows->first(fn($i) => $i->sender_member_id == $receiverId && $i->receiver_member_id == $memberId);
            $sent     = $interestRows->first(fn($i) => $i->sender_member_id == $memberId && $i->receiver_member_id == $receiverId);

            if ($received) {
                // The other member sent us the interest.
                $interestMap[$receiverId] = [
                    'interest_state'  => 'received',
                    'interest_status' => $received->receiver_response,
                    'interest_id'     => $received->id,
                ];
            } elseif ($sent) {
                $usedReminders = (int) $sent->reminder_count;
                $interestMap[$receiverId] = [
                    'interest_state'            => 'sent',
                    'interest_status'           => $sent->receiver_response,
                    'interest_id'               => $sent->id,
                    'total_reminder_count'      => $usedReminders,
                    'pending_reminder_count'    => max(0, $maxReminders - $usedReminders),
                    'can_send_reminder'         => $sent->receiver_response === 'Pending'
                        && $sent->reminder_count < $maxReminders,
                ];
            } else {
                $interestMap[$receiverId] = [
                    'interest_state'  => 'none',
                    'interest_status' => null,
                ];
            }
        }

        $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);

        $bestMatches = $resultList
            ->map(function ($row) use ($acceptedRequests, $shortlistedIds, $interestMap, $blockedMap) {
                $profile = $row->matchedMember;
                if (!$profile) {
                    return null; // profile deactivated/deleted since last night's run
                }

                $profile->matchPercent          = $row->match_percent;

                return $profile;
            })
            ->filter(fn($profile) => $profile && !$profile->isBlocked) // drop blocks since caching
            ->values();

        if ($bestMatches->isEmpty()) {
            return $this->computeBestMatchesLive($request, $memberId, $authUser);
        }

        $resultArr = ApiCommonActionModel::commonResponse(
            $bestMatches,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService
        );

        return ApiResponseService::success(
            _getLangApi($request, 'msg_data_get_success'),
            [
                'resultCount' => $resultCount,
                'resultList'  => $resultArr,
            ]
        );
    }

    /**
     * Cold-start fallback: cheap, capped live computation for members the
     * nightly cache hasn't run for yet. Not meant to replace the nightly
     * job at scale — it caps the pool at 1000 candidates to stay fast.
     */
    protected function computeBestMatchesLive($request, int $memberId, $authUser)
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
            ->limit(1000)
            ->chunkById(200, function ($profiles) use (&$scored, $memberId, $authUser) {
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
                    // $profile->matchForward = $result['forward'];
                    // $profile->matchReverse = $result['reverse'];
                    $scored[] = $profile;
                }
            });

        $bestMatches = collect($scored)
            ->sortByDesc('matchPercent')
            ->take(20)
            ->values();
        $resultCount = $bestMatches->count();

        if ($bestMatches->isEmpty()) {
            return ApiResponseService::success(
                _getLangApi($request, 'msg_data_get_success'),
                [
                    'resultCount' => 0,
                    'resultList'  => [],
                ]
            );
        }

        ## Enrich with flags — same pattern as getMatches() Step 3. Run these
        ## lookups only against the final top-20, not the full scored pool,
        ## since that's all that's actually rendered.
        $receiverIds = $bestMatches->pluck('id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestRows = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            ## Express Interests (both directions, ONE query for the whole page) :
            $interestRows = ExpressInterest::active()
                ->where(function ($q) use ($memberId, $receiverIds) {
                    $q->where('sender_member_id', $memberId)
                        ->whereIn('receiver_member_id', $receiverIds);
                })
                ->orWhere(function ($q) use ($memberId, $receiverIds) {
                    $q->whereIn('sender_member_id', $receiverIds)
                        ->where('receiver_member_id', $memberId);
                })
                ->get();
            $maxReminders = _getConstant('express_interest.max_reminders');
            foreach ($receiverIds as $receiverId) {
                $received = $interestRows->first(fn($i) => $i->sender_member_id == $receiverId && $i->receiver_member_id == $memberId);
                $sent     = $interestRows->first(fn($i) => $i->sender_member_id == $memberId && $i->receiver_member_id == $receiverId);

                if ($received) {
                    // The other member sent us the interest.
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'received',
                        'interest_status' => $received->receiver_response,
                        'interest_id'     => $received->id,
                    ];
                } elseif ($sent) {
                    $usedReminders = (int) $sent->reminder_count;
                    $interestMap[$receiverId] = [
                        'interest_state'            => 'sent',
                        'interest_status'           => $sent->receiver_response,
                        'interest_id'               => $sent->id,
                        'total_reminder_count'      => $usedReminders,
                        'pending_reminder_count'    => max(0, $maxReminders - $usedReminders),
                        'can_send_reminder'         => $sent->receiver_response === 'Pending'
                            && $sent->reminder_count < $maxReminders,
                    ];
                } else {
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'none',
                        'interest_status' => null,
                    ];
                }
            }

            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);
        }

        $resultArr = ApiCommonActionModel::commonResponse(
            $bestMatches,
            $acceptedRequests,
            $shortlistedIds,
            $interestRows,
            $blockedMap,
            $this->matchService
        );

        return ApiResponseService::success(
            _getLangApi($request, 'msg_data_get_success'),
            [
                'resultCount' => $resultCount,
                'resultList'  => $resultArr,
            ]
        );
    }

    private function applySearchFilters($query, $request)
    {
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

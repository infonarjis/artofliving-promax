<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CountryMaster;
use App\Models\EducationMaster;
use App\Models\ExpressInterest;
use App\Models\MaritalStatusMaster;
use App\Models\MatchList;
use App\Models\MotherTongueMaster;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\ReligionMaster;
use App\Models\ShortlistProfile;
use App\Models\SiteSetting;
use App\Services\Api\ApiCommonActionModel;
use App\Services\PartnerPreferenceService;
use App\Services\MatchMakingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class MatchesController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(
        MatchMakingService $matchService
    ) {
        $this->matchService = $matchService;
    }

    public function recommended(PartnerPreferenceService $prefService)
    {
        $page = __('messages.lbl_recommended_matches');
        return $this->getMatches($prefService, 'recommended', $page);
    }

    public function premium(PartnerPreferenceService $prefService)
    {
        $page = __('messages.lbl_premium_matches');
        return $this->getMatches($prefService, 'premium', $page);
    }

    public function nearByMe(PartnerPreferenceService $prefService)
    {
        $currentLanguage = App::getLocale();
        $dataArr = [
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'countryList' => CountryMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),
            'educationList' => EducationMaster::getDropdown($currentLanguage),
        ];

        $page = __('messages.lbl_near_by_me');
        return $this->getMatches($prefService, 'nearByMe', $page, $dataArr);
    }

    /**
     * Common Matches Logic
     */
    private function getMatches(PartnerPreferenceService $prefService, string $type, string $page, array $dataArr = [])
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        ## Check Plan Exists:
        $canVideoCall = false;
        $canVoiceCall = false;
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $memberId,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        $blockedIds  = BlockProfile::getBlockedMemberIds($memberId);
        $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
        $this->matchService->setPreference($partnerPref);

        $query = Register::active()
            ->common($authUser)
            ->select(ApiCommonActionModel::MEMBER_COLUMNS)
            ->where('id', '!=', $memberId)
            ->whereNotIn('id', $blockedIds)
            ->where('gender', '!=', $authUser->gender);
        // Premium match = only paid members
        if ($type === 'premium') {
            $query->where('plan_status', 'Paid');
        }
        if ($type === 'nearByMe') {
            $nearByMeKm = SiteSetting::getValue('near_by_me_km') ?? 50;

            $userLat = $authUser->latitude;
            $userLng = $authUser->longitude;

            // Skip if user location not set
            if ($userLat && $userLng) {

                // 6371 = Earth's radius in kilometers
                $haversine = "(6371 * acos(
        cos(radians($userLat)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians($userLng)) +
        sin(radians($userLat)) *
        sin(radians(latitude))
    ))";

                $query->selectRaw("$haversine AS distance")
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->having('distance', '<=', $nearByMeKm)
                    ->orderBy('distance', 'asc');
            } else {
                $query->whereRaw('1 = 0');
            }
            /**
             * APPLY SEARCH CRITERIA FILTERS FROM FORM
             */
            $req = request();
            // Age
            if ($req->filled('part_frm_age') && $req->filled('part_to_age')) {
                $fromAge = (int) $req->part_frm_age;
                $toAge   = (int) $req->part_to_age;

                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE())'), [$fromAge, $toAge]);
            }

            // Height (inches)
            if ($req->filled('part_height') && $req->filled('part_height_to')) {
                $query->whereBetween('height', [
                    (int) $req->part_height,
                    (int) $req->part_height_to
                ]);
            }

            // Marital Status
            if ($req->filled('marital_status')) {
                $query->whereIn('marital_status', $req->marital_status);
            }

            // Mother Tongue
            if ($req->filled('mother_tongue')) {
                $query->whereIn('mother_tongue', $req->mother_tongue);
            }

            // Religion
            if ($req->filled('religion')) {
                $query->whereIn('religion', $req->religion);
            }

            // Caste
            if ($req->filled('caste')) {
                $query->whereIn('caste', $req->caste);
            }

            // Country
            if ($req->filled('country_id')) {
                $query->whereIn('country_id', $req->country_id);
            }

            // Education
            if ($req->filled('education_level')) {
                $query->whereIn('education_level', $req->education_level);
            }
        }
        // Apply Partner Preference Service
        $prefService->apply($query, $partnerPref);
        $resultArr = $query->orderByDesc('id')->paginate(10);
        // Protected Image
        $receiverIds = $resultArr->pluck('id')->toArray();
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
            ## Get Profile Match Count :
            $item->matchPercent = $this->matchService->percent($item);
        }

        $dataArr = [
            'resultArr' => $resultArr,
            'type' => $type,
            'page' => $page,
            'dataArr' => $dataArr,
            'currentPlan' => $currentPlan,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall
        ];
        if (request()->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.matches.ajax_result', $dataArr)->render();
        }
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.matches.index', $dataArr);
    }

    ## Suggested Matches :
    public function suggested(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        ## Check Plan Exists:
        $canVideoCall = false;
        $canVoiceCall = false;
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $memberId,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        $partnerPref = RegisterPartner::where('member_id', $memberId)->first();
        $this->matchService->setPreference($partnerPref);

        // 1. Blocked profiles
        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

        // 2. Get members who are in MatchList with current user
        $matchedIds = MatchList::where(function ($query) use ($memberId) {
            $query->where('sender_member_id', $memberId)
                ->orWhere('receiver_member_id', $memberId);
        })
            ->get()
            ->map(function ($item) use ($memberId) {
                return $item->sender_member_id == $memberId
                    ? $item->receiver_member_id
                    : $item->sender_member_id;
            })
            ->unique()
            ->toArray();

        // 3. Remove blocked members from matched members
        $matchedIds = array_values(array_diff($matchedIds, $blockedIds));

        // 4. Suggested members query
        // ONLY members available in MatchList
        $resultArr = Register::active()
            ->select(ApiCommonActionModel::MEMBER_COLUMNS)
            ->whereIn('id', $matchedIds)
            ->where('id', '!=', $memberId)
            ->where('gender', '!=', $authUser->gender)
            ->latest()
            ->paginate(10);
        // Protected Image
        $receiverIds = $resultArr->pluck('id')->toArray();
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
            $item->is_shortlisted       = in_array($item->id, $shortlistedIds);
            $item->is_interest          = in_array($item->id, $interestIds);
            $item->isBlocked            = isset($blockedMap[$item->id]);

            ## Get Profile Match % :
            $item->matchPercent = $this->matchService->percent($item);
        }

        if ($request->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.suggestedMatches.ajax_result', compact('resultArr', 'currentPlan', 'canVideoCall', 'canVoiceCall'))->render();
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.suggestedMatches.index', compact('resultArr', 'currentPlan', 'canVideoCall', 'canVoiceCall'));
    }
}

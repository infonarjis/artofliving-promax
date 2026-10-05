<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AnnualIncomeMaster;
use App\Models\BlockProfile;
use App\Models\BloodGroupMaster;
use App\Models\BodyTypeMaster;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\ComplexionMaster;
use App\Models\CountryMaster;
use App\Models\DesignationMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\EducationMaster;
use App\Models\EmployeeMaster;
use App\Models\ExpressInterest;
use App\Models\HoroscopeMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MoonsignMaster;
use App\Models\MotherTongueMaster;
use App\Models\OccupationMaster;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ReligionMaster;
use App\Models\ResidenceMaster;
use App\Models\ShortlistProfile;
use App\Models\SmokingHabitMaster;
use App\Models\StarMaster;
use App\Models\StateMaster;
use App\Services\Api\ApiCommonActionModel;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SearchController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(
        MatchMakingService $matchService
    ) {
        $this->matchService = $matchService;
    }

    public function index(Request $request)
    {
        $type = $request->route('type');
        $activeTab = match ($type) {
            'advance-search' => 'advancesearch',
            'keyword-search' => 'keywordsearch',
            'id-search'      => 'idsearch',
            default          => 'quicksearch',
        };

        $currentLanguage = App::getLocale();

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.search.index', [
            'activeTab' => $activeTab,
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'starList' => StarMaster::getDropdown($currentLanguage),
            'manglikList' => ManglikMaster::getDropdown($currentLanguage),
            'horoscopeList' => HoroscopeMaster::getDropdown($currentLanguage),
            'moongsignList' => MoonsignMaster::getDropdown($currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),
            'topCountries' => $topCountries,
            'allCountries' => $allCountries,
            'educationList' => EducationMaster::getDropdown($currentLanguage),
            'occupationList' => OccupationMaster::getDropdown($currentLanguage),
            'employeeInList' => EmployeeMaster::getDropdown($currentLanguage),
            'designationList' => DesignationMaster::getDropdown($currentLanguage),
            'eatingHabitList' => EatingHabitMaster::getDropdown($currentLanguage),
            'smokingHabitList' => SmokingHabitMaster::getDropdown($currentLanguage),
            'drinkingHabitList' => DrinkingHabitMaster::getDropdown($currentLanguage),
            'bodyTypeList' => BodyTypeMaster::getDropdown($currentLanguage),
            'complextionList' => ComplexionMaster::getDropdown($currentLanguage),
            'incomeList' => AnnualIncomeMaster::getDropdown($currentLanguage),
            'bloodGroupList' => BloodGroupMaster::getDropdown($currentLanguage),
        ]);
    }

    public function searchResult(Request $request)
    {
        $currentLanguage = App::getLocale();

        $canVideoCall = false;
        $canVoiceCall = false;
        $currentPlan = [];
        if (auth()->guard('web')->check()) {
            $authUser = auth()->guard('web')->user();
            $memberId = $authUser->id;

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
        }

        ## Query :
        ## Ajax Pagination :
        if ($request->expectsJson()) {
            $query = Register::query()->active();
            $this->applySearchFilters($query, $request); // Apply filters
            if (auth()->guard('web')->check()) {
                $blockedIds  = BlockProfile::getBlockedMemberIds($memberId);

                $authUser = auth()->guard('web')->user();
                $query->where('gender', '!=', $authUser->gender);
                $query->whereNotIn('id', $blockedIds);
                $query->common($authUser);
            }
            $query->select(ApiCommonActionModel::MEMBER_COLUMNS);
            $query->orderBy('id', 'DESC');

            $resultArr = $query->paginate(10)->appends($request->all());
            if (auth()->guard('web')->check()) {
                $authUser = auth()->guard('web')->user();
                $partnerPref = $authUser->partnerPreference;
                $this->matchService->setPreference($partnerPref);

                // Protected Image
                $receiverIds = $resultArr->pluck('id')->toArray();
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

                foreach ($resultArr as $item) {
                    $item->hasPhotoRequestAccess = isset(
                        $acceptedRequestMap[$item->id]
                    );
                    $item->is_shortlisted = isset(
                        $shortlistedMap[$item->id]
                    );
                    $item->isBlocked = isset(
                        $blockedMap[$item->id]
                    );
                    // Profile match percentage
                    $item->matchPercent = $this->matchService->percent($item);
                    // Express Interest
                    $received = $receivedInterests->get($item->id);
                    $sent = $sentInterests->get($item->id);
                    if ($received) {
                        $item->interest_state = 'received';
                        $item->interest_status = $received->receiver_response;
                        $item->interest_id = $received->id;
                    } elseif ($sent) {

                        $item->interest_state = 'sent';
                        $item->interest_status = $sent->receiver_response;
                        $item->interest_id = $sent->id;
                        $item->reminder_count = (int) $sent->reminder_count;

                        $item->can_send_reminder = $sent->receiver_response === 'Pending' && $sent->reminder_count < $maxReminders;
                    } else {

                        $item->interest_state = 'none';
                        $item->interest_status = null;
                        $item->interest_id = null;
                        $item->reminder_count = 0;
                        $item->can_send_reminder = false;
                    }
                }
            } else {
                foreach ($resultArr as $item) {
                    $item->hasPhotoRequestAccess = 0;
                    $item->is_shortlisted = 0;
                    $item->is_interest = 0;
                    $item->isBlocked = 0;
                    $item->matchPercent = 0;
                }
            }
            return response()->json([
                'html' => view(
                    _getConstant('dir_path.WEB_DIR_PATH') . '.search.result.ajax_result',
                    compact('resultArr', 'canVideoCall', 'canVoiceCall', 'currentPlan')
                )->render(),
                'resultCount' => $resultArr->total(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
        }

        $religionArr = (array) ($request->religion ?? []);
        $countryArr = (array) ($request->country_id ?? []);
        $stateArr   = (array) ($request->state_id ?? []);

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.search.result.index', [
            'request' => $request,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall,
            'currentPlan' => $currentPlan,
            'residenceList' => ResidenceMaster::getDropdown($currentLanguage),
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'casteList' => CasteMaster::getDropdown($religionArr, $currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),
            'topCountries' => $topCountries,
            'allCountries' => $allCountries,
            'stateList' => StateMaster::getDropdown($countryArr, $currentLanguage),
            'cityList' => CityMaster::getDropdown($stateArr, $currentLanguage),
            'educationList' => EducationMaster::getDropdown($currentLanguage),
            'occupationList' => OccupationMaster::getDropdown($currentLanguage),
            'employeeInList' => EmployeeMaster::getDropdown($currentLanguage),
            'designationList' => DesignationMaster::getDropdown($currentLanguage),
            'eatingHabitList' => EatingHabitMaster::getDropdown($currentLanguage),
            'smokingHabitList' => SmokingHabitMaster::getDropdown($currentLanguage),
            'drinkingHabitList' => DrinkingHabitMaster::getDropdown($currentLanguage),
            'bodyTypeList' => BodyTypeMaster::getDropdown($currentLanguage),
            'complextionList' => ComplexionMaster::getDropdown($currentLanguage),
            'starList' => StarMaster::getDropdown($currentLanguage),
            'manglikList' => ManglikMaster::getDropdown($currentLanguage),
            'moongsignList' => MoonsignMaster::getDropdown($currentLanguage),
            'horoscopeList' => HoroscopeMaster::getDropdown($currentLanguage),
            'incomeList' => AnnualIncomeMaster::getDropdown($currentLanguage),
            'bloodGroupList' => BloodGroupMaster::getDropdown($currentLanguage),
        ]);
    }

    private function applySearchFilters($query, $request)
    {
        if (!auth()->guard('web')->check()) {
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            $query->where('user_type', '0');
        }

        ## Age :
        if ($request->filled('part_frm_age') && $request->filled('part_to_age')) {
            $minAge = (int) $request->part_frm_age;
            $maxAge = (int) $request->part_to_age;
            // Convert age to birthdate range
            $fromDate = Carbon::now()->subYears($maxAge)->startOfDay();
            $toDate   = Carbon::now()->subYears($minAge)->endOfDay();
            $query->whereBetween('birthdate', [$fromDate, $toDate]);
        }

        // HEIGHT
        if ($request->filled('part_height') && $request->filled('part_height_to')) {
            $query->whereBetween('height', [$request->part_height, $request->part_height_to]);
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
            'residence_type',
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
                // Remove "Does Not Matter"
                $values = array_filter($values, function ($val) {
                    return $val !== 'Does Not Matter';
                });
                // Apply filter only if values exist
                if (!empty($values)) {
                    $query->whereIn($field, $values);
                }
            }
        }

        ## With Photo Search :
        if ($request->filled('photo_search') && in_array('Yes', (array) $request->photo_search)) {
            $query->photoVisible();
        }

        // KEYWORD
        if ($request->filled('keyword_search')) {
            $keyword = $request->keyword_search;
            $query->where(function ($q) use ($keyword) {
                $q->where('matri_id', 'LIKE', "%{$keyword}%")
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

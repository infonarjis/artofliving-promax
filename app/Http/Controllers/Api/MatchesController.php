<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MatchList;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Models\SiteSetting;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\PartnerPreferenceService;
use App\Services\MatchMakingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MatchesController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function recommended(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'recommended');
        } catch (Throwable $e) {
            Log::error('Recommended match API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    public function premium(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'premium');
        } catch (Throwable $e) {
            Log::error('Premium match List API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    public function nearByMe(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'nearByMe');
        } catch (Throwable $e) {
            Log::error('Near By List API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Get Recently Joined :
    public function recentlyJoined(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'new_joined');
        } catch (Throwable $e) {
            Log::error('Recently Joined API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Recently Login
    public function recentlyLogin(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'login_member');
        } catch (Throwable $e) {
            Log::error('Recently login API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Featured Member :
    public function featuredMember(Request $request, PartnerPreferenceService $prefService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }
            return $this->getMatches($request, $prefService, 'featured_member');
        } catch (Throwable $e) {
            Log::error('Recently login API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    /**
     * Common Matches Logic
     */
    private function getMatches($request, PartnerPreferenceService $prefService, string $type)
    {
        $limit = (int) $request->input('limit', 10);
        $page  = (int) $request->input('page', 1);

        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

        $blockedIds  = BlockProfile::getBlockedMemberIds($memberId);
        $partnerPref = $authUser->partnerPreference;
        $this->matchService->setPreference($partnerPref);

        $query = Register::active()
            ->common($authUser)
            ->with([
                'motherTongueData',
                'maritalStatusData',
                'religionData'
            ])
            ->select(ApiCommonActionModel::relation('select'))
            ->where('id', '!=', $memberId)
            ->whereNotIn('id', $blockedIds)
            ->where('gender', '!=', $authUser->gender);

        // Featured Member :
        if ($type == 'featured_member') {
            $query->where('plan_status', 'Paid');
            $query->where('fstatus', 'Featured');
        }

        // Apply partner preference early
        if (in_array($type, ['recommended', 'premium', 'nearByMe'])) {
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
        }

        // Sorting
        if ($type === 'new_joined') {
            $query->orderByDesc('created_at');
        } elseif ($type === 'login_member') {
            $query->orderByDesc('last_login');
        } else {
            $query->orderByDesc('id');
        }

        $resultCount = (clone $query)->count();
        $resultList = $query->forPage($page, $limit)->get();

        // Protected Image
        $receiverIds = $resultList->pluck('id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestMap = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {
            ## Photo Requests :
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            ## Shortlisted Profiles :
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

            ## Block Status :
            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);
        }

        // Common Response
        $resultArr = ApiCommonActionModel::commonResponse(
            $resultList,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService,
        );

        $dataArr = [
            'resultCount' => $resultCount,
            'resultList'  => $resultArr,
        ];
        return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
    }

    ## Suggested Matches :
    public function suggested(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            ## Parner Preference :
            $partnerPref = $authUser->partnerPreference;
            $this->matchService->setPreference($partnerPref);

            // 1. Blocked profiles
            $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

            // 2. Already interacted members (sent or received)
            $matchedIds = MatchList::where('receiver_member_id', $memberId)
                ->get()
                ->flatMap(function ($item) use ($memberId) {
                    return [
                        $item->sender_member_id == $memberId
                            ? $item->receiver_member_id
                            : $item->sender_member_id
                    ];
                })
                ->unique()
                ->toArray();

            // 3. Merge all excluded IDs
            $excludeIds = array_unique(array_merge(
                [$memberId],
                $blockedIds,
                $matchedIds
            ));

            // 4. Suggested members query (from Register table)
            $query = Register::active()
                ->select(ApiCommonActionModel::relation('select'))
                ->whereNotIn('id', $excludeIds)
                ->where('gender', '!=', $authUser->gender)
                ->latest();

            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Protected Image
            $receiverIds = $resultList->pluck('id')->toArray();

            $acceptedRequests = [];
            $shortlistedIds = [];
            $interestMap = [];
            $blockedMap = [];
            if (!empty($receiverIds)) {
                ## Photo Requests :
                $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

                ## Shortlisted Profiles :
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

                ## Block Status :
                $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);
            }

            // Common Response
            $resultArr = ApiCommonActionModel::commonResponse(
                $resultList,
                $acceptedRequests,
                $shortlistedIds,
                $interestMap,
                $blockedMap,
                $this->matchService,
            );

            $dataArr = [
                'resultCount' => $resultCount,
                'resultList'  => $resultArr,
            ];
            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Suggested API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }
}

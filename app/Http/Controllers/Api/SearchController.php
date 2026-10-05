<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    private MatchMakingService $matchService;

    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function searchResult(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
                'type'  => 'nullable|in:interest_sent,interest_received',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);
            $verifyStatus  = $request->input('verify_status', '');

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            // Blocked Members
            $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

            ## Query :
            $query = Register::query()->active();
            $this->applySearchFilters($query, $request); // Apply filters :
            $authUser = auth()->guard('api')->user();
            $query->where('gender', '!=', $authUser->gender);
            $query->whereNotIn('id', $blockedIds);
            if ($verifyStatus === 'premium') {
                $query->where('plan_status', 'Paid');
            } elseif ($verifyStatus === 'verified') {
                $query->where([
                    'mobile_verify_status' => 'Yes',
                    'email_verify_status'  => 'Verify',
                ]);
            }
            $query->common($authUser);
            $query->select(ApiCommonActionModel::relation('select'));
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('id', 'DESC');
                    break;
                case 'recent':
                    $query->orderBy('last_activity', 'DESC');
                    break;
                case 'best':
                default:
                    $query->orderBy('plan_status', 'DESC')->orderBy('id', 'DESC');
                    break;
            }

            $resultCount = (clone $query)->count();
            $resultList  = $query->forPage($page, $limit)->get();

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
                $this->matchService
            );

            return ApiResponseService::success(
                _getLangApi($request, 'msg_data_get_success'),
                [
                    'resultCount' => $resultCount,
                    'resultList'  => $resultArr,
                ]
            );
        } catch (Throwable $e) {
            Log::error('Search result API Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    private function applySearchFilters($query, $request)
    {
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
            $query->whereBetween('height', [$request->part_height,$request->part_height_to]);
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

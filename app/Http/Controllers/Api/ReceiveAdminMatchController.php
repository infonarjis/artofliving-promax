<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MatchList;
use App\Models\MatchPairMeeting;
use App\Models\PhotoRequest;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ReceiveAdminMatchController extends Controller
{
    private MatchMakingService $matchService;
    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

            ## Relation :
            $relation   = 'receiver';

            ## Query :
            $query = MatchList::where('sender_member_id', $memberId)
                ->whereNotIn('sender_member_id', $blockedIds)
                ->with(ApiCommonActionModel::relation($relation))
                ->latest();
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Get all receiver ids in one go
            $receiverIds = $resultList->pluck('receiver_member_id')->toArray();

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

            ## Common Response :
            $resultArr = ApiCommonActionModel::commonResponse(
                $resultList,
                $acceptedRequests,
                $shortlistedIds,
                $interestMap,
                $blockedMap,
                $this->matchService,
                $relation
            );

            $dataArr = [
                'resultCount' => $resultCount,
                'resultList' => $resultArr,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Receive Admin Match list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }


    public function acceptReject(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'match_id' => 'required|exists:match_list,id',
                'action' => 'required|in:accept,reject',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $matchId = $request->match_id;

            if (blank($matchId)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_req_send'));
            }

            $authUser = auth()->guard('api')->user();

            $responce = 0;
            if ($request->action == 'accept') {
                $responce = 1;
            } else if ($request->action == 'reject') {
                $responce = 2;
            }

            MatchList::where('id', $matchId)->update([
                'response' => $responce,
                'updated_at' => _getCurrentDate()
            ]);

            $personalizeMemberList = MatchList::with('receiver:id,matri_id')->find($matchId);

            if ($request->action == 'accept') {
                MatchPairMeeting::updateOrInsert(
                    [
                        'match_id' => $personalizeMemberList->id,
                        'member1_id' => $authUser->id,
                        'member2_id' => $personalizeMemberList->receiver->id,
                    ],
                    [
                        'member1_matri_id' => $authUser->matri_id,
                        'member2_matri_id' => $personalizeMemberList->receiver->matri_id,
                        'staff_id' => $personalizeMemberList->sent_by_id,
                        'updated_at' => _getCurrentDate(),
                        'created_at' => _getCurrentDate(),
                    ]
                );
            }

            if ($request->action == 'reject') {
                MatchPairMeeting::where([
                    'match_id'   => $personalizeMemberList->id,
                    'member1_id' => $authUser->id,
                    'member2_id' => $personalizeMemberList->receiver->id,
                ])->delete();
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_status_updated_successfully'));
        } catch (Throwable $e) {
            Log::error('Receive Admin Match accept or reject API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

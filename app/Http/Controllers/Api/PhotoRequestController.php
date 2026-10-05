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
use App\Services\BehaviorLearningService;
use App\Services\MatchMakingService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PhotoRequestController extends Controller
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
                'limit' => 'nullable|integer|min:1',
                'type'  => 'nullable|in:request_sent,request_received',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $type  = $request->input('type', 'request_sent');
            $responseStatus  = $request->input('response_status', '');

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            ## Blocked Members :
            // $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

            ## Relation & Member Columns :
            $isSent          = $type === 'request_sent';
            $relation        = $isSent ? 'receiver' : 'sender';
            $selfColumn      = $isSent ? 'sender_member_id' : 'receiver_member_id';
            $otherColumn     = $isSent ? 'receiver_member_id' : 'sender_member_id';

            ## Query
            $query = PhotoRequest::query()
                ->active()
                ->with(ApiCommonActionModel::relation($relation))
                ->where($selfColumn, $memberId)
                ->when($responseStatus !== '', function ($q) use ($responseStatus, $relation) {
                    $q->whereHas($relation, function ($receiver) use ($responseStatus) {
                        $receiver->where('receiver_response', 'LIKE', "%{$responseStatus}%");
                    });
                })
                // ->whereNotIn($otherColumn, $blockedIds)
                ->latest();

            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            ## Other Member IDs :
            $receiverIds = $resultList->pluck($otherColumn)->unique()->values()->toArray();

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

                ## Express Interests :
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

            ## Count Queries :
            // $sentRequestQuery = PhotoRequest::where('sender_member_id', $memberId)->whereNotIn('receiver_member_id', $blockedIds);
            // $receivedRequestQuery = PhotoRequest::where('receiver_member_id', $memberId)->whereNotIn('sender_member_id', $blockedIds);
            $sentRequestQuery = PhotoRequest::where('sender_member_id', $memberId);
            $receivedRequestQuery = PhotoRequest::where('receiver_member_id', $memberId);

            $dataArr = [
                'sentCount'     => (clone $sentRequestQuery)->count(),
                'receivedCount'  => (clone $receivedRequestQuery)->count(),
                'acceptedCount' => (clone $sentRequestQuery)->where('receiver_response', 'Accepted')->count(),
                'pendingReceivedCount' => (clone $receivedRequestQuery)->where('receiver_response', 'Pending')->count(),

                'resultCount' => $resultCount,
                'resultList' => $resultArr,
            ];
            $message = _getLangApi($request, 'msg_data_get_success');
            return ApiResponseService::success($message, $dataArr);
        } catch (Throwable $e) {

            Log::error('Photo request list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Send Photo Requests:
    public function send(Request $request, BehaviorLearningService $behaviorService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_member_id' => 'required|exists:registers,id',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $senderId   = $authUser->id;
            $receiverId = $request->receiver_member_id;

            // cannot send to self :
            if ($senderId == $receiverId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
            }
            
            // already accepted before :
            $alreadyAccepted = PhotoRequest::active()
                ->accepted()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->exists();

            if ($alreadyAccepted) {
                return ApiResponseService::error(_getLangApi($request, 'msg_you_can_already_view_photo'));
            }

            $blockedIds = BlockProfile::getBlockedMemberIds($senderId);
            if (in_array($receiverId, $blockedIds)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_photo_request_sent_to_block_member'));
            }

            // already pending :
            $alreadyPending = PhotoRequest::active()
                ->pending()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->exists();

            if ($alreadyPending) {
                return ApiResponseService::error(_getLangApi($request, 'msg_photo_request_already_sent'));
            }

            $receiver = Register::where('id', $receiverId)
                ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->first();

            // create request
            PhotoRequest::create([
                'sender_member_id'   => $senderId,
                'receiver_member_id' => $receiverId,
                'sender_matri_id'    => $authUser->matri_id,
                'receiver_matri_id'  => $receiver->matri_id,
                'receiver_response'  => 'Pending',
                'status'             => 'APPROVED',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            ## AI behavior tracking :
            $behaviorService->trackPhotoRequest($senderId, $receiverId);

            ## Send Notification :
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'photo_request_received'
            );

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Photo Request Received', $receiver, []);

            return ApiResponseService::success(_getLangApi($request, 'msg_photo_request_sent_successfully'));
        } catch (Throwable $e) {

            Log::error('Send Photo request API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Accept Or Reject Photo Request :
    public function acceptReject(Request $request, BehaviorLearningService $behaviorService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo_request_id'  => 'required|exists:photo_request,id',
                'action'            => 'required|in:accept,reject',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $photoRequest = PhotoRequest::where('id', $request->photo_request_id)
                ->where('receiver_member_id', $memberId)
                ->first();

            if (!$photoRequest) {
                return ApiResponseService::error(_getLangApi($request, 'msg_unauthorized_action'));
            }

            $response = $request->action === 'accept' ? 'Accepted' : 'Rejected';

            $photoRequest->update([
                'receiver_response' => $response
            ]);

            // Send notification only when accepted
            $receiver = Register::where('id', $photoRequest->sender_member_id)
                ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->first();

            if ($request->action === 'accept') {
                ## Send Notification :
                app(NotificationService::class)->sendNotification(
                    $authUser,   // viewer
                    $receiver,   // receiver
                    'photo_request_accept'
                );

                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Photo Request Accepted', $receiver, []);
            } else {
                ## Send Notification :
                app(NotificationService::class)->sendNotification(
                    $authUser,   // viewer
                    $receiver,   // receiver
                    'photo_request_rejected'
                );

                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Photo Request Rejected', $receiver, []);
            }

            $isAccepted = $request->action === 'accept';
            ## AI behavior tracking — direction is (receiver -> sender).
            $isAccepted
                ? $behaviorService->trackPhotoAccepted($memberId, $photoRequest->sender_member_id)
                : $behaviorService->trackPhotoRejected($memberId, $photoRequest->sender_member_id);

            $message = $request->action === 'accept' ? __('messages.msg_photo_request_accept_message') : __('messages.msg_photo_request_decline_message');
            return ApiResponseService::success($message);
        } catch (Throwable $e) {

            Log::error('Photo request accept or reject API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Remove From Photo Request :
    public function updateInterest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo_request_id'  => 'required|exists:photo_request,id'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $memberId = auth()->guard('api')->id();
            $photoRequest = PhotoRequest::where('id', $request->photo_request_id)->where('sender_member_id', $memberId)->first();

            if (!$photoRequest) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            $photoRequest->delete();

            return ApiResponseService::success(_getLangApi($request, 'msg_removed_successfully'));
        } catch (Throwable $e) {

            Log::error('Pending Photo request remove API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

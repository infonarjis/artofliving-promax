<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\BehaviorLearningService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Illuminate\Http\Request;

class PhotoRequestController extends Controller
{
    public function index(Request $request)
    {
        $memberId = auth()->guard('web')->id();
        $type = $request->type ?? 'request_sent';
        ## Relation & Member Columns :
        $isIviewed      = $type === 'request_sent';
        $relation       = $isIviewed ? 'receiver' : 'sender';
        $selfColumn     = $isIviewed ? 'sender_member_id' : 'receiver_member_id';
        $otherColumn    = $isIviewed ? 'receiver_member_id' : 'sender_member_id';

        $query = PhotoRequest::query()
            ->active()
            ->with(ApiCommonActionModel::relation($relation))
            ->where($selfColumn, $memberId)
            ->latest();

        $resultData = $query->paginate(10);
        $otherMemberIds = $resultData->pluck($otherColumn)->unique()->values()->toArray();

        // Correct PhotoRequest check :
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $otherMemberIds);

        foreach ($resultData as $item) {
            $otherId = $item->{$otherColumn};

            $item->hasPhotoRequestAccess = in_array($otherId, $acceptedRequests);
        }

        if ($request->ajax()) {
            return response(
                view(
                    _getConstant('dir_path.WEB_DIR_PATH') . '.photoRequest.ajax_result',
                    compact('resultData', 'type')
                )->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(_getConstant('dir_path.WEB_DIR_PATH') . '.photoRequest.index', compact('resultData', 'type'))
        )->header('Vary', 'X-Requested-With');
    }

    // Send Photo Requests:
    public function send(Request $request, BehaviorLearningService $behaviorService)
    {
        $request->validate([
            'receiver_id' => 'required|exists:registers,id',
        ]);

        $authUser = auth()->guard('web')->user();
        $senderId   = $authUser->id;
        $receiverId = $request->receiver_id;

        // cannot send to self :
        if ($senderId == $receiverId) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_something_went_wrong')
            ]);
        }

        // already accepted before :
        $alreadyAccepted = PhotoRequest::active()
                ->accepted()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->exists();

        if ($alreadyAccepted) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_you_can_already_view_photo')
            ]);
        }

        $blockedIds = BlockProfile::getBlockedMemberIds($senderId);
        if (in_array($receiverId, $blockedIds)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_photo_request_sent_to_block_member')
            ]);
        }

        // already pending :
        $alreadyPending = PhotoRequest::active()
            ->pending()
            ->where('sender_member_id', $senderId)
            ->where('receiver_member_id', $receiverId)
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_photo_request_already_sent')
            ]);
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

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_photo_request_sent_successfully')
        ]);
    }

    public function accept($id, BehaviorLearningService $behaviorService)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $photoRequest = PhotoRequest::where('id', $id)
            ->where('receiver_member_id', $memberId)
            ->first();

        if (!$photoRequest) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_unauthorized_action')
            ]);
        }

        $photoRequest->update([
            'receiver_response' => 'Accepted'
        ]);

        $receiver = Register::where('id', $photoRequest->sender_member_id)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();

        ## AI behavior tracking :
        $behaviorService->trackPhotoAccepted($memberId, $photoRequest->sender_member_id);

        ## Send Notification :
        app(NotificationService::class)->sendNotification(
            $authUser,   // viewer
            $receiver,    // receiver
            'photo_request_accept'
        );

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Photo Request Accepted', $receiver, []);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_photo_request_accept_message')
        ]);
    }

    public function reject($id, BehaviorLearningService $behaviorService)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;
        $photoRequest = PhotoRequest::where('id', $id)
            ->where('receiver_member_id', $memberId)
            ->first();

        if (!$photoRequest) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_unauthorized_action')
            ]);
        }

        $receiver = Register::where('id', $photoRequest->sender_member_id)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();

        $photoRequest->update([
            'receiver_response' => 'Rejected'
        ]);

        ## AI behavior tracking :
        $behaviorService->trackPhotoRejected($memberId, $photoRequest->sender_member_id);

        ## Send Notification :
        app(NotificationService::class)->sendNotification(
            $authUser,   // viewer
            $receiver,    // receiver
            'photo_request_rejected'
        );

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Photo Request Rejected', $receiver, []);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_photo_request_decline_message')
        ]);
    }

    ## Remove From Photo Request :
    public function remove($id)
    {
        $memberId = auth()->guard('web')->id();
        $photoRequest = PhotoRequest::where('id', $id)->where('sender_member_id', $memberId)->first();

        if (!$photoRequest) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_record_not_found')
            ]);
        }

        $photoRequest->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_removed_successfully')
        ]);
    }
}

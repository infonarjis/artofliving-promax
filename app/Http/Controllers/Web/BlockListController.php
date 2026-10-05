<?php

namespace App\Http\Controllers\Web;

use App\Models\BlockProfile;
use App\Http\Controllers\Controller;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\BehaviorLearningService;
use Illuminate\Http\Request;

class BlockListController extends Controller
{
    public function index(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        ## Relation For Member Columns :
        $relation = 'receiver';
        $resultData = BlockProfile::with(ApiCommonActionModel::relation($relation))
            ->where('sender_member_id', $memberId)
            ->active()
            ->latest()
            ->paginate(10);

        // Get all receiver ids in one go
        $receiverIds = $resultData->pluck('receiver_member_id')->toArray();

        // Fetch accepted photo requests via model method
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        // Attach flag
        foreach ($resultData as $item) {
            $item->hasPhotoRequestAccess = in_array($item->receiver_member_id, $acceptedRequests);
        }

        if ($request->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.blocklist.ajax_result', compact('resultData'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(_getConstant('dir_path.WEB_DIR_PATH') . '.blocklist.index', compact('resultData'))
        )->header('Vary', 'X-Requested-With');
    }

    ## Add & Remove Block:
    public function addRemove(Request $request, BehaviorLearningService $behaviorService)
    {
        $request->validate([
            'receiver_member_id' => 'required|integer|exists:registers,id',
        ]);

        $sender     = auth()->guard('web')->user();
        $senderId   = $sender->id;
        $receiverId = $request->receiver_member_id;

        if ($senderId == $receiverId) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_action'),
            ]);
        }

        // IMPORTANT: do NOT use active()
        $block = BlockProfile::withTrashed()
            ->where('sender_member_id', $senderId)
            ->where('receiver_member_id', $receiverId)
            ->first();

        if ($block) {
            if ($block->trashed()) {
                // RESTORE BLOCK (was unblocked)
                $block->restore();
                $block->update([
                    'created_at' => now(),
                ]);

                ## AI behavior tracking :
                $behaviorService->trackBlock($senderId, $receiverId);

                return response()->json([
                    'status' => true,
                    'type' => 'unblocked',
                    'message' => __('messages.msg_unblocked_successfully'),
                ]);
            }

            // UNBLOCK (soft delete)
            $block->delete();

            return response()->json([
                'status' => true,
                'type' => 'blocked',
                'message' => __('messages.msg_blocked_successfully'),
            ]);
        }

        // FIRST TIME BLOCK
        BlockProfile::create([
            'sender_member_id' => $senderId,
            'sender_matri_id'    => $sender->matri_id,
            'receiver_member_id' => $receiverId,
            'receiver_matri_id'  => Register::where('id', $receiverId)->value('matri_id')
        ]);

        ## AI behavior tracking :
        $behaviorService->trackBlock($senderId, $receiverId);

        return response()->json([
            'status' => true,
            'type' => 'blocked',
            'message' => __('messages.msg_blocked_successfully'),
        ]);
    }

    ## Remove From BlockList :
    public function remove($id)
    {
        $memberId = auth()->guard('web')->id();
        $blocklist = BlockProfile::where('id', $id)->where('sender_member_id', $memberId)->first();

        if (!$blocklist) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_record_not_found')
            ]);
        }

        $blocklist->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_block_removed_successfully')
        ]);
    }
}

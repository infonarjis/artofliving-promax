<?php

namespace App\Http\Controllers\Web;

use App\Models\ShortlistProfile;
use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\BehaviorLearningService;
use Throwable;
use Illuminate\Http\Request;

class ShortListController extends Controller
{
    public function index(Request $request)
    {
        $memberId = auth()->guard('web')->id();
        
        ## Relation For Member Columns :
        $relation = 'receiver';
        $resultData = ShortlistProfile::with(ApiCommonActionModel::relation($relation))
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

        // AJAX response
        if (request()->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.shortlist.ajax_result', compact('resultData'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        // Normal page load :
        return response(
            view(_getConstant('dir_path.WEB_DIR_PATH') . '.shortlist.index', compact('resultData'))
        )->header('Vary', 'X-Requested-With');
    }

    ## Add & Remove Shortlist :
    public function addRemove(Request $request, BehaviorLearningService $behaviorService)
    {
        try {
            $request->validate([
                'receiver_member_id' => 'required|integer|exists:registers,id',
            ]);

            $sender     = auth()->guard('web')->user();
            $senderId   = $sender->id;
            $receiverId = (int) $request->receiver_member_id;

            // Prevent self shortlist
            if ($senderId === $receiverId) {
                return response()->json([
                    'status'  => false,
                    'message' => __('messages.msg_invalid_action')
                ]);
            }

            // Check block (both directions)
            $blockedIds = BlockProfile::getBlockedMemberIds($senderId);
            if (in_array($receiverId, $blockedIds)) {
                return response()->json([
                    'status'  => false,
                    'message' => __('messages.msg_user_blocked')
                ]);
            }

            // Check existing (even soft deleted)=
            $existing = ShortlistProfile::withTrashed()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->first();
            if ($existing) {

                if ($existing->trashed()) {
                    // RESTORE → Add to shortlist again
                    $existing->restore();
                    $existing->update([
                        'created_at' => now(),
                    ]);

                    ## AI behavior tracking :
                    $behaviorService->trackShortlist($senderId, $receiverId);

                    return response()->json([
                        'status'  => true,
                        'type'    => 'added',
                        'message' => __('messages.msg_shortlist_added_successfully'),
                    ]);
                }

                return response()->json([
                    'status'  => true,
                    'type'    => 'removed',
                    'message' => __('messages.msg_shortlist_removed_successfully'),
                ]);
            }

            ## AI behavior tracking :
            $behaviorService->trackShortlist($senderId, $receiverId);

            // Fresh insert
            ShortlistProfile::create([
                'sender_member_id'   => $senderId,
                'sender_matri_id'    => $sender->matri_id,
                'receiver_member_id' => $receiverId,
                'receiver_matri_id'  => Register::where('id', $receiverId)->value('matri_id'),
                'status'             => 'APPROVED',
            ]);

            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_shortlist_added_successfully')
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_something_went_wrong')
            ]);
        }
    }

    ## Remove From Shortlist :
    public function remove(int $id)
    {
        $memberId = auth()->guard('web')->id();
        $shortlist = ShortlistProfile::where('id', $id)->where('sender_member_id', $memberId)->first();

        if (!$shortlist) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_record_not_found')
            ]);
        }

        // Check block (both directions)
        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
        if (in_array($shortlist->receiver_member_id, $blockedIds)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_user_blocked')
            ]);
        }

        $shortlist->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_shortlist_removed_successfully')
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\MatchList;
use App\Models\MatchPairMeeting;
use App\Models\PhotoRequest;
use Illuminate\Http\Request;

class ReceiveAdminMatchController extends Controller
{
    public function index()
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
        $resultArr = MatchList::where('sender_member_id', $memberId)
            ->whereNotIn('sender_member_id', $blockedIds)
            ->with(['receiver'])
            ->latest()
            ->paginate(10);
        // Get all receiver ids in one go
        $receiverIds = $resultArr->pluck('receiver_member_id')->toArray();

        // Fetch accepted photo requests via model method
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        // Attach flag
        foreach ($resultArr as $item) {
            $item->hasPhotoRequestAccess = in_array($item->receiver_member_id, $acceptedRequests);
        }

        if (request()->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.receiveAdminMatch.ajax_result', compact('resultArr', 'authUser'))->render();
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.receiveAdminMatch.index', compact('resultArr', 'authUser'));
    }


    public function acceptReject(Request $request)
    {
        if (blank($request->id)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_req_send')
            ]);
        }

        $memberData = auth()->guard('web')->user();

        MatchList::where('id', $request->id)->update([
            'response' => $request->response,
            'updated_at' => _getCurrentDate()
        ]);

        $personalizeMemberList = MatchList::find($request->id);

        if ($request->response == 1) {
            MatchPairMeeting::updateOrInsert(
                [
                    'match_id' => $personalizeMemberList->id,
                    'member1_id' => $memberData->id,
                    'member2_id' => $request->otherUserId,
                ],
                [
                    'member1_matri_id' => $memberData->matri_id,
                    'member2_matri_id' => $request->otherUsermatriId,
                    'staff_id' => $personalizeMemberList->sent_by_id,
                    'updated_at' => _getCurrentDate(),
                    'created_at' => _getCurrentDate(),
                ]
            );
        }

        if ($request->response == 2) {
            MatchPairMeeting::where([
                'match_id'   => $personalizeMemberList->id,
                'member1_id' => $memberData->id,
                'member2_id' => $request->otherUserId,
            ])->delete();
        }

        return response()->json([
            'status' => true,
            'response' => $request->response,
            'message' => __('messages.msg_status_updated_successfully')
        ]);
    }
}

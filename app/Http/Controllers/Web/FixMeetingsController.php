<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MatchMemberMeeting;
use App\Models\PhotoRequest;
use Illuminate\Http\Request;

class FixMeetingsController extends Controller
{
    public function index()
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $resultArr = MatchMemberMeeting::active()
            ->with(['member1', 'member2'])
            ->where(function ($q) use ($memberId) {
                $q->where('member1_id', $memberId)
                  ->orWhere('member2_id', $memberId);
            })
            ->latest()
            ->paginate(1);

        // Collect other member ids (only current page)
        $otherIds = $resultArr->getCollection()->map(
            fn ($m) => $m->member1_id == $memberId ? $m->member2_id : $m->member1_id
        )->unique()->values()->toArray();

        $acceptedPhotoIds = PhotoRequest::active()
            ->accepted()
            ->where('sender_member_id', $memberId)
            ->whereIn('receiver_member_id', $otherIds)
            ->pluck('receiver_member_id')
            ->toArray();

        $resultArr->getCollection()->transform(function ($item) use ($memberId, $acceptedPhotoIds) {
            $other = $item->member1_id == $memberId ? $item->member2 : $item->member1;
            $other->hasPhotoRequestAccess = in_array($other->id, $acceptedPhotoIds);
            $item->otherMember = $other;
            return $item;
        });

        if (request()->ajax()) {
            return view('web.fixMeetings.ajax_result', compact('resultArr', 'authUser'))->render();
        }

        return view('web.fixMeetings.index', compact('resultArr', 'authUser'));
    }

    public function acceptReject(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer',
            'response'    => 'required|in:1,2',
            'memberLabel' => 'required|string'
        ]);

        $authId = auth()->guard('web')->id();

        ## Fetch meeting ONLY if belongs to this user :
        $meeting = MatchMemberMeeting::where('id', $request->id)
            ->where(function ($q) use ($authId) {
                $q->where('member1_id', $authId)
                  ->orWhere('member2_id', $authId);
            })
            ->first();

        if (!$meeting) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_unauthorized_action')
            ]);
        }

        ## Decide which status column user is allowed to update :
        $allowedMemberColumn = $meeting->member1_id == $authId
            ? 'member1_status'
            : 'member2_status';

        ## Validate memberLabel strictly :
        if ($request->memberLabel !== $allowedMemberColumn && $request->memberLabel !== 'meeting_status') {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_invalid_action_column')
            ]);
        }

        ## Meeting completion allowed only if both accepted :
        if ($request->memberLabel === 'meeting_status') {
            if (
                $meeting->member1_status != 1 ||
                $meeting->member2_status != 1
            ) {
                return response()->json([
                    'status'  => false,
                    'message' => __('messages.msg_both_members_must_accept_first')
                ]);
            }
        }

        ## Protect reject / remark columns :
        $allowedRejectColumns = [
            'member1_reject_remark',
            'member2_reject_remark',
            'meeting_remark'
        ];

        if ($request->filled('rejectBy') && !in_array($request->rejectBy, $allowedRejectColumns)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_invalid_remark_field')
            ]);
        }

        ## Prepare update data:
        $updateArr = [
            $request->memberLabel => $request->response,
            'updated_at' => now()
        ];

        ## Reject remark :
        if ($request->response == 2 && $request->filled('rejectBy')) {
            $updateArr[$request->rejectBy] = $request->member_reject_remark;
        }

        ## Complete remark:
        if ($request->memberLabel === 'meeting_status' && $request->filled('meeting_remark')) {
            $updateArr['meeting_remark'] = $request->meeting_remark;
        }

        $meeting->update($updateArr);

        return response()->json([
            'status'  => true,
            'message' => _getLang('msg_status_changed_successfully')
        ]);
    }
}
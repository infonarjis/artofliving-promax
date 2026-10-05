<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiMatchQueue;
use App\Models\BlockProfile;
use App\Models\PhotoRequest;
use App\Models\Register;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AiAutoInterestController extends Controller
{
    public function index()
    {   
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
        $todayDate = Carbon::today()->format('Y-m-d');
        $aiMatchProfile = AiMatchQueue::with(['matchedMember'])
            ->where('member_id',$authUser->id)
            ->whereNotIn('matched_member_id', $blockedIds)
            ->whereDate('match_date', $todayDate)->get();
        // Get all receiver ids in one go
        $receiverIds = $aiMatchProfile->pluck('receiver_member_id')->toArray();
        // Fetch accepted photo requests via model method
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        // Attach flag
        foreach ($aiMatchProfile as $item) {
            $item->hasPhotoRequestAccess = in_array($item->receiver_member_id, $acceptedRequests);
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.aiAutoInterest.index', compact('authUser','aiMatchProfile'));
    }

    public function toggleAutoInterest(Request $request)
    {
        $memberId = auth()->guard('web')->id();
        $member = Register::findOrFail($memberId);

        $member->update([
            'auto_interest_enabled' => $request->auto_interest_enabled ? 1 : 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Auto interest updated successfully',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'daily_interest_limit' => 'required|integer|min:1|max:100',
            'min_match_percentage' => 'required|integer|min:0|max:100',
        ]);

        $memberId = auth()->guard('web')->id();
        $member = Register::findOrFail($memberId);

        $member->update([
            'daily_interest_limit' => $request->daily_interest_limit,
            'min_match_percentage' => $request->min_match_percentage,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Settings updated successfully',
        ]);
    }
}

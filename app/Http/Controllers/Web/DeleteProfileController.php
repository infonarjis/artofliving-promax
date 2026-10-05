<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MemberDeleteProfile;
use App\Services\AdminCommonActionModel;
use Illuminate\Http\Request;

class DeleteProfileController extends Controller
{
    public function index()
    {
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.deleteProfile.index');
    }

    public function deleteProfile(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

        // Prevent duplicate pending request
        $alreadyPending = MemberDeleteProfile::where('sender', $memberId)
            ->where('admin_action_status', 0)
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_you_already_have_a_pending_delete_request')
            ]);
        }

        MemberDeleteProfile::create([
            'sender'  => $memberId,
            'reason'  => $request->reason,
            'sent_on' => _getCurrentDate(),
        ]);

        ## Send Admin Notification :
        AdminCommonActionModel::sendAdminNotification($authUser->id, $authUser->matri_id, 'profile_delete_request');

        return response()->json([
            'status'  => true,
            'message' => __('messages.msg_your_delete_profile_request_has_been_sent_to_admin')
        ]);
    }
}

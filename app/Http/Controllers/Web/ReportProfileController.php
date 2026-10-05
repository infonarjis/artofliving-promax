<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProfileReportSpam;
use App\Models\Register;
use Illuminate\Http\Request;

class ReportProfileController extends Controller
{

    public function submit(Request $request)
    {
        $authUser = auth()->guard('web')->user();

        $request->validate([
            'report_member_id'   => 'required|integer|exists:registers,id',
            'report_type' => 'required|integer',
            'report_reason' => 'nullable|string|max:1000',
        ]);

        $reportId = (int) $request->report_member_id;

        // Cannot report yourself
        if ($authUser->id === $reportId) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_you_cannot_report_your_own_profile')
            ]);
        }

        // Already reported check
        if (ProfileReportSpam::alreadyReported($reportId, $authUser->id)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_you_already_reported_this_profile')
            ]);
        }

        $reportedUser = Register::select(['id','matri_id'])->find($reportId);

        ProfileReportSpam::create([
            'report_id'            => $reportedUser->id,
            'report_matri_id'      => $reportedUser->matri_id,
            'report_by'            => $authUser->id,
            'report_by_matri_id'   => $authUser->matri_id,
            'report_type'          => $request->report_type ?? null,
            'reason'               => $request->report_reason ?? null,
            'created_at'           => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_profile_reported_successfully')
        ]);
    }
}

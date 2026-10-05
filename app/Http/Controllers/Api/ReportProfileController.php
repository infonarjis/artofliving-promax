<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileReportSpam;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ReportProfileController extends Controller
{

    public function submit(Request $request): JsonResponse
    {

        $authUser = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'report_member_id'   => 'required|integer|exists:registers,id',
            'report_type' => 'required|integer',
            'report_reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }
        try {

            $reportId = (int) $request->report_member_id;

            // Cannot report yourself
            if ($authUser->id === $reportId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_you_cannot_report_your_own_profile'));
            }

            // Already reported check
            if (ProfileReportSpam::alreadyReported($reportId, $authUser->id)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_you_already_reported_this_profile'));
            }

            $reportedUser = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->find($reportId);

            ProfileReportSpam::create([
                'report_id'            => $reportedUser->id,
                'report_matri_id'      => $reportedUser->matri_id,
                'report_by'            => $authUser->id,
                'report_by_matri_id'   => $authUser->matri_id,
                'report_type'          => $request->report_type ?? null,
                'reason'               => $request->report_reason ?? null,
                'created_at'           => now()
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_profile_reported_successfully'));
        } catch (Throwable $e) {

            Log::error('report spam profile API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

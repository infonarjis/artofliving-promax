<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\Request;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileVerificationController extends Controller
{

    public function generateOtp(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\Register|null $user */
            $user = auth()->guard('api')->user();

            if ($user->mobile_verify_status === 'Yes') {
                return ApiResponseService::error(_getLangApi($request, 'msg_mobile_already_verified'));
            }

            // Cooldown 60 sec
            if ($user->mobile_otp_expires_at && now()->lt($user->mobile_otp_expires_at->subSeconds(240))) {
                return ApiResponseService::error(_getLangApi($request, 'msg_please_wait_before_requesting_new_otp'));
            }

            $otp = _generateOtp(6);

            $user->update([
                'mobile_otp' => $otp,
                'mobile_otp_expires_at' => now()->addMinutes(5)
            ]);

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp' => $otp]);

            return ApiResponseService::success(_getLangApi($request, 'msg_otp_has_sent_successfully'), ['otp_verify' => $otp]);
        } catch (Throwable $e) {

            Log::error('My profile generate otp API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function resendOtp(): JsonResponse
    {
        return $this->generateOtp(request());
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|digits:6'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            /** @var \App\Models\Register|null $user */
            $user = auth()->guard('api')->user();

            if ($request->otp != $user->mobile_otp) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_otp_request'));
            }

            $user->update([
                'mobile_verify_status' => 'Yes',
                'mobile_otp' => null,
                'mobile_otp_expires_at' => null
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_mobile_verified_successfully'));
        } catch (Throwable $e) {
            Log::error('My profile verify otp API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

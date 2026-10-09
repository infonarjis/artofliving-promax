<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\Register;
use App\Models\UserLoginHistory;
use App\Services\Api\ApiResponseService;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Throwable;

class LoginController extends Controller
{
    /**
     * Login API
     */
    public function authenticate(Request $request): JsonResponse
    {
        // try {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|string',
            'mobile' => 'required',
            'password'          => 'required|string',
            'user_agent'        => 'required|string',
            'android_device_id' => 'nullable|string',
            'ios_device_id'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        ## Login by Email / Matri ID :
        $mobile = $request->country_code . '-' . $request->mobile;
        $user = Register::where('mobile', $mobile)->first();
        // Check whether the user exists before accessing properties.
        if (!$user) {
            return ApiResponseService::error(
                _getLangApi($request, 'msg_incorrect_login_details')
            );
        }
        // Password Check (handles legacy MD5 + migration to Hash)
        if ($this->isLegacyMd5Hash($user->password)) {
            if (!hash_equals($user->password, md5($request->password))) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_incorrect_login_details')
                );
            }
            // Migrate legacy MD5 password to Laravel's secure hash.
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();
        } else {
            if (!Hash::check($request->password, $user->password)) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_incorrect_login_details')
                );
            }
        }

        ## Status Check :
        if (method_exists($user, 'trashed') && $user->trashed()) {
            return ApiResponseService::error(_getLangApi($request, 'msg_account_deactivated'));
        }

        if ($user->status === 'Suspended') {
            return ApiResponseService::error(_getLangApi($request, 'msg_account_suspended'));
        }

        if ($user->status === 'UNAPPROVED' && $user->is_verify === 'Yes') {
            return ApiResponseService::error(_getLangApi($request, 'msg_account_not_approved'));
        }

        $canLogin = ($user->status === 'APPROVED' && $user->is_verify === 'Yes')
            || ($user->status === 'UNAPPROVED' && $user->is_verify === 'No') || ($user->status === 'APPROVED' && $user->is_verify === 'No');

        if (!$canLogin) {
            return ApiResponseService::error(_getLangApi($request, 'msg_incorrect_login_details'));
        }

        /*
            |--------------------------------------------------------------------------
            | Delete old tokens (optional)
            |--------------------------------------------------------------------------
            */
        $user->tokens()->delete();

        /*
            |--------------------------------------------------------------------------
            | Sanctum Token
            |--------------------------------------------------------------------------
            */
        $token = $user->createToken('mobile')->plainTextToken;

        ## Stepper Enable OR  Disable:
        $user->register_step_status = 'Disable'; // Enable OR  Disable:

        ## Login History :
        $this->logHistory($request, $user);
        return ApiResponseService::success(
            _getLangApi($request, 'msg_login_successfull'),
            [
                'token' => $token,
                'member_data' => $user
            ]
        );
        // } catch (Throwable $e) {
        //     Log::error('Check login authenticate API failed.', [
        //         'message' => $e->getMessage(),
        //         'file'    => $e->getFile(),
        //         'line'    => $e->getLine(),
        //     ]);
        //     return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        // }
    }

    /**
     * Detect a legacy raw MD5 password hash (32 hex chars),
     * as opposed to a bcrypt/argon2 hash produced by Laravel's Hash facade.
     */
    private function isLegacyMd5Hash(?string $hash): bool
    {
        return is_string($hash) && preg_match('/^[a-f0-9]{32}$/i', $hash) === 1;
    }

    /**
     * Login History
     */
    private function logHistory(Request $request, Register $user): void
    {
        $updateData = [
            'latitude'  => $request->latitude ?? '',
            'longitude' => $request->longitude ?? '',
        ];

        if ($request->user_agent === _getConstant('user_agent.ANDROID_USER_AGENT')) {
            $updateData = array_merge($updateData, [
                'android_device_id' => $request->android_device_id ?? '',
                'user_agent'        => _getConstant('user_agent.ANDROID_USER_AGENT'),
            ]);
        } elseif ($request->user_agent === _getConstant('user_agent.IOS_USER_AGENT')) {
            $updateData = array_merge($updateData, [
                'ios_device_id'     => $request->ios_device_id ?? '',
                'user_agent'        => _getConstant('user_agent.IOS_USER_AGENT'),
            ]);
        }
        Register::where('id', $user->id)->update($updateData);

        ## Update Login History :
        $agent = new Agent();
        if ($request->user_agent === 'NI-IOS') {
            $loginFrom = 'IOS APP';
        } elseif ($request->user_agent === 'NI-AOS') {
            $loginFrom = 'Android APP';
        } else {
            $loginFrom = 'Unknown';
        }
        UserLoginHistory::create([
            'member_id' => $user->id,
            'matri_id' => $user->matri_id,
            'email' => $user->email,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'login_from' => $loginFrom,
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'device' => $agent->device(),
            'is_mobile' => $agent->isMobile(),
        ]);
    }

    ## Send Otp :
    public function sendOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required|string',
                'mobile' => 'required|digits_between:8,12',
                'otp_login_method' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $mobile = $request->country_code . '-' . $request->mobile;

            $user = Register::where('mobile', $mobile)->where('status', 'APPROVED')->first();

            if (!$user) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_mobile_number'));
            }

            $otpLoginMethod = $request->otp_login_method ?? 'sms'; // Otp Verification Types :
            $data = [];
            if ($otpLoginMethod == 'sms') {
                $otp = _generateOtp(6);

                LoginOtp::create([
                    'mobile' => $mobile,
                    'otp' => Hash::make($otp),
                    'expires_at' => now()->addMinutes(5),
                    'is_used' => 0
                ]);

                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp' => $otp]);

                $data = [
                    'otp_verify' => $otp
                ];

                $message = _getLangApi($request, 'msg_otp_has_sent_successfully');
            } else {
                $message = _getLangApi($request, 'msg_mobile_verified_successfully');
            }

            return ApiResponseService::success($message, $data);
        } catch (Throwable $e) {
            Log::error('login send otp API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Resend Otp :
    public function resendOtp(Request $request): JsonResponse
    {
        return $this->sendOtp($request);
    }

    ## Verify Otp :
    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'country_code'      => 'required|string',
                'mobile'            => 'required|digits_between:8,12',
                'otp'               => 'nullable|digits:6',
                'user_agent'        => 'nullable|string',
                'android_device_id' => 'nullable|string',
                'ios_device_id'     => 'nullable|string',
                'otp_login_method'  => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $mobile = $request->country_code . '-' . $request->mobile;

            $otpLoginMethod = $request->otp_login_method ?? 'sms'; // Otp Verification Types :
            if ($otpLoginMethod == 'sms') {

                $otpRow = LoginOtp::where('mobile', $mobile)->where('is_used', 0)->where('expires_at', '>', now())->latest()->first();
                if (!$otpRow || !Hash::check($request->otp, $otpRow->otp)) {
                    return ApiResponseService::error(
                        __('messages.msg_invalid_or_expired_otp')
                    );
                }
            }

            $user = Register::where('mobile', $mobile)->where('status', 'APPROVED')->first();
            if (!$user) {
                return ApiResponseService::error(
                    __('messages.msg_invalid_mobile_number')
                );
            }
            if ($user->mobile_verify_status === 'No') {
                $user->update([
                    'mobile_verify_status' => 'Yes'
                ]);
            }

            if ($otpLoginMethod == 'sms') {
                $otpRow->update([
                    'is_used' => 1
                ]);
            }
            $this->logHistory($request, $user);

            $user->tokens()->delete();
            $token = $user->createToken('mobile')->plainTextToken;

            ## Stepper Enable OR  Disable:
            $user->register_step_status = 'Disable'; // Enable OR  Disable:

            return ApiResponseService::success(
                _getLangApi($request, 'msg_login_successfull'),
                [
                    'token' => $token,
                    'member_data' => $user
                ]
            );
        } catch (Throwable $e) {
            Log::error('verify otp API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return ApiResponseService::error(_getLangApi($request, 'msg_unauthenticated'));
            }
            $user->tokens()->delete();

            Register::where('id', $user->id)->update([
                'android_device_id' => '',
                'ios_device_id'     => ''
            ]);
            return ApiResponseService::success(_getLangApi($request, 'msg_logged_out_successfully'));
        } catch (Throwable $e) {

            Log::error('logout API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Get Tocken List Api:
    public function getVerifyContent(Request $request): JsonResponse
    {
        try {
            $resultArr = [
                'lbl_under_review' => __('messages.lbl_under_review'),
                'lbl_pending_verification' => __('messages.lbl_pending_verification'),
                'lbl_thank_you_signing_up_verification' => __('messages.lbl_thank_you_signing_up_verification'),
                'lbl_registration_submitted' => __('messages.lbl_registration_submitted'),
                'lbl_registration_submitted_desc' => __('messages.lbl_registration_submitted_desc'),
                'lbl_profile_verification' => __('messages.lbl_profile_verification'),
                'lbl_profile_verification_desc' => __('messages.lbl_profile_verification_desc'),
                'lbl_access_activation' => __('messages.lbl_access_activation'),
                'lbl_access_activation_desc' => __('messages.lbl_access_activation_desc'),
                'lbl_phone_support' => __('messages.lbl_phone_support'),
                'mobile' => '(+91) 9900038442/2',
                'lbl_email_support' => __('messages.lbl_email_support'),
                'email' => 'info@artofliving.org',
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $resultArr);
        } catch (Throwable $e) {
            Log::error('Get Tocken API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

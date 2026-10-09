<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;
use App\Models\CountryMaster;
use App\Models\LoginOtp;
use App\Models\Register;
use App\Models\UserLoginHistory;
use App\Services\EmailSendService;
use App\Services\FirebaseAuthService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Throwable;

class LoginController extends Controller
{
    /** +91 => custom SMS, every other country code => Firebase */
    private const SMS_COUNTRY_CODE = '+1';

    public function index()
    {
        $captchaCode = CaptchaHelper::generate('login_captcha');

        $siteSetting = _getSiteSetting();
        $firebaseConfig = $siteSetting['firebase_configuration'] ?? null;
        if (is_string($firebaseConfig)) {
            $firebaseConfig = json_decode($firebaseConfig, true) ?: null;
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.login.index', compact(
            'captchaCode',
            'firebaseConfig'
        ));
    }

    /**
     * MOBILE + PASSWORD LOGIN
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string',
            'mobile' => 'required',
            'password' => 'required|string',
            'captcha_code' => 'required|string'
        ]);

        if (!CaptchaHelper::validate($request->captcha_code, 'login_captcha')) {
            return $this->captchaError();
        }

        $mobile = $request->country_code . '-' . $request->mobile;
        $credentials = ['mobile' => $mobile, 'password' => $request->password];

        if (!Auth::guard('web')->attempt($credentials, $request->remember ?? false)) {
            return $this->invalidLogin();
        }

        /** @var \App\Models\Register|null $user */
        $user = Auth::guard('web')->user();

        if ($user->trashed()) {
            Auth::guard('web')->logout();
            return $this->invalidLogin('msg_account_deactivated');
        }

        if ($user->status === 'Suspended') {
            Auth::guard('web')->logout();
            return $this->invalidLogin('msg_account_suspended');
        }

        if ($user->status === 'UNAPPROVED' && $user->is_verify === 'Yes') {
            Auth::guard('web')->logout();
            return $this->invalidLogin('msg_account_not_approved');
        }

        $canLogin = ($user->status === 'APPROVED' && $user->is_verify === 'Yes')
            || ($user->status === 'UNAPPROVED' && $user->is_verify === 'No')
            || ($user->status === 'APPROVED' && $user->is_verify === 'No');

        if (!$canLogin) {
            Auth::guard('web')->logout();
            return $this->invalidLogin();
        }

        $this->logHistory($request);

        return response()->json([
            'status' => true,
            'redirect' => route('web.dashboard.index')
        ]);
    }

    /**
     * CHECK MOBILE (non +91): called by the browser BEFORE Firebase sends the SMS,
     * so SMS is never sent to a number that is not in the system.
     */
    public function checkMobile(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'string', 'exists:country_master,country_code'],
            'mobile' => 'required|digits_between:6,12',
        ]);

        if ($request->country_code === self::SMS_COUNTRY_CODE) {
            return $this->mobileError('msg_invalid_request');
        }

        $mobile = $request->country_code . '-' . $request->mobile;

        [$user, $error] = $this->resolveLoginUser($mobile);
        if (!$user) {
            return $this->mobileError($error);
        }

        return response()->json(['status' => true]);
    }

    /**
     * SEND OTP (custom SMS, +91 only)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'string', 'exists:country_master,country_code'],
            'mobile' => 'required|digits_between:8,12'
        ]);

        if ($request->country_code !== self::SMS_COUNTRY_CODE) {
            return $this->mobileError('msg_invalid_request');
        }

        $mobile = $request->country_code . '-' . $request->mobile;

        [$user, $error] = $this->resolveLoginUser($mobile);
        if (!$user) {
            return $this->mobileError($error);
        }

        if ($this->isOtpCooldownActive($mobile)) {
            return $this->mobileError('msg_please_wait_before_requesting_a_new_otp');
        }

        // Invalidate previous unused OTPs
        LoginOtp::where('mobile', $mobile)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        $otp = _generateOtp(6);

        LoginOtp::create([
            'mobile' => $mobile,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'is_used' => 0
        ]);

        app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp' => $otp]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_otp_has_sent_successfully')
        ]);
    }

    /**
     * RESEND OTP (custom SMS, +91 only)
     */
    public function resendOtp(Request $request)
    {
        $request->validate(['mobile' => 'required|string']);

        if (!$this->isSmsMobile($request->mobile)) {
            return $this->mobileError('msg_invalid_request');
        }

        $mobile = $request->mobile;

        [$user, $error] = $this->resolveLoginUser($mobile);
        if (!$user) {
            return $this->mobileError($error);
        }

        if ($this->isOtpCooldownActive($mobile)) {
            return $this->mobileError('msg_please_wait_before_requesting_a_new_otp');
        }

        // Invalidate previous unused OTPs
        LoginOtp::where('mobile', $mobile)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        $otp = _generateOtp(6);

        LoginOtp::create([
            'mobile' => $mobile,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'is_used' => 0
        ]);

        app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp' => $otp]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_otp_resent_successfully')
        ]);
    }

    /**
     * VERIFY OTP (custom SMS, +91 only)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|digits:6'
        ]);

        if (!$this->isSmsMobile($request->mobile)) {
            return $this->mobileError('msg_invalid_request');
        }

        $otpRow = LoginOtp::where('mobile', $request->mobile)
            ->where('is_used', 0)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRow || !Hash::check($request->otp, $otpRow->otp)) {
            return $this->mobileError('msg_invalid_or_expired_otp');
        }

        [$user, $error] = $this->resolveLoginUser($request->mobile);
        if (!$user) {
            return $this->mobileError($error);
        }

        if ($user->mobile_verify_status === 'No') {
            $user->update(['mobile_verify_status' => 'Yes']);
        }

        Auth::guard('web')->login($user, true);

        $otpRow->update(['is_used' => 1]);
        $this->logHistory($request);

        return response()->json([
            'status' => true,
            'redirect' => route('web.dashboard.index')
        ]);
    }

    /**
     * VERIFY FIREBASE OTP (non +91)
     */
    public function verifyFirebaseOtp(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $phoneNumber = app(FirebaseAuthService::class)->verifyIdToken($request->id_token);

        if (!$phoneNumber) {
            return $this->mobileError('msg_invalid_or_expired_otp');
        }

        $mobile = $this->normalizeFirebasePhone($phoneNumber);

        // +91 numbers must never log in through Firebase
        if (!$mobile || $this->isSmsMobile($mobile)) {
            return $this->mobileError('msg_invalid_mobile_number');
        }

        [$user, $error] = $this->resolveLoginUser($mobile);
        if (!$user) {
            return $this->mobileError($error);
        }

        if ($user->mobile_verify_status === 'No') {
            $user->update(['mobile_verify_status' => 'Yes']);
        }

        Auth::guard('web')->login($user, true);
        $this->logHistory($request);

        return response()->json([
            'status' => true,
            'redirect' => route('web.dashboard.index')
        ]);
    }

    /* =========================================================
       HELPERS
    ========================================================= */

    private function normalizeFirebasePhone(string $e164Phone): ?string
    {
        $codes = CountryMaster::orderByRaw('LENGTH(country_code) DESC')
            ->pluck('country_code');

        foreach ($codes as $code) {
            if (str_starts_with($e164Phone, $code)) {
                $number = substr($e164Phone, strlen($code));
                return $code . '-' . $number;
            }
        }

        return null;
    }

    private function isSmsMobile(string $mobile): bool
    {
        return str_starts_with($mobile, self::SMS_COUNTRY_CODE . '-');
    }

    /**
     * Single source of truth for "can this mobile log in?"
     * Returns [Register|null, errorMessageKey|null]
     */
    private function resolveLoginUser(string $mobile): array
    {
        $user = Register::where('mobile', $mobile)->first();

        if (!$user) {
            return [null, 'msg_invalid_mobile_number'];
        }
        if ($user->trashed()) {
            return [null, 'msg_account_deactivated'];
        }
        if ($user->status === 'Suspended') {
            return [null, 'msg_account_suspended'];
        }
        if ($user->status === 'UNAPPROVED' && $user->is_verify === 'Yes') {
            return [null, 'msg_account_not_approved'];
        }

        $canLogin = ($user->status === 'APPROVED' && $user->is_verify === 'Yes')
            || ($user->status === 'UNAPPROVED' && $user->is_verify === 'No')
            || ($user->status === 'APPROVED' && $user->is_verify === 'No');

        if (!$canLogin) {
            return [null, 'msg_invalid_mobile_number'];
        }

        return [$user, null];
    }

    private function isOtpCooldownActive(string $mobile, int $seconds = 30): bool
    {
        $lastOtp = LoginOtp::where('mobile', $mobile)->latest()->first();

        return $lastOtp && $lastOtp->created_at->diffInSeconds(now()) < $seconds;
    }

    private function mobileError(string $messageKey)
    {
        return response()->json([
            'status' => false,
            'message' => __('messages.' . $messageKey),
        ]);
    }

    private function logHistory($request)
    {
        /** @var \App\Models\Register|null $user */
        $user = Auth::guard('web')->user();

        $updateData = [];
        if (!blank($request->token) && $request->token !== $user->web_device_id) {
            $updateData = [
                'web_device_id' => $request->token
            ];
        }
        $updateData['last_login'] = _getCurrentDate();
        $user->update($updateData);

        $agent = new Agent();

        UserLoginHistory::create([
            'member_id' => $user->id,
            'matri_id' => $user->matri_id,
            'email' => $user->email,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'login_from' => 'Website',
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'device' => $agent->device(),
            'is_mobile' => $agent->isMobile(),
        ]);
    }

    public function refreshCaptcha()
    {
        return response()->json([
            'success' => true,
            'captchaCode' => CaptchaHelper::generate('login_captcha')
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        // Only wipe the whole session if no other guard is still using it.
        $otherGuards = ['web', 'admin', 'staff', 'franchise', 'affiliate'];
        $stillLoggedIn = collect($otherGuards)->contains(
            fn($guard) => Auth::guard($guard)->check()
        );

        if (! $stillLoggedIn) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('web.login.index')->with('success', __('messages.msg_logged_out_successfully'));
    }

    private function invalidLogin($messageKey = 'msg_incorrect_login_details')
    {
        return response()->json([
            'status' => false,
            'message' => __('messages.' . $messageKey),
            'refresh_captcha' => true,
            'captchaCode' => CaptchaHelper::generate('login_captcha')
        ]);
    }

    private function captchaError()
    {
        return response()->json([
            'status' => false,
            'message' => __('messages.msg_invalid_captcha'),
            'refresh_captcha' => true,
            'captchaCode' => CaptchaHelper::generate('login_captcha')
        ]);
    }

    public function verifyEmail(string $token)
    {
        DB::beginTransaction();

        try {
            $hashedToken = hash('sha256', $token);

            $member = Register::where('email_verification_token', $hashedToken)
                ->where('email_verification_token_expires_at', '>', Carbon::now())
                ->lockForUpdate()
                ->first();

            if (!$member) {
                DB::rollBack();

                return view(_getConstant('dir_path.WEB_DIR_PATH') . '.login.verify_email', [
                    'status'  => 'error',
                    'message' => __('messages.msg_verification_link_expired_or_already_used'),
                    'redirectUrl' => route('web.login.index'),
                ]);
            }

            $member->update([
                'email_verify_status' => 'Verify',
                'status' => 'APPROVED',
                'email_verification_token' => null,
                'email_verification_token_expires_at' => null,
            ]);

            DB::commit();

            app(SmsSendService::class)->sendTemplate('Profile Approved', $member, []);

            $replaceArr = [
                'user_name'  => $member->fullname,
                'user_matri_id' => $member->matri_id,
                'user_email' => $member->email,
            ];
            app(EmailSendService::class)->send('Active Member', $member->email, $replaceArr, ['memberData' => $member]);

            app(NotificationService::class)->sendNotification(
                $member,
                $member,
                'profile_approved'
            );

            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.login.verify_email', [
                'status'  => 'success',
                'message' => __('messages.msg_your_profile_has_been_activated_successfully'),
                'redirectUrl' => route('web.login.index'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('web.login.index')->with('error', __('messages.msg_unexpected_error_occured'));
        }
    }
}

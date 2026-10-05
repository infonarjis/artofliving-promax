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
    public function index()
    {
        $captchaCode = CaptchaHelper::generate('login_captcha');

        $otpLoginMethod = $this->getOtpLoginMethod(); // 'sms' | 'firebase'

        $firebaseConfig = null;
        if ($otpLoginMethod === 'firebase') {
            $siteSetting = _getSiteSetting();
            // Front-end safe web config (apiKey, authDomain, projectId, appId, etc.)
            // NOTE: this is DIFFERENT from firebase_json (service account) used server-side.
            $firebaseConfig = $siteSetting['firebase_configuration'] ?? null;
            if (is_string($firebaseConfig)) {
                $firebaseConfig = json_decode($firebaseConfig, true) ?: null;
            }
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.login.index', compact(
            'captchaCode',
            'otpLoginMethod',
            'firebaseConfig'
        ));
    }

    /**
     * EMAIL / MATRI ID LOGIN
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'captcha_code' => 'required|string'
        ]);

        if (!CaptchaHelper::validate($request->captcha_code, 'login_captcha')) {
            return $this->captchaError();
        }

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'matri_id';
        $credentials = [$field => $request->login, 'password' => $request->password];

        if (!Auth::guard('web')->attempt($credentials, $request->remember ?? false)) {
            return $this->invalidLogin();
        }

        /** @var \App\Models\Register|null $user */
        $user = Auth::guard('web')->user();

        if ($user->status !== 'APPROVED' || $user->trashed()) {
            Auth::guard('web')->logout();
            if ($user->trashed()) {
                return $this->invalidLogin('msg_account_deactivated');
            }
            if ($user->status === 'UNAPPROVED') {
                return $this->invalidLogin('msg_account_not_approved');
            }
            if ($user->status === 'Suspended') {
                return $this->invalidLogin('msg_account_suspended');
            }
            return $this->invalidLogin();
        }

        $this->logHistory($request);

        return response()->json([
            'status' => true,
            'redirect' => route('web.dashboard.index')
        ]);
    }

    /**
     * SEND OTP (SMS method only)
     */
    public function sendOtp(Request $request)
    {
        if ($this->getOtpLoginMethod() !== 'sms') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_request')
            ]);
        }

        $request->validate([
            'country_code' => [
                'required',
                'string',
                'exists:country_master,country_code',
            ],
            'mobile' => 'required|digits_between:8,12'
        ]);

        $mobile = $request->country_code . '-' . $request->mobile;

        $user = Register::where('mobile', $mobile)
            ->where('status', 'APPROVED')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_mobile_number')
            ]);
        }

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
     * RESEND OTP (SMS method only)
     */
    public function resendOtp(Request $request)
    {
        if ($this->getOtpLoginMethod() !== 'sms') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_request')
            ]);
        }

        $request->validate(['mobile' => 'required|string']);
        $mobile = $request->mobile;

        $user = Register::where('mobile', $mobile)
            ->where('status', 'APPROVED')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_mobile_number')
            ]);
        }

        $lastOtp = LoginOtp::where('mobile', $mobile)->latest()->first();
        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 30) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_please_wait_before_requesting_a_new_otp')
            ]);
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
     * VERIFY OTP (SMS method only)
     */
    public function verifyOtp(Request $request)
    {
        if ($this->getOtpLoginMethod() !== 'sms') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_request')
            ]);
        }

        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|digits:6'
        ]);

        $otpRow = LoginOtp::where('mobile', $request->mobile)
            ->where('is_used', 0)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRow || !Hash::check($request->otp, $otpRow->otp)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_or_expired_otp')
            ]);
        }

        $user = Register::where('mobile', $request->mobile)
            ->where('status', 'APPROVED')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_mobile_number')
            ]);
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

    public function verifyFirebaseOtp(Request $request)
    {
        if ($this->getOtpLoginMethod() !== 'firebase') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_request')
            ]);
        }

        $request->validate([
            'id_token' => 'required|string',
        ]);

        $phoneNumber = app(FirebaseAuthService::class)->verifyIdToken($request->id_token);

        if (!$phoneNumber) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_or_expired_otp')
            ]);
        }

        $mobile = $this->normalizeFirebasePhone($phoneNumber);

        if (!$mobile) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_mobile_number')
            ]);
        }

        $user = Register::where('mobile', $mobile)
            ->where('status', 'APPROVED')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_invalid_mobile_number')
            ]);
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

    private function getOtpLoginMethod(): string
    {
        $siteSetting = _getSiteSetting();
        $method = $siteSetting['otp_login_method'] ?? 'sms';

        return in_array($method, ['sms', 'firebase'], true) ? $method : 'sms';
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

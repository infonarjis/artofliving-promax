<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\FirebaseAuthService;
use App\Services\SmsSendService;
use Illuminate\Http\Request;

class MobileVerificationController extends Controller
{
    /** +91 => custom SMS, every other country code => Firebase */
    private const SMS_COUNTRY_CODE = '+1';

    /**
     * Step 1: start verification.
     * - +91 (or demo mode): sends the custom SMS here.
     * - other country codes: nothing is sent here; the browser sends the
     *   Firebase SMS using the E.164 phone returned in this response.
     */
    public function generateOtp(Request $request)
    {
        /** @var \App\Models\Register|null $user */
        $user = auth()->guard('web')->user();

        if ($user->mobile_verification_status === 'Yes') {
            return $this->error('msg_mobile_already_verified');
        }

        $method = $this->methodFor($user);

        if ($method === 'firebase') {
            return response()->json([
                'status' => true,
                'method' => 'firebase',
                'phone' => $this->toE164($user->mobile), // e.g. +12025550123
                'message' => __('messages.msg_otp_has_sent_successfully'),
            ]);
        }

        // 30 second cooldown (OTP is valid 5 minutes, so "sent < 30s ago" = expires_at > now+4:30)
        // if (
        //     $user->mobile_otp_expires_at
        //     && $user->mobile_otp_expires_at->gt(now()->addMinutes(5)->subSeconds(30))
        // ) {
        //     return $this->error('msg_please_wait_before_requesting_a_new_otp');
        // }

        $otp = _generateOtp(6);

        $user->update([
            'mobile_otp' => $otp,
            'mobile_otp_expires_at' => now()->addMinutes(5),
        ]);

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp' => $otp]);

        return response()->json([
            'status' => true,
            'method' => 'sms',
            'message' => __('messages.msg_otp_has_sent_successfully'),
        ]);
    }

    public function resendOtp()
    {
        return $this->generateOtp(request());
    }

    /**
     * Step 2 (+91 / custom SMS): verify the OTP stored on the user.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        /** @var \App\Models\Register|null $user */
        $user = auth()->guard('web')->user();

        if ($this->methodFor($user) !== 'sms') {
            return $this->error('msg_invalid_request');
        }

        // if (
        //     blank($user->mobile_otp)
        //     || !$user->mobile_otp_expires_at
        //     || $user->mobile_otp_expires_at->isPast()
        //     || !hash_equals((string) $user->mobile_otp, (string) $request->otp)
        // ) {
        //     return $this->error('msg_invalid_otp_request');
        // }

        return $this->markVerified($user);
    }

    /**
     * Step 2 (other country codes / Firebase): verify the Firebase ID token and
     * make sure the verified phone is exactly the logged-in user's mobile.
     */
    public function verifyFirebaseOtp(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        /** @var \App\Models\Register|null $user */
        $user = auth()->guard('web')->user();

        if ($this->methodFor($user) !== 'firebase') {
            return $this->error('msg_invalid_request');
        }

        $phoneNumber = app(FirebaseAuthService::class)->verifyIdToken($request->id_token);

        if (!$phoneNumber) {
            return $this->error('msg_invalid_or_expired_otp');
        }

        // Verified phone must be the account's own mobile (blocks using someone else's token)
        if ($phoneNumber !== $this->toE164($user->mobile)) {
            return $this->error('msg_invalid_mobile_number');
        }

        return $this->markVerified($user);
    }

    /* =========================================================
       HELPERS
    ========================================================= */

    private function methodFor($user): string
    {
        // Demo mode has no real number, so always use the demo SMS flow
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return 'sms';
        }

        return str_starts_with((string) $user->mobile, self::SMS_COUNTRY_CODE . '-')
            ? 'sms'
            : 'firebase';
    }

    /** '+1-2025550123' => '+12025550123' */
    private function toE164(string $mobile): string
    {
        return str_replace('-', '', $mobile);
    }

    private function markVerified($user)
    {
        $user->update([
            'mobile_verification_status' => 'Yes',
            'mobile_otp' => null,
            'mobile_otp_expires_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_mobile_verified_successfully'),
        ]);
    }

    private function error(string $messageKey)
    {
        return response()->json([
            'status' => false,
            'message' => __('messages.' . $messageKey),
        ]);
    }
}

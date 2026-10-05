<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmsSendService;

class MobileVerificationController extends Controller
{
    public function generateOtp(Request $request)
    {
        /** @var \App\Models\Register|null $user */
        $user = auth()->guard('web')->user();

        if ($user->mobile_verification_status === 'Yes') {
            return response()->json(['status' => false, 'message' => __('messages.msg_mobile_already_verified')]);
        }

        $otp = _generateOtp(6);

        $user->update([
            'mobile_otp' => $otp,
            'mobile_otp_expires_at' => now()->addMinutes(5)
        ]);

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Mobile Verification', $user, ['otp'=>$otp]);

        return response()->json(['status' => true, 'message' => __('messages.msg_otp_has_sent_successfully')]);
    }

    public function resendOtp()
    {
        return $this->generateOtp(request());
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        /** @var \App\Models\Register|null $user */
        $user = auth()->guard('web')->user();

        if ($request->otp != $user->mobile_otp) {
            return response()->json(['status' => false, 'message' => __('messages.msg_invalid_otp_request')]);
        }

        $user->update([
            'mobile_verification_status' => 'Yes',
            'mobile_otp' => null,
            'mobile_otp_expires_at' => null
        ]);

        return response()->json(['status' => true, 'message' => __('messages.msg_mobile_verified_successfully')]);
    }
}
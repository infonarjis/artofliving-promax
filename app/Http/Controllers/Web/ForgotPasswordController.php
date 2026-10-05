<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Services\EmailSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form and generate captcha.
     */
    public function index()
    {
        $captchaCode = CaptchaHelper::generate('forgot_password_captcha');

        return view(_getConstant('dir_path.WEB_DIR_PATH').'.forgotPassword.index', [
            'captchaCode' => $captchaCode,
        ]);
    }

    /**
     * Refresh captcha via AJAX.
     */
    public function refreshCaptcha()
    {
        $captchaCode = CaptchaHelper::generate('forgot_password_captcha');

        return response()->json([
            'success'     => true,
            'captchaCode' => $captchaCode,
        ]);
    }

    /**
     * Validate captcha + email and send password reset link via AJAX.
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => ['required', 'email'],
            'captcha_code' => ['required', 'string'],
        ], [
            'email.required'        => __('messages.msg_email_required'),
            'email.email'           => __('messages.msg_email_valid_format'),
            'captcha_code.required' => __('messages.msg_captcha_code_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!CaptchaHelper::validate($request->captcha_code, 'forgot_password_captcha')) {
            $captchaCode = CaptchaHelper::generate('forgot_password_captcha');
            return response()->json([
                'status'      => 'error',
                'captchaCode' => $captchaCode,
                'errors'      => [
                    'captcha_code' => [__('messages.msg_invalid_captcha')],
                ],
            ], 422);
        }

        // Check user exists in registers table
        $user = Register::where('email', $request->email)->where('status', 'APPROVED')->first();
        if (!$user) {
            $captchaCode = CaptchaHelper::generate('forgot_password_captcha');
            return response()->json([
                'status'      => 'error',
                'captchaCode' => $captchaCode,
                'errors'      => [
                    'email' => [__('messages.msg_user_email_not_found')],
                ],
            ], 422);
        }

        // Generate reset link token via Laravel's broker
        $token = Str::random(64);
        DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        $resetLink   = url(route('web.resetPassword.index', [
            'token' => $token,
            'email' => $request->email,
        ], false));

        ## Send Email
        $replaceArr = [
            'user_name'  => $user->fullname,
            'user_matri_id' => $user->matri_id,
            'user_email' => $user->email,
            'forgot_password_link' => $resetLink,
        ];
        app(EmailSendService::class)->send('Forgot Password', $user->email, $replaceArr,['memberData' => $user]);

        // Refresh captcha after successful submit
        $captchaCode = CaptchaHelper::generate('forgot_password_captcha');

        return response()->json([
            'status'      => 'success',
            'captchaCode' => $captchaCode,
            'resetLink' => $resetLink,
            'message'     => __('messages.msg_reset_password_email_sent'),
        ]);
    }
}
<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateMember;
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
        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH').'.forgotPassword.index');
    }

    /**
     * Validate captcha + email and send password reset link via AJAX.
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => ['required', 'email'],
        ], [
            'email.required'        => __('messages.msg_email_required'),
            'email.email'           => __('messages.msg_email_valid_format'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check user exists in AffiliateMember table
        $user = AffiliateMember::where('email', $request->email)->where('status', 'APPROVED')->first();
        if (!$user) {
            return response()->json([
                'status'      => 'error',
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

        $resetLink   = url(route('affiliate.resetPassword.index', [
            'token' => $token,
            'email' => $request->email,
        ], false));

        ## Send Email
        $replaceArr = [
            'user_name'  => $user->fullname,
            'user_email' => $user->email,
            'forgot_password_link' => $resetLink,
        ];
        app(EmailSendService::class)->send('Affiliate Forgot Password', $user->email, $replaceArr,['memberData' => $user]);


        return response()->json([
            'status'      => 'success',
            'resetLink' => $resetLink,
            'message'     => __('messages.msg_reset_password_email_sent'),
        ]);
    }
}
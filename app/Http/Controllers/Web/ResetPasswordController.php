<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Register;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Show reset password form.
     */
    public function index(Request $request, string $token)
    {
        // Verify token + email exist before showing the form
        $email = $request->query('email');

        if (!$email || !$token) {
            return redirect()->route('web.forgotPassword.index')
                ->with('error', __('messages.msg_reset_password_invalid_link'));
        }

        // Check token validity in password_reset_tokens table
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return redirect()->route('web.forgotPassword.index')
                ->with('error', __('messages.msg_reset_password_invalid_link'));
        }

        // Check if token is expired (default Laravel: 60 minutes)
        $expiresAt = config('auth.passwords.users.expire', 60);
        $createdAt = Carbon::parse($record->created_at);

        if ($createdAt->addMinutes($expiresAt)->isPast()) {
            // Clean up expired token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('web.forgotPassword.index')
                ->with('error', __('messages.msg_reset_password_expired_link'));
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH').'.resetPassword.index', [
            'token'       => $token,
            'email'       => $email
        ]);
    }

    /**
     * Handle reset password form submission via AJAX.
     */
    public function resetPassword(Request $request)
    {
        // 1. Validate inputs
        $validator = Validator::make($request->all(), [
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
            'password_confirmation' => ['required', 'string'],
        ], [
            'token.required'                 => __('messages.msg_reset_token_missing'),
            'email.required'                 => __('messages.msg_email_required'),
            'email.email'                    => __('messages.msg_email_valid_format'),
            'password.required'              => __('messages.msg_password_required'),
            'password.min'                   => __('messages.msg_password_min_length'),
            'password.confirmed'             => __('messages.msg_passwords_do_not_match'),
            'password.regex'                 => __('messages.msg_password_must_contain_uppercase_lowercase_number_special_character'),
            'password_confirmation.required' => __('messages.msg_password_confirmation_required')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check user exists in registers table
        $user = Register::where('email', $request->email)
            ->where('status', 'APPROVED')
            ->first();

        if (!$user) {
            return response()->json([
                'status'      => 'error',
                'errors'      => [
                    'email' => [__('messages.msg_user_email_not_found')],
                ],
            ], 422);
        }

        // Reset the password via Laravel's Password broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($brokerUser) use ($request, $user) {
                // Update password in your registers table
                $user->update([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ]);

                event(new PasswordReset($brokerUser));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status'   => 'success',
                'message'  => __('messages.msg_password_reset_successfully'),
                'redirect' => route('web.login.index'),
            ]);
        }

        return response()->json([
            'status'      => 'error',
            'errors'      => [
                'token' => [__($status)],
            ],
        ], 422);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Services\Api\ApiResponseService;
use App\Services\EmailSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email']
            ], [
                'email.required' => __('messages.msg_email_required'),
                'email.email' => __('messages.msg_email_valid_format'),
            ]);
            if ($validator->fails()) {
                return ApiResponseService::validationError(
                    $validator
                );
            }

            ## Check User :
            $user = Register::query()->where('email', $request->email)->where('status', 'APPROVED')->first();
            if (!$user) {
                return ApiResponseService::error(__('messages.msg_user_email_not_found'));
            }

            ## Create Reset Token  :
            $token = Str::random(64);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')
                ->insert([
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]);

            ## Reset URL :
            $resetLink = route(
                'web.resetPassword.index',
                [
                    'token' => $token,
                    'email' => $request->email
                ]
            );

            ## Send Email :
            $replaceArr = [
                'user_name' => $user->fullname,
                'user_matri_id' => $user->matri_id,
                'user_email' => $user->email,
                'forgot_password_link' => $resetLink,
            ];
            app(EmailSendService::class)->send(
                'Forgot Password',
                $user->email,
                $replaceArr,
                [
                    'memberData' => $user
                ]
            );

            return ApiResponseService::success(_getLangApi($request, 'msg_reset_password_email_sent'));
        } catch (Throwable $e) {
            Log::error('forgot password API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

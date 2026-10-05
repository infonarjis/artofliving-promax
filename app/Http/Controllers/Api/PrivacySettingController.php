<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\MemberAlertSetting;
use App\Models\NotificationTemplate;
use App\Models\Register;
use App\Models\SmsTemplate;
use App\Services\Api\ApiResponseService;
use App\Services\EmailSendService;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PrivacySettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $authUser = auth()->guard('api')->user();

            ## Get all member alert settings
            $memberSettings = MemberAlertSetting::where('member_id', $authUser->id)->get()->groupBy('template_type');
            $isFirstTime = $memberSettings->isEmpty();
            $notificationIds = $memberSettings->get('notification', collect())->where('enabled', true)->pluck('template_id')->toArray();
            $emailIds = $memberSettings->get('email', collect())->where('enabled', true)->pluck('template_id')->toArray();
            $smsIds = $memberSettings->get('sms', collect())->where('enabled', true)->pluck('template_id')->toArray();

            ## Notification Templates
            $notificationTemplates = NotificationTemplate::active()->select('id', 'title')->get();
            foreach ($notificationTemplates as $template) {
                $template->selected = $isFirstTime ? true : in_array($template->id, $notificationIds);
            }
            ## Email Templates
            $emailTemplate = EmailTemplate::active()->select('id', 'template_name')->get();
            foreach ($emailTemplate as $template) {
                $template->selected = $isFirstTime ? true : in_array($template->id, $emailIds);
            }
            ## SMS Templates
            $smsTemplate = SmsTemplate::active()->select('id', 'template_name')->get();
            foreach ($smsTemplate as $template) {
                $template->selected = $isFirstTime ? true : in_array($template->id, $smsIds);
            }

            $dataArr = [
                'photo_visibility'              => $authUser->photo_visibility,
                'contact_visibility'            => $authUser->contact_visibility,
                'video_call_setting'            => $authUser->video_call_setting,
                'voice_call_setting'            => $authUser->voice_call_setting,

                'emailTemplate'                 => $emailTemplate,
                'smsTemplate'                   => $smsTemplate,
                'notificationTemplates'         => $notificationTemplates,
            ];
            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('privacy setting list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Update Privacy Settings:
    public function updatePrivacySetting(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo_visibility'   => 'nullable|integer|in:0,1,2',
                'contact_visibility' => 'nullable|integer|in:0,1',
                'video_call_setting' => 'nullable|integer|in:0,1',
                'voice_call_setting' => 'nullable|integer|in:0,1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            ## Update Data :
            $fieldArr = [
                'photo_visibility',
                'contact_visibility',
                'video_call_setting',
                'voice_call_setting'
            ];
            $updateData = [];
            foreach ($fieldArr as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            if (empty($updateData)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_no_data_to_update'));
            }

            $updateData['updated_at'] = _getCurrentDate();
            Register::where('id', $memberId)->update($updateData);

            return ApiResponseService::success(_getLangApi($request, 'msg_privacy_settings_updated_successfully'));
        } catch (Throwable $e) {
            Log::error('Update privacy setting API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Update password:
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            /** @var \App\Models\Register $authUser */
            $authUser = auth()->guard('api')->user();

            if (!Hash::check($request->old_password, $authUser->password)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_old_password_is_incorrect'));
            }

            $authUser->update([
                'password' => Hash::make($request->new_password),
            ]);

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Change Password', $authUser, []);
            ## Send Email :
            $replaceArr = [
                'user_name'  => $authUser->fullname,
                'user_matri_id' => $authUser->matri_id,
                'user_email' => $authUser->email,
            ];
            app(EmailSendService::class)->send('Change Password', $authUser->email, $replaceArr, ['memberData' => $authUser]);

            return ApiResponseService::success(_getLangApi($request, 'msg_password_changed_successfully'));
        } catch (Throwable $e) {
            Log::error('Change password API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Update member alerts email, sms and notification settings :
    public function updateAlertSetting(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'template_type' => 'required|in:email,sms,notification',
                'template_ids'  => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $memberId = auth()->guard('api')->id();
            $type     = $request->template_type;

            $ids = collect(explode(',', (string) $request->template_ids))
                ->map(fn($id) => trim($id))
                ->filter()
                ->unique()
                ->values()
                ->all();

            DB::transaction(function () use ($memberId, $type, $ids) {

                // Soft delete :
                MemberAlertSetting::where([
                    'member_id'     => $memberId,
                    'template_type' => $type,
                ])->delete();

                // Get all template IDs for the selected type
                switch ($type) {
                    case 'notification':
                        $templateIds = NotificationTemplate::active()->pluck('id')->toArray();
                        break;

                    case 'email':
                        $templateIds = EmailTemplate::active()->pluck('id')->toArray();
                        break;

                    case 'sms':
                        $templateIds = SmsTemplate::active()->pluck('id')->toArray();
                        break;

                    default:
                        $templateIds = [];
                }

                $data = [];

                foreach ($templateIds as $templateId) {
                    $data[] = [
                        'member_id'     => $memberId,
                        'template_id'   => $templateId,
                        'template_type' => $type,
                        'enabled'       => in_array($templateId, $ids),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }

                MemberAlertSetting::upsert(
                    $data,
                    ['member_id', 'template_type', 'template_id'], // Unique keys
                    ['enabled', 'updated_at']            // Columns to update
                );
            });

            $message = empty($ids)
                ? _getLangApi($request, 'msg_all_alert_settings_disabled', [
                    'type' => ucfirst($type)
                ])
                : _getLangApi($request, 'msg_alert_settings_updated', [
                    'type' => ucfirst($type)
                ]);

            return ApiResponseService::success($message);
        } catch (Throwable $e) {
            Log::error('Update Alert Settings API failed', [
                'user_id' => auth()->guard('api')->id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\MemberAlertSetting;
use App\Models\NotificationTemplate;
use App\Models\Register;
use App\Models\SmsTemplate;
use App\Services\EmailSendService;
use App\Services\SmsSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PrivacySettingController extends Controller
{
    public function index()
    {
        $authUser = auth()->guard('web')->user();

        $emailTemplate = EmailTemplate::active()
            ->select('id', 'template_name')
            ->get();

        $smsTemplate = SmsTemplate::active()
            ->select('id', 'template_name')
            ->get();

        $notificationTemplates = NotificationTemplate::active()
            ->select('id', 'title')
            ->get();

        $memberSettings = MemberAlertSetting::where('member_id', $authUser->id)->get();

        $memberSettingEnabled = $memberSettings
            ->where('enabled', true) // Only enabled templates
            ->groupBy('template_type')
            ->map(function ($rows) {
                return $rows->pluck('template_id')->toArray();
            })
            ->toArray();

        $hasSmsSettings = $memberSettings->where('template_type', 'sms')->isNotEmpty();
        $hasEmailSettings = $memberSettings->where('template_type', 'email')->isNotEmpty();
        $hasNotificationSettings = $memberSettings->where('template_type', 'notification')->isNotEmpty();

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.privacySetting.index',
            compact(
                'authUser',
                'emailTemplate',
                'smsTemplate',
                'notificationTemplates',
                'memberSettingEnabled',
                'hasSmsSettings',
                'hasEmailSettings',
                'hasNotificationSettings'
            )
        );
    }

    ## Update Privacy Settings:
    public function updatePrivacySetting(Request $request)
    {
        $request->validate([
            'photo_visibility'      => 'required|in:0,1,2',
            'contact_visibility'    => 'required|in:0,1',
            'video_call_setting'    => 'required|in:0,1',
            'voice_call_setting'    => 'required|in:0,1',
        ]);

        /** @var \App\Models\Register|null $authUser */
        $authUser = auth()->guard('web')->user();

        Register::where('id', $authUser->id)->update([
            'photo_visibility'   => $request->photo_visibility,
            'contact_visibility' => $request->contact_visibility,
            'video_call_setting' => $request->video_call_setting,
            'voice_call_setting' => $request->voice_call_setting,
            'updated_at'         => _getCurrentDate(),
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_privacy_settings_updated_successfully')
        ]);
    }

    ## Update password:
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\Register|null $authUser */
        $authUser = auth()->guard('web')->user();

        if (!Hash::check($request->old_password, $authUser->password)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_old_password_is_incorrect')
            ], 422);
        }

        $authUser->password = Hash::make($request->password);
        $authUser->save();

        // IMPORTANT — keep user logged in
        auth()->guard('web')->login($authUser);
        $request->session()->regenerate();

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Change Password', $authUser, []);
        ## Send Email :
        $replaceArr = [
            'user_name'  => $authUser->fullname,
            'user_matri_id' => $authUser->matri_id,
            'user_email' => $authUser->email,
        ];
        app(EmailSendService::class)->send('Change Password', $authUser->email, $replaceArr, ['memberData' => $authUser]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_password_changed_successfully')
        ]);
    }

    ## update member alerts email, sms and notification settings :
    public function updateAlertSetting(Request $request)
    {
        $request->validate([
            'template_type' => 'required|in:email,sms,notification',
            'template_ids'  => 'array'
        ]);

        $memberId = auth()->guard('web')->id();
        $type     = $request->template_type;
        $ids      = $request->template_ids ?? [];

        DB::transaction(function () use ($memberId, $type, $ids) {

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
            ? __('messages.msg_all_alert_settings_disabled', [
                'type' => ucfirst($type),
            ])
            : __('messages.msg_alert_settings_updated', [
                'type' => ucfirst($type),
            ]);

        return response()->json([
            'status'  => true,
            'message' => $message
        ]);
    }
}

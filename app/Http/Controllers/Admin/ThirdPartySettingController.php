<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use App\Services\EmailSendService;
use App\Services\FirebaseWebPushService;
use App\Services\ThemeSettingService;

class ThirdPartySettingController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;
    }

    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js/thirdPartySetting/addEdit.js'
        ];

        $testEmailElementArr = array(
            'send_email_to' => array('is_required' => 'required', 'label' => 'Test SMTP Configuration', 'display_info' => '(ex: xxxx@gmail.com)', 'placeholder' => 'Email', 'input_type' => 'email', 'column' => '6'),
        );
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        $dataArr = [
            'pageName' => 'Third Party Settings',
            'mode' => 'edit',
            'id' => '1',
            'languageDataArr' => _getActiveLanguage(),
            'extraJsArr' => $extraJsArr,
            'emailSettingHtml' => $this->emailAddEditForm(),
            'zegoCloudSettingHtml' => $this->zegoCloudSetting(),
            'firebaseSettingHtml' => $this->firebaseAddEditForm(),
            'aiApiKeySettingHtml' => $this->aiApiKeysSetting(),
            'testEmailFormHtml' => $this->adminFormBuilderService->generateFormElement($testEmailElementArr, $otherData),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/thirdPartySetting/addEdit', $dataArr);
    }

    ## Email Setting :
    public function emailAddEditForm()
    {
        $elementArr = array(
            'mail_send_status' => array(
                'label' => 'Email Configuration',
                'type' => 'radio',
                'is_register' => 'Yes',
                'modeType' => 'edit',
                'isDisableInDemo' => 'Yes',
                'value_arr' => array('Enabled' => 'Enabled', 'Disabled' => 'Disabled'),
                'column' => '6'
            ),
            'contact_email' => array('is_required' => 'required', 'input_type' => 'email', 'label' => 'Contact Email', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mailer_type' => array('label' => 'Mail Type', 'type' => 'dropdown', 'is_required' => 'required', 'value_arr' => array('smtp' => 'SMTP'), 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_host' => array('is_required' => 'required', 'label' => 'Mail Host', 'display_info' => 'Replace with your SMTP server, e.g., smtp.gmail.com', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_port' => array('is_required' => 'required', 'label' => 'Mail Port', 'display_info' => 'Typical ports are 587 for TLS, 465 for SSL', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_from_address' => array('is_required' => 'required', 'label' => 'Mail From Address', 'display_info' => 'Your email address', 'input_type' => 'email', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_username' => array('is_required' => 'required', 'label' => 'Username', 'display_info' => 'Your email address', 'input_type' => 'email', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_password' => array('is_required' => 'required', 'label' => 'Password', 'display_info' => 'Your email password or app-specific password (if applicable)', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_encryption' => array('is_required' => 'required', 'label' => 'Mail Encryption', 'display_info' => 'Use "tls" or "ssl"', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'mail_title' => array('is_required' => 'required', 'label' => 'Mail Title', 'display_info' => 'Sender"s name, defaults to your app"s name', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function emailAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'email')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            // 'from_email',
            'contact_email',
            'mailer_type',
            'mail_host',
            'mail_port',
            'mail_from_address',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_title',
            'mail_send_status'
        );

        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return back()->with('active_tab', 'email')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'email')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Send Test Email :
    public function sendTestEmail(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'email')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();
        $configArr = _getSiteSetting();
        $toEmail = $postData['send_email_to'];
        $emailSubject = 'Test Email';
        $emailContent = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . $configArr['web_frienly_name'] . '</title>
            </head>
            <body>
                <h1>Test Email</h1>
                <p>This is a test email from ' . $configArr['web_frienly_name'] . '!</p>
            </body>
            </html>';

        //  Send Mail :
        app(EmailSendService::class)->sendMail($toEmail, $emailSubject, $emailContent);

        return back()->with('active_tab', 'email')->with('success', 'Email Send Successful.');
    }

    ## Firebase Setting :
    public function firebaseAddEditForm()
    {
        $elementArr = array(
            'firebase_project_id' => array('is_required' => 'required', 'label' => 'Firebase Project ID', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'firebase_vapid_key' => array('is_required' => 'required', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'firebase_json' => array('is_required' => 'required', 'type' => 'textarea', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'firebase_configuration' => array('is_required' => 'required', 'type' => 'textarea', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'firebase_chat_url' => array('is_required' => 'required', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'firebase_status' => array('type' => 'radio', 'is_register' => 'Yes', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes', 'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'), 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function firebaseAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'firebase')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            'firebase_project_id',
            'firebase_vapid_key',
            'firebase_json',
            'firebase_configuration',
            'firebase_status',
        );

        $updateData = _getRequestData($updateArr, $postData);
        if (isset($postData['firebase_chat_url']) && !empty($postData['firebase_chat_url'])) {
            $updateData['firebase_chat_url'] = $postData['firebase_chat_url'];
        }
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            FirebaseWebPushService::generateServiceWorker();

            return back()->with('active_tab', 'firebase')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'firebase')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Zego Cloud Setting :
    public function zegoCloudSetting()
    {
        $elementArr = array(
            'zegocloud_appid' => array('label' => 'App ID', 'column' => '4', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'zegocloud_server_secret_key' => array('label' => 'Server Secret Key', 'class' => 'required', 'column' => '4', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'zegocloud_appsign_key' => array('label' => 'App Sign Key', 'class' => 'required', 'column' => '4', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'zego_video_call_setting' => array('label' => 'Video Call Setting', 'type' => 'radio', 'is_register' => 'Yes', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes', 'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'), 'column' => '6'),
            'zego_voice_call_setting' => array('label' => 'Voice Call Setting', 'type' => 'radio', 'is_register' => 'Yes', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes', 'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'), 'column' => '6')
        );
        ## Extra Js :
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function zegoCloudSettingAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'zego_cloud')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();
        $updateArr = array(
            'zegocloud_appid',
            'zegocloud_server_secret_key',
            'zegocloud_appsign_key',
            'zego_video_call_setting',
            'zego_voice_call_setting'
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);

                ## Update in app setting also :
                $updateAppSetting = [
                    'enable_video_call' => ($postData['zego_video_call_setting'] ?? '') === 'APPROVED' ? 'Yes' : 'No',
                    'enable_voice_call' => ($postData['zego_voice_call_setting'] ?? '') === 'APPROVED' ? 'Yes' : 'No',
                ];
                ThemeSettingService::set($updateAppSetting);
            }
            return back()->with('active_tab', 'zego_cloud')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'zego_cloud')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Ai Api Keys Setting :
    public function aiApiKeysSetting()
    {
        $elementArr = array(
            'gemini_api_key' => array('is_required' => 'required', 'label' => 'Gemini Api Key', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'gemini_api_status' => array(
                'label' => 'Gemini Api Key Status',
                'type' => 'radio',
                'is_register' => 'Yes',
                'modeType' => 'edit',
                'isDisableInDemo' => 'Yes',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'),
                'column' => '6'
            )
        );
        ## Extra Js :
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function aiApiKeysSettingAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'gemini')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }
        if (_getConstant('AI_MODE') != 'Enabled') {
            return back()->with('active_tab', 'gemini')->with('error', 'You dont have access for this');
        }

        $postData = $request->all();
        $updateArr = array(
            'gemini_api_key',
            'gemini_api_status'
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return back()->with('active_tab', 'gemini')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'gemini')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
## Services
use App\Services\AdminFormBuilderService;

class SiteSettingController extends Controller
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

    ## Bsic Site Setting :
    public function index()
    {
        $elementArr = array(
            'web_name' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'web_frienly_name' => array('is_required' => 'required', 'label' => 'Web Friendly Name', 'class' => 'required', 'column' => '6'),
            'website_description' => array('type' => 'textarea', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'website_keywords' => array('type' => 'textarea', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'contact_email' => array('is_required' => 'required', 'input_type' => 'email', 'label' => 'Contact Email', 'column' => '6', 'modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'contact_no' => array('is_required' => 'required', 'class' => 'required', 'type' => 'mobile', 'modeType' => 'edit', 'column' => '6', 'isDisableInDemo' => 'Yes'),
            'footer_text' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'full_address' => array('type' => 'textarea', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'map_address' => array('type' => 'textarea', 'is_required' => 'required', 'column' => '6'),
            'map_tooltip' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'default_currency' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'relation' => array('rel_model' => 'CurrencyMaster', 'key_val' => 'currency_code', 'key_disp' => 'currency_name', 'column' => '6'),
                'class' => 'required select2 ',
                'column' => '6'
            ),
            'default_country_code' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'relation' => array('rel_model' => 'CountryMaster', 'key_val' => 'country_code', 'key_disp' => 'country_code', 'column' => '6'),
                'class' => 'required select2 ',
                'column' => '6'
            ),
            'tax_applicable' => array('type' => 'radio', 'value_arr' => array('Yes' => 'Yes', 'No' => 'No'), 'column' => '6'),
            'tax_name' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'service_tax' => array('is_required' => 'required', 'label' => 'Service Tax (%)', 'other' => "min='0' max='100'", 'class' => 'required', 'column' => '6'),
            'near_by_me_km' => array('is_required' => 'required', 'label' => 'Near By Me Km (For Matches)', 'other' => "min='0' max='100'", 'class' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        $dataArr = [
            'pageName' => 'Basic Site Setting',
            'settingFormHtml' => $this->adminFormBuilderService->generateFormElement($elementArr, $otherData),
            'logoFaviconHtml' => $this->logoFaviconAddEditForm(),
            'matriPrefixHtml' => $this->matriPrefixAddEditForm(),
            'googleAnalyticsHtml' => $this->analyticsCodeAddEditForm(),
            'appLinkHtml' => $this->appLinkAddEditForm(),
            'socialSiteHtml' => $this->socialSiteSettingAddEditForm(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/siteSetting/addEdit', $dataArr);
    }

    ## Submit Form :
    public function basicSiteSettingsAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'general')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            'web_name',
            'web_frienly_name',
            'website_description',
            'footer_text',
            'contact_no',
            'contact_email',
            'website_keywords',
            'full_address',
            'map_address',
            'map_tooltip',
            'default_currency',
            'default_country_code',
            'tax_applicable',
            'tax_name',
            'service_tax',
            'near_by_me_km',
        );
        $updateData = _getRequestData($updateArr, $postData);
        $updateData['map_address'] = $postData['map_address'];
        if (isset($postData['contact_no_country_code']) && !blank($postData['contact_no_country_code']) && isset($postData['contact_no']) &&  !blank($postData['contact_no'])) {
            $updateData['contact_no'] = $postData['contact_no_country_code'] . '-' . $postData['contact_no'];
        }
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return back()->with('active_tab', 'general')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'general')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Logo & Favicon Setting :
    public function logoFaviconAddEditForm()
    {

        $elementArr = array(
            'upload_logo' => array(
                'type' => 'file',
                'path_value' => 'upload_path.LOGO_IMAGE_URL',
                'other' => 'data-width="232" data-height="64"',
                'crop_image' => 'Yes'
            ),
            'upload_favicon' => array(
                'type' => 'file',
                'path_value' => 'upload_path.LOGO_IMAGE_URL',
                'other' => 'data-width="75" data-height="75"',
                'crop_image' => 'Yes'
            ),
            'watermark_logo' => array(
                'type' => 'file',
                'path_value' => 'upload_path.LOGO_IMAGE_URL',
                'other' => 'data-width="20" data-height="200"',
                'crop_image' => 'Yes'
            ),
        );
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function logoFaviconAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'logo')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();
        $siteSettingsArr = _getSiteSetting();

        $validateArr = [
            'upload_logo' => 'required|image|mimes:png,jpg,jpeg,webp|max:3072',
            'upload_favicon' => 'required|image|mimes:png,jpg,jpeg,webp|max:3072',
        ];

        foreach ($siteSettingsArr as $key => $value) {
            if (!blank($value)) {
                unset($validateArr[$key]);
            }
        }

        $validationMsgArr = [
            'upload_logo.mimes' => 'The Photo Must Be A File Of Type: Jpg, Png, Jpeg, Gif, Bmp.',
            'upload_favicon.mimes' => 'The Photo Must Be A File Of Type: Jpg, Png, Jpeg, Gif, Bmp.',
            'watermark_logo.mimes' => 'The Photo Must Be A File Of Type: Jpg, Png, Jpeg, Gif, Bmp.',
            'upload_logo.image' => 'The Photo Must Be An Image.',
            'upload_favicon.image' => 'The Photo Must Be An Image.',
            'watermark_logo.image' => 'The Photo Must Be An Image.',
        ];
        $validator = Validator::make($request->all(), $validateArr, $validationMsgArr);

        if ($validator->fails()) {
            return back()->with('active_tab', 'logo')->with('error', ucwords($validator->errors()->first()));
        } else {
            $updateArr = array(
                'upload_logo',
                'upload_favicon',
                'watermark_logo',
            );
            $updateData = _getRequestData($updateArr, $postData);
            ## Check If File Exit Or Not And Validation:
            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    if (!empty($value['name'])) {
                        $path = _getConstant($postData[$key . '_path']);
                        $oldValue = '';
                        if (!blank($postData[$key . '_val'])) {
                            $oldValue = $postData[$key . '_val'];
                        }
                        $fileName = '';
                        if ($key == 'upload_logo') {
                            $fileName = 'logo.png';
                        } elseif ($key == 'upload_favicon') {
                            $fileName = 'favicon.png';
                        } elseif ($key == 'watermark_logo') {
                            $fileName = 'watermark.png';
                        }
                        $uploadedFiles = UploadHelper::uploadFile($request->file($key), $path, $oldValue, $fileName, 0);
                        ## Watermark Logo :
                        if($key == 'watermark_logo'){
                            $cachePath = public_path('watermark_cache');

                            if (File::exists($cachePath)) {
                                File::cleanDirectory($cachePath);
                            }
                        }

                        $updateData[$key] = $uploadedFiles;
                    }
                }
            }
            if (!empty($updateData)) {
                $setting = SiteSetting::find($postData['id']);
                if ($setting) {
                    $setting->update($updateData);
                }
                return back()->with('active_tab', 'logo')->with('error', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            return back()->with('active_tab', 'logo')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Matri Prefix Setting :
    public function matriPrefixAddEditForm()
    {
        $elementArr = array(
            'matri_prefix' => array('is_required' => 'required', 'column' => '6')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function matriPrefixAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'prefix')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            'matri_prefix'
        );

        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
                return back()->with('active_tab', 'prefix')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
        }
        return back()->with('active_tab', 'prefix')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    ## Analytics Code Setting :
    public function analyticsCodeAddEditForm()
    {
        $elementArr = array(
            'google_analytics_code' => array('type' => 'textarea','modeType' => 'edit', 'isDisableInDemo' => 'Yes')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];

        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function analyticsCodeAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'google-analytics')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $setting = SiteSetting::find($request->id);

        if (!$setting) {
            return back()->with('active_tab', 'google-analytics')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }

        $setting->update([
            'google_analytics_code' => $request->input('google_analytics_code'),
        ]);

        return back()->with('active_tab', 'google-analytics')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    ## App Link Setting :
    public function appLinkAddEditForm()
    {
        $elementArr = array(
            'android_app_link' => array('is_required' => 'required', 'input_type' => 'url', 'column' => '6'),
            'ios_app_link' => array('is_required' => 'required', 'input_type' => 'url', 'column' => '6')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1'),
            'callbackUrl' => 'admin.appLinkAddEditForm'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function appLinkAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'app-link')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            'android_app_link',
            'ios_app_link',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return back()->with('active_tab', 'app-link')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'app-link')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## Social Site Setting :
    public function socialSiteSettingAddEditForm()
    {
        $elementArr = array(
            'facebook_link' => array('input_type' => 'url', 'column' => '6'),
            'twitter_link' => array('input_type' => 'url', 'column' => '6'),
            'instagram_link' => array('input_type' => 'url', 'column' => '6'),
            'youtube_link' => array('input_type' => 'url', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1'),
            'callbackUrl' => 'admin.socialSiteSettingAddEditForm'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function socialSiteSettingAddEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'facebook_link',
            'twitter_link',
            'instagram_link',
            'youtube_link',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }

            return back()->with('active_tab', 'social-site')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'social-site')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    public function checkAuthentication(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $requiredString = 'required|string';
        $validator = Validator::make(
            $request->all(),
            [
                'admin_username' => $requiredString,
                'admin_password' => $requiredString
            ]
        );
        if ($validator->fails()) {
            $responseArr['msg'] = ucwords($validator->errors()->first());
        } else {
            $postData = $request->all();
            $adminUsername = $postData['admin_username'];
            $adminPassword = $postData['admin_password'];

            $isExist = Admin::where('email', $adminUsername)->select('id', 'email', 'password', 'status')->first();

            if ($isExist === null) {
                $responseArr['msg'] = 'Please enter valid username.';
            } elseif (!empty($isExist->password) && Hash::check($adminPassword, $isExist->password)) {
                $responseArr['status'] = 'success';
            } else {
                $responseArr['msg'] = 'Please enter valid password.';
            }
        }
        return response()->json($responseArr, 200);
    }

    ## Change Password Setting :
    public function changePasswordAddEditForm()
    {
        $authUserId = Auth::guard('admin')->user()->id;

        $elementArr = array(
            'current_password' => array('is_required' => 'required', 'type' => 'password', 'class' => 'required'),
            'new_password' => array('is_required' => 'required', 'type' => 'password', 'class' => 'required'),
            'confirm_password' => array('is_required' => 'required', 'type' => 'password', 'class' => 'required')
        );

        ## Extra Js :
        $extraJsArr = [
            '/custom/js/jquery.validate.min.js'
        ];

        $otherData = [
            'mode' => 'edit',
            'id' => $authUserId,
            'rowData' => Admin::where('id',$authUserId)->first(),
            'callbackUrl' => 'admin.changePasswordAddEditForm'
        ];

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => 'Change Password',
            'elementArr' => $elementArr,
            'formUrl' => 'admin.changePasswordAddEdit',
            'formId' => 'changePasswordUpdate',
            'formName' => 'changePasswordUpdate',
            'formSubmitBtnClass' => 'submitChangePassword',
            'formSubmitBtnId' => 'submitChangePassword',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/siteSetting/changePasswordAddEdit', $dataArr);
    }

    ## Submit Form :
    public function changePasswordAddEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $adminData = Admin::where('id', $postData['id'])->select(['id', 'password'])->first();

        if (!empty($adminData) && isset($adminData->password)) {
            ## Check Current Password :
            if (Hash::check($postData['current_password'], $adminData->password)) {
                $whereArr = ['id' => $postData['id']];
                $updateData = array(
                    'password' => Hash::make($postData['new_password'])
                );

                if (!empty($updateData)) {
                    Admin::where($whereArr)->update($updateData);
                    return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
                } else {
                    return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
                }
            } else {
                return redirect()->route($postData['callbackUrl'])->with('error', 'Current password is invalid!');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', 'Something went wrong!');
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Cache;

class OtherWebsiteLayoutController extends Controller
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
        $extraJsArr = [];

        $dataArr = [
            'pageName' => 'Home Page Sections',
            'mode' => 'edit',
            'id' => '1',
            'extraJsArr' => $extraJsArr,
            'featuresControlAddEditForm' => $this->featuresControlAddEditForm(),
            'chatModuleAddEditForm' => $this->chatModuleAddEditForm(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/otherWebsiteLayout/addEdit', $dataArr);
    }

    ## Homepage Text Setting :
    public function chatModuleAddEditForm()
    {
        $elementArr = array(
            'chat_module_design' => array('type' => 'radio', 'label' => 'Chat Module Design', 'value_arr' => array('popup' => 'Popup Chat', 'window' => 'Window Chat'), 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function update(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $postData = $request->all();

        $updateArr = array(
            'chat_module_design',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);

                Cache::forget('site_settings');
            }
            return back()->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    public function featuresControlAddEditForm()
    {
        $elementArr = array(
            'enable_video_call' => ['label' => 'Video Call', 'type' => 'radio','value_arr' => ['Yes' => 'Yes', 'No' => 'No'],'column' => '3'],
            'enable_voice_call' => ['label' => 'Voice Call', 'type' => 'radio','value_arr' => ['Yes' => 'Yes', 'No' => 'No'],'column' => '3'],
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }
}

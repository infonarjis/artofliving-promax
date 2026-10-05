<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

## Services :
use App\Services\AdminFormBuilderService;

class WhatsappConfigurationController extends Controller
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

    ## SMS Api Configuration :
    public function whatsappConfigurationAddEditForm()
    {
        $elementArr = array(
            'whatsapp_api' => array('is_required' => 'required', 'class' => 'required','modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'whatsapp_api_status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        ## Extra Js :
        $extraJsArr = [];
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1'),
            'callbackUrl' => 'admin.whatsappConfigurationAddEditForm'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => 'Update Whatsapp Configuration',
            'elementArr' => $elementArr,
            'formUrl' => 'admin.whatsappConfigurationAddEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/whatsappConfiguration/whatsappConfigurationAddEdit', $dataArr);
    }

    ## SMS Api Configuration Submit :
    public function whatsappConfigurationAddEdit(Request $request)
    {
        $postData = $request->all();

        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $updateArr = array(
            'whatsapp_api',
            'whatsapp_api_status'
        );
        $updateData = _getRequestData($updateArr, $postData);
        $whereArr = ['id' => $postData['id']];
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return redirect()->route($postData['callbackUrl'])
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }
}

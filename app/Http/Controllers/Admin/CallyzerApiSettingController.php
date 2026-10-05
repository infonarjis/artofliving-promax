<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallyzerApiSetting;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class CallyzerApiSettingController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;

    public function __construct(AdminFormBuilderService $adminFormBuilderServic)
    {
        $this->adminFormBuilderService = new $adminFormBuilderServic;
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->directoryName = '/callyzerApiSetting';
        $this->pageName = 'Callyzer API Settings';
    }

    ## Popup Update :
    public function addEditForm($id = '1')
    {
        $elementArr = array(
            'api_mode' => array('type' => 'radio', 'value_arr' => _getStaticArr('paymentModeArr'), 'is_required' => 'required', 'column' => '6'),
            'api_key' => array('label' => 'API Keys', 'column' => '6'),
            'status' => [
                'type' => 'radio',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ],
        );

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = CallyzerApiSetting::find($id);
        }
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.callyzerApiSetting.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.callyzerApiSetting.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'api_mode',
            'api_key',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                CallyzerApiSetting::where('id', $postData['id'])->update($updateData);

                // CallyzerApiSetting::where('id','!=',$postData['id'])->update(['status'=>$postData['status']]);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                CallyzerApiSetting::created($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', 'Data not updated!');
        }
    }
}

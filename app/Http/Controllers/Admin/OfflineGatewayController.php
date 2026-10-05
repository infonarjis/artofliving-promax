<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\OfflinePayment;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class OfflineGatewayController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/membershipPlan';
    }

    public function offlinePaymentAddEditForm()
    {
        $elementArr = array(
            'account_holder_number' => array('is_required' => 'required', 'label' => 'Account Holder Name', 'column' => '6'),
            'account_number' => array('is_required' => 'required', 'label' => 'Account Number', 'column' => '6'),
            'bank_name' => array('is_required' => 'required', 'label' => 'Bank Name', 'column' => '6'),
            'bank_branch' => array('is_required' => 'required', 'label' => 'Bank Branch', 'column' => '6'),
            'ifsc_code' => array('is_required' => 'required', 'label' => 'IFSC Code', 'column' => '6'),
            'qr_code' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.PAYMENT_LOGO_URL',
                'class' => 'required',
                'label' => 'Qr Code'
            ),
            'payment_gateway_img' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.PAYMENT_LOGO_URL',
                'class' => 'required',
                'label' => 'Payment Gateway Image'
            ),
            'status' => array(
                'type' => 'radio',
                'form_group_class' => ' mt-4',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => OfflinePayment::find('1'),
            'callbackUrl' => 'admin.offlinePaymentAddEditForm'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => 'Update Offline Payment',
            'elementArr' => $elementArr,
            'formUrl' => 'admin.offlinePaymentAddEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/offlinePaymentAddEdit', $dataArr);
    }

    ## Submit Form :
    public function offlinePaymentAddEdit(Request $request)
    {
        $postData = $request->all();
        $updateArr = array(
            'account_holder_number',
            'account_number',
            'bank_name',
            'bank_branch',
            'ifsc_code',
            'payment_gateway_img',
            'qr_code',
            'status',
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
                    $uploadedFiles = UploadHelper::uploadFile($request->file($key), $path, $oldValue);
                    $updateData[$key] = $uploadedFiles;
                }
            }
        }

        if (!empty($updateData)) {
            $whereArr = ['id' => $postData['id']];
            OfflinePayment::where($whereArr)->update($updateData);
            return redirect()->route($postData['callbackUrl'])
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }
}

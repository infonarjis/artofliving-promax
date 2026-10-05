<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class PaymentOptionsController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/paymentOptions';
        $this->searchColumn = ['name'];
        $this->pageName = 'Manage Payment Gateway';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.paymentOptions.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.paymentOptions.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 0,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.paymentOptions.addForm',
                'edit' => 'admin.paymentOptions.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        $htmlDataArr = [];
        if (isset($postData) && !blank($postData)) {

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = PaymentMethod::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'DESC')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.paymentOptions.editForm',
                    'view' => 'admin.paymentOptions.viewDetails',
                ],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = [];
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = PaymentMethod::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
        }

        return $tabWiseCountData;
    }

    public function onSearchKeyword($postData)
    {
        $whereStr = '';
        if (
            isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' &&
            !empty($this->searchColumn)
        ) {
            $searchKeyword = $postData['searchKeyword'];
            foreach ($this->searchColumn as $key => $value) {
                if ($key != 0) {
                    $whereStr .= " OR ";
                }
                $whereStr .= "$value like '%$searchKeyword%' ";
            }
            if ($whereStr != '') {
                $whereStr = "( $whereStr )";
            }
        }
        return $whereStr;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (
            isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
            isset($postData['conditionVal']) && $postData['conditionVal'] != ''
        ) {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
    }

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
        ];

        $postData = $request->all();
        if (empty($postData)) {
            return response()->json($responseArr, 200);
        }
        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            PaymentMethod::where('id', $postData['id'])->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            PaymentMethod::where('id', $postData['id'])->update($updateData);

            ## Other Status Should Be Updated :
            if (isset($postData['status']) && $postData['status'] == 'APPROVED') {
                PaymentMethod::where('id', '!=', $postData['id'])->update(['status' => 'UNAPPROVED']);
            }
        }

        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $mode = ($id != '') ? 'edit' : 'add';

        $paymentMethod = '';
        if($mode == 'edit'){
            $paymentMethod = 'disabled';
        }

        $elementArr = array(
            'name' => array('is_required' => 'required', 'class' => 'required', 'column' => '6', 'other' => $paymentMethod),
            'payment_mode' => array('type' => 'radio', 'value_arr' => _getStaticArr('paymentModeArr'), 'is_required' => 'required', 'column' => '6','modeType' => $mode, 'isDisableInDemo' => 'Yes'),
            'client_id' => array('label' => 'Client Id', 'column' => '6','modeType' => $mode, 'isDisableInDemo' => 'Yes'),
            'client_secret' => array('label' => 'Client Secret', 'column' => '6','modeType' => $mode, 'isDisableInDemo' => 'Yes'),
            'app_payment_source' => array(
                'type' => 'radio',
                'class' => 'required',
                'is_required' => 'required',
                'column' => '6',
                'value_arr' => array('IN_APP' => 'In App', 'PAYMENT_GATEWAY' => 'Payment Gateway')
            ),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
            'logo' => array(
                'is_required' => 'required',
                'type' => 'file',
                'other' => 'data-width="400" data-height="100"',
                'crop_image' => 'Yes',
                'path_value' => 'upload_path.PAYMENT_LOGO_URL',
                'class' => 'required'
            ),
        );

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = PaymentMethod::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.paymentOptions.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.paymentOptions.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        ## Disable In Demo :
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $updateArr = array(
            'name',
            'payment_mode',
            'client_id',
            'client_secret',
            'app_payment_source',
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
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                PaymentMethod::where($whereArr)->update($updateData);

                ## Other Status Should Be Updated :
                if (isset($postData['status']) && $postData['status'] == 'APPROVED') {
                    PaymentMethod::where('id', '!=', $postData["id"])->update(['status' => 'UNAPPROVED']);
                }
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                $payment = PaymentMethod::create($updateData);
                ## Other Status Should Be Updated :
                if (isset($postData['status']) && $postData['status'] == 'APPROVED') {
                    PaymentMethod::where('id', '!=', $payment->id)->update(['status' => 'UNAPPROVED']);
                }
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', 'Data not updated!');
        }
    }

    public function viewDetails($id = 0)
    {
        $resultArr = PaymentMethod::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

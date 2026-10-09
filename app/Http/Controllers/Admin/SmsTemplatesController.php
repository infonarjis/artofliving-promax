<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class SmsTemplatesController extends Controller
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
        $this->directoryName = '/smsTemplates';
        $this->searchColumn = ['template_name', 'sms_content'];
        $this->pageName = 'Manage SMS Templates';
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
            'ajaxPaginationRequestUrl' => 'admin.smsTemplates.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.smsTemplates.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.smsTemplates.addForm',
                'edit' => 'admin.smsTemplates.editForm/',
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
            $resultArr = SmsTemplate::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'asc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.smsTemplates.editForm',
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
            $tabWiseCountData[$tabId] = SmsTemplate::query()
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
        if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {
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

        $ids = $postData['id'] ?? [];
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter($ids);
        if (empty($ids)) {
            $responseArr['msg'] = 'Invalid IDs supplied.';
            return response()->json($responseArr, 200);
        }

        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            SmsTemplate::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            SmsTemplate::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $elementArr = array(
            'template_name' => array('is_required' => 'required', 'class' => 'required', 'other' => 'disabled'),
            'template_id' => array('is_required' => 'required', 'class' => 'required'),
            'sms_content' => array('type' => 'textarea'),
            'status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $mode = ($id != '') ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = SmsTemplate::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.smsTemplates.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.smsTemplates.addEdit',
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
            'template_name',
            'template_id',
            'sms_content',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                SmsTemplate::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                SmsTemplate::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## SMS Api Configuration :
    public function smsConfigurationAddEditForm()
    {
        $elementArr = array(
            'sms_api_url' => array('is_required' => 'required', 'class' => 'required','modeType' => 'edit', 'isDisableInDemo' => 'Yes'),
            'sms_api' => array('is_required' => 'required', 'class' => 'required','modeType' => 'edit', 'isDisableInDemo' => 'Yes','column'=> '6'),
            'sms_api_sender_id' => array('is_required' => 'required', 'class' => 'required','modeType' => 'edit', 'isDisableInDemo' => 'Yes','column'=> '6'),
            'sms_api_status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find(1),
            'callbackUrl' => 'admin.smsConfigurationAddEditForm'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => 'Update Sms Configuration',
            'elementArr' => $elementArr,
            'formUrl' => 'admin.smsConfigurationAddEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/smsTemplates/smsConfigurationAddEdit', $dataArr);
    }

    ## SMS Api Configuration Submit :
    public function smsConfigurationAddEdit(Request $request)
    {
        $postData = $request->all();

        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $updateArr = array(
            'sms_api_url',
            'sms_api',
            'sms_api_sender_id',
            'sms_api_status'
        );
        $updateData = _getRequestData($updateArr, $postData);
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffLeave;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

## Services
use App\Services\AdminFormBuilderService;

class StaffLeaveManagementController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staffLeaveManagement';
        $this->searchColumn = ['leave_type', 'subject','message','status'];
        $this->pageName = 'Manage Staff Leave';
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
            'pendingTab' => [
                'label' => 'Pending list',
                'id' => 'pendingData',
                'class' => '',
                'conditionVal' => 'PENDING',
                'conditionColumn' => 'status',
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
            'rejectTab' => [
                'label' => 'Rejected list',
                'id' => 'rejectedData',
                'class' => '',
                'conditionVal' => 'REJECTED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
        ];
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.staffLeaveManagement.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.staffLeaveManagement.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'view' => 0,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.staffLeaveManagement.addForm',
                'edit' => '',
                'view' => '',
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
        if (isset($postData) && !blank($postData)) {

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            $whereArr['staff_id'] = auth()->guard('staff')->user()->id;
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
            
            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = StaffLeave::when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [],
            ];
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr','dataArr'));
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
            $whereArr['staff_id'] = auth()->guard('staff')->user()->id;
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = StaffLeave::query()
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

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $elementArr = [
            'subject' => ['is_required' => 'required', 'class' => 'required', 'column' => '6'],
            'total_leave' => ['is_required' => 'required', 'class' => 'required', 'column' => '6', 'input_type' => 'number'],
            'message' => [
                'type' => 'textarea',
                'is_required' => 'required', 'class' => 'required',
                'column' => '12',
            ],
            'leave_start_date' => ['is_required' => 'required', 'input_type' => 'date', 'class' => 'required','column' => '6'],
            'leave_end_date' => ['is_required' => 'required', 'input_type' => 'date', 'class' => 'required','column' => '6'],
            'leave_type' => ['type' => 'dropdown', 'column' => '6', 'value_arr' => ['Sick Leave' => 'Sick Leave', 'Paid Leave' => 'Paid Leave']],
        ];

        $mode = ($id != '') ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = StaffLeave::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.staffLeaveManagement.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.staffLeaveManagement.addEdit',
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
            'subject',
            'leave_type',
            'total_leave',
            'message',
            'leave_start_date',
            'leave_end_date',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $updateData['staff_id'] = auth()->guard('staff')->user()->id;
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                StaffLeave::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])
                    ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                StaffLeave::create($updateData);
                return redirect()->route($postData['callbackUrl'])
                    ->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }
}

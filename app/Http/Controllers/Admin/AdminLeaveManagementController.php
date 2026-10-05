<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffLeave;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AdminLeaveManagementController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->adminFormBuilderService = $adminFormBuilderService;

        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->directoryName = '/adminLeaveManagement';
        $this->searchColumn = ['leave_type', 'subject', 'message', 'status'];
        $this->pageName = 'Manage Admin Leave';
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
        session()->forget('whereStrFilter');
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.adminLeaveManagement.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.adminLeaveManagement.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 1,
                'approve' => 1,
                'reject' => 1,
                'pending' => 1,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'view' => 0,
                'filter' => ($userType == 'Admin') ? 1 : 0,
                'downloadCsv' => ($userType == 'Admin') ? 1 : 0,
                'downloadPdf' => ($userType == 'Admin') ? 1 : 0,
            ],
            'actionButtonUrl' => [
                'add' => '',
                'edit' => '',
                'view' => '',
                'filter' => 'admin.adminLeaveManagement.getFilter',
                'downloadCsv' => 'admin.adminLeaveManagement.downloadCsv',
                'downloadPdf' => 'admin.adminLeaveManagement.downloadPdf',
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
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffIds = $postData['staff_id'];
                    $ids = implode("','", $staffIds); // e.g. 11,12,13
                    $whereStrFilter .= (!blank($whereStrFilter) ? " AND " : "") .
                        "(staff_leaves.staff_id IN ('$ids'))";
                }
            }
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }
            session(['whereStrFilter' => $whereStr]);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = StaffLeave::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
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
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr', 'dataArr'));
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

    ## Change Status :
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
            StaffLeave::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            StaffLeave::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    public function getFilter(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();

        $currentDate = _getCurrentDate('Y-m-d');
        if (isset($postData) && !blank($postData)) {
            $elementArr = array(
                'staff_id' => array(
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Staff List',
                    'type' => 'dropdown',
                    'relation' => array(
                        'rel_model' => 'Staff',
                        'key_val' => 'id',
                        'key_disp' => 'username',
                        'rel_col_name' => 'type',
                        'rel_col_val' => 'Staff'
                    )
                ),
            );

            $otherData = [
                'rowData' => [],
            ];
            $fromHtml =
                $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.salesReports.getAjaxPaginationData',
                'formId' => 'filterForm',
                'formName' => 'filterForm',
                'formSubmitBtnClass' => 'filterFormSubmitBtn',
                'formSubmitBtnId' => 'filterFormSubmitBtn',
                'fromHtml' => $fromHtml,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/filterPopup', $dataArr);
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function getRecords()
    {
        $staffListArr = Staff::get();
        $year_first_date = date('Y-01-01', strtotime(date('Y-m-d')));
        $previous_month_end = date('Y-m-t', strtotime('-1 month', strtotime(date('Y-m-d'))));
        foreach ($staffListArr as $staffKey => $staffVal) {
            ## Old Leave Calculation :
            $total_sick_leave = StaffLeave::where([
                'status'     => 'APPROVED',
                'leave_type' => 'Sick Leave',
                'staff_id'   => $staffVal->id,
            ])
                ->where(function ($query) use ($year_first_date, $previous_month_end) {

                    $query->whereBetween('leave_start_date', [$year_first_date, $previous_month_end])

                        ->orWhereBetween('leave_end_date', [$year_first_date, $previous_month_end])

                        ->orWhereRaw(
                            '? BETWEEN leave_start_date AND leave_end_date',
                            [$year_first_date]
                        );
                })
                ->selectRaw("
                    SUM(
                        DATEDIFF(
                            LEAST(leave_end_date, ?),
                            GREATEST(leave_start_date, ?)
                        ) + 1
                    ) AS total_leave_days
                ", [$previous_month_end, $year_first_date])
                ->first();
            $staffListArr[$staffKey]->total_sick_leave_taken = $total_sick_leave->total_leave_days ?? '0';
            $dataArr['total_sick_leave'] = $total_sick_leave;
            $total_paid_leave = StaffLeave::where([
                'status'     => 'APPROVED',
                'leave_type' => 'Paid Leave',
                'staff_id'   => $staffVal->id,
            ])
                ->where(function ($query) use ($year_first_date, $previous_month_end) {

                    $query->whereBetween('leave_start_date', [$year_first_date, $previous_month_end])

                        ->orWhereBetween('leave_end_date', [$year_first_date, $previous_month_end])

                        ->orWhereRaw(
                            '? BETWEEN leave_start_date AND leave_end_date',
                            [$year_first_date]
                        );
                })
                ->selectRaw("
                    SUM(
                        DATEDIFF(
                            LEAST(leave_end_date, ?),
                            GREATEST(leave_start_date, ?)
                        ) + 1
                    ) AS total_leave_days
                ", [$previous_month_end, $year_first_date])
                ->first();
            $staffListArr[$staffKey]->total_paid_leave_taken = $total_paid_leave->total_leave_days ?? '0';

            $staffListArr[$staffKey]->sick_leave_balance = $staffVal->total_sick_leave - $staffListArr[$staffKey]->total_sick_leave_taken;
            $staffListArr[$staffKey]->paid_leave_balance = $staffVal->total_paid_leave - $staffListArr[$staffKey]->total_paid_leave_taken;
        }
        return $staffListArr;
    }

    public function getResultDataCsv($resultArr)
    {
        $resultDataCsv = [];
        foreach ($resultArr as $key => $value) {
            $resultDataCsv[] = [
                'Staff Code' => $value->staff_prefix,
                'Total Sick Leave' => $value->total_sick_leave,
                'Total Sick Leave Taken' => $value->total_sick_leave_taken,
                'Sick Leave Balance' => $value->sick_leave_balance,
                'Total Paid Leave' => $value->total_paid_leave,
                'Total Paid Leave Taken' => $value->total_paid_leave_taken,
                'Paid Leave Balance' => $value->paid_leave_balance,
            ];
        }
        return $resultDataCsv;
    }

    public function downloadCsv()
    {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_leave_balance_' . date('ymdhis');
        $dataTableColm = array('Staff Code', 'Total Sick Leave', 'Total Sick Leave Taken', 'Sick Leave Balance', 'Total Paid Leave', 'Total Paid Leave Taken', 'Paid Leave Balance');
        $array[] = $dataTableColm;
        foreach ($resultDataCsv as $key => $row) {
            $line = array();
            foreach ($dataTableColm as $item) {
                $line[] = $row[$item];
            }
            $array[] = $line;
        }
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"$filename" . ".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        $handle = fopen('php://output', 'w');
        foreach ($array as $array) {
            fputcsv($handle, $array);
        }
        fclose($handle);
    }

    public function downloadPdf()
    {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_leave_balance_' . date('ymdhis');
        $dataTableColm = array('Staff Code', 'Total Sick Leave', 'Total Sick Leave Taken', 'Sick Leave Balance', 'Total Paid Leave', 'Total Paid Leave Taken', 'Paid Leave Balance');
        $dataArr['dataTableColm'] = $dataTableColm;
        $dataArr['resultDataCsv'] = $resultDataCsv;
        $dataArr['reportTitle'] = 'Staff Leave Balance Report';
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
        $pdf = Pdf::loadHTML($html);

        return $pdf->download($filename . '.pdf');
    }
}

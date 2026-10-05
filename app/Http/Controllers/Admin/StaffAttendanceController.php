<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StaffAttendanceController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staffAttendance';
        $this->searchColumn = ['punch_in_remarks', 'punch_out_remarks', 'attendance_status'];
        $this->pageName = 'Manage Staff Attendance';
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
        ];
    }

    ## List :
    public function index()
    {
        session()->forget('whereStrFilter');
        $authUserType = Auth::user()->type;
        $userType = _adminUserType($authUserType);

        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];
        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.staffAttendance.getAjaxPaginationData',
            'changeStatusUrl' => '',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'view' => 0,
                'filter' => ($userType == 'Admin') ? 1 : 0,
                'downloadCsv' => ($userType == 'Admin') ? 1 : 0,
                'downloadPdf' => ($userType == 'Admin') ? 1 : 0,
            ],
            'actionButtonUrl' => [
                'filter' => 'admin.staffAttendance.getFilter',
                'downloadCsv' => 'admin.staffAttendance.downloadCsv',
                'downloadPdf' => 'admin.staffAttendance.downloadPdf',
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
                        "(staff_attendance.staff_id IN ('$ids'))";
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
            $authUserType = Auth::user()->type;
            $userType = _adminUserType($authUserType);
            if ($userType == 'Staff') {
                $whereArr['staff_id'] = auth()->guard('staff')->user()->id;
            }
            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = StaffAttendance::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
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
            $authUserType = Auth::user()->type;
            $userType = _adminUserType($authUserType);
            if ($userType == 'Staff') {
                $whereArr['staff_id'] = auth()->guard('staff')->user()->id;
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = StaffAttendance::query()
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

    public function punchInOut(Request $request)
    {
        $postData = $request->all();
        $authUser = Auth::user();
        if (isset($postData['id']) && $postData['id'] != '') {
            $whereArr = [
                'id' => $postData['id']
            ];
            $updateArr = [
                'punch_out' => _getCurrentDate(),
                'punch_out_remarks' => $postData['punch_out_remarks'],
                'updated_at' => _getCurrentDate()
            ];
            StaffAttendance::where($whereArr)->update($updateArr);
            return redirect()->back()->with('success', 'Punched out successfully.');
        } else {
            $insertArr = [
                'staff_id' => $authUser->id,
                'punch_in' => _getCurrentDate(),
                'punch_in_remarks' => $postData['punch_in_remarks'],
                'attendance_status' => 'Present',
                'ip_address' => $request->ip(),
                'created_at' => _getCurrentDate()
            ];
            StaffAttendance::create($insertArr);
            return redirect()->back()->with('success', 'Punched in successfully.');
        }
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
        $authUser = Auth::user();

        $whereStrFilter = session()->get('whereStrFilter');
        ## Condition Column Value:
        $whereArr = [];
        ## Check Search Keyword :
        $whereStr = $whereStrFilter;
        if ($userType == 'Staff') {
            $whereArr['staff_id'] = $authUser->id;
        }
        // Data
        $resultArr = StaffAttendance::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
            $q->where($whereArr);
        })
            ->when(!empty($whereStr), function ($q) use ($whereStr) {
                $q->whereRaw($whereStr);
            })
            ->orderBy('id', 'desc')->get();
        return $resultArr;
    }

    public function getResultDataCsv($resultArr)
    {
        $resultDataCsv = [];
        foreach ($resultArr as $key => $value) {
            $resultDataCsv[] = [
                'Username' => $value->staff->username,
                'Punch In' => $value->punch_in,
                'Punch Out' => $value->punch_out,
                'Attendance Status' => $value->attendance_status,
                'IP Address' => $value->ip_address,
                'Created At' => $value->created_at,
                'Total Working Hours' => _calculateTimeDifference($value->punch_in, $value->punch_out),
            ];
        }
        return $resultDataCsv;
    }

    public function downloadCsv()
    {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_attendance_history_' . date('ymdhis');
        $dataTableColm = array('Username', 'Punch In', 'Punch Out', 'Attendance Status', 'IP Address', 'Created At', 'Total Working Hours');
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
        $filename = 'staff_attendance_history_' . date('ymdhis');
        $dataTableColm = array('Username', 'Punch In', 'Punch Out', 'Attendance Status', 'IP Address', 'Created At', 'Total Working Hours');
        $dataArr['dataTableColm'] = $dataTableColm;
        $dataArr['resultDataCsv'] = $resultDataCsv;
        $dataArr['reportTitle'] = 'Staff Attendance History Report';
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
        $pdf = Pdf::loadHTML($html);

        return $pdf->download($filename . '.pdf');
    }
}

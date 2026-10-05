<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSalaryMaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
## Services
use App\Services\AdminFormBuilderService;

class StaffSalarySlipController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staffSalarySlip';
        $this->searchColumn = ['basic_salary', 'total_earning', 'total_deduction', 'total_net_payable_salary', 'month_year'];
        $this->pageName = 'Staff Salary Slips';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ]
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
            'ajaxPaginationRequestUrl' => 'admin.staffSalarySlip.getAjaxPaginationData',
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
                'add' => '',
                'edit' => '',
                'view' => '',
                'filter' => 'admin.staffSalarySlip.getFilter',
                'downloadCsv' => 'admin.staffSalarySlip.downloadCsv',
                'downloadPdf' => 'admin.staffSalarySlip.downloadPdf',
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
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if ($userType == 'Staff') {
                $whereArr['staff_id'] = $authUser->id;
            }
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffIds = $postData['staff_id'];
                    $ids = implode("','", $staffIds); // e.g. 11,12,13
                    $whereStrFilter .= (!blank($whereStrFilter) ? " AND " : "") .
                        "(staff_salary_master.staff_id IN ('$ids'))";
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
            $resultArr = StaffSalaryMaster::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
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
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if ($userType == 'Staff') {
                $whereArr['staff_id'] = $authUser->id;
            }
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = StaffSalaryMaster::query()
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
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);

        $whereStrFilter = session()->get('whereStrFilter');
        ## Condition Column Value:
        $whereArr = [];
        ## Check Search Keyword :
        $whereStr = $whereStrFilter;
        if ($userType == 'Staff') {
            $whereArr['staff_id'] = $authUser->id;
        }
        // Data
        $resultArr = StaffSalaryMaster::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
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
            $salary_pay_date = '';
            if (isset($value['salary_pay_date']) && $value['salary_pay_date'] != '0000-00-00') {
                $salary_pay_date = $value['salary_pay_date'];
            }
            $resultDataCsv[] = [
                'Username' => $value->staff->username,
                'Payable Days' => $value->payable_days,
                'Basic Salary' => $value->basic_salary,
                'Earnings' => $value->total_earning,
                'Deductions' => $value->total_deduction,
                'Net Salary' => $value->total_net_payable_salary,
                'Salary Month' => $value->month_year,
                'Salary Pay Date' => $salary_pay_date,
            ];
        }
        return $resultDataCsv;
    }

    public function downloadCsv()
    {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_payroll_history_' . date('YmdHis');
        $dataTableColm = array('Username', 'Payable Days', 'Basic Salary', 'Earnings', 'Deductions', 'Net Salary', 'Salary Month', 'Salary Pay Date');
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
        $filename = 'staff_payroll_history_' . date('YmdHis');
        $dataTableColm = array('Username', 'Payable Days', 'Basic Salary', 'Earnings', 'Deductions', 'Net Salary', 'Salary Month', 'Salary Pay Date');
        $dataArr['dataTableColm'] = $dataTableColm;
        $dataArr['resultDataCsv'] = $resultDataCsv;
        $dataArr['reportTitle'] = 'Staff Payroll Report';
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
        $pdf = Pdf::loadHTML($html);

        return $pdf->download($filename . '.pdf');
    }
}

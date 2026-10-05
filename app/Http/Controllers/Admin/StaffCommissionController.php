<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffCommission;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StaffCommissionController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staffCommission';
        $this->searchColumn = ['matri_id', 'plan_name','plan_amount','plan_offer_amount','currency','commission_percentage', 'commssion_amount'];
        $this->pageName = 'Staff Commission';
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
        $authUserType = Auth::user()->type;
        $userType = _adminUserType($authUserType);
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.staffCommission.getAjaxPaginationData',
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
                'filter' => 'admin.staffCommission.getFilter',
                'downloadCsv' => 'admin.staffCommission.downloadCsv',
                'downloadPdf' => 'admin.staffCommission.downloadPdf',
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
                        "(staff_commissions.staff_id IN ('$ids'))";
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
            $resultArr = StaffCommission::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
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
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = StaffCommission::query()
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

    public function getRecords() {
        $whereStrFilter = session()->get('whereStrFilter');
        ## Condition Column Value:
        $whereArr = [];
        ## Check Search Keyword :
        $whereStr = $whereStrFilter;
        $authUserType = Auth::user()->type;
        $userType = _adminUserType($authUserType);
        if ($userType == 'Staff') {
            $whereArr['staff_id'] = auth()->guard('staff')->user()->id;
        }
        // Data
        $resultArr = StaffCommission::with('staff')->when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
            ->when(!empty($whereStr), function ($q) use ($whereStr) {
                $q->whereRaw($whereStr);
            })
            ->orderBy('id', 'desc')->get();
        return $resultArr;
    }

    public function getResultDataCsv($resultArr) {
        $resultDataCsv = [];
        foreach ($resultArr as $key => $value) {
            $resultDataCsv[] = [
                'Username' => $value->staff->username,
                'Matri Id' => $value->matri_id,
                'Plan Name' => $value->plan_name,
                'Plan Amount' => $value->plan_amount,
                'Plan Offer Amount' => $value->plan_offer_amount,
                'Commission Percentage' => $value->commission_percentage,
                'Commission Amount' => $value->commssion_amount,
                'Currency' => $value->currency,
                'Created On' => $value->created_at,
            ];
        }
        return $resultDataCsv;
    }

    public function downloadCsv() {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_commission_history_' . date('ymdhis');
        $dataTableColm = array('Username','Matri Id','Plan Name','Plan Amount','Plan Offer Amount','Commission Percentage','Commission Amount','Currency', 'Created On');
        $array[] = $dataTableColm;
        foreach ($resultDataCsv as $key => $row){
            $line = array();
            foreach ($dataTableColm as $item){									
                $line[] = $row[$item]; 
            }
            $array[] = $line;
        }
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"$filename".".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        $handle = fopen('php://output', 'w');
        foreach ($array as $array) {
            fputcsv($handle, $array);
        }
        fclose($handle);
    }

    public function downloadPdf() {
        $resultArr = $this->getRecords();
        $resultDataCsv = $this->getResultDataCsv($resultArr);
        $filename = 'staff_commission_history_' . date('ymdhis');
        $dataTableColm = array('Username','Matri Id','Plan Name','Plan Amount','Plan Offer Amount','Commission Percentage','Commission Amount','Currency', 'Created On');
        $dataArr['dataTableColm'] = $dataTableColm;
        $dataArr['resultDataCsv'] = $resultDataCsv;
        $dataArr['reportTitle'] = 'Staff Commission Report';
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
        $pdf = Pdf::loadHTML($html);

        return $pdf->download($filename.'.pdf');
    }
}

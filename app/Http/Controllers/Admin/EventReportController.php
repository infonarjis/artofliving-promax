<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Http\Controllers\Controller;
use App\Models\EventRegister;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

## Services
use App\Services\AdminFormBuilderService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class EventReportController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/eventReport';
        $this->searchColumn = ['name', 'email', 'mobile'];
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
    public function index($eventId = "")
    {
        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'Event Reports',
            'ajaxPaginationRequestUrl' => 'admin.eventReport.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.eventReport.changeStatus',
            'extraJsArr' => $extraJsArr,
            'eventId' => $eventId,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'filter' => 1,
                'downloadBtn' => 1
            ],
            'actionButtonUrl' => [
                'add' => 'admin.eventReport.addForm',
                'edit' => 'admin.eventReport.editForm',
                'filter' => 'admin.eventReport.getFilter'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.eventReport.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
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
            if (isset($postData['eventId']) && $postData['eventId'] != '') {
                $whereArr['event_id'] = base64_decode($postData['eventId']);
            }
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                if (
                    isset($postData['created_from']) && !blank($postData['created_from'])
                    && isset($postData['created_to']) && !blank($postData['created_to'])
                ) {
                    $fromDate = (string)$postData['created_from'];
                    $toDate = (string)$postData['created_to'];
                    $whereStrFilter .= "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['event_id']) && !blank($postData['event_id'])) {
                    $eventId  = $postData['event_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = 'AND';
                    }
                    $whereStrFilter .= "$checkAnd ( event_id = '$eventId')";
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
            $resultArr = EventRegister::with(['event:id,title'])->when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
            $responseArr['data'] = $htmlDataArr;
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
            $tabWiseCountData[$tabId] = EventRegister::query()
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
                'created_from' => array(
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => "Date Registered From"
                ),
                'created_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Date Registered To"
                ),
                'event_id' => array(
                    'label' => 'Event List',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'Event',
                        'key_val' => 'id',
                        'class' => '',
                        'key_disp' => 'title'
                    )
                ),
            );

            $otherData = [
                'rowData' => [],
            ];
            $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.eventReport.getAjaxPaginationData',
                'formId' => 'filterForm',
                'formName' => 'filterForm',
                'formSubmitBtnClass' => 'filterFormSubmitBtn',
                'formSubmitBtnId' => 'filterFormSubmitBtn',
                'fromHtml' => $fromHtml,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/filterPopup', $dataArr);
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function downloadReport(Request $request)
    {
        $postData = $request->all();

        $query = EventRegister::with([
            'event:id,title'
        ]);
        // Proper date filtering (no whereRaw)
        if (!blank($postData['filedownloadDate'])) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate   = Carbon::parse($toDate)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $resultDataArr = $query->orderByDesc('id')->get();

        if ($resultDataArr->isEmpty()) {
            return redirect()->route('admin.eventReport.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }
        $exportData = [];
        foreach ($resultDataArr as $row) {
            ## Disable In Demo:
            if (_getConstant('DISABLE_DEMO') ==  'Enabled') {
                $row->email = _getConstant('DISABLE_IN_DEMO_LABEL');
                $row->mobile = _getConstant('DISABLE_IN_DEMO_LABEL');
            }
            $exportData[] = [
                'Event Name'      => $row->event->title ?? '',
                'Name'         => $row->name ?? '',
                'Mobile No'         => $row->mobile ?? '',
                'Email'         => $row->email ?? '',
                'Hear About Us'         => $row->hear_about_us ?? '',
                'Ticket Quantity'         => $row->tickets_qty ?? '',
                'Grand Total'         => $row->grand_total ?? '',
                'Payment Mode'         => $row->payment_mode ?? '',
                'Registered On'   => $row->created_at->format('d-m-Y H:i:s'),
            ];
        }
        ## Heading :
        $heading = ['Event Name', 'Name', 'Mobile No', 'Email', 'Hear About Us', 'Ticket Quantity', 'Grand Total', 'Payment Mode', 'Registered On'];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');
        $fileName = 'Event Report (' . $currentDate . ')';

        if ($postData['downloadFormat'] == 'PDF') {
            $dataArr = [
                'title' => 'Event Report',
                'resultDataArr' => $resultDataArr,
                'heading' => $heading,
            ];
            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, $dataArr)->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path()])->setPaper('A4', 'potrait');
            return $pdf->download($fileName . '.pdf');
        } elseif ($postData['downloadFormat'] == 'CSV') {
            return Excel::download(new CommonAdminExport(collect($exportData), $heading, ['created_at']), $fileName . '.xlsx');
        }
    }

}

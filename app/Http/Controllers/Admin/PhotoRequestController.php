<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Http\Controllers\Controller;
use App\Models\PhotoRequest;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PhotoRequestController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $modelName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/photoRequest';
        $this->modelName = 'PhotoRequest';
        $this->searchColumn = ['sender_matri_id', 'receiver_matri_id','receiver_response'];
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
    public function index($matriID = '')
    {
        session()->forget('whereStrFilter');
        
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'Manage Photo Request',
            'ajaxPaginationRequestUrl' => 'admin.photoRequest.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'memberMatriId' => $matriID,
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
                'add' => 'admin.photoRequest.addForm',
                'edit' => 'admin.photoRequest.editForm/',
                'filter' => 'admin.photoRequest.getFilter'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.photoRequest.downloadReport'),
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
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
            ## Check Member Id  :
            if (isset($postData['memberMatriId']) && $postData['memberMatriId'] != '') {
                $memberMatriId = addslashes($postData['memberMatriId']);
                if (!blank($whereStr)) {
                    $whereStr .= " AND ";
                }
                $whereStr .= "(sender_matri_id = '" . $memberMatriId . "' OR receiver_matri_id = '" . $memberMatriId . "')";
            }
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
                    $whereStrFilter .= $this->modelName . ".created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['sender_matri_id']) && !blank($postData['sender_matri_id'])) {
                    $memberMatriId  = $postData['sender_matri_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = 'AND';
                    }
                    $whereStrFilter .= "$checkAnd ( sender_matri_id = '$memberMatriId')";
                }
                if (isset($postData['receiver_matri_id']) && !blank($postData['receiver_matri_id'])) {
                    $memberMatriId  = $postData['receiver_matri_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = 'AND';
                    }
                    $whereStrFilter .= "$checkAnd ( receiver_matri_id = '$memberMatriId')";
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
            $resultArr = PhotoRequest::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            ## Tab Wise Count Data :
            $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr, [], $postData);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    /**
     * Tab Wise Count
     */
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
            $tabWiseCountData[$tabId] = PhotoRequest::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
        }

        return $tabWiseCountData;
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
                    'label' => "Date Range From"
                ),
                'created_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Date Range To"
                ),
                'sender_matri_id' => array(
                    'label' => 'Sender Matri Id',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'class' => 'select2',
                    'relation' => array(
                        'rel_model' => $this->modelName,
                        'key_val' => 'sender_matri_id',
                        'class' => '',
                        'key_disp' => 'sender_matri_id'
                    )
                ),
                'receiver_matri_id' => array(
                    'label' => 'Receiver Matri Id',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'class' => 'select2',
                    'relation' => array(
                        'rel_model' => $this->modelName,
                        'key_val' => 'receiver_matri_id',
                        'class' => '',
                        'key_disp' => 'receiver_matri_id'
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
                'formUrl' => 'admin.photoRequest.getAjaxPaginationData',
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

    public function downloadReport(Request $request)
    {
        $postData = $request->all();

        $query = PhotoRequest::query();

        if (!blank($postData['filedownloadDate'] ?? null)) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $from = Carbon::parse($fromDate)->startOfDay();
            $to   = Carbon::parse($toDate)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        $resultDataArr = $query->orderByDesc('id')->get(['sender_matri_id', 'receiver_matri_id', 'receiver_response', 'created_at']);

        if (blank($resultDataArr)) {
            return redirect()->route('admin.photoRequest.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }
        ## Heading :
        $heading = ['Sender Matri Id', 'Receiver Matri Id', 'Receiver Response', 'Send Date'];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');
        $fileName = 'Photo Request Report (' . $currentDate . ')';

        if ($postData['downloadFormat'] == 'PDF') {
            $dataArr = [
                'title' => 'Photo Request Report',
                'resultDataArr' => $resultDataArr,
                'heading' => $heading,
            ];
            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, $dataArr)->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path()])->setPaper('A4', 'potrait');
            return $pdf->download($fileName . '.pdf');
        } elseif ($postData['downloadFormat'] == 'CSV') {
            return Excel::download(
                new CommonAdminExport($resultDataArr, $heading, ['created_at']),
                $fileName . '.xlsx'
            );
        }
    }
}

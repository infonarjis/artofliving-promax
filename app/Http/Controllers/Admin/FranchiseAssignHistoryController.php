<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Http\Controllers\Controller;
use App\Models\AssignHistory;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FranchiseAssignHistoryController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/franchiseAssignHistory';
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
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => 'Manage Assigned Members To Franchise',
            'ajaxPaginationRequestUrl' => 'admin.franchiseAssignHistory.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.franchiseAssignHistory.changeStatus',
            'extraJsArr' => $extraJsArr,
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
            'statusTabArr' => $this->statusTabArr,
            'actionButtonUrl' => [
                'filter' => 'admin.franchiseAssignHistory.getFilter'
            ],
            'downloadDropdownArr' => [
                'route' => route('admin.franchiseAssignHistory.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $query = $this->getAssignHistoryQuery($request);
        $resultArr = $query->orderByDesc('id')->paginate($limit, ['*'], 'page', $page);

        $htmlDataArr['tabCount'] = $this->tabWiseCountData(
            $this->statusTabArr,
            $query
        );

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

        $responseArr['status'] = 'success';
        $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = $html->render();

        return response()->json($responseArr);
    }

    private function getAssignHistoryQuery(Request $request)
    {
        $query = AssignHistory::with([
            'register:id,matri_id,fullname,email',
            'franchise:id,username'
        ])->whereNotNull('member_id')->where('user_type', 'Franchise')->where('action', 'Assign');

        ## Franchise Restriction :
        if (Auth::user()->type !== 'Admin') {
            $query->where('assign_to', Auth::id());
        }

        ## Search :
        if ($request->filled('searchKeyword')) {
            $keyword = $request->searchKeyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('register', function ($q) use ($keyword) {
                    $q->where('matri_id', 'like', "%{$keyword}%")
                        ->orWhere('fullname', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })
                    ->orWhereHas('franchise', function ($q) use ($keyword) {
                        $q->where('username', 'like', "%{$keyword}%");
                    });
            });
        }

        ## Date Filter :
        if ($request->filled('created_from') && $request->filled('created_to')) {
            $fromDate = Carbon::parse($request->created_from)->startOfDay();
            $toDate = Carbon::parse($request->created_to)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        ## Matri ID Filter :
        if ($request->filled('member_matri_id')) {
            $matriId = $request->member_matri_id;
            $query->whereHas('register', function ($q) use ($matriId) {
                $q->where('matri_id', $matriId);
            });
        }
        return $query;
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $query)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $tabQuery = clone $query;
            if (!blank($value['conditionColumn'])) {
                $tabQuery->where($value['conditionColumn'], $value['conditionVal']);
            }
            $tabWiseCountData[$tabId] = $tabQuery->count();
        }
        return $tabWiseCountData;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
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
                    'label' => "Date Range From"
                ),
                'created_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Date Range To"
                ),
            );

            $otherData = [
                'rowData' => [],
            ];
            $fromHtml =
                $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.franchiseAssignHistory.getAjaxPaginationData',
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
        $query = AssignHistory::with([
            'register:id,matri_id,fullname,email',
            'franchise:id,username'
        ])->whereNotNull('member_id')->where('user_type', 'Franchise')->where('action', 'Assign');

        ## Proper date filtering (no whereRaw):
        if (!blank($postData['filedownloadDate'])) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $query->whereBetween('assign_date', [$fromDate, $toDate]);
        }
        $resultDataArr = $query->orderByDesc('id')->get();
        if ($resultDataArr->isEmpty()) {
            return redirect()->route('admin.franchiseAssignHistory.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }
        $exportData = [];
        foreach ($resultDataArr as $row) {
            ## Disable In Demo:
            if (_getConstant('DISABLE_DEMO') ==  'Enabled') {
                $email = _getConstant('DISABLE_IN_DEMO_LABEL');
            } else {
                $email = $row->register->email ?? '';
            }
            $exportData[] = [
                'Matri Id'      => $row->register->matri_id ?? '',
                'Username'      => $row->register->fullname ?? '',
                'Email'         => $email ?? '',
                'Assign To'     => $row->franchise->username ?? '',
                'Assign Date'   => _displayDate($row->assign_date, 'j F, Y h:i A'),
            ];
        }

        ## Heading :
        $heading = ['Matri Id', 'Username', 'Email', 'Assign to', 'Assign Date'];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');
        $fileName = 'Franchise Assign History Report (' . $currentDate . ')';

        if ($postData['downloadFormat'] == 'PDF') {
            $dataArr = [
                'title' => 'Franchise Assign History Report',
                'resultDataArr' => $resultDataArr,
                'heading' => $heading,
            ];
            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, $dataArr)->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path()])->setPaper('A4', 'potrait');
            return $pdf->download($fileName . '.pdf');
        } elseif ($postData['downloadFormat'] == 'CSV') {
            return Excel::download(
                new CommonAdminExport(collect($exportData), $heading, ['assign_date']),
                $fileName . '.xlsx'
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Http\Controllers\Controller;
use App\Models\AssignHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FranchiseLeadAssignHistoryController extends Controller
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
        $this->directoryName = '/franchiseLeadAssignHistory';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => '',
            ],
        ];
    }

    public function index()
    {
        session()->forget('whereStrFilter');

        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js',
        ];

        $dataArr = [
            'pageName' => 'Assigned Lead Generation To Franchise',
            'ajaxPaginationRequestUrl' =>
            'admin.franchiseLeadAssignHistory.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'filter' => 1,
                'downloadBtn' => 1,
            ],
            'actionButtonUrl' => [
                'filter' =>
                'admin.franchiseLeadAssignHistory.getFilter',
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.franchiseLeadAssignHistory.downloadReport'),
                'downloadType' => ['CSV', 'PDF',],
            ],
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

        $postData = $request->all();

        if (!isset($postData) || blank($postData)) {
            return response()->json($responseArr, 200);
        }

        session()->forget('whereStrFilter');
        $page = (isset($postData['page']) && $postData['page'] !== '') ? (int) $postData['page'] : 1;
        $limit = (isset($postData['limit']) && $postData['limit'] !== '') ? (int) $postData['limit'] : 10;
        ## Condition Column Value :
        $whereArr = $this->conditionValue($postData);

        ## Franchise Restriction :
        $authUser = Auth::user();
        if ($authUser && $authUser->type !== 'Admin') {
            $whereArr['assign_to'] = $authUser->id;
        }
        ## Main Query :
        $query = AssignHistory::with(['leadGeneration:id,username,email,interest', 'franchise:id,username',])
            ->where('lead_generation_id', '!=', '')
            ->where('action', 'Assign')
            ->where('user_type', 'Franchise')
            ## Condition Filter :
            ->when(
                !empty($whereArr),
                function (Builder $query) use ($whereArr) {
                    $query->where($whereArr);
                }
            )
            ## Search Filter :
            ->when(
                !empty($postData['searchKeyword']),
                function (Builder $query) use ($postData) {
                    $this->applySearchFilter(
                        $query,
                        $postData['searchKeyword']
                    );
                }
            )
            ## Date and Other Filters :
            ->when(
                $postData['isFilterApply'] ?? false,
                function (Builder $query) use ($postData) {
                    if (!empty($postData['created_from']) && !empty($postData['created_to'])) {
                        $query->whereBetween(
                            'assign_date',
                            [
                                $postData['created_from'],
                                $postData['created_to'],
                            ]
                        );
                    }
                    if (!empty($postData['member_matri_id'])) {
                        $query->where('matri_id', $postData['member_matri_id']);
                    }
                }
            );

        ## Tab-wise Counts :
        $htmlDataArr['tabCount'] =
            $this->tabWiseCountData(
                $this->statusTabArr,
                $postData,
                $whereArr
            );

        ## Pagination :
        $resultArr = $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

        ## Render HTML :
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();
        $responseArr['status'] = 'success';
        $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = $html;
        return response()->json($responseArr, 200);
    }

    ## Apply Search Filter :
    private function applySearchFilter(Builder $query, string $searchKeyword)
    {
        $searchKeyword = trim($searchKeyword);
        $query->where(function (Builder $query) use ($searchKeyword) {
            ## Search Lead Username :
            $query->whereHas(
                'leadGeneration',
                function (Builder $leadQuery) use ($searchKeyword) {
                    $leadQuery->where('username', 'LIKE', '%' . $searchKeyword . '%');
                }
            )
                ## Search Lead Email
                ->orWhereHas(
                    'leadGeneration',
                    function (Builder $leadQuery) use ($searchKeyword) {
                        $leadQuery->where('email', 'LIKE', '%' . $searchKeyword . '%');
                    }
                )
                ->orWhereHas(
                    'leadGeneration',
                    function (Builder $leadQuery) use ($searchKeyword) {
                        $leadQuery->where('interest', 'LIKE', '%' . $searchKeyword . '%');
                    }
                )
                ## Search Assigned Franchise Username
                ->orWhereHas(
                    'franchise',
                    function (Builder $franchiseQuery) use ($searchKeyword) {
                        $franchiseQuery->where('username', 'LIKE', '%' . $searchKeyword . '%');
                    }
                );
        });
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $postData = [], $whereArr = [])
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = AssignHistory::query()->where('lead_generation_id', '!=', '')
                ->where('action', 'Assign')
                ->where('user_type', 'Franchise');
            ## Tab Conditions :
            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }
            $strWhere = $value['strWhere'] ?? '';
            if (is_array($strWhere) && !empty($strWhere)) {
                $query->where($strWhere);
            }
            ## Franchise Restriction :
            if (!empty($whereArr)) {
                $query->where($whereArr);
            }
            ## Search Filter :
            if (!empty($postData['searchKeyword'])) {
                $this->applySearchFilter(
                    $query,
                    $postData['searchKeyword']
                );
            }

            ## Date Filter :
            if (($postData['isFilterApply'] ?? false) && !empty($postData['created_from']) && !empty($postData['created_to'])) {
                $query->whereBetween('assign_date', [$postData['created_from'], $postData['created_to']]);
            }

            ## Member Matri ID Filter :
            if (($postData['isFilterApply'] ?? false) && !empty($postData['member_matri_id'])) {
                $query->where('matri_id', $postData['member_matri_id']);
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    ## Search Keyword :
    public function onSearchKeyword($postData)
    {
        return '';
    }

    ## Condition Value :
    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] !== '' && isset($postData['conditionVal']) && $postData['conditionVal'] !== '') {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
    }

    ## Get Filter :
    public function getFilter(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];

        $postData = $request->all();
        $currentDate = _getCurrentDate('Y-m-d');

        if (isset($postData) && !blank($postData)) {
            $elementArr = [
                'created_from' => [
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => 'Date Range From',
                ],
                'created_to' => [
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' =>
                    'max="' . $currentDate . '"',
                    'label' => 'Date Range To',
                ],
            ];
            $otherData = [
                'rowData' => [],
            ];

            $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' =>
                'admin.franchiseLeadAssignHistory.getAjaxPaginationData',
                'formId' => 'filterForm',
                'formName' => 'filterForm',
                'formSubmitBtnClass' =>
                'filterFormSubmitBtn',
                'formSubmitBtnId' =>
                'filterFormSubmitBtn',
                'fromHtml' => $fromHtml,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/filterPopup', $dataArr)->render();

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = $html;
        }

        return response()->json($responseArr, 200);
    }

    ## Download Report :
    public function downloadReport(Request $request)
    {
        $postData = $request->all();

        $query = AssignHistory::with([
            'leadGeneration:id,username,email,interest',
            'franchise:id,username',
        ])->where('lead_generation_id', '!=', '')
            ->where('action', 'Assign')
            ->where('user_type', 'Franchise');
        if (!blank($postData['filedownloadDate'] ?? '')) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
            $query->whereBetween('assign_date', [$fromDate, $toDate]);
        }
        $resultDataArr = $query->orderByDesc('id')->get();

        if ($resultDataArr->isEmpty()) {
            return redirect()->route('admin.franchiseLeadAssignHistory.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }

        $exportData = [];
        foreach ($resultDataArr as $row) {
            $exportData[] = [
                'Username' => $row->leadGeneration->username ?? '',
                'Email' => $row->leadGeneration->email ?? '',
                'Lead Interest' => $row->leadGeneration->interest ?? '',
                'Assign To' => $row->franchise->username ?? '',
                'Assign Date' => $row->assign_date->format('d-m-Y H:i:s'),
            ];
        }

        $heading = [
            'Lead Name',
            'Lead Email',
            'Lead Interest',
            'Assign to',
            'Assign Date',
        ];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');

        $fileName = 'Franchise Lead Assign History Report (' . $currentDate . ')';

        if ($postData['downloadFormat'] === 'PDF') {
            $dataArr = [
                'title' => 'Franchise Lead Assign History Report',
                'resultDataArr' => $resultDataArr,
                'heading' => $heading,
            ];

            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, $dataArr)->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path(),])->setPaper('A4', 'portrait');

            return $pdf->download($fileName . '.pdf');
        }

        if ($postData['downloadFormat'] === 'CSV') {
            return Excel::download(
                new CommonAdminExport(collect($exportData), $heading, ['assign_date']),
                $fileName . '.xlsx'
            );
        }
    }
}

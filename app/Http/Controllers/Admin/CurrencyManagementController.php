<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\CurrencyMaster;
use App\Services\AdminFormBuilderService;

class CurrencyManagementController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;
    private $duplicateKeyCheck;
    private $searchColumn;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/currencyManagement';
        $this->duplicateKeyCheck = ['currency_name','currency_code'];
        $this->pageName = 'Currency Management';
        $this->searchColumn = ['currency_name'];
        $this->statusTabArr = [
            'all' => ['label' => 'All', 'id' => 'allData', 'isActive' => 1, 'conditionColumn' => '', 'conditionVal' => ''],
            'approveTab' => ['label' => 'Approved', 'id' => 'approvedData', 'conditionColumn' => 'status', 'conditionVal' => 'APPROVED'],
            'unapproveTab' => ['label' => 'Unapproved', 'id' => 'unapprovedData', 'conditionColumn' => 'status', 'conditionVal' => 'UNAPPROVED'],
        ];
    }

    // List Page
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.currency.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.currency.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => ['add' => 1,'delete' => 1,'approve' => 1,'unapprove' => 1,'edit' => 1,'isSearch' => 1],
            'actionButtonUrl' => ['add' => 'admin.currency.addForm','edit' => 'admin.currency.editForm/'],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    // AJAX Pagination
    public function getAjaxPaginationData(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $searchKeyword = $request->input('searchKeyword');
        $conditionColumn = $request->input('conditionColumn');
        $conditionVal = $request->input('conditionVal');

        $query = CurrencyMaster::query();

        if ($conditionColumn && $conditionVal) {
            $query->where($conditionColumn, $conditionVal);
        }

        if ($searchKeyword) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('currency_name', 'like', "%$searchKeyword%")
                  ->orWhere('currency_code', 'like', "%$searchKeyword%");
            });
        }

        $resultCount = $query->count();
        $results = $query->orderBy('id', 'desc')->forPage($page, $limit)->get();

        // Tab counts
        $tabCount = [];
        foreach ($this->statusTabArr as $tab) {
            $tabQuery = CurrencyMaster::query();
            if (!empty($postData['searchKeyword'])) {
                    $keyword = $postData['searchKeyword'];
                    $tabQuery->where(function ($q) use ($keyword) {
                        foreach ($this->searchColumn as $col) {
                            $q->orWhere($col, 'like', "%$keyword%");
                        }
                    });
                }
            if (!empty($tab['conditionColumn'])) {
                $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
            }
            $tabCount[$tab['id']] = $tabQuery->count();
        }

        $resultArr = new LengthAwarePaginator($results, $resultCount, $limit, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();

        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'data' => ['tabCount' => $tabCount],
            'html' => $html
        ]);
    }

    // Change Status
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
            CurrencyMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            CurrencyMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    // Add/Edit Form
    public function addEditForm($id = null)
    {
        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? CurrencyMaster::find($id) : null;

        $elementArr = [
            'currency_name' => ['is_required' => 'required', 'class' => 'required'],
            'currency_code' => ['is_required' => 'required', 'class' => 'required'],
            'status' => ['type' => 'radio', 'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']],
        ];

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.currency.index'
        ]);

        $dataArr = [
            'pageName' => $this->pageName.' '.ucwords($mode),
            'elementArr' => $elementArr,
            'fromHtml' => $fromHtml,
            'formUrl' => 'admin.currency.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'rowData' => $rowData,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    // Submit Add/Edit
    public function addEdit(Request $request)
    {
        $data = $request->only(['currency_name','currency_code','status']);
        $mode = $request->input('mode');
        $callbackUrl = $request->input('callbackUrl', 'admin.currency.index');

        // Check duplicate
        $duplicate = $this->checkDuplicate($request);
        if ($duplicate) {
            return redirect()->route($callbackUrl)->with('error', "Duplicate Data found for $duplicate");
        }

        if ($mode == 'edit') {
            $id = $request->input('id');
            CurrencyMaster::where('id', $id)->update($data);
            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }

        if ($mode == 'add') {
            CurrencyMaster::create($data);
            return redirect()->route($callbackUrl)->with('success', 'Data added successfully.');
        }

        return redirect()->route($callbackUrl)->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    // Check Duplicate
    private function checkDuplicate(Request $request)
    {
        $query = CurrencyMaster::query();
        foreach ($this->duplicateKeyCheck as $key) {
            $query->where($key, $request->input($key));
        }
        if ($request->input('id')) {
            $query->where('id', '!=', $request->input('id'));
        }

        return $query->exists() ? implode(', ', $this->duplicateKeyCheck) : '';
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MoonsignMaster;
use App\Services\AdminFormBuilderService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class MoonsignController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName = '/moonsign';
    private $pageName = 'Manage Moonsign';
    private $statusTabArr = [];
    private $duplicateKeyCheck = ['moonsign_name'];

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;
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
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ]
        ];
    }

    /** Index Page */
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.moonsign.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.moonsign.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => ['add' => 1, 'delete' => 1, 'approve' => 1, 'unapprove' => 1, 'edit' => 1, 'isSearch' => 1],
            'actionButtonUrl' => ['add' => 'admin.moonsign.addForm', 'edit' => 'admin.moonsign.editForm/'],
            'statusTabArr' => $this->statusTabArr
        ]);
    }

    /** AJAX Pagination */
    public function getAjaxPaginationData(Request $request)
    {
        $page = $request->post('page', 1);
        $limit = $request->post('limit', 10);
        $searchKeyword = $request->post('searchKeyword', '');
        $conditionColumn = $request->post('conditionColumn', '');
        $conditionVal = $request->post('conditionVal', '');
        $langCode = _getDefaultLanguage();

        $query = MoonsignMaster::query()->where('lang_code', $langCode);

        if (!blank($searchKeyword)) {
            $query->where('moonsign_name', 'like', "%$searchKeyword%");
        }

        if ($conditionColumn && $conditionVal) {
            $query->where($conditionColumn, $conditionVal);
        }

        $total = $query->count();
        $resultArr = $query->orderBy('id', 'desc')->forPage($page, $limit)->get();

        $paginator = new LengthAwarePaginator($resultArr, $total, $limit, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page'
        ]);

        $tabCount = [];
        foreach ($this->statusTabArr as $key => $tab) {
            $tabQuery = MoonsignMaster::query()->where('lang_code', $langCode);
            if (!blank($tab['conditionColumn'])) {
                $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
            }
            $tabCount[$tab['id']] = $tabQuery->count();
        }

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', ['resultArr' => $paginator])->render();

        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'data' => ['tabCount' => $tabCount],
            'html' => $html,
        ]);
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
            MoonsignMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            MoonsignMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    /** Show Add/Edit Form */
    public function addEditForm($id = null)
    {
        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? MoonsignMaster::find($id) : null;

        $elementArr = [
            'moonsign_name' => ['is_required' => 'required', 'class' => 'required'],
            'status' => ['type' => 'radio', 'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']]
        ];

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.moonsign.index'
        ]);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'fromHtml' => $fromHtml,
            'formUrl' => 'admin.moonsign.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'rowData' => $rowData,
            'id' => $id,
            'mode' => $mode,
            'languageDataArr' => _getActiveLanguage(),
            'languageColumn' => 'moonsign_name',
            'languageChangeRoute' => route('admin.moonsign.getLangData'),
            'callbackUrl' => 'admin.moonsign.index'
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    /** Handle Add/Edit Submission */
    public function addEdit(Request $request)
    {
        $updateData = $request->only(['moonsign_name', 'status']);

        $mode = $request->input('mode');
        $callbackUrl = $request->input('callbackUrl');
        $defaultLanguage = _getDefaultLanguage();
        try {
            // ================= ADD =================
            if ($mode === 'add') {
                $exists = MoonsignMaster::where('moonsign_name', $request->moonsign_name)
                    ->where('lang_code', $defaultLanguage)
                    ->exists();
                if ($exists) {
                    return redirect()->route($callbackUrl)->with('error', 'Already exists.');
                }
                MoonsignMaster::create($updateData);
                return redirect()->route($callbackUrl)->with('success', 'Data added successfully.');
            }

            // ================= UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = MoonsignMaster::find($request->id);
                if (!$model) {
                    return redirect()->route($callbackUrl)->with('error', 'Record not found.');
                }
                $model->update($updateData);
            } else {

                $existingLangRow = MoonsignMaster::where([
                    'lang_id'   => $request->id,
                    'lang_code' => $request->lang_code
                ])->first();

                if ($existingLangRow) {
                    $existingLangRow->update($updateData);
                } else {
                    $updateData['lang_id'] = $request->id;
                    $updateData['lang_code'] = $request->lang_code;
                    $updateData['status'] = 'APPROVED';

                    MoonsignMaster::create($updateData);
                }
            }

            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($callbackUrl)->with('error', $e->getMessage());
        }
    }

    /** Check Duplicate */
    private function checkDuplicate($postData)
    {
        $query = MoonsignMaster::query();
        foreach ($this->duplicateKeyCheck as $key) {
            $query->where($key, $postData[$key]);
        }
        if (!empty($postData['id'])) {
            $query->where('id', '!=', $postData['id']);
        }

        if ($query->exists()) {
            $labels = array_map(fn($v) => _createLabel($v), $this->duplicateKeyCheck);
            return implode(', ', $labels);
        }
        return '';
    }

    ## Language Code:
    public function getLangData(Request $request)
    {
        $id        = $request->id;
        $langCode  = $request->langCode;
        $column    = $request->languageColumn;

        $defaultLanguage = _getDefaultLanguage();

        // Step 1: Get master row (always default language row)
        $masterRow = MoonsignMaster::where(function ($q) use ($id, $defaultLanguage) {
            $q->where('id', $id)
                ->where('lang_code', $defaultLanguage);
        })->orWhere(function ($q) use ($id, $defaultLanguage) {
            $q->where('lang_id', $id)
                ->where('lang_code', $defaultLanguage);
        })->first();

        if (!$masterRow) {
            return response()->json([
                'lang_id'   => $id,
                'lang_code' => $langCode,
                'key'       => $column,
                'value'     => ''
            ]);
        }

        // Step 2: If default language → use master row
        if ($langCode === $defaultLanguage) {
            $value = $masterRow->$column ?? '';
            $langId = $masterRow->id;
        } else {
            // Step 3: Find translation row using lang_id
            $translation = MoonsignMaster::where([
                'lang_id'   => $masterRow->id,
                'lang_code' => $langCode,
            ])->first();

            $value  = $translation->$column ?? '';
            $langId = $translation->id ?? $masterRow->id;
        }

        return response()->json([
            'lang_id'   => $langId,
            'lang_code' => $langCode,
            'key'       => $column,
            'value'     => $value,
        ], 200);
    }
}

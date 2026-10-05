<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\StarMaster;
use App\Services\AdminFormBuilderService;

class StarController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $duplicateKeyCheck;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/star';
        $this->searchColumn = ['star_name'];
        $this->duplicateKeyCheck = ['star_name'];
        $this->pageName = 'Manage Star';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
            ],
        ];
    }

    // List page
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.star.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.star.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.star.addForm',
                'edit' => 'admin.star.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    // AJAX Pagination
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
        $conditionColumn = $request->input('conditionColumn');
        $conditionVal = $request->input('conditionVal');
        $searchKeyword = $request->input('searchKeyword');

        $query = StarMaster::query()->where('lang_code', _getDefaultLanguage());

        // Apply condition
        if ($conditionColumn && $conditionVal) {
            $query->where($conditionColumn, $conditionVal);
        }

        // Search
        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                foreach ($this->searchColumn as $col) {
                    $q->orWhere($col, 'like', "%$searchKeyword%");
                }
            });
        }

        $resultCount = $query->count();
        $results = $query->orderBy('id', 'desc')
            ->forPage($page, $limit)
            ->get();

        // Tab-wise count
        $tabCount = [];
        foreach ($this->statusTabArr as $tab) {
            $tabQuery = StarMaster::query()->where('lang_code', _getDefaultLanguage());
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

        $responseArr = [
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'data' => ['tabCount' => $tabCount],
            'html' => $html,
        ];

        return response()->json($responseArr);
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
            StarMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            StarMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    // Show Add/Edit Form
    public function addEditForm($id = null)
    {
        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? StarMaster::find($id) : null;

        $elementArr = [
            'star_name' => ['is_required' => 'required', 'class' => 'required'],
            'status' => [
                'type' => 'radio',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ]
        ];

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.star.index'
        ]);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'fromHtml' => $fromHtml,
            'formUrl' => 'admin.star.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'rowData' => $rowData,
            'languageDataArr' => _getActiveLanguage(),
            'id' => $id,
            'mode' => $mode,
            'languageColumn' => 'star_name',
            'languageChangeRoute' => route('admin.star.getLangData'),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    // Submit Add/Edit
    public function addEdit(Request $request)
    {
        $data = $request->only(['star_name', 'status', 'lang_code', 'lang_id']);
        $mode = $request->input('mode');
        $callbackUrl = $request->input('callbackUrl', 'admin.star.index');

        // Check duplicate
        $duplicate = $this->checkDuplicate($request);
        if ($duplicate) {
            return redirect()->route($callbackUrl)->with('error', "Duplicate Data found for $duplicate");
        }

        $defaultLang = _getDefaultLanguage();

        if ($mode == 'edit') {
            $id = $request->input('id');

            // Handle multi-language
            if ($data['lang_code'] != $defaultLang) {
                if ($data['lang_id'] != $id) {
                    // Update language entry
                    StarMaster::where('id', $data['lang_id'])->where('lang_code', $data['lang_code'])->update($data);
                } else {
                    // Insert new language entry
                    $data['lang_id'] = $id;
                    StarMaster::create($data);
                }
            } else {
                // Update default
                StarMaster::where('id', $id)->update($data);
            }

            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }

        if ($mode == 'add') {
            StarMaster::create($data);
            return redirect()->route($callbackUrl)->with('success', 'Data added successfully.');
        }

        return redirect()->route($callbackUrl)->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    // Check Duplicate
    private function checkDuplicate(Request $request)
    {
        $query = StarMaster::query()->where('lang_code', $request->input('lang_code', _getDefaultLanguage()));

        foreach ($this->duplicateKeyCheck as $key) {
            $query->where($key, $request->input($key));
        }

        if ($request->input('id')) {
            $query->where('id', '!=', $request->input('id'));
        }

        if ($query->exists()) {
            return implode(', ', $this->duplicateKeyCheck);
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
        $masterRow = StarMaster::where(function ($q) use ($id, $defaultLanguage) {
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
            $translation = StarMaster::where([
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

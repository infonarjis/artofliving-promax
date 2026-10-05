<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;
use App\Models\MotherTongueMaster;
use Exception;

class MotherTongueController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;
    private $statusTabArr;
    private $searchColumn;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/motherTongue';
        $this->pageName = 'Manage Mother Tongue';
        $this->searchColumn = ['mtongue_name'];
        $this->statusTabArr = [
            'all' => ['label' => 'All', 'id' => 'allData', 'isActive' => 1, 'conditionColumn' => '', 'conditionVal' => ''],
            'approveTab' => ['label' => 'Approved', 'id' => 'approvedData', 'conditionColumn' => 'status', 'conditionVal' => 'APPROVED'],
            'unapproveTab' => ['label' => 'Unapproved', 'id' => 'unapprovedData', 'conditionColumn' => 'status', 'conditionVal' => 'UNAPPROVED'],
        ];
    }

    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.motherTongue.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.motherTongue.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => ['add' => 1, 'delete' => 1, 'approve' => 1, 'unapprove' => 1, 'edit' => 1, 'isSearch' => 1],
            'actionButtonUrl' => ['add' => 'admin.motherTongue.addForm', 'edit' => 'admin.motherTongue.editForm/'],
            'statusTabArr' => $this->statusTabArr
        ]);
    }

    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $responseArr = ['status' => 'error', 'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'), 'html' => '', 'data' => []];

        if (!blank($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = MotherTongueMaster::where('lang_code', _getDefaultLanguage());

            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            if (!empty($postData['searchKeyword'])) {
                $keyword = $postData['searchKeyword'];
                $query->where(function ($q) use ($keyword) {
                    foreach ($this->searchColumn as $col) {
                        $q->orWhere($col, 'like', "%$keyword%");
                    }
                });
            }

            $total = $query->count();
            $results = $query->orderBy('id', 'DESC')->skip(($page - 1) * $limit)->take($limit)->get();
            $resultArr = new LengthAwarePaginator($results, $total, $limit, $page, ['path' => Paginator::resolveCurrentPath()]);

            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = MotherTongueMaster::where('lang_code', _getDefaultLanguage());
                if (!empty($postData['searchKeyword'])) {
                    $keyword = $postData['searchKeyword'];
                    $tabQuery->where(function ($q) use ($keyword) {
                        foreach ($this->searchColumn as $col) {
                            $q->orWhere($col, 'like', "%$keyword%");
                        }
                    });
                }
                if (!blank($tab['conditionColumn'])) {
                    $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
                }
                $tabCount[$tab['id']] = $tabQuery->count();
            }

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();
            $responseArr = ['status' => 'success', 'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'), 'html' => $html, 'data' => ['tabCount' => $tabCount]];
        }

        return response()->json($responseArr, 200);
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
            MotherTongueMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            MotherTongueMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    public function addEditForm($id = '')
    {
        $elementArr = [
            'mtongue_name' => ['is_required' => 'required', 'class' => 'required','label'=>'Mother Tongue Name'],
            'status' => ['type' => 'radio', 'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']],
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? MotherTongueMaster::find($id) : null;

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.motherTongue.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.motherTongue.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'languageDataArr' => _getActiveLanguage(),
            'id' => $id,
            'mode' => $mode,
            'languageColumn' => 'mtongue_name',
            'languageChangeRoute' => route('admin.motherTongue.getLangData'),
            'rowData' => $rowData
        ]);
    }

    ## Add/Edit submit :
    public function addEdit(Request $request)
    {
        $updateData = $request->only(['mtongue_name', 'status']);

        $mode = $request->input('mode');
        $callbackUrl = $request->input('callbackUrl');
        $defaultLanguage = _getDefaultLanguage();
        try {
            // ================= ADD =================
            if ($mode === 'add') {
                $exists = MotherTongueMaster::where('mtongue_name', $request->mtongue_name)
                    ->where('lang_code', $defaultLanguage)
                    ->exists();
                if ($exists) {
                    return redirect()->route($callbackUrl)->with('error', 'Already exists.');
                }
                MotherTongueMaster::create($updateData);
                return redirect()->route($callbackUrl)->with('success', 'Data added successfully.');
            }

            // ================= UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = MotherTongueMaster::find($request->id);
                if (!$model) {
                    return redirect()->route($callbackUrl)->with('error', 'Record not found.');
                }
                $model->update($updateData);
            } else {

                $existingLangRow = MotherTongueMaster::where([
                    'lang_id'   => $request->id,
                    'lang_code' => $request->lang_code
                ])->first();

                if ($existingLangRow) {
                    $existingLangRow->update($updateData);
                } else {
                    $updateData['lang_id'] = $request->id;
                    $updateData['lang_code'] = $request->lang_code;
                    $updateData['status'] = 'APPROVED';

                    MotherTongueMaster::create($updateData);
                }
            }

            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($callbackUrl)->with('error', $e->getMessage());
        }
    }

    ## Language Code:
    public function getLangData(Request $request)
    {
        $id        = $request->id;
        $langCode  = $request->langCode;
        $column    = $request->languageColumn;

        $defaultLanguage = _getDefaultLanguage();

        // Step 1: Get master row (always default language row)
        $masterRow = MotherTongueMaster::where(function ($q) use ($id, $defaultLanguage) {
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
            $translation = MotherTongueMaster::where([
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

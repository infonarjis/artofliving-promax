<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;
use App\Models\StateMaster;
use Exception;

class StateController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;
    private $statusTabArr;
    private $routeName;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/state';
        $this->pageName = 'Manage State';
        $this->routeName = 'state';
        $this->statusTabArr = [
            'all' => ['label' => 'All', 'id' => 'allData', 'isActive' => 1, 'conditionColumn' => '', 'conditionVal' => ''],
            'approveTab' => ['label' => 'Approved', 'id' => 'approvedData', 'conditionColumn' => 'status', 'conditionVal' => 'APPROVED'],
            'unapproveTab' => ['label' => 'Unapproved', 'id' => 'unapprovedData', 'conditionColumn' => 'status', 'conditionVal' => 'UNAPPROVED'],
        ];
    }

    // List view
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.' . $this->routeName . '.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.' . $this->routeName . '.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => ['add' => 1, 'delete' => 1, 'approve' => 1, 'unapprove' => 1, 'edit' => 1, 'isSearch' => 1],
            'actionButtonUrl' => ['add' => 'admin.' . $this->routeName . '.addForm', 'edit' => 'admin.' . $this->routeName . '.editForm/'],
            'statusTabArr' => $this->statusTabArr
        ]);
    }

    ## Ajax pagination :
    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $responseArr = ['status' => 'error', 'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'), 'html' => '', 'data' => []];

        if (!blank($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = StateMaster::with('country')->where('lang_code', _getDefaultLanguage());

            // Filter by tab/status
            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            // Search
            if (!empty($postData['searchKeyword'])) {
                $keyword = $postData['searchKeyword'];
                $query->where(function ($q) use ($keyword) {
                    $q->where('state_name', 'like', "%$keyword%")
                        ->orWhereHas('country', function ($cq) use ($keyword) {
                            $cq->where('country_name', 'like', "%$keyword%");
                        });
                });
            }

            $total = $query->count();
            $results = $query->orderBy('id', 'DESC')->skip(($page - 1) * $limit)->take($limit)->get();

            $resultArr = new LengthAwarePaginator($results, $total, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page'
            ]);

            // Tab counts
            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = StateMaster::where('lang_code', _getDefaultLanguage());
                if (!empty($postData['searchKeyword'])) {
                    $keyword = $postData['searchKeyword'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('state_name', 'like', "%$keyword%")
                            ->orWhereHas('country', function ($cq) use ($keyword) {
                                $cq->where('country_name', 'like', "%$keyword%");
                            });
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
            StateMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            StateMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    // Add/Edit form
    public function addEditForm($id = '')
    {
        $elementArr = [
            'country_id' => [
                'is_required' => 'required',
                'label' => 'Country Name',
                'class' => 'required select2 not_reset',
                'type' => 'dropdown',
                'relation' => ['rel_model' => 'CountryMaster', 'key_val' => 'id', 'key_disp' => 'country_name', 'lang' => _getDefaultLanguage()]
            ],
            'state_name' => ['is_required' => 'required', 'class' => 'required', 'label' => 'State Name'],
            'status' => ['type' => 'radio', 'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']],
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? StateMaster::find($id) : null;

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.' . $this->routeName . '.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.' . $this->routeName . '.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'languageDataArr' => _getActiveLanguage(),
            'id' => $id,
            'mode' => $mode,
            'languageChangeRoute' => route('admin.' . $this->routeName . '.getLangData'),
            'languageColumn' => 'state_name',
            'rowData' => $rowData
        ]);
    }

    ## Add/Edit submit :
    public function addEdit(Request $request)
    {
        $updateData = $request->only(['country_id', 'state_name', 'status']);

        $mode = $request->input('mode');
        $callbackUrl = $request->input('callbackUrl');
        $defaultLanguage = _getDefaultLanguage();
        try {
            // ================= ADD =================
            if ($mode === 'add') {
                $exists = StateMaster::where('country_id', $request->country_id)->where('state_name', $request->state_name)
                    ->where('lang_code', $defaultLanguage)
                    ->exists();
                if ($exists) {
                    return redirect()->route($callbackUrl)->with('error', 'Already exists.');
                }
                StateMaster::create($updateData);
                return redirect()->route($callbackUrl)->with('success', 'Data added successfully.');
            }

            // ================= UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = StateMaster::find($request->id);
                if (!$model) {
                    return redirect()->route($callbackUrl)->with('error', 'Record not found.');
                }
                $model->update($updateData);
            } else {

                $existingLangRow = StateMaster::where([
                    'lang_id'   => $request->id,
                    'lang_code' => $request->lang_code
                ])->first();

                if ($existingLangRow) {
                    $existingLangRow->update($updateData);
                } else {
                    $updateData['lang_id'] = $request->id;
                    $updateData['lang_code'] = $request->lang_code;
                    $updateData['status'] = 'APPROVED';

                    StateMaster::create($updateData);
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
        $masterRow = StateMaster::where(function ($q) use ($id, $defaultLanguage) {
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
            $translation = StateMaster::where([
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

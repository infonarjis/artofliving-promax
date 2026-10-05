<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\MemberDefaultPlaceholder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;
use Exception;
use Illuminate\Support\Facades\DB;

class MemberPlaceholderController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/memberPlaceholder';
        $this->searchColumn = ['title'];
        $this->pageName = 'Member Default Placeholders';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'isActive' => 1,
                'conditionColumn' => '',
                'conditionVal' => '',
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'APPROVED',
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'UNAPPROVED',
            ]
        ];
    }

    // List view
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index',
            [
                'pageName' => $this->pageName,
                'ajaxPaginationRequestUrl' => 'admin.memberPlaceholder.getAjaxPaginationData',
                'changeStatusUrl' => 'admin.memberPlaceholder.changeStatus',
                'extraJsArr' => $extraJsArr,
                'actionBtnArr' => [
                    'add' => 1,
                    'delete' => 0,
                    'approve' => 1,
                    'unapprove' => 1,
                    'edit' => 1,
                    'isSearch' => 1,
                ],
                'actionButtonUrl' => [
                    'add' => 'admin.memberPlaceholder.addForm',
                    'edit' => 'admin.memberPlaceholder.editForm/',
                ],
                'statusTabArr' => $this->statusTabArr,
            ]
        );
    }

    ## Ajax pagination :
    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => []
        ];

        if (!blank($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = MemberDefaultPlaceholder::query();

            // Filter by tab conditions
            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            // Search keyword
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

            $resultArr = new LengthAwarePaginator($results, $total, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.memberPlaceholder.editForm',
                ],
            ]);

            // Tab counts
            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = MemberDefaultPlaceholder::query();
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

            $responseArr = [
                'status' => 'success',
                'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
                'html' => $html,
                'data' => ['tabCount' => $tabCount]
            ];
        }

        return response()->json($responseArr, 200);
    }

    // Change status
    public function changeStatus(Request $request)
    {
        DB::transaction(function () use ($request) {

            // 1) Make all UNAPPROVED
            MemberDefaultPlaceholder::query()
                ->update(['status' => 'UNAPPROVED']);

            // 2) Approve selected one
            MemberDefaultPlaceholder::where('id', $request->id)
                ->update(['status' => 'APPROVED']);

            // 3) Clear cache
            MemberDefaultPlaceholder::clearApprovedCache();
        });

        return response()->json([
            'status' => 'success',
            'msg'    => _getConstant('responce_message.RECORD_UPDATED_SUCCESS'),
            'data'   => []
        ]);
    }

    // Add/Edit form
    public function addEditForm($id = '')
    {
        $elementArr = [
            'male_public_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.PLACEHOLDERS_IMAGE', 'class' => 'required', 'label' => 'Male Default Image'),
            'female_public_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.PLACEHOLDERS_IMAGE', 'class' => 'required', 'label' => 'Female Default Image'),
            'male_protected_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.PLACEHOLDERS_IMAGE', 'class' => 'required', 'label' => 'Male Protected Image'),
            'female_protected_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.PLACEHOLDERS_IMAGE', 'class' => 'required', 'label' => 'Female Protected Image'),
            'status' => [
                'type' => 'radio',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ],
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = MemberDefaultPlaceholder::find($id);
        }

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.memberPlaceholder.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.memberPlaceholder.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'id' => $id,
            'mode' => $mode,
            'rowData' => $rowData,
        ]);
    }

    // Add/Edit submit
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateData = $request->only([
            'male_public_image',
            'female_public_image',
            'male_protected_image',
            'female_protected_image',
            'status'
        ]);

        ## Check If File Exit Or Not And Validation:
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (!empty($value['name'])) {
                    $path = _getConstant($postData[$key . '_path']);
                    $oldValue = '';
                    if (!blank($postData[$key . '_val'])) {
                        $oldValue = $postData[$key . '_val'];
                    }
                    $uploadedFiles = UploadHelper::uploadFile($request->file($key), $path, $oldValue);
                    $updateData[$key] = $uploadedFiles;
                }
            }
        }

        try {
            // if (isset($request->mode) && $request->mode == 'edit') {
            //     $model = MemberDefaultPlaceholder::find($request->id);
            //     if (!$model) {
            //         return redirect()->route($postData['callbackUrl'])->with('error', 'Record not found.');
            //     }
            //     $model->update($updateData);
            // } else {
            //     MemberDefaultPlaceholder::create($updateData);
            // }

            DB::transaction(function () use ($request, $updateData) {
                if ($request->mode === 'edit') {
                    $model = MemberDefaultPlaceholder::find($request->id);
                    if (!$model) {
                        return redirect()->route($request->callbackUrl)->with('error', 'Record not found.');
                    }

                    $model->update($updateData);
                } else {
                    $model = MemberDefaultPlaceholder::create($updateData);
                }
                // 1. Make all records UNAPPROVED
                MemberDefaultPlaceholder::query()->update(['status' => 'UNAPPROVED',]);
                // 2. Approve the selected record using its actual ID
                MemberDefaultPlaceholder::where('id', $model->id)->update(['status' => 'APPROVED',]);
                // 3. Clear approved cache
                MemberDefaultPlaceholder::clearApprovedCache();
            });

            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($postData['callbackUrl'])->with('error', $e->getMessage());
        }
    }
}

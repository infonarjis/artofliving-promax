<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\MemberCartDesignLayout;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;
use Exception;
use Illuminate\Support\Facades\DB;

class MemberDesignLayoutController extends Controller
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
        $this->directoryName = '/memberLayoutsDesign';
        $this->searchColumn = ['title'];
        $this->pageName = 'Manage Member Design Layouts';
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
            ],
        ];
    }

    public function index()
    {
        $extraJsArr = [
            '/custom/js/commonList.js'
        ];
        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index',
            [
                'pageName' => $this->pageName,
                'ajaxPaginationRequestUrl' => 'admin.memberLayoutsDesign.getAjaxPaginationData',
                'changeStatusUrl' => 'admin.memberLayoutsDesign.changeStatus',
                'extraJsArr' => $extraJsArr,
                'actionBtnArr' => [
                    'add' => 0,
                    'delete' => 0,
                    'approve' => 1,
                    'unapprove' => 1,
                    'edit' => 1,
                    'isSearch' => 1,
                ],
                'actionButtonUrl' => [
                    'add' => 'admin.memberLayoutsDesign.addForm',
                    'edit' => 'admin.memberLayoutsDesign.editForm/',
                ],
                'statusTabArr' => $this->statusTabArr,
            ]
        );
    }

    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];

        if (!blank($postData)) {
            $page = (int) ($postData['page'] ?? 1);
            $limit = (int) ($postData['limit'] ?? 10);
            $page = max($page, 1);
            $limit = max($limit, 1);
            $query = MemberCartDesignLayout::query();

            ## TAB FILTER :
            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            ## SEARCH :
            if (!empty($postData['searchKeyword'])) {
                $keyword = trim($postData['searchKeyword']);
                $query->where(function ($q) use ($keyword) {
                    foreach ($this->searchColumn as $column) {
                        $q->orWhere($column, 'like', '%' . $keyword . '%');
                    }
                });
            }
            $total = $query->count();
            $results = $query->orderBy('id', 'ASC')->skip(($page - 1) * $limit)->take($limit)->get();

            $resultArr = new LengthAwarePaginator(
                $results,
                $total,
                $limit,
                $page,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                    'actionButtonUrl' => [
                        'edit' => 'admin.memberLayoutsDesign.editForm',
                    ],
                ]
            );

            ## TAB COUNTS :
            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = MemberCartDesignLayout::query();
                if (!empty($postData['searchKeyword'])) {
                    $keyword = trim($postData['searchKeyword']);
                    $tabQuery->where(function ($q) use ($keyword) {
                        foreach ($this->searchColumn as $column) {
                            $q->orWhere($column, 'like', '%' . $keyword . '%');
                        }
                    });
                }
                ## Status condition :
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
                'data' => [
                    'tabCount' => $tabCount
                ],
            ];
        }
        return response()->json($responseArr, 200);
    }

    public function changeStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => ['required', 'integer', 'exists:member_cart_design_layouts,id',],
                'status' => ['nullable', 'in:APPROVED,UNAPPROVED',],
            ]);
            $result = DB::transaction(function () use ($request) {
                $selectedId = (int) $request->id;
                $selectedLayout = MemberCartDesignLayout::query()->lockForUpdate()->find($selectedId);

                if (!$selectedLayout) {
                    return [
                        'status' => 'error',
                        'msg' => 'Record not found.',
                    ];
                }

                $newStatus = $request->input('status');
                if (blank($newStatus)) {
                    $newStatus = $selectedLayout->status === 'APPROVED' ? 'UNAPPROVED' : 'APPROVED';
                }

                if ($newStatus === 'APPROVED') {
                    MemberCartDesignLayout::query()->where('id', '!=', $selectedId)->update(['status' => 'UNAPPROVED',]);
                    $selectedLayout->update([
                        'status' => 'APPROVED',
                    ]);
                    MemberCartDesignLayout::clearApprovedCache();
                    return [
                        'status' => 'success',
                        'msg' => _getConstant(
                            'responce_message.RECORD_UPDATED_SUCCESS'
                        ),
                    ];
                }

                if ($newStatus === 'UNAPPROVED' && $selectedLayout->status === 'APPROVED') {
                    return [
                        'status' => 'error',
                        'msg' => 'At least one layout must be approved.',
                    ];
                }
                $selectedLayout->update([
                    'status' => 'UNAPPROVED',
                ]);
                MemberCartDesignLayout::clearApprovedCache();
                return [
                    'status' => 'success',
                    'msg' => _getConstant(
                        'responce_message.RECORD_UPDATED_SUCCESS'
                    ),
                ];
            });
            return response()->json([
                'status' => $result['status'],
                'msg' => $result['msg'],
                'data' => [],
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'msg' => $e->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function addEditForm($id = '')
    {
        $elementArr = [
            'title' => ['is_required' => 'required', 'class' => 'required',],
            'preview_image' => [
                'is_required' => 'required',
                'type' => 'file',
                'path_value' =>
                'upload_path.DYNAMIC_LAYOUT_IMAGE',
                'class' => 'required',
                'label' => 'Preview Image',
            ],
            'status' => [
                'type' => 'radio',
                'value_arr' => [
                    'APPROVED' => 'APPROVED',
                    'UNAPPROVED' => 'UNAPPROVED',
                ],
            ],
        ];
        $mode = $id ? 'edit' : 'add';

        $rowData = [];
        if ($mode === 'edit') {
            $rowData = MemberCartDesignLayout::find($id);
        }
        $fromHtml = $this->adminFormBuilderService->generateFormElement(
            $elementArr,
            [
                'mode' => $mode,
                'id' => $id,
                'rowData' => $rowData,
                'callbackUrl' => 'admin.memberLayoutsDesign.index',
            ]
        );

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit',
            [
                'pageName' => $this->pageName . ' ' . ucwords($mode),
                'elementArr' => $elementArr,
                'formUrl' => 'admin.memberLayoutsDesign.addEdit',
                'formId' => 'addEditForm',
                'formName' => 'addEditForm',
                'formSubmitBtnClass' => 'formSubmitBtn',
                'formSubmitBtnId' => 'formSubmitBtn',
                'fromHtml' => $fromHtml,
                'id' => $id,
                'mode' => $mode,
                'rowData' => $rowData,
            ]
        );
    }

    public function addEdit(Request $request)
    {
        $postData = $request->all();
        $updateData = $request->only(['title', 'preview_image', 'status',]);

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
            DB::transaction(function () use ($request, $updateData) {
                if (isset($request->mode) && $request->mode === 'edit') {
                    $model = MemberCartDesignLayout::query()->lockForUpdate()->findOrFail($request->id);
                    $newStatus = $updateData['status'] ?? $model->status;
                    if ($newStatus === 'APPROVED') {
                        MemberCartDesignLayout::query()->where('id', '!=', $model->id)->update(['status' => 'UNAPPROVED',]);
                        $updateData['status'] = 'APPROVED';
                        $model->update($updateData);
                    } elseif ($newStatus === 'UNAPPROVED' && $model->status === 'APPROVED') {
                        throw new Exception(
                            'At least one layout must be approved.'
                        );
                    } else {
                        $model->update($updateData);
                    }
                } else {
                    $approvedExists = MemberCartDesignLayout::query()->where('status', 'APPROVED')->exists();
                    if (!$approvedExists) {
                        $updateData['status'] = 'APPROVED';
                    }
                    if (($updateData['status'] ?? null) === 'APPROVED') {
                        MemberCartDesignLayout::query()->update(['status' => 'UNAPPROVED',]);
                    }
                    MemberCartDesignLayout::create(
                        $updateData
                    );
                }

                MemberCartDesignLayout::clearApprovedCache();
            });

            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($postData['callbackUrl'])->with('error', $e->getMessage());
        }
    }
}

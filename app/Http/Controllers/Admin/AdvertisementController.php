<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementMaster;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;

class AdvertisementController extends Controller
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
        $this->directoryName = '/advertisement';
        $this->searchColumn = ['type', 'link'];
        $this->pageName = 'Manage Advertisement';
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
        $extraJsArr = ['/custom/js' . $this->directoryName . '/list.js'];

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index',
            [
                'pageName' => $this->pageName,
                'ajaxPaginationRequestUrl' => 'admin.advertisement.getAjaxPaginationData',
                'changeStatusUrl' => 'admin.advertisement.changeStatus',
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
                    'add' => 'admin.advertisement.addForm',
                    'edit' => 'admin.advertisement.editForm/',
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

            $query = AdvertisementMaster::query();

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
            ]);

            // Tab counts
            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = AdvertisementMaster::query();
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
            AdvertisementMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            AdvertisementMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    // Add/Edit form
    public function addEditForm($id = '')
    {
        $elementArr = array(
            'adv_type' => array(
                'is_required' => 'required',
                'type' => 'radio',
                'value' => 'banner',
                'value_arr' => array('banner' => 'Banner Advertisement', 'adsense' => 'Google AdSense Advertisement'),
                'class' => 'adv_type',
                'column' => '12',
            ),
            'level' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'value_arr' => [
                    'Level 1' => 'Level 1 (Size : 300 × 300 px)',
                    'Level 2' => 'Level 2 (Size : 300 × 600 px)',
                    'Above Footer' => 'Above Footer (Size : 1920 × 220 px)',
                    'Dashboard Banner' => 'Dashboard Banner (Size : 1056 × 287 px)'
                ],
                'column' => '12',
            ),
            'link' => array(
                'is_required' => 'required',
                'input_type' => 'url',
                'form_group_class' => ' banner_adv',
                'column' => '12',
            ),
            'banner' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.ADVERTISE_IMAGE_URL',
                'form_group_class' => ' banner_adv',
            ),
            'google_adsense' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'form_group_class' => ' adsence_adv'
            ),
            'status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        ## Extra Js :
        $extraJsArrAdd = [
            '/custom/js' . $this->directoryName . '/addEdit.js'
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = AdvertisementMaster::find($id);
        }

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.advertisement.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.advertisement.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'id' => $id,
            'mode' => $mode,
            'rowData' => $rowData,
            'extraJsArr' => $extraJsArrAdd
        ]);
    }

    // Add/Edit submit
    public function addEdit(Request $request)
    {
        $postData = $request->all();
        $updateData = $request->only([
            'type',
            'link',
            'banner',
            'level',
            'google_adsense',
            'status',
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

        if ($postData['mode'] == 'edit') {
            $advertisement = AdvertisementMaster::find($postData['id']);
            if ($advertisement) {
                $advertisement->update($updateData);
            }
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }

        if ($postData['mode'] == 'add') {
            AdvertisementMaster::create($updateData);
            return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
        }

        return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }
}

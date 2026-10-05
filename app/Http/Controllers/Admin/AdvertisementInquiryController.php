<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvertisementInquiry;

class AdvertisementInquiryController extends Controller
{
    private $directoryName;
    private $customJsDirectory;
    private $pageName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/advertisementInquiry';
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Advertisement Inquiry';
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
            'pendingTab' => [
                'label' => 'Pending list',
                'id' => 'pendingData',
                'class' => '',
                'conditionVal' => 'PENDING',
                'conditionColumn' => 'status',
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

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.advertisementInquiry.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.advertisementInquiry.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [],
            'statusTabArr' => $this->statusTabArr
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
            'data' => []
        ];

        $postData = $request->all();

        if (!blank($postData)) {

            $page  = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = AdvertisementInquiry::query();

            // Status filter :
            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            // Search filter :
            if (!empty($postData['searchKeyword'])) {
                $search = $postData['searchKeyword'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            // Pagination data :
            $resultArr = $query->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            // Tab counts :
            $dataArr['tabCount'] = $this->tabWiseCountDataEloquent($postData);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            )->render();

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = $html;
        }

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountDataEloquent($postData)
    {
        $tabWiseCountData = [];

        foreach ($this->statusTabArr as $key => $value) {

            $tabId = $value['id'] ?? $key;

            $query = AdvertisementInquiry::query();

            // Apply tab condition
            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }

            // Apply search filter (IMPORTANT)
            if (!empty($postData['searchKeyword'])) {
                $search = $postData['searchKeyword'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    ## Change Status Data :
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
            AdvertisementInquiry::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            AdvertisementInquiry::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }
}

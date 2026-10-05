<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginHistoryController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->directoryName = '/userLoginHistory';
        $this->searchColumn = ['user_login_history.matri_id', 'user_login_history.email'];
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index($matriID = '')
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'User Login History',
            'ajaxPaginationRequestUrl' => 'admin.userLoginHistory.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.userLoginHistory.changeStatus',
            'extraJsArr' => $extraJsArr,
            'memberMatriId' => $matriID,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.userLoginHistory.addForm',
                'edit' => 'admin.userLoginHistory.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];
        $postData = $request->all();
        $htmlDataArr = [];

        if (!blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? (int) $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? (int) $postData['limit'] : 10;

            $whereArr = $this->conditionValue($postData);

            $whereStr = $this->onSearchKeyword($postData);
            $memberMatriId = $postData['memberMatriId'] ?? null;
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);

            $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

            $htmlDataArr['tabCount'] = $this->tabWiseCountData(
                $this->statusTabArr,
                $whereStr,
                $whereArr,
                $memberMatriId,
                $userType,
                $userId,
                $addBtnPermission
            );

            $resultArr = UserLoginHistory::with(['member:staff_assign_id,franchise_assign_id'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!blank($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->when(!empty($memberMatriId), function ($q) use ($memberMatriId) {
                    $q->where('member_id', $memberMatriId);
                })
                ->when(
                    $addBtnPermission == 'Own Members' && !blank($userId && $userType == 'Staff'),
                    function ($q) use ($userId) {
                        $q->whereHas('member', function ($memberQuery) use ($userId) {
                            $memberQuery->where(
                                'staff_assign_id',
                                $userId
                            );
                        });
                    }
                )
                ->when(
                    $userType == 'Franchise' && !blank($userId),
                    function ($q) use ($userId) {
                        $q->whereHas('member', function ($memberQuery) use ($userId) {
                            $memberQuery->where(
                                'franchise_assign_id',
                                $userId
                            );
                        });
                    }
                )
                ->orderByDesc('id')
                ->paginate($limit, ['*'], 'page', $page);
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = (string) $html;
        }

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count
    public function tabWiseCountData($tabArr, $whereStr, $whereArr = [], $memberMatriId = null, $userType = null, $userId = null, $addBtnPermission = null)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $tabWhereArr = [];
            if (!blank($value['conditionColumn'])) {
                $tabWhereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? '';

            $query = UserLoginHistory::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($tabWhereArr), function ($q) use ($tabWhereArr) {
                    $q->where($tabWhereArr);
                })
                ->when(!blank($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->when(
                    is_string($strWhere) && trim($strWhere) !== '',
                    function ($q) use ($strWhere) {
                        $q->whereRaw($strWhere);
                    }
                )->when(
                    !empty($memberMatriId),
                    function ($q) use ($memberMatriId) {
                        $q->where('member_id', $memberMatriId);
                    }
                );
            if ($addBtnPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
                $query->whereHas('member', function ($memberQuery) use ($userId) {
                    $memberQuery->where(
                        'staff_assign_id',
                        $userId
                    );
                });
            } elseif ($userType == 'Franchise' && !blank($userId)) {
                $query->whereHas('member', function ($memberQuery) use ($userId) {
                    $memberQuery->where(
                        'franchise_assign_id',
                        $userId
                    );
                });
            }
            $tabWiseCountData[$tabId] = $query->count();
        }
        return $tabWiseCountData;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (
            isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
            isset($postData['conditionVal']) && $postData['conditionVal'] != ''
        ) {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
    }

    public function onSearchKeyword($postData)
    {
        $whereStr = '';
        if (
            isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' &&
            !empty($this->searchColumn)
        ) {
            $searchKeyword = $postData['searchKeyword'];
            foreach ($this->searchColumn as $key => $value) {
                if ($key != 0) {
                    $whereStr .= " OR ";
                }
                $whereStr .= "$value like '%$searchKeyword%' ";
            }
            if ($whereStr != '') {
                $whereStr = "( $whereStr )";
            }
        }
        return $whereStr;
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
            UserLoginHistory::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            UserLoginHistory::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }
}

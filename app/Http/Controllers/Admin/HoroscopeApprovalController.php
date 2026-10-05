<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\Register;
use Illuminate\Http\Request;

## Services
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class HoroscopeApprovalController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {   
        $this->directoryName = '/horoscopeApproval';
        $this->searchColumn = ['matri_id', 'email'];
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
                'conditionColumn' => 'horoscope_status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'horoscope_status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('horoscope_upload', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        ## Button Permission Access :
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $horoscopePhotoPermission = _checkPermission($userType, $roleId, 'horoscope_approval');
        if ($horoscopePhotoPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Button Permission Access :
        $horoscopeDeleteBtnPermission = _checkPermission($userType, $roleId, 'horoscope_delete');
        $deleteBtn = 1;
        if ($horoscopeDeleteBtnPermission == 'No' || ($horoscopeDeleteBtnPermission == 'Own Members' && $horoscopePhotoPermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Manage Horoscope',
            'ajaxPaginationRequestUrl' => 'admin.approvehoroscope.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.approvehoroscope.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => $deleteBtn,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.approvehoroscope.addForm',
                'edit' => 'admin.approvehoroscope.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        $htmlDataArr = [];
        if (isset($postData) && !blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            if (blank($whereStr)) {
                $whereStr = "(horoscope_file != '')";
            } else {
                $whereStr .= " AND (horoscope_file != '')";
            }

            ## Check Roles Permission:
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'horoscope_approval');
            $whereConditions = [];
            if ($addBtnPermission == 'Own Members' && !blank($roleId) && $userType == 'Staff') {
                $whereConditions[] = "staff_assign_id = $roleId";
            }
            ## Franchise member Check :
            if ($userType == 'Franchise' && !blank($roleId)) {
                $whereConditions[] = "franchise_assign_id = $roleId";
            }
            $whereArrStr = implode(' AND ', $whereConditions);

            if (!blank($whereArrStr)) {
                if (blank($whereStr)) {
                    $whereStr = $whereArrStr;
                } else {
                    $whereStr .= " AND " . $whereArrStr;
                }
            }

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = Register::select(['id', 'matri_id', 'horoscope_status', 'horoscope_uploaded_on', 'email', 'birthdate', 'horoscope_file','staff_assign_id','franchise_assign_id'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                // ->
                ->orderBy('horoscope_uploaded_on', 'DESC')
                ->paginate($limit, ['*'], 'page', $page);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = [];
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = Register::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
        }

        return $tabWiseCountData;
    }

    ## Staff Assign Check:
    public function staffAssignCheck()
    {
        ## Staff Role Data :
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

        $whereArrStr = '';
        if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
            $whereArrStr = " AND (staff_assign_id = $userId)";
        }

        return $whereArrStr;
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

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (!empty($postData)) {
            $updateData = [];
            if (isset($postData['is_deleted']) && $postData['is_deleted'] == 'Yes') {
                foreach (explode(',', $postData['id']) as $memberId) {
                    ## Get Member Data :
                    $memberData = Register::where('id', $memberId)->select(['matri_id', 'horoscope_file'])->first();
                    // Delete ID Proof:
                    $deletePhotoArr = ['horoscope_file'];
                    foreach ($deletePhotoArr as $photoField) {
                        if (!blank($memberData->$photoField)) {
                            UploadHelper::deleteFile(_getConstant('upload_path.MEMBER_HOROSCOPE_URL'), $memberData->$photoField);
                        }
                    }
                }
                $updateData['horoscope_file'] = '';
                $updateData['horoscope_status'] = 'UNAPPROVED';
            } else {
                $updateData['horoscope_status'] = $postData['horoscope_status'];
                ## Send Notification When Id Proof Approved :
                if (isset($postData['horoscope_status']) && $postData['horoscope_status'] == 'APPROVED') {
                    ## Send Featured Member Notification:
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $member = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        app(NotificationService::class)->sendNotification(
                            $member,
                            $member,
                            'horoscope_approval'
                        );
                    }
                }
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

            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Register::whereIn('id', $ids)->update($updateData);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }
}

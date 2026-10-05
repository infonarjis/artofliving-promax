<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\Register;
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\NotificationService;
use Illuminate\Http\Request;

## Services
use Illuminate\Support\Facades\Auth;

class SelfieApprovalController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct() {

        $this->directoryName = '/selfiePhotoApproval';
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
                'conditionColumn' => 'selfie_photo_status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'selfie_photo_status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('selfie_upload', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        ## Button Permission Access :
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $selfiePhotoApprovalPermission = _checkPermission($userType, $roleId, 'selfie_photo_approval');
        if ($selfiePhotoApprovalPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Button Permission Access :
        $selfiePhotoDeleteBtnPermission = _checkPermission($userType, $roleId, 'selfie_photo_delete');
        $deleteBtn = 1;
        if ($selfiePhotoDeleteBtnPermission == 'No' || ($selfiePhotoDeleteBtnPermission == 'Own Members' && $selfiePhotoApprovalPermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Manage Selfie Photo',
            'ajaxPaginationRequestUrl' => 'admin.selfiePhotoApproval.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.selfiePhotoApproval.changeStatus',
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
                'add' => 'admin.selfiePhotoApproval.addForm',
                'edit' => 'admin.selfiePhotoApproval.editForm/',
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
        if (isset($postData) && !blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            if (blank($whereStr)) {
                $whereStr = "(selfie_photo != '')";
            } else {
                $whereStr .= " AND (selfie_photo != '')";
            }

            ## Check Roles Permission:
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'selfie_photo_approval');
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
            $resultArr = Register::select(['id', 'matri_id', 'selfie_photo', 'selfie_photo_status','selfie_photo_uploaded_on','email', 'birthdate','staff_assign_id','franchise_assign_id','gender'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                // ->
                ->orderBy('selfie_photo_uploaded_on', 'DESC')
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
        ## Button Permission Access :
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

        $whereArrStr = '';
        if ($addBtnPermission == 'Own Members' && $roleId != '' && $userType == 'Staff') {
            $whereArrStr = " AND (staff_assign_id = $roleId)";
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
                    $memberData = Register::where('id', $memberId)->select(['matri_id', 'selfie_photo','selfie_photo_status'])->first();
                    // Delete ID Proof:
                    $deletePhotoArr = ['selfie_photo'];
                    foreach ($deletePhotoArr as $photoField) {
                        if (!blank($memberData->$photoField)) {
                            UploadHelper::deleteFile(_getConstant('upload_path.SELFIE_PHOTOS_URL'), $memberData->$photoField);
                        }
                    }
                }
                $updateData['selfie_photo'] = '';
                $updateData['selfie_photo_status'] = 'UNAPPROVED';
            } else {
                $updateData['selfie_photo_status'] = $postData['selfie_photo_status'];
                ## Send Notification When Id Proof Approved :
                if (isset($postData['selfie_photo_status']) && $postData['selfie_photo_status'] == 'APPROVED') {
                    ## Send Featured Member Notification:
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $member = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        app(NotificationService::class)->sendNotification(
                            $member,
                            $member,
                            'selfie_approval'
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

            if (isset($postData['is_deleted'])) {
                Register::whereIn('id', $ids)->update($updateData);
            }else{
                $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
                unset($updateData['id']);
                Register::whereIn('id', $ids)->update($updateData);
            }
            
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }
}

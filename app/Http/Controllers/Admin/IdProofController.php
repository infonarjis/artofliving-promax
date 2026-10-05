<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\Register;
use Illuminate\Http\Request;

## Services :
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class IdProofController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {
        $this->directoryName = '/idProof';
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
                'conditionColumn' => 'id_proof_status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'id_proof_status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $idProofApprovalPermission = _checkPermission($userType, $roleId, 'id_proof_approval');
        if ($idProofApprovalPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('id_proof_upload', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        ## Button Permission Access :
        $idProofDeleteBtnPermission = _checkPermission($userType, $roleId, 'id_proof_delete');
        $deleteBtn = 1;
        if ($idProofDeleteBtnPermission == 'No' || ($idProofDeleteBtnPermission == 'Own Members' && $idProofApprovalPermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Manage Id Proof',
            'ajaxPaginationRequestUrl' => 'admin.idProof.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.idProof.changeStatus',
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
                'add' => 'admin.idProof.addForm',
                'edit' => 'admin.idProof.editForm/',
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

            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if ($userType == 'Staff') {
                if (blank($whereStr)) {
                    $whereStr = "id_proof_status = 'UNAPPROVED'";
                } else {
                    $whereStr .= " AND id_proof_status = 'UNAPPROVED'";
                }
            }

            ## Check Roles Permission:
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'id_proof_approval');
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
            $resultArr = Register::select(['id', 'status', 'matri_id', 'email', 'id_proof_uploaded_on', 'id_proof_front', 'id_proof_back', 'id_proof_status','staff_assign_id','franchise_assign_id','id_proof_type'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                // ->
                ->orderBy('id_proof_uploaded_on', 'DESC')
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

    public function onSearchKeyword($postData)
    {
        $whereStr = '';
        if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {
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

        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

        if (blank($whereStr)) {
            $whereStr = "(id_proof_front != '' OR id_proof_back != '')";
        } else {
            $whereStr .= " AND (id_proof_front != '' OR id_proof_back != '')";
        }

        if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
            if (blank($whereStr)) {
                $whereStr = "staff_assign_id = $userId";
            } else {
                $whereStr .= " AND staff_assign_id = $userId";
            }
        }

        ## Franchise member Check:
        if ($userType == 'Franchise' && !blank($userId)) {
            if (blank($whereStr)) {
                $whereStr = "franchise_assign_id = $userId";
            } else {
                $whereStr .= " AND franchise_assign_id = $userId";
            }
        }
        return $whereStr;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
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
                    $memberData = Register::where('id', $memberId)->select(['matri_id', 'id_proof_front', 'id_proof_back'])->first();
                    ## Delete ID Proof:
                    $deletePhotoArr = ['id_proof_front', 'id_proof_back'];
                    foreach ($deletePhotoArr as $photoField) {
                        if (!blank($memberData->$photoField)) {
                            UploadHelper::deleteFile(_getConstant('upload_path.MEMBER_IDPROOF_URL'), $memberData->$photoField);
                        }
                    }
                }
                $updateData['id_proof_front'] = '';
                $updateData['id_proof_back'] = '';
                $updateData['id_proof_status'] = 'UNAPPROVED';
            } else {
                $updateData['id_proof_status'] = $postData['id_proof_status'];
                ## Send Notification When Id Proof Approved :
                if (isset($postData['id_proof_status']) && $postData['id_proof_status'] == 'APPROVED') {
                    ## Send Featured Member Notification:
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $member = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        app(NotificationService::class)->sendNotification($member,$member,'id_proof_approval');
                    }
                }
            }
            unset($updateData['id']);

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
                Register::whereIn('id', $ids)->update($updateData);
            } else {
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

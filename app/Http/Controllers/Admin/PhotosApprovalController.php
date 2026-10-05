<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\Register;
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PhotosApprovalController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {
        $this->directoryName = '/photosApproval';
        $this->searchColumn = ['matri_id', 'email', 'fullname'];
        $this->statusTabArr = [
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'photo_status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'photo_status',
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
        $photoApprovalPermission = _checkPermission($userType, $roleId, 'photo_approval');
        if ($photoApprovalPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('photo_upload', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        ## Button Permission Access :
        $photoDeleteBtnPermission = _checkPermission($userType, $roleId, 'photo_delete');
        $photoDeleteBtn = 1;
        if ($photoDeleteBtnPermission == 'No' || ($photoDeleteBtnPermission == 'Own Members' && $photoApprovalPermission != 'Own Members')) {
            $photoDeleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Manage Photos Approval',
            'ajaxPaginationRequestUrl' => 'admin.photosApproval.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.photosApproval.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => $photoDeleteBtn,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.photosApproval.addForm',
                'edit' => 'admin.photosApproval.editForm/',
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

        if (!blank($postData)) {

            $page = !empty($postData['page']) ? $postData['page'] : 1;
            $limit = !empty($postData['limit']) ? $postData['limit'] : 10;

            // Keep conditionVal ignored here because we will apply it separately.
            $whereStr = $this->onSearchKeyword($postData, true);
            $latestPhotoExpr = "GREATEST(
                COALESCE(photo1_uploaded_on, '0000-00-00 00:00:00'),
                COALESCE(photo2_uploaded_on, '0000-00-00 00:00:00'),
                COALESCE(photo3_uploaded_on, '0000-00-00 00:00:00'),
                COALESCE(photo4_uploaded_on, '0000-00-00 00:00:00')
            )";
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'photo_approval');
            $whereConditions = [];
            if ($addBtnPermission == 'Own Members' && !blank($roleId) && $userType == 'Staff') {
                $whereConditions[] = "staff_assign_id = " . (int) $roleId;
            }
            if ($userType == 'Franchise' && !blank($roleId)) {
                $whereConditions[] = "franchise_assign_id = " . (int) $roleId;
            }

            if (!empty($whereConditions)) {
                $whereArrStr = implode(' AND ', $whereConditions);
                if (blank($whereStr)) {
                    $whereStr = $whereArrStr;
                } else {
                    $whereStr .= " AND " . $whereArrStr;
                }
            }

            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            $query = Register::query();
            if (!blank($whereStr)) {
                $query->whereRaw($whereStr);
            }
            if (!blank($postData['conditionVal'] ?? '')) {
                $this->applyPhotoStatusCondition(
                    $query,
                    $postData['conditionVal']
                );
            }
            $resultArr = $query->orderByRaw("$latestPhotoExpr DESC")->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH')
                    . $this->directoryName
                    . '/ajaxResultData',
                compact('resultArr')
            );

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant(
                'responce_message.DATA_GET_SUCCESS'
            );
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = (string) $html;
        }

        return response()->json($responseArr, 200);
    }

    private function applyPhotoStatusCondition($query, $status)
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where(function ($q) use ($status) {

            for ($i = 1; $i <= 4; $i++) {

                $q->orWhere(function ($sub) use ($i, $status) {

                    $sub->where("photo{$i}_status", $status)
                        ->whereNotNull("photo{$i}")
                        ->where("photo{$i}", '!=', '');
                });
            }
        });
    }

    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = Register::query();
            // Base where
            if (!blank($whereStr)) {
                $query->whereRaw($whereStr);
            }
            // Photo status condition for this tab
            if (!blank($value['conditionVal'])) {
                $status = $value['conditionVal'];
                $query->where(function ($q) use ($status) {
                    for ($i = 1; $i <= 4; $i++) {
                        $q->orWhere(function ($sub) use ($i, $status) {
                            $sub->where("photo{$i}_status", $status)
                                ->whereNotNull("photo{$i}")
                                ->where("photo{$i}", '!=', '');
                        });
                    }
                });
            }
            $tabWiseCountData[$tabId] = $query->count();
        }
        return $tabWiseCountData;
    }

    public function onSearchKeyword($postData, $ignoreConditionVal = false)
    {
        $whereStr = '';
        if (
            isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' &&
            !empty($this->searchColumn)
        ) {
            $searchKeyword = $postData['searchKeyword'];
            $searchParts = [];
            foreach ($this->searchColumn as $column) {
                $searchParts[] = "$column LIKE '%$searchKeyword%'";
            }

            if (!empty($searchParts)) {
                $whereStr = '(' . implode(' OR ', $searchParts) . ')';
            }
        }

        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

        if ($addBtnPermission === 'Own Members' && $userId) {
            $staffCondition = "staff_assign_id = $userId";
            $whereStr = blank($whereStr) ? $staffCondition : "$whereStr AND $staffCondition";
        }

        if ($userType === 'Franchise' && $userId) {
            $franchiseCondition = "franchise_assign_id = $userId";
            $whereStr = blank($whereStr) ? $franchiseCondition : "$whereStr AND $franchiseCondition";
        }

        if (!$ignoreConditionVal && !blank($postData['conditionVal'])) {
            $statusCondition = "((photo1_status = '{$postData['conditionVal']}' AND photo1 != '') OR (photo2_status = '{$postData['conditionVal']}' AND photo2 != '') OR (photo3_status = '{$postData['conditionVal']}' AND photo3 != '') OR (photo4_status = '{$postData['conditionVal']}' AND photo4 != ''))";
            $whereStr = blank($whereStr) ? $statusCondition : "$whereStr AND $statusCondition";
        }
        return $whereStr;
    }

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (!empty($postData)) {
            if (isset($postData['type']) && $postData['type'] == 'photo') {
                $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
                ## Send Notification On Photo Approvals:
                $photoNumber = collect([1, 2, 3, 4])->first(fn($number) => ($postData["photo{$number}_status"] ?? null) === 'APPROVED');
                if ($photoNumber) {
                    $photoLabelKey = "Photo {$photoNumber}";
                    ## Send Featured Member Notification:
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $member = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        app(NotificationService::class)->sendNotification(
                            $member,
                            $member,
                            'photos_approval',
                            ['photo_number' => $photoLabelKey]
                        );
                        ## Send SMS Message:
                        app(SmsSendService::class)->sendTemplate('Photo Approved', $member, []);
                    }
                }
            } elseif (isset($postData['type']) && $postData['type'] == 'deletePhoto') {
                $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
                $photoNumber = $postData['photo_num'];
                $whereArr = ['id' => $postData["id"]];
                $memberData = Register::where($whereArr)->select(["$photoNumber"]);
                if (!blank($memberData->$photoNumber)) {
                    $updateData[$photoNumber . '_status'] = 'UNAPPROVED';
                    Storage::disk('public')->delete(_getConstant('upload_path.MEMBER_PHOTOS_URL') . $memberData->$photoNumber);
                }
            } else {
                if (isset($postData['is_deleted']) && $postData['is_deleted'] == 'Yes') {
                    $updateData['photo1'] = '';
                    $updateData['photo2'] = '';
                    $updateData['photo3'] = '';
                    $updateData['photo4'] = '';
                    $updateData['photo1_status'] = 'UNAPPROVED';
                    $updateData['photo2_status'] = 'UNAPPROVED';
                    $updateData['photo3_status'] = 'UNAPPROVED';
                    $updateData['photo4_status'] = 'UNAPPROVED';
                } else {
                    $updateData['photo1_status'] = $postData['photo_status'];
                    $updateData['photo2_status'] = $postData['photo_status'];
                    $updateData['photo3_status'] = $postData['photo_status'];
                    $updateData['photo4_status'] = $postData['photo_status'];
                }
            }
            $ids = $postData['id'] ?? [];
            // Convert to array safely
            if (!is_array($ids)) {
                $ids = explode(',', $ids);   // handles "12" and "12,13"
            }
            $ids = array_filter($ids);
            Register::whereIn('id', $ids)->update($updateData);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }
}

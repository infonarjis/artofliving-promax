<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Exports\LeadImportSampleCSVExport;
use App\Http\Controllers\Controller;
use App\Imports\LeadImport;
use App\Models\AssignHistory;
use App\Models\CommentsOfLeadGeneration;
use App\Models\Franchise;
use App\Models\LeadGeneration;
use App\Models\MaritalStatusMaster;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\Staff;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use App\Services\AdminCommonActionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class LeadGenerationController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private AdminCommonActionModel $adminCommonActionModel;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderService,
        AdminCommonActionModel $adminCommonActionModel
    ) {
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->adminCommonActionModel  = $adminCommonActionModel;

        $this->directoryName = '/leadGeneration';
        $this->searchColumn = ['username', 'email', 'gender', 'marital_status', 'phone_no_1', 'phone_no_2', 'phone_no_3', 'interest', 'country'];
        $this->pageName = 'Manage Manage Lead Generation';
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
            'incomingTab' => [
                'label' => 'Incoming Call',
                'id' => 'incomingData',
                'class' => '',
                'conditionVal' => 'Incoming Call',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'approvedFinalTab' => [
                'label' => 'Approved Final',
                'id' => 'approvedFinalData',
                'class' => '',
                'conditionVal' => 'Approved Final',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'someResponseTab' => [
                'label' => 'Some Response',
                'id' => 'someResponseData',
                'class' => '',
                'conditionVal' => 'Some Response',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'neverTalkedTab' => [
                'label' => 'Never Talked',
                'id' => 'neverTalkedData',
                'class' => '',
                'conditionVal' => 'Never Talked',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'halfLevelTab' => [
                'label' => '50-50',
                'id' => 'halfLevelData',
                'class' => '',
                'conditionVal' => '50-50',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'noResponseTab' => [
                'label' => 'No Response',
                'id' => 'noResponseData',
                'class' => '',
                'conditionVal' => 'No Response',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'newRegisterTab' => [
                'label' => 'New Register',
                'id' => 'newRegisterData',
                'class' => '',
                'conditionVal' => 'New Register',
                'conditionColumn' => 'interest',
                'strWhere' => ''
            ],
            'convertedToMemberTab' => [
                'label' => 'Converted to Member',
                'id' => 'convertedToMemberData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'is_registered',
                'strWhere' => ''
            ],
        ];
    }

    ## List :
    public function index()
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $leadGeneratePermission = _checkPermission($userType, $roleId, 'view_lead_generation');
        if ($leadGeneratePermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $interestList = [
            'New Register' => 'New Register',
            'Some Response' => 'Some Response',
            'Never Talked' => 'Never Talked',
            '50-50' => '50-50',
            'No Response' => 'No Response',
            'Approved Final' => 'Approved Final',
            'Incoming Call' => 'Incoming Call'
        ];

        ## Button Permission Access :
        $addBtn = _checkPermission($userType, $roleId, 'add_lead_generation') != 'No' ? 1 : 0;
        $leadImportBtn = _checkPermission($userType, $roleId, 'lead_import') != 'No' ? 1 : 0;
        ## Delete Lead Import :
        $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_lead_generation');
        $deleteBtn = 1;
        if ($deleteBtnPermission == 'No' || ($deleteBtnPermission == 'Own Members' && $leadGeneratePermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.leadGeneration.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.leadGeneration.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'franchiseListArr' => Franchise::active()->get(),
            'interestList' => $interestList,
            'actionBtnArr' => [
                'add' => $addBtn,
                'delete' => $deleteBtn,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 1,
                'isSearch' => 1,
                'addComment' => 1,
                'viewComment' => 1,
                'filter' => 1,
                'downloadBtn' => 1,
                'importLead' => $leadImportBtn,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
                'changeInterest' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.leadGeneration.addForm',
                'edit' => 'admin.leadGeneration.editForm',
                'viewComment' => 'admin.leadGeneration.viewComment',
                'addComment' => 'admin.leadGeneration.addComment',
                'filter' => 'admin.leadGeneration.getFilter',
                'importLeadUrl' => 'admin.leadGeneration.importLead',
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
                'staffUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'franchiseAssignbtn' => 'admin.leadGeneration.assignMember',
                'franchiseUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'changeInterestbtn' => 'admin.leadGeneration.changeInterest'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.leadGeneration.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
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
            $page = 1;
            $limit = 10;
            if (isset($postData['limit']) && $postData['limit'] != '') {
                $limit = $postData['limit'];
            }
            if (isset($postData['page']) && $postData['page'] != '') {
                $page = $postData['page'];
            }
            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = ['is_registered' => 'No'];
            if (
                isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
                isset($postData['conditionVal']) && $postData['conditionVal'] != ''
            ) {
                $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
            }

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

            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                if (isset($postData['created_from']) && !blank($postData['created_from']) && isset($postData['created_to']) && !blank($postData['created_to'])) {
                    $fromDate = (string)$postData['created_from'];
                    $toDate = (string)$postData['created_to'];
                    $whereStrFilter = "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['interest']) && !blank($postData['interest'])) {
                    $interestType  = $postData['interest'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( interest = '$interestType')";
                }
                if (isset($postData['gender']) && !blank($postData['gender'])) {
                    $genderType  = $postData['gender'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    if ($genderType != 'All') {
                        $whereStrFilter .= "$checkAnd ( gender = '$genderType')";
                    }
                }
                if (isset($postData['country']) && !blank($postData['country'])) {
                    $countryType = (array) $postData['country'];
                    $countryType = array_map(function ($country) {
                        return "'" . addslashes($country) . "'";
                    }, $countryType);

                    $checkAnd = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }

                    $whereStrFilter .= "$checkAnd (country IN (" . implode(',', $countryType) . "))";
                }
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffAssignId  = $postData['staff_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( staff_assign_id = '$staffAssignId')";
                }
            }

            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_lead_generation');

            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
                $whereArrStr = "(staff_assign_id = $userId)";
            }

            if (blank($whereStr)) {
                $whereStr = "$whereArrStr";
            } elseif (!blank($whereArrStr)) {
                $whereStr .= "AND $whereArrStr";
            }

            ## Filter Add:
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }

            if (blank($whereStr)) {
                $whereStr = " lead_status = 'open'";
            } else {
                $whereStr .= " AND lead_status = 'open'";
            }

            ## Tab Wise Count Data :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            ## Result Count:
            $resultArr = LeadGeneration::with(['staff:id,username', 'franchise:id,username'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'Desc')
                ->paginate($limit, ['*'], 'page', $page);


            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.leadGeneration.editForm',
                    'viewComment' => 'admin.leadGeneration.viewComment',
                    'addComment' => 'admin.leadGeneration.addComment',
                    'convertMember' => 'admin.leadGeneration.convertMember',
                ],
                'actionBtnArr' => [],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## List :
    public function freshFollowUp()
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $leadGeneratePermission = _checkPermission($userType, $roleId, 'view_lead_generation');
        if ($leadGeneratePermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $interestList =
            array(
                'New Register' => 'New Register',
                'Some Response' => 'Some Response',
                'Never Talked' => 'Never Talked',
                '50-50' => '50-50',
                'No Response' => 'No Response',
                'Approved Final' => 'Approved Final',
                'Incoming Call' => 'Incoming Call'
            );

        ## Button Permission Access :
        $addBtn = _checkPermission($userType, $roleId, 'add_lead_generation') != 'No' ? 1 : 0;
        $leadImportBtn = _checkPermission($userType, $roleId, 'lead_import') != 'No' ? 1 : 0;
        ## Delete Lead Import :
        $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_lead_generation');
        $deleteBtn = 1;
        if ($deleteBtnPermission == 'No' || ($deleteBtnPermission == 'Own Members' && $leadGeneratePermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Fresh Follow Up',
            'ajaxPaginationRequestUrl' => 'admin.leadGeneration.getAjaxPaginationDataFresh',
            'changeStatusUrl' => 'admin.leadGeneration.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'franchiseListArr' => Franchise::active()->get(),
            'interestList' => $interestList,
            'actionBtnArr' => [
                'add' => $addBtn,
                'delete' => $deleteBtn,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 1,
                'isSearch' => 1,
                'addComment' => 1,
                'viewComment' => 1,
                'filter' => 1,
                'downloadBtn' => 1,
                'importLead' => $leadImportBtn,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
                'changeInterest' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.leadGeneration.addForm',
                'edit' => 'admin.leadGeneration.editForm',
                'viewComment' => 'admin.leadGeneration.viewComment',
                'addComment' => 'admin.leadGeneration.addComment',
                'filter' => 'admin.leadGeneration.getFilter',
                'importLeadUrl' => 'admin.leadGeneration.importLead',
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
                'staffUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'franchiseAssignbtn' => 'admin.leadGeneration.assignMember',
                'franchiseUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'changeInterestbtn' => 'admin.leadGeneration.changeInterest'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.leadGeneration.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationDataFresh(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $page = 1;
            $limit = 10;
            if (isset($postData['limit']) && $postData['limit'] != '') {
                $limit = $postData['limit'];
            }
            if (isset($postData['page']) && $postData['page'] != '') {
                $page = $postData['page'];
            }
            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = ['is_registered' => 'No', 'commented' => '0', 'lead_status' => 'open'];
            if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
                $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
            }

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

            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                if (
                    isset($postData['created_from']) && !blank($postData['created_from'])
                    && isset($postData['created_to']) && !blank($postData['created_to'])
                ) {
                    $fromDate = (string)$postData['created_from'];
                    $toDate = (string)$postData['created_to'];
                    $whereStrFilter = "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['interest']) && !blank($postData['interest'])) {
                    $interestType  = $postData['interest'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( interest = '$interestType')";
                }
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffAssignId  = $postData['staff_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( staff_assign_id = '$staffAssignId')";
                }
            }

            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_lead_generation');

            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
                $whereArrStr = "(staff_assign_id = $userId)";
            }

            if (blank($whereStr)) {
                $whereStr = "$whereArrStr";
            } elseif (!blank($whereArrStr)) {
                $whereStr .= "AND $whereArrStr";
            }

            ## Filter Add:
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }
            if (blank($whereStr)) {
                $whereStr = " commented = '0' AND lead_status = 'open'";
            } else {
                $whereStr .= " AND commented = '0' AND lead_status = 'open'";
            }
            ## Tab Wise Count Data :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            ## Result Count:
            $resultArr = LeadGeneration::with(['staff:id,username', 'franchise:id,username'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'Desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.leadGeneration.editForm',
                    'viewComment' => 'admin.leadGeneration.viewComment',
                    'addComment' => 'admin.leadGeneration.addComment',
                    'convertMember' => 'admin.leadGeneration.convertMember',
                ],
                'actionBtnArr' => [],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## List :
    public function repeatedFollowUp()
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $leadGeneratePermission = _checkPermission($userType, $roleId, 'view_lead_generation');
        if ($leadGeneratePermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $interestList =
            array(
                'New Register' => 'New Register',
                'Some Response' => 'Some Response',
                'Never Talked' => 'Never Talked',
                '50-50' => '50-50',
                'No Response' => 'No Response',
                'Approved Final' => 'Approved Final',
                'Incoming Call' => 'Incoming Call'
            );

        ## Button Permission Access :
        $addBtn = _checkPermission($userType, $roleId, 'add_lead_generation') != 'No' ? 1 : 0;
        $leadImportBtn = _checkPermission($userType, $roleId, 'lead_import') != 'No' ? 1 : 0;
        ## Delete Lead Import :
        $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_lead_generation');
        $deleteBtn = 1;
        if ($deleteBtnPermission == 'No' || ($deleteBtnPermission == 'Own Members' && $leadGeneratePermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Repeated Follow Up',
            'ajaxPaginationRequestUrl' => 'admin.leadGeneration.getAjaxPaginationDataRepeated',
            'changeStatusUrl' => 'admin.leadGeneration.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'franchiseListArr' => Franchise::active()->get(),
            'interestList' => $interestList,
            'actionBtnArr' => [
                'add' => $addBtn,
                'delete' => $deleteBtn,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 1,
                'isSearch' => 1,
                'addComment' => 1,
                'viewComment' => 1,
                'filter' => 1,
                'downloadBtn' => 1,
                'importLead' => $leadImportBtn,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
                'changeInterest' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.leadGeneration.addForm',
                'edit' => 'admin.leadGeneration.editForm',
                'viewComment' => 'admin.leadGeneration.viewComment',
                'addComment' => 'admin.leadGeneration.addComment',
                'filter' => 'admin.leadGeneration.getFilter',
                'importLeadUrl' => 'admin.leadGeneration.importLead',
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
                'staffUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'franchiseAssignbtn' => 'admin.leadGeneration.assignMember',
                'franchiseUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'changeInterestbtn' => 'admin.leadGeneration.changeInterest'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.leadGeneration.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationDataRepeated(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $page = 1;
            $limit = 10;
            if (isset($postData['limit']) && $postData['limit'] != '') {
                $limit = $postData['limit'];
            }
            if (isset($postData['page']) && $postData['page'] != '') {
                $page = $postData['page'];
            }
            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = ['is_registered' => 'No', 'commented' => '1', 'lead_status' => 'open'];
            if (
                isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
                isset($postData['conditionVal']) && $postData['conditionVal'] != ''
            ) {
                $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
            }

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

            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                if (
                    isset($postData['created_from']) && !blank($postData['created_from'])
                    && isset($postData['created_to']) && !blank($postData['created_to'])
                ) {
                    $fromDate = (string)$postData['created_from'];
                    $toDate = (string)$postData['created_to'];
                    $whereStrFilter = "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['interest']) && !blank($postData['interest'])) {
                    $interestType  = $postData['interest'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( interest = '$interestType')";
                }
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffAssignId  = $postData['staff_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( staff_assign_id = '$staffAssignId')";
                }
            }

            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_lead_generation');

            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
                $whereArrStr = "(staff_assign_id = $userId)";
            }

            if (blank($whereStr)) {
                $whereStr = "$whereArrStr";
            } elseif (!blank($whereArrStr)) {
                $whereStr .= "AND $whereArrStr";
            }

            ## Filter Add:
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }
            if (blank($whereStr)) {
                $whereStr = " commented = '1' AND lead_status = 'open'";
            } else {
                $whereStr .= " AND commented = '1' AND lead_status = 'open'";
            }
            ## Tab Wise Count Data :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            ## Result Count:
            $resultArr = LeadGeneration::with(['staff:id,username', 'franchise:id,username'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'Desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.leadGeneration.editForm',
                    'viewComment' => 'admin.leadGeneration.viewComment',
                    'addComment' => 'admin.leadGeneration.addComment',
                    'convertMember' => 'admin.leadGeneration.convertMember',
                ],
                'actionBtnArr' => [],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## List :
    public function closedLeads()
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $leadGeneratePermission = _checkPermission($userType, $roleId, 'view_lead_generation');
        if ($leadGeneratePermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $interestList =
            array(
                'New Register' => 'New Register',
                'Some Response' => 'Some Response',
                'Never Talked' => 'Never Talked',
                '50-50' => '50-50',
                'No Response' => 'No Response',
                'Approved Final' => 'Approved Final',
                'Incoming Call' => 'Incoming Call'
            );

        ## Button Permission Access :
        $addBtn = _checkPermission($userType, $roleId, 'add_lead_generation') != 'No' ? 1 : 0;
        $leadImportBtn = _checkPermission($userType, $roleId, 'lead_import') != 'No' ? 1 : 0;
        ## Delete Lead Import :
        $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_lead_generation');
        $deleteBtn = 1;
        if ($deleteBtnPermission == 'No' || ($deleteBtnPermission == 'Own Members' && $leadGeneratePermission != 'Own Members')) {
            $deleteBtn = 0;
        }

        $dataArr = [
            'pageName' => 'Closed Leads',
            'ajaxPaginationRequestUrl' => 'admin.leadGeneration.getAjaxPaginationDataClosed',
            'changeStatusUrl' => 'admin.leadGeneration.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'franchiseListArr' => Franchise::active()->get(),
            'interestList' => $interestList,
            'actionBtnArr' => [
                'add' => $addBtn,
                'delete' => $deleteBtn,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 1,
                'isSearch' => 1,
                'addComment' => 1,
                'viewComment' => 1,
                'filter' => 1,
                'downloadBtn' => 1,
                'importLead' => $leadImportBtn,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
                'changeInterest' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.leadGeneration.addForm',
                'edit' => 'admin.leadGeneration.editForm',
                'viewComment' => 'admin.leadGeneration.viewComment',
                'addComment' => 'admin.leadGeneration.addComment',
                'filter' => 'admin.leadGeneration.getFilter',
                'importLeadUrl' => 'admin.leadGeneration.importLead',
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
                'staffUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'franchiseAssignbtn' => 'admin.leadGeneration.assignMember',
                'franchiseUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'changeInterestbtn' => 'admin.leadGeneration.changeInterest'
            ],
            'statusTabArr' => $this->statusTabArr,
            'downloadDropdownArr' => [
                'route' => route('admin.leadGeneration.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ]
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationDataClosed(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $page = 1;
            $limit = 10;
            if (isset($postData['limit']) && $postData['limit'] != '') {
                $limit = $postData['limit'];
            }
            if (isset($postData['page']) && $postData['page'] != '') {
                $page = $postData['page'];
            }
            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = ['is_registered' => 'No', 'lead_status' => 'close'];
            if (
                isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
                isset($postData['conditionVal']) && $postData['conditionVal'] != ''
            ) {
                $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
            }

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

            ## Filter Apply:
            $whereStrFilter = '';
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                if (
                    isset($postData['created_from']) && !blank($postData['created_from'])
                    && isset($postData['created_to']) && !blank($postData['created_to'])
                ) {
                    $fromDate = (string)$postData['created_from'];
                    $toDate = (string)$postData['created_to'];
                    $whereStrFilter = "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['interest']) && !blank($postData['interest'])) {
                    $interestType  = $postData['interest'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( interest = '$interestType')";
                }
                if (isset($postData['staff_id']) && !blank($postData['staff_id'])) {
                    $staffAssignId  = $postData['staff_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = ' AND';
                    }
                    $whereStrFilter .= "$checkAnd ( staff_assign_id = '$staffAssignId')";
                }
            }

            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_lead_generation');

            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
                $whereArrStr = "(staff_assign_id = $userId)";
            }

            if (blank($whereStr)) {
                $whereStr = "$whereArrStr";
            } elseif (!blank($whereArrStr)) {
                $whereStr .= "AND $whereArrStr";
            }

            ## Filter Add:
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }
            if (blank($whereStr)) {
                $whereStr = " lead_status = 'close'";
            } else {
                $whereStr .= " AND lead_status = 'close'";
            }
            ## Tab Wise Count Data :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            ## Result Count:
            $resultArr = LeadGeneration::with(['staff:id,username', 'franchise:id,username'])
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'Desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.leadGeneration.editForm',
                    'viewComment' => 'admin.leadGeneration.viewComment',
                    'addComment' => 'admin.leadGeneration.addComment',
                    'convertMember' => 'admin.leadGeneration.convertMember',
                ],
                'actionBtnArr' => [],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = ['is_registered' => 'No'];
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = LeadGeneration::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
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
            LeadGeneration::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            LeadGeneration::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        ## Auth User
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $mode = ($id != '') ? 'edit' : 'add';
        if ($mode == 'edit') {
            ## Check Edit Permission
            $editLeadGeneratePermission = _checkPermission($userType, $roleId, 'edit_lead_generation');
            ## No Permission
            if ($editLeadGeneratePermission == 'No') {
                return redirect()->route('admin.leadGeneration.index');
            }
            ## Member Data
            $query = LeadGeneration::where('id', $id);
            ## Own Members Permission
            if ($editLeadGeneratePermission == 'Own Members' && $userType == 'Staff') {
                $query->where('staff_assign_id', $authUser->id);
            }
            ## Get Member
            $rowMemberData = $query->first();
            ## Member Not Found / Not Accessible :
            if (blank($rowMemberData)) {
                return redirect()->route('admin.leadGeneration.index')->with('error', 'Lead not found or you do not have permission to edit this lead.');
            }
        } else {
            ## Check Edit Permission
            $addLeadGeneratePermission = _checkPermission($userType, $roleId, 'add_lead_generation');
            ## No Permission
            if ($addLeadGeneratePermission == 'No') {
                return redirect()->route('admin.leadGeneration.index');
            }
        }

        $elementArr = array(
            'gender' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('Male' => 'Male', 'Female' => 'Female'),
                'value' => 'Male'
            ),
            'username' => array(
                'is_required' => 'required',
                'label' => 'Full Name',
                'class' => 'required',
                'column' => '6',
            ),
            'email' => array(
                'input_type' => 'email',
                'class' => '',
                'column' => '6',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes'
            ),
            'phone_no_1' => array('is_required' => 'required', 'type' => 'mobile', 'label' => 'Mobile Number', 'modeType' => $mode, 'isDisableInDemo' => 'Yes', 'column' => '6', 'class' => 'required', 'maxLength' => '15', 'type_num_alph' => 'tel'),
            'phone_no_2' => array('label' => 'Mobile Number 2', 'type' => 'mobile', 'column' => '6', 'modeType' => $mode, 'isDisableInDemo' => 'Yes', 'maxLength' => '15', 'type_num_alph' => 'tel'),
            'phone_no_3' => array('label' => 'Mobile Number 3', 'type' => 'mobile', 'column' => '6', 'modeType' => $mode, 'isDisableInDemo' => 'Yes', 'maxLength' => '15', 'type_num_alph' => 'tel'),
            'marital_status' => array(
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'is_register' => 'yes',
                'class' => 'single select2',
                'relation' => array(
                    'rel_model' => 'MaritalStatusMaster',
                    'key_val' => 'marital_status_name',
                    'key_disp' => 'marital_status_name'
                ),
            ),
            'country' => array(
                'type' => 'dropdown',
                'class' => ' select2 ',
                'column' => '6',
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'country_name',
                    'key_disp' => 'country_name'
                )
            ),
            'interest' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'class' => 'required select2 ',
                'column' => '6',
                'value_arr' => array(
                    'New Register' => 'New Register',
                    'Some Response' => 'Some Response',
                    'Never Talked' => 'Never Talked',
                    '50-50' => '50-50',
                    'No Response' => 'No Response',
                    'Approved Final' => 'Approved Final',
                    'Incoming Call' => 'Incoming Call'
                )
            ),
        );

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = LeadGeneration::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.leadGeneration.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.leadGeneration.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $fields = [
            'username',
            'email',
            'gender',
            'marital_status',
            'phone_no_1_country_code',
            'phone_no_1',
            'phone_no_2_country_code',
            'phone_no_2',
            'phone_no_3_country_code',
            'phone_no_3',
            'country',
            'interest',
        ];

        $data = _getRequestData($fields, $postData);
        ## Mobile Number :
        foreach (range(1, 3) as $i) {
            $countryCode = $postData["phone_no_{$i}_country_code"] ?? null;
            $phone       = $postData["phone_no_{$i}"] ?? null;
            if ($countryCode && $phone) {
                $data["phone_no_{$i}"] = $countryCode . '-' . $phone;
            }
            unset(
                $data["phone_no_{$i}_country_code"],
            );
        }
        DB::beginTransaction();
        try {
            // ================= EDIT =================
            if ($postData['mode'] === 'edit') {
                $lead = LeadGeneration::withoutGlobalScope('not_deleted')->find($postData['id']);

                if (!$lead) {
                    DB::rollBack();
                    return redirect()->route($postData['callbackUrl'])->with('error', 'Record not found.');
                }

                $lead->update($data);
            }
            // ================= ADD =================
            if ($postData['mode'] === 'add') {
                $auth = Auth::user();
                $userType = _adminUserType($auth->type);
                $data = array_merge($data, [
                    'adminrole_id' => null,
                    'staff_assign_id' => null,
                    'franchised_by' => null,
                    'franchise_assign_id' => null,
                    'staff_assign_date' => null,
                    'franchise_assign_date' => null,
                    'created_at' => now(),
                ]);
                if ($userType === 'Staff') {
                    $data['adminrole_id'] = $auth->id;
                    $data['staff_assign_id'] = $auth->id;
                    $data['staff_assign_date'] = now();
                }
                if ($userType === 'Franchise') {
                    $data['franchised_by'] = $auth->id;
                    $data['franchise_assign_id'] = $auth->id;
                    $data['franchise_assign_date'] = now();
                }
                LeadGeneration::create($data);
            }
            DB::commit();
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route($postData['callbackUrl'])->with('error', $e->getMessage());
        }
    }

    ## Admin Assign Staff && Franchise :
    public function assignMember(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];

        $postData = $request->all();

        if (!empty($postData)) {
            ## User Type :
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if (isset($postData['subAdminId'])) {
                $assignId = $postData['subAdminId'];
                $assignUserType = 'Staff';
            }
            if (isset($postData['subAdminFranchiseId'])) {
                $assignId = $postData['subAdminFranchiseId'];
                $assignUserType = 'Franchise';
            }

            $action = 'Assign';
            $configArr = _getSiteSetting();
            $selectedValue = explode(",", $postData["id"]);

            foreach ($selectedValue as $valueId) {
                $assignHistory = AssignHistory::where(['lead_generation_id' => $valueId, 'user_type' => $assignUserType])->get();

                ## Update Arr:
                $updateDataArr = array(
                    'assign_by' => $userType,
                    'assign_by_email' => $configArr['contact_email'],
                    'assign_to' => $assignId,
                    'user_type' => $assignUserType,
                    'lead_generation_id' => $valueId,
                    'assign_date' => _getCurrentDate(),
                    'action' => $action,
                );

                if (!blank($assignHistory)) {
                    $whereUpdateArr = ['lead_generation_id' => $valueId, 'user_type' => $assignUserType];
                    AssignHistory::where($whereUpdateArr)->update($updateDataArr);
                } else {
                    AssignHistory::updateOrCreate(
                        ['user_type' => $assignUserType],
                        $updateDataArr
                    );
                }

                if ($action == 'Assign') {
                    ## Update Action When Member Assign To Franchise Or Staff :
                    AssignHistory::where([
                        ['lead_generation_id', '=', $valueId],
                        ['user_type', '!=', $assignUserType],
                        ['action', '=', $action],
                    ])->update([
                        'action' => 'Unassigned'
                    ]);
                }

                if (isset($assignUserType) && $assignUserType == 'Staff') {
                    $updateData = array(
                        'adminrole_id' => $assignId,
                        'staff_assign_id' => $assignId,
                        'staff_assign_date' => _getCurrentDate(),
                        'franchise_assign_id' => 0,
                        'franchised_by' => 0,
                        'franchise_assign_date' => null,
                    );
                } elseif (isset($assignUserType) && $assignUserType == 'Franchise') {
                    $updateData = array(
                        'adminrole_id' => 0,
                        'staff_assign_id' => 0,
                        'staff_assign_date' => null,
                        'franchised_by' => $assignId,
                        'franchise_assign_id' => $assignId,
                        'franchise_assign_date' => _getCurrentDate(),
                    );
                }
                ## Update Record :
                $whereUpdate = ['id' => $valueId];
                LeadGeneration::where($whereUpdate)->update($updateData);

                ## Send Admin Notification:
                AdminCommonActionModel::sendAdminNotification($valueId, '', 'lead_assign', $assignUserType, $assignId);
            }

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## Admin Assign Staff :
    public function unAssignMember(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];

        $postData = $request->all();

        if (!empty($postData)) {
            ## User Type :
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if (isset($postData['subAdminId'])) {
                $assignId = $postData['subAdminId'];
                $assignUserType = 'Staff';
            }

            if (isset($postData['subAdminFranchiseId'])) {
                $assignId = $postData['subAdminFranchiseId'];
                $assignUserType = 'Franchise';
            }

            $action = 'Unassigned';
            $configArr = _getSiteSetting();
            $selectedValue = explode(",", $postData["id"]);

            foreach ($selectedValue as $valueId) {
                $assignHistory = AssignHistory::where(['lead_generation_id' => $valueId, 'user_type' => $assignUserType])->get();

                ## Update Arr:
                $updateDataArr = array(
                    'assign_by' => $userType,
                    'assign_by_email' => $configArr['contact_email'],
                    'assign_to' => $assignId,
                    'user_type' => $assignUserType,
                    'lead_generation_id' => $valueId,
                    'assign_date' => _getCurrentDate(),
                    'action' => $action,
                );

                if (!blank($assignHistory)) {
                    $whereUpdateArr = ['lead_generation_id' => $valueId, 'user_type' => $assignUserType];
                    AssignHistory::where($whereUpdateArr)->update($updateDataArr);
                } else {
                    ## Update Action When Member Assign To Franchise Or Staff :
                    AssignHistory::updateOrCreate(
                        ['user_type' => $assignUserType],
                        $updateDataArr
                    );
                }

                if (isset($assignUserType) && $assignUserType == 'Staff') {
                    $updateData = array(
                        'adminrole_id' => 0,
                        'staff_assign_id' => 0,
                        'staff_assign_date' => null
                    );
                } elseif (isset($assignUserType) && $assignUserType == 'Franchise') {
                    $updateData = array(
                        'franchise_assign_id' => 0,
                        'franchised_by' => 0,
                        'franchise_assign_date' => null,
                    );
                }
                ## Update Record :
                $whereUpdate = ['id' => $valueId];
                LeadGeneration::where($whereUpdate)->update($updateData);
            }
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## Change Interest :
    public function changeInterest(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
        ];

        $request->validate([
            'id' => 'required',
            'interestValue' => 'required|string',
        ]);

        $ids = $request->input('id');

        // Convert to array safely
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        // Sanitize IDs
        $ids = array_map('intval', array_filter($ids));

        if (empty($ids)) {
            $responseArr['msg'] = 'No valid IDs provided.';
            return response()->json($responseArr, 422);
        }

        $updateRecord = LeadGeneration::whereIn('id', $ids)
            ->update(['interest' => $request->interestValue]);

        if ($updateRecord > 0) {
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        } else {
            $responseArr['msg'] = _getConstant('responce_message.DATA_NOT_UPDATED');
        }

        return response()->json($responseArr, 200);
    }

    ## View Comment :
    public function viewComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            ## Comment Lead Data Arr List:
            $resultDataArr = CommentsOfLeadGeneration::with(['staff'])->where('lead_generation_id', $postData['id'])->latest()->get();

            ## Lead Data List:
            $leadData = LeadGeneration::where('id', $postData['id'])->first();

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/viewCommentPopup',
                compact('resultDataArr', 'leadData')
            );

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Add Comment :
    public function addComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            ## Lead Data List:
            $leadData = LeadGeneration::where('id', $postData['id'])->first();

            ## commentData :
            $commentById = 1;
            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $followUpStatus = 1;
            $commentData = [
                'commented_user_type' => $userType,
                'posted_by' => $userId,
                'comment_by_id' => $commentById,
                'follow_up_status' => $followUpStatus,
            ];

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addCommentPopup',
                compact('commentData', 'leadData')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Add Comment :
    public function saveComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            ## Button Permission Access :
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);

            ## Update Old Followp:
            CommentsOfLeadGeneration::where('lead_generation_id', $postData['member_id'])->update(['follow_up_status' => 0]);

            if($postData['lead_status'] != 'close'){
                $insertData = [
                    'lead_generation_id' => $postData['member_id'],
                    'posted_user_type' => $postData['commented_user_type'],
                    'posted_by' => $postData['posted_by'],
                    'comment' => $postData['comment'],
                    'created_at' => _getCurrentDate(),
                    'next_followup_date' => $postData['next_followup_date'],
                    'next_followup_time' => $postData['next_followup_time'] ?? null,
                    'follow_up_status'  => 1,
                ];
                CommentsOfLeadGeneration::create($insertData);
            }

            // if ($userType == 'Admin') {
                ## Update Arr :
                $updateData = [
                    'commented' => '1',
                    'followup_date' => $postData['next_followup_date'],
                    'followup_time' => $postData['next_followup_time'] ?? null,
                    'lead_status' => $postData['lead_status'],
                ];
                LeadGeneration::where('id', $postData['member_id'])->update($updateData);
            // }

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    public function getFilter(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        $currentDate = _getCurrentDate('Y-m-d');
        if (isset($postData) && !blank($postData)) {
            $elementArr = array(
                'created_from' => array(
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => "Date Range From"
                ),
                'created_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Date Range To"
                ),
                'interest' => array(
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'value_arr' => array(
                        'New Register' => 'New Register',
                        'Some Response' => 'Some Response',
                        'Never Talked' => 'Never Talked',
                        '50-50' => '50-50',
                        'No Response' => 'No Response',
                        'Approved Final' => 'Approved Final',
                        'Incoming Call' => 'Incoming Call'
                    ),
                ),
                'staff_id' => array(
                    'label' => 'Assigned to',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'Staff',
                        'key_val' => 'id',
                        'class' => '',
                        'key_disp' => 'username'
                    )
                ),
                'country' => array(
                    'class' => ' not_reset single',
                    'label' => 'Country',
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'CountryMaster',
                        'key_val' => 'country_name',
                        'key_disp' => 'country_name'
                    )
                ),
                'gender' => array(
                    'display_in' => '2',
                    'type' => 'radio',
                    'value_arr' => ['All' => 'All', 'Male' => 'Male', 'Female' => 'Female'],
                    'value' => 'All'
                ),
            );

            $otherData = [
                'rowData' => [],
            ];
            $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.leadGeneration.getAjaxPaginationData',
                'formId' => 'filterForm',
                'formName' => 'filterForm',
                'formSubmitBtnClass' => 'filterFormSubmitBtn',
                'formSubmitBtnId' => 'filterFormSubmitBtn',
                'fromHtml' => $fromHtml,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/filterPopup', $dataArr);
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function downloadReport(Request $request)
    {
        $postData = $request->all();

        $selectedField = [
            'username',
            'gender',
            'email',
            'country',
            'marital_status',
            'phone_no_1',
            'interest',
            'staff_assign_id',
            'created_at'
        ];
        $query = LeadGeneration::with(['staff:id,username', 'franchise:id,username']);
        if (!blank($postData['filedownloadDate'] ?? null)) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $from = Carbon::parse($fromDate)->startOfDay();
            $to   = Carbon::parse($toDate)->endOfDay();
            // Works for same day and range both, and uses index
            $query->whereBetween('created_at', [$from, $to]);
        }
        $resultDataArr = $query->orderByDesc('id')->get($selectedField);

        if (empty($resultDataArr)) {
            return redirect()->route('admin.leadGeneration.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }

        foreach ($resultDataArr as $key => $value) {
            ## Disable In Demo:
            if (_getConstant('DISABLE_DEMO') ==  'Enabled') {
                $resultDataArr[$key]->email = _getConstant('DISABLE_IN_DEMO_LABEL');
                $resultDataArr[$key]->phone_no_1 = _getConstant('DISABLE_IN_DEMO_LABEL');
            }
            $resultDataArr[$key]->staff_assign_id = $value->staff->username ?? '-' ?? null;
        }

        ## Heading :
        $heading = ['Full Name', 'Gender', 'Email', 'Country', 'Marital Status', 'Mobile Number', 'Interest', 'Assign to Staff', 'Registered Date'];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');
        $fileName = 'Lead Generation Report (' . $currentDate . ')';

        if ($postData['downloadFormat'] == 'PDF') {
            $dataArr = [
                'title' => 'Lead Generation Report',
                'resultDataArr' => $resultDataArr,
                'heading' => $heading,
            ];
            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, $dataArr)->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path()])->setPaper('A4', 'potrait');
            return $pdf->download($fileName . '.pdf');
        } elseif ($postData['downloadFormat'] == 'CSV') {
            return Excel::download(
                new CommonAdminExport($resultDataArr, $heading, ['created_at']),
                $fileName . '.xlsx'
            );
        }
    }

    public function importLead()
    {
        ## Check Edit Permission
        ## Auth User
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $importLeadGeneratePermission = _checkPermission($userType, $roleId, 'lead_import');
        ## No Permission
        if ($importLeadGeneratePermission == 'No') {
            return redirect()->route('admin.leadGeneration.index');
        }

        $dataArr = [
            'pageName' => 'Import Lead'
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/importLead', $dataArr);
    }

    public function importLeadData(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
            ]);

            $file = $request->file('file');

            $import = new LeadImport;

            Excel::import($import, $file);

            $message = "Imported: {$import->importedCount} record(s).";

            if ($import->skippedCount > 0) {
                $message .= " Skipped {$import->skippedCount} row(s).";

                if (!empty($import->skippedReasons)) {
                    $message .= " Reasons: "
                        . implode(' | ', array_unique($import->skippedReasons));
                }
            }

            return response()->json([
                'success' => $import->importedCount > 0,
                'message' => $message,
                'importedCount' => $import->importedCount,
                'skippedCount' => $import->skippedCount,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Please upload a valid Excel or CSV file.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {

            Log::error('Lead import failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lead import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    ## Download Sample CSV:
    public function downloadSampleCsv()
    {
        $file = Excel::raw(new LeadImportSampleCSVExport(), \Maatwebsite\Excel\Excel::CSV);
        return response($file, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Lead Sample File.csv"',
        ]);
    }

    ## Convert Member :
    public function convertMember($id = null)
    {
        if (!$id) {
            return redirect()->route('admin.leadGeneration.index')->with('error', _getConstant('responce_message.SOMETHING_WENT_WRONG'));
        }
        ## Lead Data List:
        $leadData = LeadGeneration::where('id', $id)->first();
        if (!$leadData) {
            return redirect()->route('admin.leadGeneration.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }
        ## Check Duplicate Email and Mobile Number:
        $existingMember = Register::where('email', $leadData->email)->orWhere('mobile', $leadData->phone_no_1)->first();
        if ($existingMember) {
            return redirect()->route('admin.leadGeneration.index')->with('error', 'A member with the same email or mobile number already exists.');
        }

        $maritalStatusId = MaritalStatusMaster::where(
            'marital_status_name',
            $leadData->marital_status
        )->value('id');

        $insertMemberArr = [
            'fullname' => $leadData->username,
            'email' => $leadData->email,
            'gender' => $leadData->gender,
            'marital_status' => $maritalStatusId,
            'mobile' => $leadData->phone_no_1,
            'min_match_percentage' => 0,
            'is_converted_from_lead' => 'Yes',
        ];
        $registeredMember = Register::create($insertMemberArr);
        ## Add Partner Preference:
        RegisterPartner::updateOrCreate(
            ['member_id' => $registeredMember->id],
            ['member_id' => $registeredMember->id]
        );

        ## Generate Matri Id After Convert Member:
        $matriId = Register::updateMatriId($registeredMember->id);
        ## Update Lead Data After Convert Member:
        LeadGeneration::where('id', $id)->update(['is_registered' => 'Yes', 'member_matri_id' => $matriId]);

        return redirect()->route('admin.leadGeneration.index')->with('success', 'Lead converted to member successfully.');
    }

    ## Staff Wise Lead Report :
    public function staffWiseReport()
    {
        $staffList = Staff::get();
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        if ($userType == 'Staff') {
            $staffList = Staff::where('id', $authUser->id)->get();
        }
        foreach ($staffList as $key => $staff) {
            $staffList[$key]->today_followup = LeadGeneration::where('staff_assign_id', $staff->id)->whereDate('followup_date', _getCurrentDate('Y-m-d'))->count();
            $staffList[$key]->total_lead = LeadGeneration::where('staff_assign_id', $staff->id)->count();
            $staffList[$key]->fresh_followup = LeadGeneration::where('staff_assign_id', $staff->id)->where('commented', '0')->count();
            $staffList[$key]->repeat_followup = LeadGeneration::where('staff_assign_id', $staff->id)->where('commented', '1')->count();
            $staffList[$key]->open_followup = LeadGeneration::where('staff_assign_id', $staff->id)->where('lead_status', 'open')->count();
            $staffList[$key]->close_followup = LeadGeneration::where('staff_assign_id', $staff->id)->where('lead_status', 'close')->count();
            $staffList[$key]->profile_image = _assetUrl('upload_path.STAFF_IMAGE_URL') . '/' . $staff->profile_image;
        }
        $leadData['today_followup'] = LeadGeneration::whereDate('followup_date', _getCurrentDate('Y-m-d'))->count();
        $leadData['total_lead'] = LeadGeneration::count();
        $leadData['fresh_followup'] = LeadGeneration::where('commented', '0')->count();
        $leadData['repeat_followup'] = LeadGeneration::where('commented', '1')->count();
        $leadData['open_followup'] = LeadGeneration::where('lead_status', 'open')->count();
        $leadData['close_followup'] = LeadGeneration::where('lead_status', 'close')->count();
        $dataArr = [
            'pageName' => 'Staff Wise Lead Report',
            'staffList' => $staffList,
            'leadData' => $leadData,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/staffWiseReport', $dataArr);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonAdminExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
## Services
use App\Services\AdminFormBuilderService;
use App\Services\AdminCommonActionModel;
use App\Helpers\UploadHelper;
use App\Models\AddOnPackage;
use App\Models\AddOnPayment;
use App\Models\AdminAlert;
use App\Models\AffiliateMember;
use App\Models\AffiliateMemberAssign;
use App\Models\AffiliateMemberIncome;
use App\Models\AnnualIncomeMaster;
use App\Models\AssignHistory;
use App\Models\CasteMaster;
use App\Models\CommentMaster;
use App\Models\CountryMaster;
use App\Models\EducationMaster;
use App\Models\ExpressInterest;
use App\Models\Franchise;
use App\Models\FranchiseActivity;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MemberDeleteProfile;
use App\Models\MembershipPlan;
use App\Models\MotherTongueMaster;
use App\Models\OccupationMaster;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\ReligionMaster;
use App\Models\Staff;
use App\Models\StaffActivity;
use App\Models\StateMaster;
use App\Models\ViewContactDetail;
use App\Services\Admin\AdminResponseServices;
use App\Services\Admin\MatchMakingCountService;
use App\Services\AiGenerateService;
use App\Services\Api\ApiCommonActionModel;
use App\Services\UpgradeMembershipPlanService;
use App\Services\EmailSendService;
use App\Services\NotificationService;
use App\Services\ProfileCompletionService;
use App\Services\SmsSendService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class MemberController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private AdminCommonActionModel $adminCommonActionModel;
    private $directoryName;
    private $searchColumn;
    private $customJsDirectory;
    private $pageName;
    private $statusTabArr;
    public function __construct(
        AdminFormBuilderService $adminFormBuilderService,
        AdminCommonActionModel $adminCommonActionModel
    ) {
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->adminCommonActionModel  = $adminCommonActionModel;

        $this->directoryName = '/member';
        $this->searchColumn = ['fullname', 'email', 'matri_id', 'gender', 'mobile'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Manage Member';
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
            ],
            'suspendedTab' => [
                'label' => 'Suspended list',
                'id' => 'suspendedData',
                'class' => '',
                'conditionVal' => 'Suspended',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'paidTab' => [
                'label' => 'Paid list',
                'id' => 'paidData',
                'class' => '',
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => [
                    'plan_status' => 'Paid',
                    'status' => 'APPROVED'
                ]
            ],
            'expiredMemberTab' => [
                'label' => 'Expired list',
                'id' => 'expiredMemberData',
                'class' => '',
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => [
                    'plan_status' => 'Expired',
                    'status' => 'APPROVED'
                ]
            ],
            'featuredTab' => [
                'label' => 'Featured list',
                'id' => 'featuredData',
                'class' => '',
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => [
                    'fstatus' => 'Featured',
                    'status' => 'APPROVED',
                    'plan_status' => 'Paid'
                ]
            ],
        ];
    }

    ## List :
    public function index(Request $request)
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $memberViewPermission = _checkPermission($userType, $roleId, 'view_member');
        if ($memberViewPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('member_register', AdminAlert::STATUS_READ);

        ## Dashboard Data Filter:
        $setDashboardData = $request->session()->get('setDashboardData');
        if (!blank($setDashboardData) && $setDashboardData == 'Yes') {
            foreach (['setDashboardData', 'dashboardKey', 'dashboardValue'] as $key) {
                session()->forget($key);
            }
        } else {
            $dashboardKey = $request->session()->get('dashboardKey');
            $dashboardValue = $request->session()->get('dashboardValue');
            $tabMap = [
                'plan_status:Paid' => 'paidTab',
                'plan_status:Expired' => 'expiredMemberTab',
                'status:APPROVED' => 'approveTab',
                'status:UNAPPROVED' => 'unapproveTab',
                'status:Suspended' => 'suspendedTab',
            ];
            foreach ($this->statusTabArr as $key => $value) {
                $tabKey = $tabMap["{$dashboardKey}:{$dashboardValue}"] ?? null;
                if ($tabKey && isset($this->statusTabArr[$tabKey])) {
                    unset($this->statusTabArr[$key]['isActive']);
                    $this->statusTabArr[$tabKey]['isActive'] = 1;
                }
            }
        }

        ## Extra Js :
        $extraJsArrIndex = [
            $this->customJsDirectory . $this->directoryName . '/list.js',
        ];

        ## Button Permission Access :
        if (isset($userType) && $userType == 'Admin') {
            $userType = 'Admin';
            $assign = 1;
        } elseif (isset($userType) && $userType == 'Franchise') {
            $userType = 'Admin';
            $assign = 0;
        } else {
            $assign = 0;
        }
        ## Staff Role Data:
        $staffRoleId = Auth::user()->role_id;
        $addBtnPermission = _checkPermission($userType, $staffRoleId, 'add_member');
        $addBtn = 0;
        if ($addBtnPermission != 'No') {
            $addBtn = 1;
        }
        ## Check Button Permission :
        $deleteBtnPermission = _checkPermission($userType, $staffRoleId, 'delete_member');
        $deleteBtn = 1;
        if ($deleteBtnPermission == 'No' || ($deleteBtnPermission == 'Own Members' && $memberViewPermission != 'Own Members')) {
            $deleteBtn = 0;
        }
        $approveBtnPermission = _checkPermission($userType, $staffRoleId, 'approve_member');
        $approveBtn = 1;
        if ($approveBtnPermission == 'No' || ($approveBtnPermission == 'Own Members' && $memberViewPermission != 'Own Members')) {
            $approveBtn = 0;
        }
        $unApproveBtnPermission = _checkPermission($userType, $staffRoleId, 'unapprove_member');
        $unApproveBtn = 1;
        if ($unApproveBtnPermission == 'No' || ($unApproveBtnPermission == 'Own Members' && $memberViewPermission != 'Own Members')) {
            $unApproveBtn = 0;
        }
        $suspendBtnPermission = _checkPermission($userType, $staffRoleId, 'suspend_member');
        $suspendBtn = 1;
        if ($suspendBtnPermission == 'No' || ($suspendBtnPermission == 'Own Members' && $memberViewPermission != 'Own Members')) {
            $suspendBtn = 0;
        }
        $downloadBtn = 0;
        if (isset($userType) && $userType == 'Admin') {
            $downloadBtn = 1;
        }

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.member.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.member.changeStatus',
            'extraJsArr' => $extraJsArrIndex,
            'staffListArr' => Staff::active()->get(),
            'franchiseListArr' => Franchise::active()->get(),
            'actionBtnArr' => [
                'add' => $addBtn,
                'delete' => $deleteBtn,
                'approve' => $approveBtn,
                'unapprove' => $unApproveBtn,
                'suspend' => $suspendBtn,
                'isSearch' => 1,
                'isAssign' => $assign,
                'verifyBtn' => 1,
                'fstatus' => 0,
                'filter' => 1,
                'downloadBtn' => $downloadBtn,
                'downloadBiodatabtn' => 1
            ],
            'actionButtonUrl' => [
                'add' => 'admin.member.addForm',
                'editPlan' => 'admin.member.editPlan',
                'currentPlan' => 'admin.member.currentPlan',
                'viewComment' => 'admin.member.viewComment',
                'addComment' => 'admin.member.addComment',
                'downloadBiodata' => 'admin.member.downloadBiodataPdf'
            ],
            'downloadDropdownArr' => [
                'route' => route('admin.member.downloadReport'),
                'downloadType' => ['CSV', 'PDF']
            ],
            'statusTabArr' => $this->statusTabArr,
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
            $htmlDataArr = [];
            if (isset($postData['limit']) && $postData['limit'] != '') {
                $limit = $postData['limit'];
            }
            if (isset($postData['page']) && $postData['page'] != '') {
                $page = $postData['page'];
            }
            $orderBy = 'id';
            $orderByType = 'DESC';
            if (isset($postData['order']) && $postData['order'] != '') {
                $orderList = explode('-', $postData['order']);
                $orderBy = $orderList[0];
                $orderByType = $orderList[1];
            }

            # Check Roles Permission:
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);

            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = [];
            $queryNotNullColumn = null;
            if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
                if ($postData['conditionVal'] === 'NOT_NULL') {
                    $queryNotNullColumn = $postData['conditionColumn'];
                } else {
                    $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
                }
            }
            $activeTabData = '';
            foreach ($this->statusTabArr as $tab) {
                if ($tab['id'] === $postData['activeTab']) {
                    $activeTabData = $tab;
                    break;
                }
            }
            if (isset($activeTabData['strWhere']) && !blank($activeTabData['strWhere'])) {
                $whereArr = array_merge($whereArr, $activeTabData['strWhere']);
            }

            ## Filter Apply:
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                $whereStr = _getAdminFilterWhereStr($postData);
            }

            ## Staff Activities filter:
            $staffActivity = $request->session()->get('staff_activity_type');
            if (isset($staffActivity) && !blank($staffActivity)) {
                // Safe date quoting
                $start = !blank($staffActivity['start_date']) ? "'" . $staffActivity['start_date'] . "'" : null;
                $end   = !blank($staffActivity['end_date']) ? "'" . $staffActivity['end_date'] . "'" : null;

                if ($staffActivity['activity_type'] == 'assigned_member') {

                    $whereStr = "(staff_assign_id IS NOT NULL 
                      AND staff_assign_id != '' 
                      AND staff_assign_id != '0')";

                    if (!blank($staffActivity['staff_id'])) {
                        $whereStr .= " AND staff_assign_id = " . intval($staffActivity['staff_id']);
                    }

                    if ($start && $end) {
                        $whereStr .= " AND created_at BETWEEN $start AND $end";
                    }
                } elseif ($staffActivity['activity_type'] == 'unassigned_member') {

                    $whereStr = "(staff_assign_id IS NULL 
                      OR staff_assign_id = '' 
                      OR staff_assign_id = '0')";

                    if ($start && $end) {
                        $whereStr .= " AND created_at BETWEEN $start AND $end";
                    }
                } else {
                    $memberIds = StaffActivity::query()
                        ->where('activity_type', $staffActivity['activity_type'])
                        ->when(
                            !blank($staffActivity['staff_id'] ?? null),
                            fn($q) => $q->where('staff_id', $staffActivity['staff_id'])
                        )
                        ->when(
                            !blank($staffActivity['franchise_id'] ?? null),
                            fn($q) => $q->where('franchise_id', $staffActivity['franchise_id'])
                        )
                        ->when(
                            !blank($staffActivity['start_date'] ?? null) &&
                                !blank($staffActivity['end_date'] ?? null),
                            fn($q) => $q->whereBetween('created_at', [
                                $staffActivity['start_date'],
                                $staffActivity['end_date'],
                            ])
                        )
                        ->pluck('member_id')
                        ->toArray();

                    if (!empty($memberIds)) {
                        $ids = implode(',', array_map('intval', $memberIds));

                        $whereStr = blank($whereStr)
                            ? "id IN($ids)"
                            : $whereStr . " AND id IN($ids)";
                    }
                }
            }

            ## Franchise Activities Filter:
            $franchiseActivity = $request->session()->get('franchise_activity_type');
            if (isset($franchiseActivity) && !blank($franchiseActivity)) {
                // Helper for safe date quoting
                $start = !blank($franchiseActivity['start_date']) ? "'" . $franchiseActivity['start_date'] . "'" : null;
                $end   = !blank($franchiseActivity['end_date']) ? "'" . $franchiseActivity['end_date'] . "'" : null;
                if ($franchiseActivity['activity_type'] == 'assigned_member') {
                    $whereStr = "(franchise_assign_id IS NOT NULL 
                      AND franchise_assign_id != '' 
                      AND franchise_assign_id != '0')";
                    if (!blank($franchiseActivity['franchise_id'])) {
                        $whereStr .= " AND staff_assign_id = " . intval($franchiseActivity['franchise_id']);
                    }
                    if ($start && $end) {
                        $whereStr .= " AND created_at BETWEEN $start AND $end";
                    }
                } elseif ($franchiseActivity['activity_type'] == 'unassigned_member') {
                    $whereStr = "(franchise_assign_id IS NULL 
                      OR franchise_assign_id = '' 
                      OR franchise_assign_id = '0')";
                    if ($start && $end) {
                        $whereStr .= " AND created_at BETWEEN $start AND $end";
                    }
                } else {
                    $memberIds = FranchiseActivity::query()
                        ->where('activity_type', $franchiseActivity['activity_type'])
                        ->when(
                            !blank($franchiseActivity['franchise_id'] ?? null),
                            fn($q) => $q->where('franchise_id', $franchiseActivity['franchise_id'])
                        )
                        ->when(
                            $start && $end,
                            fn($q) =>
                            $q->whereBetween('created_at', [
                                $franchiseActivity['start_date'],
                                $franchiseActivity['end_date'],
                            ])
                        )
                        ->pluck('member_id')
                        ->toArray();

                    if (!empty($memberIds)) {
                        $ids = implode(',', array_map('intval', $memberIds));

                        if (blank($whereStr)) {
                            $whereStr = "id IN($ids)";
                        } else {
                            $whereStr .= " AND id IN($ids)";
                        }
                    }
                }
            }

            ## Search Keyword :
            if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {
                $searchKeyword = $postData['searchKeyword'];
                $searchParts = [];
                foreach ($this->searchColumn as $col) {
                    $searchParts[] = "$col like '%$searchKeyword%'";
                }
                $keywordStr = implode(' OR ', $searchParts);
                $whereStr .= blank($whereStr) ? "($keywordStr)" : " AND ($keywordStr)";
            }

            ## Button Permission Access :
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');
            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
                if (blank($whereArrStr)) {
                    $whereArrStr = "(staff_assign_id = $userId)";
                } else {
                    $whereArrStr .= " AND (staff_assign_id = $userId)";
                }
            }

            ## Franchise member Check :
            if ($userType == 'Franchise' && !blank($userId)) {
                if (blank($whereArrStr)) {
                    $whereArrStr = "(franchise_assign_id = $userId)";
                } else {
                    $whereArrStr .= " AND (franchise_assign_id = $userId)";
                }
            }

            if (!blank($whereArrStr)) {
                if (blank($whereStr)) {
                    $whereStr = $whereArrStr;
                } else {
                    $whereStr .= " AND " . $whereArrStr;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Dashboard Data Filter
            |--------------------------------------------------------------------------
            */
            $dashboardFilter = $this->getDashboardFilter($request);
            if (!empty($dashboardFilter)) {
                $whereArr = array_merge(
                    $whereArr,
                    $dashboardFilter
                );
                session([
                    'setDashboardData' => 'Yes'
                ]);
            }
            ## Tab Wise Count Data :
            $htmlDataArr = [];
            $htmlDataArr['tabCount'] = $this->tabWiseCountData(
                $request,
                $this->statusTabArr,
                $whereStr
            );

            ## Result Arr :
            $resultArr = Register::query()
                ->when($queryNotNullColumn, function ($q) use ($queryNotNullColumn) {
                    $q->whereNotNull($queryNotNullColumn);
                })
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy($orderBy, $orderByType)
                ->paginate($limit, ['*'], 'page', $page);



            $service = app(MatchMakingCountService::class);
            $resultArr->getCollection()->transform(function ($member) use ($service) {
                $member->matchMakingCount = $service->getMatchCount($member);
                return $member;
            });

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'add' => 'admin.member.addForm',
                    'edit' => 'admin.member.editForm',
                    'view' => 'admin.member.viewDetails',
                    'editPlan' => 'admin.member.editPlan',
                    'currentPlan' => 'admin.member.currentPlan',
                    'viewComment' => 'admin.member.viewComment',
                    'addComment' => 'admin.member.addComment',
                    'confirmationEmail' => 'admin.member.sendConfirmationEmail',
                    'downloadBiodata' => 'admin.member.downloadBiodataPdf',
                    'loginHistory' => 'admin.userLoginHistory.memberIndex'
                ],
                'actionBtnArr' => [
                    // 'match_making' => $matchMakingBtn,
                    // 'addComment' => $addCommentBtn,
                    // 'viewComment' => $viewCommentBtn,
                    'confirmEmail' => 1,
                    // 'view' => $viewBtn,
                    // 'edit' => $editBtn,
                    'downloadBiodatabtn' => 1,
                    // 'activeToPaidBtn' => $activeToPaidBtn
                ],
                'displayKeyArr' => [
                    'photo' => [
                        'photo1' => [
                            'type' => 'img',
                            'imageDirPath' => 'upload_path.MEMBER_PHOTOS_URL',
                            'noImage' => 'noImage.png',
                        ],
                    ],
                    'title' => [
                        'matri_id' => [
                            'label' => 'Matri Id',
                            'type' => 'str',
                        ],
                    ],
                    'field' => [
                        'left' => [
                            'fullname' => [
                                'label' => 'Full Name',
                                'type' => 'str',
                            ],
                            'email' => [
                                'label' => 'Email',
                                'type' => 'str',
                            ],
                            'gender' => [
                                'label' => 'Gender',
                                'type' => 'str',
                            ],
                            'birthdate' => [
                                'label' => 'Date of birth',
                                'type' => 'birthdate',
                            ],
                            'plan_name' => [
                                'label' => 'Plan Name',
                                'type' => 'str',
                            ],
                            'username' => ['label' => 'Assign to Staff', 'type' => 'master', 'relation' => 'staffData'],
                            'last_login' => ['label' => 'Last Login', 'type' => 'date']
                        ],
                        'center' => [
                            'user_type' => ['label' => 'User Type', 'type' => 'user_type'],
                            'mobile' => ['label' => 'Mobile Number', 'type' => 'str'],
                            'religion_name' => ['label' => 'Religion', 'type' => 'master', 'relation' => 'religionData'],
                            'marital_status_name' => ['label' => 'Marital Status', 'type' => 'master', 'relation' => 'maritalStatusData'],
                            'plan_expired_on' => ['label' => 'Plan Expired', 'type' => 'date'],
                            'username' => ['label' => 'Assign to Franchise', 'type' => 'master', 'relation' => 'franchiseData'],
                            'created_at' => ['label' => 'Registered On', 'type' => 'date']
                        ]
                    ]
                ]
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr', 'dataArr'));
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Get Dashboard Session Filter :
    private function getDashboardFilter(Request $request): array
    {
        $dashboardKey = $request->session()->get('dashboardKey');
        $dashboardValue = $request->session()->get('dashboardValue');
        if (blank($dashboardKey) || blank($dashboardValue)) {
            return [];
        }
        return [
            $dashboardKey => $dashboardValue
        ];
    }

    ## Tab Wise Count :
    /**
     * Tab Wise Count
     */
    public function tabWiseCountData(Request $request, array $tabArr, string $whereStr = '')
    {
        $tabWiseCountData = [];
        ## Dashboard filter :
        $dashboardFilter = $this->getDashboardFilter($request);
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = Register::query();

            ## Tab Condition :
            $conditionColumn = $value['conditionColumn'] ?? null;
            $conditionValue  = $value['conditionVal'] ?? null;
            if (!blank($conditionColumn) && !blank($conditionValue)) {
                if ($conditionValue === 'NOT_NULL') {
                    $query->whereNotNull($conditionColumn);
                } else {
                    $query->where(
                        $conditionColumn,
                        $conditionValue
                    );
                }
            }
            ## Tab Additional Conditions :
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere) && !empty($strWhere)) {
                $query->where($strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $query->whereRaw($strWhere);
            }
            ## Dashboard Filter :
            if (!empty($dashboardFilter)) {
                $query->where($dashboardFilter);
            }
            ## Common Where String :
            if (!blank($whereStr)) {
                $query->whereRaw($whereStr);
            }
            $tabWiseCountData[$tabId] = $query->count();
        }
        return $tabWiseCountData;
    }

    ## Add Edit Form :
    public function addEditForm($id = '')
    {
        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js',
        ];
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);

        ## Birthdate :
        $currentData = _getCurrentDate('Y-m-d');
        $date = strtotime($currentData . ' -18 year');
        $maxBirthdate = date('Y-m-d', $date);
        $mode = ($id != '') ? 'edit' : 'add';
        $rowMemberData = [];
        $rowPartnerData = [];
        if ($mode == 'edit') {
            ## Staff Role Data :
            $editMemberPermission = _checkPermission($userType, $roleId, 'edit_member');
            ## Member Data:
            $query = Register::withTrashed()->where('id', $id);
            if ($editMemberPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
                $query->where('staff_assign_id', $userId);
            }
            $rowMemberData = $query->first();
            ## Member Data:
            if (empty($rowMemberData) || $rowMemberData->trashed()) {
                $pageName = 'Member Edit';
                $registerArr = $rowMemberData;

                return view(
                    _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/userNotFound',
                    compact('registerArr', 'pageName')
                );
            }
            ## Partner Data :
            $rowPartnerData = RegisterPartner::where('member_id', $id)->first();
        } else {
            ## Check Add Permission :
            $addMemberPermission = _checkPermission($userType, $roleId, 'add_member');
            if ($addMemberPermission == 'No') {
                return redirect()->route('admin.member.index');
            }
        }

        ## Step : 1
        ## Section 1
        $elementArrStep1 = array(
            // 2024-10-02
            'user_type' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['0' => 'Online', '1' => 'Personlize'],
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes'
            ),
            'gender' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['Male' => 'Male', 'Female' => 'Female'],
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
                'label' => 'Gender'
            ),
            'profileby' => array(
                'type' => 'dropdown',
                'class' => 'single required',
                'is_required' => 'required',
                'relation' => array(
                    'rel_model' => 'ProfileByMaster',
                    'key_val' => 'id',
                    'key_disp' => 'profileby_name'
                ),
                'label' => 'Profile By',
                'is_register' => 'yes'
            ),
            'fullname' => array(
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
                'label' => 'Full Name',
                'type_num_alph' => 'alpha'
            ),
            'email' => array(
                'is_required' => 'required',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'input_type' => 'email',
                'check_duplicate' => 'Yes',
                'label' => 'Email',
                'is_register' => 'yes',
                'class' => 'required',
            ),
            'email_verify_status' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['Verify' => 'Verify', 'Not-Verify' => 'Not-Verify'],
                'value' => 'Not-Verify',
                'is_register' => 'yes',
                'label' => 'Email Verify Status'
            ),
            'mobile' => array(
                'is_required' => 'required',
                'type' => 'mobile',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'class' => 'required',
                'label' => 'Mobile Number',
                'is_register' => 'yes',
                'maxLength' => '15',
                'type_num_alph' => 'tel'
            ),
            'mobile_verify_status' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['Yes' => 'Yes', 'No' => 'No'],
                'value' => 'No',
                'is_register' => 'yes'
            ),
            'password' => array(
                'is_required' => 'required',
                'type' => 'password',
                'is_register' => 'yes',
                'label' => 'Password',
                'class' => 'required',
                'mode' => $mode
            ),
            'mother_tongue' => array(
                'is_required' => 'required',
                'class' => 'single not_reset required',
                'label' => 'Mother Tongue',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'MotherTongueMaster',
                    'key_val' => 'id',
                    'class' => 'single required',
                    'key_disp' => 'mtongue_name'
                )
            ),
            'birthdate' => array(
                'is_required' => 'required',
                'placeholder' => 'Date of birth',
                'input_type' => 'date',
                'label' => 'Date of birth',
                'class' => 'required',
                'is_register' => 'yes',
                'other' => 'max="' . $maxBirthdate . '"'
            ),
            'marital_status' => array(
                'is_required' => 'required',
                'label' => 'Marital Status',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'MaritalStatusMaster',
                    'key_val' => 'id',
                    'key_disp' => 'marital_status_name'
                ),
                'class' => 'single required'
            ),
            'total_children' => array(
                'is_required' => 'required',
                'form_register_class' => 'total_children',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Total Children',
                'relation' => array(
                    'rel_model' => 'TotalChildMaster',
                    'key_val' => 'id',
                    'key_disp' => 'total_child_name'
                ),
                'class' => 'single'
            ),
            'status_children' => array(
                'is_required' => 'required',
                'form_register_class' => 'status_children',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Status Children',
                'relation' => array(
                    'rel_model' => 'StatusChildMaster',
                    'key_val' => 'id',
                    'key_disp' => 'status_child_name'
                ),
                'class' => 'single'
            ),
            'birthplace' => array(
                'class' => '',
                'label' => 'Birth Place',
                'is_register' => 'yes',
                'placeholder' => 'Enter Birth Place'
            ),
            'birthtime' => array(
                'label' => 'Birth Time',
                'input_type' => 'time',
                'is_register' => 'yes'
            ),
        );
        ## Affilate Marketing Module:
        if (_getConstant('PERSONALIZE_MODULE') !=  'Enabled') {
            unset($elementArrStep1['user_type']);
        }
        ## Remove Gender In Edit Mode:
        if ($mode == 'edit') {
            // unset($elementArrStep1['gender']);
        }
        ## Section 2
        $elementArrReligionStep1 = array(
            'religion' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Religion',
                'onchange' => "dropdownChange('religion','caste','caste_list')",
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'ReligionMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'religion_name'
                )
            ),
            'caste' => array(
                'is_required' => 'required',
                'label' => 'Caste',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'CasteMaster',
                    'key_val' => 'id',
                    'key_disp' => 'caste_name',
                    'rel_col_name' => 'religion_id',
                    'not_load_add' => 'yes',
                    'cus_rel_col_val' => 'religion'
                )
            ),
            'subcaste' => array('label' => 'Sub Caste', 'is_register' => 'yes', 'placeholder' => 'Enter Sub Caste'),
            'manglik' => array(
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'ManglikMaster',
                    'key_val' => 'id',
                    'key_disp' => 'manglik_name'
                ),
                'label' => 'Manglik',
                'is_register' => 'yes'
            ),
            'gothra' => array('is_register' => 'yes', 'label' => 'Gothra', 'placeholder' => 'Enter Gothra'),
            'moonsign' => array(
                'type' => 'dropdown',
                'class' => 'single',
                'is_register' => 'yes',
                'label' => 'Moonsign',
                'relation' => array(
                    'rel_model' => 'MoonsignMaster',
                    'key_val' => 'id',
                    'class' => 'single required',
                    'key_disp' => 'moonsign_name'
                )
            ),
            'star' => array(
                'type' => 'dropdown',
                'class' => 'single ',
                'is_register' => 'yes',
                'label' => 'Star',
                'relation' => array(
                    'rel_model' => 'StarMaster',
                    'key_val' => 'id',
                    'class' => 'single required',
                    'key_disp' => 'star_name'
                )
            ),
            'horoscope' => array(
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'HoroscopeMaster',
                    'key_val' => 'id',
                    'key_disp' => 'horoscope_name'
                ),
                'label' => 'Horoscope',
                'is_register' => 'yes'
            ),
        );
        ## Section 3
        $elementArrEducationStep1 = array(
            'education_level' => array(
                'is_required' => 'required',
                'label' => 'Education',
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'is_register' => 'yes',
                'class' => 'single required',
                'is_multiple' => 'yes',
                'relation' => array(
                    'rel_model' => 'EducationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'education_name'
                )
            ),
            'education_details' => array(
                'is_register' => 'yes',
                'label' => 'Enter Education Details'
            ),
            'employee_in' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'EmployeeMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'employee_name'
                ),
                'label' => 'Employee In'
            ),
            'occupation' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'OccupationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'occupation_name'
                ),
                'label' => 'Occupation'
            ),
            'income' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'AnnualIncomeMaster',
                    'key_val' => 'id',
                    'class' => 'single',
                    'key_disp' => 'annual_income_name'
                ),
                'label' => 'Annual Income',
                'is_register' => 'yes'
            ),
            'designation_level' => array(
                'is_required' => 'required',
                'label' => 'Designation',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'relation' => array(
                    'rel_model' => 'DesignationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'designation_name'
                )
            ),
        );
        ## Section 4
        $year = _yearFormat();
        $fromHtmlArtOfLivingStep1 = array(
            'Yesart_of_living_teacher' => array(
                'is_required' => 'required',
                'type' => 'radio', 'value_arr' => array('Yes' => 'Yes', 'No' => 'No'),
                'value' => 'No', 'label' => 'Art of Living Teacher',
                'form_group_class' => ' Yesart_of_living_teacher',
                'is_register' => 'yes', 'class' => 'required'
            ),
            'teacher_code' => array(
                'label' => 'Teacher Code',
                'form_group_class' => ' teacher_code',
                'other' => "pattern='[a-zA-Z0-9]+' minlength='4'",
                'is_register' => 'yes'
            ),
            'teaching_courses' => array(
                'form_register_class' => 'teaching_courses',
                'label' => 'I Teach', 'type' => 'dropdown', 'is_register' => 'yes',
                'is_multiple' => 'yes', 'display_placeholder' => 'No',
                'class' => 'single', 'relation' => array(
                    'rel_model' => 'CourseDetailMaster',
                    'key_val' => 'id', 'class' => 'required', 'key_disp' => 'course_name'
                ),
            ),
            'have_art_of_living_program' => array(
                'is_required' => 'required',
                'type' => 'radio', 'value_arr' => array('Yes' => 'Yes', 'No' => 'No'),
                'value' => 'No', 'label' => 'Art Of Living Program', 'is_register' => 'yes',
                'class' => 'required'
            ),
            'teacher_name' => array(
                'class' => '', 'is_register' => 'yes',
                'form_group_class' => ' teacher_name', 'label' => 'Reference Teacher'
            ),
            'teacher_mobile_no' => array(
                'type' => 'mobile', 'class' => '',
                'form_group_class' => ' teacher_mobile_no', 'label' => 'Teacher Mobile Number',
                'is_register' => 'yes'
            ),
            'art_of_living_program' => array(
                'form_group_class' => ' art_of_living_program',
                'label' => 'Course Completed', 'type' => 'dropdown', 'is_register' => 'yes',
                'is_multiple' => 'yes', 'display_placeholder' => 'No', 'class' => 'single',
                'relation' => array(
                    'rel_model' => 'CourseDetailMaster', 'key_val' => 'id',
                    'class' => 'required', 'key_disp' => 'course_name'
                ),
            ),
            'no_of_years_in_artofliving' => array(
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'class' => 'single', 'value_arr' => $year,
                'label' => 'Years with Art of Living', 'is_register' => 'yes'
            ),
        );
        ## Step : 2
        $elementArrStep2 = array(
            'country_id' => array(
                'is_required' => 'required',
                'class' => 'single not_reset required',
                'label' => 'Country',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'onchange' => "dropdownChange('country_id','state_id','state_list')",
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'country_name'
                ),
            ),
            'state_id' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'StateMaster',
                    'key_val' => 'id',
                    'key_disp' => 'state_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'country_id'
                ),
                'label' => 'State',
                'class' => 'single required',
                'onchange' => "dropdownChange('state_id','city','city_list')"
            ),
            'city' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'CityMaster',
                    'key_val' => 'id',
                    'key_disp' => 'city_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'state_id'
                ),
                'label' => 'City',
                'class' => 'single required'
            ),
            'residence_type' => array(
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'ResidenceMaster',
                    'key_val' => 'id',
                    'key_disp' => 'residence_name'
                ),
                'label' => 'Residence Type',
                'is_register' => 'yes'
            ),
            'alternate_number' => array(
                'type_num_alph' => 'tel',
                'input_type' => 'tel',
                'type' => 'mobile',
                'maxlength' => '20',
                'is_required' => 'required',
                'label' => 'Alternative Number',
                'is_register' => 'yes',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes'
            ),
            'nri_country' => array('label' => 'If NRI Originated Country', 'is_register' => 'yes', 'placeholder' => 'Enter If NRI Originated Country'),
            'address' => array(
                'type' => 'textarea',
                'class' => 'form-textarea',
                'is_register' => 'yes',
                'label' => 'Address',
                'maxlength' => '500',
                'placeholder' => 'Enter Address'
            ),
        );
        ## Step : 3
        ## Section 1
        $elementArrStep3 = array(
            'height' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'value_arr' => _heightList(),
                'label' => 'Height',
                'class' => 'single'
            ),
            'weight' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single',
                'value_arr' => _weightList(),
                'label' => 'Weight'
            ),
            'diet' => array(
                'label' => 'Eating Habits',
                'is_register' => 'yes',
                'class' => 'single ',
                'type' => 'dropdown',
                'relation' => array(
                    'rel_model' => 'EatingHabitMaster',
                    'key_val' => 'id',
                    'key_disp' => 'eating_habit_name'
                ),
            ),
            'smoke' => array(
                'type' => 'dropdown',
                'label' => 'Smoking Habit',
                'relation' => array(
                    'rel_model' => 'SmokingHabitMaster',
                    'key_val' => 'id',
                    'key_disp' => 'smoking_habit_name'
                ),
                'is_register' => 'yes',
                'class' => 'single'
            ),
            'drink' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Drinking Habit',
                'relation' => array(
                    'rel_model' => 'DrinkingHabitMaster',
                    'key_val' => 'id',
                    'key_disp' => 'drinking_habit_name'
                ),
                'class' => 'single'
            ),
            'body_type' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Body type',
                'relation' => array(
                    'rel_model' => 'BodyTypeMaster',
                    'key_val' => 'id',
                    'key_disp' => 'body_type_name'
                ),
                'class' => 'single'
            ),
            'complexion' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Complexion',
                'relation' => array(
                    'rel_model' => 'ComplexionMaster',
                    'key_val' => 'id',
                    'key_disp' => 'complexion_name'
                ),
                'class' => 'single'
            ),
            'blood_group_id' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Blood Group',
                'relation' => array(
                    'rel_model' => 'BloodGroupMaster',
                    'key_val' => 'id',
                    'key_disp' => 'blood_group_name'
                ),
                'class' => 'single'
            ),
        );
        ## Section 2 :
        $elementArrAboutStep3 = array(
            'about_me_description' => array(
                'type' => 'textarea',
                'class' => 'form-textarea',
                'label' => 'About Me',
                'is_register' => 'yes',
                'button_display' => _getConstant('AI_MODE') == 'Enabled'
                    ? '<button type="button" id="generateAboutMeBtn" class="generate-about-me-btn">Generate With AI</button>'
                    : '',
                'maxlength' => '500',
                'placeholder' => 'Enter About Me',
                'display_note' => _getConstant('AI_MODE') == 'Enabled'
                    ? 'Note: The AI will create your “About Me” using your full name, birth date, education, occupation, employer, and designation details.'
                    : ''
            ),
        );
        ## Step : 4
        $elementArrStep4 = array(
            'family_type' => array(
                'is_register' => 'yes',
                'type' => 'dropdown',
                'class' => 'single',
                'label' => 'Family Type',
                'relation' => array(
                    'rel_model' => 'FamilyTypeMaster',
                    'key_val' => 'id',
                    'key_disp' => 'family_type_name'
                ),
            ),
            'family_status' => array(
                'is_register' => 'yes',
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'FamilyStatusMaster',
                    'key_val' => 'id',
                    'key_disp' => 'family_status_name'
                ),
                'label' => 'Family Status'
            ),
            'father_name' => array('is_required' => 'required', 'is_register' => 'yes', 'label' => 'Father Name', 'placeholder', 'Enter Father Name'),
            'father_occupation' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'label' => 'Father Occupation',
                'relation' => array(
                    'rel_model' => 'OccupationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'occupation_name'
                ),
            ),
            'mother_name' => array(
                'is_required' => 'required',
                'is_register' => 'yes',
                'label' => 'Mother Name',
                'placeholder',
                'Enter Mother Name'
            ),
            'mother_occupation' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'class' => 'single required',
                'label' => 'Mother Occupation',
                'relation' => array(
                    'rel_model' => 'OccupationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'occupation_name'
                ),
            ),
            'no_of_brother' => array(
                'is_register' => 'yes',
                'label' => 'No Of Brothers',
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'NoOfBroSisMaster',
                    'key_val' => 'id',
                    'key_disp' => 'no_of_bro_sis_name'
                ),
            ),
            'no_of_married_brother' => array(
                'is_register' => 'yes',
                'label' => 'No Of Married Brothers',
                'type' => 'dropdown',
                'class' => 'single',
                'relation' => array(
                    'rel_model' => 'MarriedBroMaster',
                    'key_val' => 'id',
                    'key_disp' => 'married_bro_name'
                ),
            ),
            'no_of_sister' => array(
                'is_register' => 'yes',
                'type' => 'dropdown',
                'class' => 'single',
                'label' => 'No Of Sisters',
                'relation' => array(
                    'rel_model' => 'NoOfBroSisMaster',
                    'key_val' => 'id',
                    'key_disp' => 'no_of_bro_sis_name'
                ),
            ),
            'no_of_married_sister' => array(
                'is_register' => 'yes',
                'type' => 'dropdown',
                'class' => 'single',
                'label' => 'No Of Married Sisters',
                'relation' => array(
                    'rel_model' => 'MarriedSisMaster',
                    'key_val' => 'id',
                    'key_disp' => 'married_sis_name'
                ),
            ),
            'family_details' => array(
                'type' => 'textarea',
                'class' => 'form-textarea',
                'is_register' => 'yes',
                'label' => 'Family Details',
                'maxlength' => '500',
                'placeholder',
                'Enter Family Details'
            ),
        );
        $elementArrStep5 = array(
            'part_frm_age' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'value_arr' => _ageRang(),
                'label' => "Partner From Age",
                'class' => 'single'
            ),
            'part_to_age' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'value_arr' => _ageRang(),
                'label' => "Partner To Age",
                'class' => 'single'
            ),
            'part_height' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'value_arr' => _heightList(),
                'label' => "Partner From Height",
                'class' => 'single'
            ),
            'part_height_to' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'value_arr' => _heightList(),
                'label' => "Partner To Height",
                'class' => 'single'
            ),
            'part_religion' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Partner Religion',
                'onchange' => "dropdownChange('part_religion','part_caste','caste_list')",
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'class' => 'single disbaledValue',
                'relation' => array(
                    'rel_model' => 'ReligionMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'religion_name'
                )
            ),
            'part_caste' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Partner Caste',
                'relation' => array(
                    'rel_model' => 'CasteMaster',
                    'key_val' => 'id',
                    'key_disp' => 'caste_name',
                    'not_load_add' => 'yes',
                    'rel_col_name' => 'religion_id',
                    'cus_rel_col_val' => 'part_religion'
                ),
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'class' => 'single disbaledValue'
            ),
            'part_country' => array(
                'class' => ' not_reset single disbaledValue',
                'label' => 'Partner Country',
                'type' => 'dropdown',
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'is_register' => 'yes',
                'onchange' => "dropdownChange('part_country','part_state','state_list')",
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'id',
                    'key_disp' => 'country_name'
                )
            ),
            'part_state' => array(
                'type' => 'dropdown',
                'label' => 'Partner State',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'StateMaster',
                    'key_val' => 'id',
                    'key_disp' => 'state_name',
                    'not_load_add' => 'yes',
                    'rel_col_name' => 'country_id',
                    'cus_rel_col_val' => 'part_country'
                ),
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'class' => 'single disbaledValue'
            ),
            'part_marital_status' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Partner Marital status',
                'relation' => array(
                    'rel_model' => 'MaritalStatusMaster',
                    'key_val' => 'id',
                    'key_disp' => 'marital_status_name'
                ),
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'class' => 'single disbaledValue'
            ),
            'part_income' => array(
                'type' => 'dropdown',
                'label' => 'Partner Annual Income',
                'display_placeholder' => 'No',
                'is_register' => 'yes',
                'is_multiple' => 'yes',
                'relation' => array(
                    'rel_model' => 'AnnualIncomeMaster',
                    'key_val' => 'id',
                    'class' => 'single',
                    'key_disp' => 'annual_income_name'
                ),
                'class' => 'single disbaledValue'
            ),
            'part_education' => array(
                'label' => 'Partner Education',
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'is_register' => 'yes',
                'class' => 'single disbaledValue',
                'is_multiple' => 'yes',
                'relation' => array(
                    'rel_model' => 'EducationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'education_name'
                )
            ),
            'part_occupation' => array(
                'type' => 'dropdown',
                'is_multiple' => 'yes',
                'is_register' => 'yes',
                'class' => 'single disbaledValue',
                'display_placeholder' => 'No',
                'relation' => array(
                    'rel_model' => 'OccupationMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'occupation_name'
                ),
                'label' => 'Partner Occupation'
            ),
            'part_mothertongue' => array(
                'display_placeholder' => 'No',
                'is_multiple' => 'yes',
                'class' => 'single not_reset',
                'label' => 'Partner Mother Tongue',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'MotherTongueMaster',
                    'key_val' => 'id',
                    'class' => 'single disbaledValue',
                    'key_disp' => 'mtongue_name'
                )
            ),
            'part_manglik' => array(
                'type' => 'dropdown',
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'class' => 'single disbaledValue',
                'relation' => array(
                    'rel_model' => 'ManglikMaster',
                    'key_val' => 'id',
                    'key_disp' => 'manglik_name'
                ),
                'label' => 'Partner Manglik',
                'is_register' => 'yes'
            ),
            'part_art_of_living_teacher' => array(
                'type' => 'dropdown', 'is_multiple' => 'yes',
                'display_placeholder' => 'No', 'is_register' => 'yes',
                'class' => 'single disbaledValue',
                'value_arr' => array('Does Not Matter' => 'Does Not Matter', 'Yes' => 'Yes', 'No' => 'No'),
                'label' => 'Partner Art Of Living Teacher'
            ),
            'part_have_art_of_living_program' => array(
                'type' => 'dropdown', 'is_multiple' => 'yes',
                'display_placeholder' => 'No', 'is_register' => 'yes',
                'class' => 'single disbaledValue',
                'value_arr' => array('Does Not Matter' => 'Does Not Matter', 'Yes' => 'Yes', 'No' => 'No'),
                'label' => 'Partner Art Of Living Program'
            ),
        );
        ## Selfie Photo:
        $statusArr = array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED');
        $elementArrStep6 = array();
        $elementArrStep6['selfie_photo'] = array(
            'label' => 'Selfie Photo',
            'class' => '',
            'type' => 'file',
            'path_value' => 'upload_path.SELFIE_PHOTOS_URL',
            'is_register' => 'yes',
            'other' => 'data-width="170" data-height="300"',
            'crop_image' => 'Yes'
        );
        $elementArrStep6['selfie_photo_status'] = array(
            'type' => 'radio',
            'value' => 'UNAPPROVED',
            'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'),
            'label' => 'Selfie Photo Status',
            'is_register' => 'no'
        );
        $elementArrStep6['photo_visibility'] = array(
            'type' => 'radio',
            'value' => '2',
            'value_arr' => array('Hide for All Members', 'View to All Members', 'Visible only to paid Members'),
            'label' => 'Photo Privacy Settings',
            'is_register' => 'no',
            'column' => '12'
        );
        ## Photo Upload :
        $photoCount = 4;
        for ($ip = 1; $ip <= $photoCount; $ip++) {
            $isRequired = '';
            if ($ip == 1) {
                $isRequired = 'required';
            }
            $elementArrStep6['photo' . $ip] =
                array(
                    'type' => 'file',
                    'path_value' => 'upload_path.MEMBER_PHOTOS_URL',
                    'is_register' => 'yes',
                    'label' => 'Photo ' . $ip,
                    'is_required' => $isRequired,
                    'other' => 'data-width="200" data-height="200"',
                    'crop_image' => 'Yes'
                );
            $elementArrStep6['photo' . $ip . '_status'] =
                array(
                    'type' => 'radio',
                    'value' => 'UNAPPROVED',
                    'custom_id' => 'STATUS' . $ip . '',
                    'value_arr' => $statusArr,
                    'label' => 'Photo ' . $ip . ' Status',
                    'is_register' => 'no',
                    'column' => '12'
                );
        }
        ## Id Proof Upload :
        $idProofType = array(
            'type' => 'radio',
            'value_arr' => _getStaticArr('idProofTypeArr'),
            'label' => 'ID Proof Type',
            'is_register' => 'no',
            'column' => '12'
        );
        $idProofFront = array(
            'class' => '',
            'type' => 'file',
            'path_value' => 'upload_path.MEMBER_IDPROOF_URL',
            'inline_style' => 'height:100px;width:150px;',
            'is_register' => 'yes',
            'other' => 'data-width="1024" data-height="576"',
            'crop_image' => 'Yes'
        );
        $idProofBack = array(
            'class' => '',
            'type' => 'file',
            'path_value' => 'upload_path.MEMBER_IDPROOF_URL',
            'inline_style' => 'height:100px;width:150px;',
            'is_register' => 'yes',
            'other' => 'data-width="1024" data-height="576"',
            'crop_image' => 'Yes'
        );
        $idProofApprove = array(
            'type' => 'radio',
            'value' => 'UNAPPROVED',
            'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'),
            'label' => 'ID Proof Status',
            'is_register' => 'no'
        );
        if ($userType == 'Admin' || $userType == 'Staff' || $userType == 'franchise' && isset($rowMemberData->id_proof_status) && $rowMemberData->id_proof_status != 'APPROVED') {
            $elementArrStep6['id_proof_type'] = $idProofType;
            $elementArrStep6['id_proof_front'] = $idProofFront;
            $elementArrStep6['id_proof_back'] = $idProofBack;
            $elementArrStep6['id_proof_status'] = $idProofApprove;
        }
        ## Horoscope Upload :
        $horoscopeFile = array(
            'label' => 'Horoscope',
            'class' => '',
            'type' => 'file',
            'path_value' => 'upload_path.MEMBER_HOROSCOPE_URL',
            'inline_style' => 'height:100px;width:150px;',
            'is_register' => 'yes',
            'other' => 'data-width="1024" data-height="576"',
            'crop_image' => 'Yes'
        );
        $horoscopeStatus = array(
            'type' => 'radio',
            'value' => 'UNAPPROVED',
            'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'),
            'label' => 'Horoscope Status',
            'is_register' => 'no'
        );
        $elementArrStep6['horoscope_file'] = $horoscopeFile;
        $elementArrStep6['horoscope_status'] = $horoscopeStatus;

        ## Form Data:
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'formUrl' => 'admin.member.addEdit',
            'columnName' => '*',
            'rowData' => $rowMemberData,
            'isDynamic' => 'Yes',
            'callbackUrl' => 'admin.member.index'
        ];
        $otherDataPartner = [
            'mode' => $mode,
            'id' => $id,
            'formUrl' => 'admin.member.addEdit',
            'columnName' => '*',
            'rowData' => $rowPartnerData,
            'isDynamic' => 'Yes',
            'callbackUrl' => 'admin.member.index'
        ];

        $fromHtmlStep1 = $this->adminFormBuilderService->generateFormElement($elementArrStep1, $otherData);
        $fromHtmlReligionStep1 = $this->adminFormBuilderService->generateFormElement($elementArrReligionStep1, $otherData);
        $fromHtmlEducationStep1 = $this->adminFormBuilderService->generateFormElement($elementArrEducationStep1, $otherData);
        $fromHtmlArtOfLivingStep1 = $this->adminFormBuilderService->generateFormElement($fromHtmlArtOfLivingStep1, $otherData);
        $fromHtmlStep2 = $this->adminFormBuilderService->generateFormElement($elementArrStep2, $otherData);
        $fromHtmlStep3 = $this->adminFormBuilderService->generateFormElement($elementArrStep3, $otherData);
        $fromHtmlAboutStep3 = $this->adminFormBuilderService->generateFormElement($elementArrAboutStep3, $otherData);
        $fromHtmlStep4 = $this->adminFormBuilderService->generateFormElement($elementArrStep4, $otherData);
        $fromHtmlStep5 = $this->adminFormBuilderService->generateFormElement($elementArrStep5, $otherDataPartner);
        $fromHtmlStep6 = $this->adminFormBuilderService->generateFormElement($elementArrStep6, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'formUrl' => 'admin.member.addEdit',
            'successUrl' => 'admin.member.index',
            'extraJsArr' => $extraJsArrAdd,
            'mode' => $mode,
            'id' => $id,
            'formSubmitBtnClass' => 'formSubmitBtn',
            'fromHtmlStep1' => $fromHtmlStep1,
            'fromHtmlReligionStep1' => $fromHtmlReligionStep1,
            'fromHtmlEducationStep1' => $fromHtmlEducationStep1,
            'fromHtmlArtOfLivingStep1' => $fromHtmlArtOfLivingStep1,
            'formId1' => 'addEditForm1',
            'formName1' => 'addEditForm1',
            'formSubmitBtnId1' => 'formSubmitBtn1',
            'fromHtmlStep2' => $fromHtmlStep2,
            'formId2' => 'addEditForm2',
            'formName2' => 'addEditForm2',
            'formSubmitBtnId2' => 'formSubmitBtn2',
            'fromHtmlStep3' => $fromHtmlStep3,
            'fromHtmlAboutStep3' => $fromHtmlAboutStep3,
            'formId3' => 'addEditForm3',
            'formName3' => 'addEditForm3',
            'formSubmitBtnId3' => 'formSubmitBtn3',
            'fromHtmlStep4' => $fromHtmlStep4,
            'formId4' => 'addEditForm4',
            'formName4' => 'addEditForm4',
            'formSubmitBtnId4' => 'formSubmitBtn4',
            'fromHtmlStep5' => $fromHtmlStep5,
            'formId5' => 'addEditForm5',
            'formName5' => 'addEditForm5',
            'formSubmitBtnId5' => 'formSubmitBtn5',
            'fromHtmlStep6' => $fromHtmlStep6,
            'formId6' => 'addEditForm6',
            'formName6' => 'addEditForm6',
            'formSubmitBtnId6' => 'formSubmitBtn6',
            'formId7' => 'addEditForm7',
            'formName7' => 'addEditForm7',
            'formSubmitBtnId7' => 'formSubmitBtn7',
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request): JsonResponse
    {
        try {
            $responseArr = [
                'status' => 'error',
                'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
                'html'   => '',
                'data'   => [],
            ];
            $postData = $request->all();

            if (blank($postData) || !isset($postData['step'])) {
                return response()->json($responseArr, 200);
            }

            $step   = (string) $postData['step'];
            $mode   = $postData['mode'] ?? 'add';
            $memberId = $postData['id'] ?? null;

            $member = null;
            if ($mode === 'edit' && !blank($memberId)) {
                $member = Register::find($memberId);
                if (!$member) {
                    return AdminResponseServices::error(_getConstant('responce_message.SOMETHING_WENT_WRONG'));
                }
            }

            $tableName  = 'registers';
            $updateData = [];
            switch ($step) {
                ## Step 1: Basic details :
                case '1':
                    $rules = [
                        'profileby'              => 'required|string',
                        'gender'              => 'required|string',
                        'fullname'            => 'required|string',
                        'email'               => 'required|email:rfc,dns',
                        'mobile_country_code' => 'required|string',
                        'mobile'              => 'required|string',
                        'birthdate'           => 'required|string',
                        'marital_status'      => 'required|string',
                        'religion'            => 'required|string',
                        'caste'               => 'required|string',
                        'education_level'     => 'required',
                        'occupation'          => 'required|string',
                        'Yesart_of_living_teacher'          => 'required',
                        'have_art_of_living_program'          => 'required',
                    ];

                    if ($mode === 'edit') {
                        unset($rules['gender']);
                        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
                            unset($rules['email']);
                            unset($rules['mobile_country_code']);
                            unset($rules['mobile']);
                        }
                    }

                    $validator = Validator::make($postData, $rules);
                    if ($validator->fails()) {
                        return AdminResponseServices::validationError($validator);
                    }

                    $updateData = [
                        'user_type'            => $postData['user_type'] ?? null,
                        'profileby'            => $postData['profileby'] ?? null,
                        'fullname'             => $postData['fullname'] ?? null,
                        'email'                => $postData['email'] ?? null,
                        'marital_status'       => $postData['marital_status'] ?? null,
                        'mother_tongue'        => $postData['mother_tongue'] ?? null,
                        'birthdate'            => $postData['birthdate'] ?? null,
                        'birthplace'           => $postData['birthplace'] ?? null,
                        'birthtime'            => $postData['birthtime'] ?? null,
                        'religion'             => $postData['religion'] ?? null,
                        'caste'                => $postData['caste'] ?? null,
                        'subcaste'             => $postData['subcaste'] ?? null,
                        'manglik'              => $postData['manglik'] ?? null,
                        'star'                 => $postData['star'] ?? null,
                        'gothra'               => $postData['gothra'] ?? null,
                        'moonsign'             => $postData['moonsign'] ?? null,
                        'horoscope'            => $postData['horoscope'] ?? null,
                        'designation_level'    => $postData['designation_level'] ?? null,
                        'education_details'    => $postData['education_details'] ?? null,
                        'employee_in'          => $postData['employee_in'] ?? null,
                        'occupation'           => $postData['occupation'] ?? null,
                        'income'               => $postData['income'] ?? null,
                        'mobile_verify_status' => $postData['mobile_verify_status'] ?? null,
                        'email_verify_status'  => $postData['email_verify_status'] ?? null,
                        'Yesart_of_living_teacher'  => $postData['Yesart_of_living_teacher'] ?? null,
                        'teacher_code'  => $postData['teacher_code'] ?? null,
                        'have_art_of_living_program'  => $postData['have_art_of_living_program'] ?? null,
                        'teacher_name'  => $postData['teacher_name'] ?? null,
                        'no_of_years_in_artofliving'  => $postData['no_of_years_in_artofliving'] ?? null,
                    ];
                    ## Gender :
                    if (!empty($postData['gender'])) {
                        $updateData['gender'] =  $postData['gender'] ?? null;
                    }

                    ## Education level can arrive as a single value or an array. :
                    if (!empty($postData['education_level'])) {
                        $educationLevel = is_array($postData['education_level']) ? $postData['education_level'] : [$postData['education_level']];
                        $updateData['education_level'] = implode(',', array_filter($educationLevel));
                    }

                    ## Mobile number: combine country code + number. :
                    if (!blank($postData['mobile_country_code'] ?? null) && !blank($postData['mobile'] ?? null)) {
                        $updateData['mobile'] = $postData['mobile_country_code'] . '-' . $postData['mobile'];
                    }
                    // total_children / status_children are only relevant for
                    // certain marital statuses on this step's data set.
                    if (isset($postData['total_children'])) {
                        $updateData['total_children'] = $postData['total_children'];
                    }
                    if (isset($postData['status_children'])) {
                        $updateData['status_children'] = $postData['status_children'];
                    }

                     ## Teaching courses can arrive as a single value or an array. :
                    if (!empty($postData['teaching_courses'])) {
                        $teachingCourses = is_array($postData['teaching_courses']) ? $postData['teaching_courses'] : [$postData['teaching_courses']];
                        $updateData['teaching_courses'] = implode(',', array_filter($teachingCourses));
                    }
                    if (!empty($postData['art_of_living_program'])) {
                        $artOfLivingProgram = is_array($postData['art_of_living_program']) ? $postData['art_of_living_program'] : [$postData['art_of_living_program']];
                        $updateData['art_of_living_program'] = implode(',', array_filter($artOfLivingProgram));
                    }
                    ## Teacher number: combine country code + number. :
                    if (!blank($postData['teacher_mobile_no_country_code'] ?? null) && !blank($postData['teacher_mobile_no'] ?? null)) {
                        $updateData['teacher_mobile_no'] = $postData['teacher_mobile_no_country_code'] . '-' . $postData['teacher_mobile_no'];
                    }

                    if ($postData['Yesart_of_living_teacher'] == 'No') {
                        $updateData['teacher_code'] = null;
                        $updateData['teaching_courses'] = null;
                    }
                    if ($postData['have_art_of_living_program'] == 'No') {
                        $updateData['teacher_name'] = null;
                        $updateData['teacher_mobile_no'] = null;
                        $updateData['art_of_living_program'] = null;
                    }

                    break;

                ## Step 2: Location details :
                case '2':
                    $validator = Validator::make($postData, [
                        'country_id' => 'required|string',
                        'state_id'   => 'required|string',
                        'city'       => 'required|string',
                    ]);
                    if ($validator->fails()) {
                        return AdminResponseServices::validationError($validator);
                    }
                    $alternateMobileNumber = null;
                    if (isset($postData['alternate_number_country_code']) && $postData['alternate_number_country_code'] != '' && isset($postData['alternate_number']) && $postData['alternate_number'] != '') {
                        $alternateMobileNumber = $postData['alternate_number_country_code'] . '-' . $postData['alternate_number'];
                    }

                    $updateData = [
                        'country_id'       => $postData['country_id'] ?? null,
                        'state_id'         => $postData['state_id'] ?? null,
                        'city'             => $postData['city'] ?? null,
                        'alternate_number' => $alternateMobileNumber ?? null,
                        'nri_country'      => $postData['nri_country'] ?? null,
                        'residence_type'   => $postData['residence_type'] ?? null,
                        'address'          => $postData['address'] ?? null,
                    ];
                    break;

                ## Step 3: Physical information :
                case '3':
                    $updateData = [
                        'height'               => $postData['height'] ?? null,
                        'weight'               => $postData['weight'] ?? null,
                        'diet'                 => $postData['diet'] ?? null,
                        'smoke'                => $postData['smoke'] ?? null,
                        'drink'                => $postData['drink'] ?? null,
                        'body_type'            => $postData['body_type'] ?? null,
                        'complexion'           => $postData['complexion'] ?? null,
                        'about_me_description' => $postData['about_me_description'] ?? null,
                        'blood_group_id'       => $postData['blood_group_id'] ?? null,
                    ];
                    break;

                ## Step 4: Family details :
                case '4':
                    $validator = Validator::make($postData, [
                        'father_name'       => 'required|string',
                        'father_occupation' => 'required|string',
                        'mother_name'       => 'required|string',
                        'mother_occupation' => 'required|string',
                    ]);
                    if ($validator->fails()) {
                        return AdminResponseServices::validationError($validator);
                    }
                    $updateData = [
                        'family_type'            => $postData['family_type'] ?? null,
                        'father_name'            => $postData['father_name'] ?? null,
                        'father_occupation'      => $postData['father_occupation'] ?? null,
                        'mother_name'            => $postData['mother_name'] ?? null,
                        'mother_occupation'      => $postData['mother_occupation'] ?? null,
                        'family_status'          => $postData['family_status'] ?? null,
                        'no_of_brother'          => $postData['no_of_brother'] ?? null,
                        'no_of_sister'           => $postData['no_of_sister'] ?? null,
                        'no_of_married_brother'  => $postData['no_of_married_brother'] ?? null,
                        'no_of_married_sister'   => $postData['no_of_married_sister'] ?? null,
                        'family_details'         => $postData['family_details'] ?? null,
                    ];
                    break;

                ## Step 5: Partner preference (separate table) :
                case '5':
                    $tableName = 'register_partners';
                    $updateData = [
                        'part_marital_status' => $postData['part_marital_status'] ?? null,
                        'part_country'        => $postData['part_country'] ?? null,
                        'part_religion'       => $postData['part_religion'] ?? null,
                        'part_caste'          => $postData['part_caste'] ?? null,
                        'part_mothertongue'   => $postData['part_mothertongue'] ?? null,
                        'part_frm_age'        => $postData['part_frm_age'] ?? null,
                        'part_to_age'         => $postData['part_to_age'] ?? null,
                        'part_height'         => $postData['part_height'] ?? null,
                        'part_height_to'      => $postData['part_height_to'] ?? null,
                        'part_income'         => $postData['part_income'] ?? null,
                        'part_education'      => $postData['part_education'] ?? null,
                        'part_occupation'     => $postData['part_occupation'] ?? null,
                        'part_state'          => $postData['part_state'] ?? null,
                        'part_manglik'        => $postData['part_manglik'] ?? null,
                        'part_art_of_living_teacher'        => $postData['part_art_of_living_teacher'] ?? null,
                        'part_have_art_of_living_program'        => $postData['part_have_art_of_living_program'] ?? null,
                    ];
                    break;
                ## Step 6: Photos / ID proof / horoscope uploads :
                case '6':
                    $validator = Validator::make($postData, [
                        'photo1'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                        'photo2'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                        'photo3'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                        'photo4'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                        'id_proof_front'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                        'id_proof_back'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
                    ], [
                        'image' => 'The file must be an image (jpeg, png, jpg, webp).',
                        'mimes' => 'The image must be a file of type: jpeg, png, jpg, webp.',
                        'max'   => 'The image size must not exceed 10MB.',
                    ]);
                    if ($validator->fails()) {
                        return AdminResponseServices::validationError($validator);
                    }
                    $filePathMap = [
                        'selfie_photo'   => 'upload_path.SELFIE_PHOTOS_URL',
                        'photo1'         => 'upload_path.MEMBER_PHOTOS_URL',
                        'photo2'         => 'upload_path.MEMBER_PHOTOS_URL',
                        'photo3'         => 'upload_path.MEMBER_PHOTOS_URL',
                        'photo4'         => 'upload_path.MEMBER_PHOTOS_URL',
                        'id_proof_front' => 'upload_path.MEMBER_IDPROOF_URL',
                        'id_proof_back'  => 'upload_path.MEMBER_IDPROOF_URL',
                        'horoscope_file' => 'upload_path.MEMBER_HOROSCOPE_URL',
                    ];
                    $blurEligible = ['photo1', 'photo2', 'photo3', 'photo4'];
                    foreach ($filePathMap as $key => $pathConstant) {
                        if (!$request->hasFile($key)) {
                            continue;
                        }
                        $path     = _getConstant($pathConstant);
                        $blurPath = in_array($key, $blurEligible, true) ? _getConstant('upload_path.MEMBER_BLUR_PHOTOS_URL') : null;
                        $oldValue = $member->{$key} ?? '';
                        $uploadedFile = UploadHelper::uploadFile($request->file($key), $path, $oldValue, '', 1, $blurPath);
                        if (!$uploadedFile) {
                            continue;
                        }
                        $updateData[$key] = $uploadedFile;

                        if (in_array($key, $blurEligible, true)) {
                            $updateData[$key . '_uploaded_on']  = _getCurrentDate();
                        } elseif ($key === 'id_proof_front' || $key === 'id_proof_back') {
                            $updateData['id_proof_uploaded_on'] = _getCurrentDate();
                        } elseif ($key === 'horoscope_file') {
                            $updateData['horoscope_uploaded_on'] = _getCurrentDate();
                        } elseif ($key === 'selfie_photo') {
                            $updateData['selfie_photo_uploaded_on'] = _getCurrentDate();
                        }
                    }
                    $updateData['photo_visibility'] = $request->photo_visibility;
                    $updateData['photo1_status']        = $postData['photo1_status'] ?? 'APPROVED';
                    $updateData['photo2_status']        = $postData['photo2_status'] ?? 'APPROVED';
                    $updateData['photo3_status']        = $postData['photo3_status'] ?? 'APPROVED';
                    $updateData['photo4_status']        = $postData['photo4_status'] ?? 'APPROVED';
                    $updateData['id_proof_status']      = $postData['id_proof_status'] ?? 'APPROVED';
                    $updateData['horoscope_status']     = $postData['horoscope_status'] ?? 'APPROVED';
                    $updateData['selfie_photo_status']  = $postData['selfie_photo_status'] ?? 'APPROVED';

                    break;
                case '7':
                    $updateData = [];
                    break;

                default:
                    return AdminResponseServices::error(_getConstant('responce_message.SOMETHING_WENT_WRONG'));
            }

            if (empty($updateData) && $step !== '7') {
                return AdminResponseServices::error(_getConstant('responce_message.DATA_NOT_UPDATED'));
            }

            ## Extra Update Code Here :
            if (isset($postData['password']) && $postData['password'] != '') {
                $updateData['password'] = Hash::make($postData['password']);
            }
            ## Uniqueness check (email / mobile) — step 1 only. :
            if ($step === '1') {
                $emailQuery = Register::select('id', 'email', 'mobile')
                    ->where('email', $postData['email'] ?? '');
                $mobileQuery = Register::select('id', 'email', 'mobile')
                    ->where('mobile', $updateData['mobile'] ?? '');

                if ($mode === 'edit') {
                    $emailQuery->where('id', '!=', $memberId);
                    $mobileQuery->where('id', '!=', $memberId);
                }

                if ($emailQuery->exists() || $mobileQuery->exists()) {
                    return AdminResponseServices::error('Mobile number or Email is already registered');
                }
            }

            ## Check Roles Permission:
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);

            if ($mode === 'edit') {
                if ($tableName === 'register_partners') {
                    RegisterPartner::updateOrCreate(['member_id' => $memberId], $updateData);
                } else {
                    Register::where('id', $memberId)->update($updateData);
                }
            } else {
                ## add mode — only step 1 creates the base record. :
                if ($step === '1') {
                    $updateData['registered_from'] = 'Admin';
                    $updateData['created_at']      = _getCurrentDate();

                    if ($userType === 'Staff') {
                        $updateData['staff_assign_id']   = $userId;
                        $updateData['staff_assign_date'] = _getCurrentDate();
                    } elseif ($userType === 'Franchise') {
                        $updateData['franchised_by']        = $userId;
                        $updateData['franchise_assign_id']  = $userId;
                        $updateData['franchise_assign_date'] = _getCurrentDate();
                    }

                    $register = Register::create($updateData);
                    $memberId = $register->id;

                    $siteConfigArr = _getSiteSetting();
                    Register::where('id', $memberId)->update([
                        'matri_id' => $siteConfigArr['matri_prefix'] . $memberId,
                        'prefix'   => $siteConfigArr['matri_prefix'],
                    ]);
                } elseif ($tableName === 'register_partners') {
                    RegisterPartner::updateOrCreate(['member_id' => $memberId], $updateData);
                } else {
                    Register::where('id', $memberId)->update($updateData);
                }
            }

            ## Register Partner Create If Not exists :
            RegisterPartner::updateOrCreate(['member_id' => $memberId], ['member_id' => $memberId]);

            $this->adminCommonActionModel->addPersonalizeMemberChat($memberId);

            ## Member Status Change Logs:
            DB::table('admin_member_status_change_logs')->insert([
                'status_change_by'   => _adminUserType($authUser->type),
                'action_by_user_logs' => json_encode($authUser),
                'change_details'     => json_encode(
                    $request->except(['_token', 'isPost'])
                ),
                'created_on'         => now(),
            ]);

            $staffActivity  = $mode === 'edit' ? 'edit_profiles' : 'add_profiles';
            if ($step === '6' && $request->hasAny(['photo1', 'photo2', 'photo3', 'photo4', 'id_proof_front', 'id_proof_back'])) {
                $staffActivity = 'upload_photos';
            }

            if (in_array($userType, ['Staff', 'Franchise'], true)) {
                AdminCommonActionModel::addStaffFranchiseActivity($memberId, $userId, $staffActivity, $userType);
            }

            return AdminResponseServices::success('Data added successfully.', [
                'mode'      => $mode,
                'member_id' => $memberId,
                'step'      => $step,
            ]);
        } catch (Throwable $e) {
            return AdminResponseServices::error(_getConstant('responce_message.SOMETHING_WENT_WRONG'));
        }
    }

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (!empty($postData)) {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            if (!isset($postData['is_verify'])) {
                unset($updateData['id']);
                if (isset($postData['id_proof_front']) && empty($postData['id_proof_front']) && $postData['id_proof_front'] == '') {
                    $updateData['id_proof_front'] = '';
                }
                if (isset($postData['id_proof_back']) && empty($postData['id_proof_back']) && $postData['id_proof_back'] == '') {
                    $updateData['id_proof_back'] = '';
                }
                $ids = $postData['id'] ?? [];
                // Convert to array safely
                if (!is_array($ids)) {
                    $ids = explode(',', $ids);   // handles "12" and "12,13"
                }
                $ids = array_filter($ids);
                if (isset($postData['is_deleted'])) {
                    Register::whereIn('id', $ids)->delete();
                } else {
                    Register::whereIn('id', $ids)->update($updateData);
                }

                ## Send Expired Notification :
                $sendNotification = 0;
                if (isset($postData['status']) && in_array($postData['status'], ['UNAPPROVED', 'Suspended'])) {
                    $sendNotification = 1;
                }
                // if (isset($postData['is_deleted']) && in_array($postData['is_deleted'], ['Yes'])) {
                //     $sendNotification = 1;
                // }
                // if ($sendNotification == 1) {
                //     $memberIdArr = explode(',', $postData['id']);
                //     foreach ($memberIdArr as $memberId) {
                //         $notificationArr = array(
                //             'receiverMemberId' => $memberId,
                //             'senderMemberId' => 0,
                //             'notificationType' => 'session_expired',
                //             'title' => 'Session Expired',
                //             'message' => 'Your session has been expired!',
                //             'dataArr' => array(
                //                 'title' => 'Session Expired',
                //                 'message' => 'Your session has been expired!',
                //             ),
                //         );
                //         // $this->apiCommonActionModel->sendNotification($notificationArr);
                //     }
                // }

                ## Send Notification :
                if (isset($postData['fstatus']) && $postData['fstatus'] == 'Featured') {
                    ## Send Featured Member Notification:
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $member = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        app(NotificationService::class)->sendNotification(
                            $member,
                            $member,
                            'featured_profile'
                        );

                        ## Send SMS Message:
                        app(SmsSendService::class)->sendTemplate('Featured Profile', $member, []);
                    }
                }

                ## For Pause Plan :
                if (isset($postData['status']) && $postData['status'] == 'APPROVED') {
                    foreach (explode(',', $postData['id']) as $memberId) {
                        ## Get Member Data :
                        $memberData = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', $memberId)->first();
                        ## Active Member Email:
                        $replaceArr = [
                            'user_name'  => $memberData->fullname,
                            'user_matri_id' => $memberData->matri_id,
                            'user_email' => $memberData->email
                        ];
                        app(EmailSendService::class)->send('Active Member', $memberData->email, $replaceArr, ['memberData' => $memberData]);

                        ## Active Member Sms:
                        app(SmsSendService::class)->sendTemplate('Profile Approved', $memberData, []);

                        ## Active Profile Notification :
                        app(NotificationService::class)->sendNotification(
                            $memberData,
                            $memberData,
                            'profile_approved'
                        );

                        ## Check Deactive Request With Pause :
                        $isExistPause = MemberDeleteProfile::where('is_pause_plan', 1)->where('sender', $memberId)->first();
                        if (!empty($isExistPause)) {
                            if (!empty($memberData)) {
                                $planExpiredDate = '';
                                $daysDifferent = _getDaysBetweenTwoDates(
                                    $memberData->plan_expired_on,
                                    $isExistPause->sent_on
                                );
                                $addDate = _getCurrentDate('Y-m-d');
                                if ($memberData->plan_expired_on >= _getCurrentDate('Y-m-d')) {
                                    $addDate = _getCurrentDate($memberData->plan_expired_on);
                                    $daysDifferent = _getDaysBetweenTwoDates(
                                        _getCurrentDate('Y-m-d'),
                                        $isExistPause->sent_on
                                    );
                                }
                                if ($daysDifferent > 0) {
                                    $planExpiredDate = _addDayInDate($addDate, $daysDifferent);
                                }
                                ## Now Update Expiry date :
                                if ($planExpiredDate) {
                                    Register::where('id', $memberId)->update(
                                        ['plan_expired_on' => $planExpiredDate, 'plan_status' => 'Paid']
                                    );
                                    ## Update In Payment Table :
                                    Payment::where('member_id', $memberData->id)->update(
                                        ['plan_expired' => $planExpiredDate, 'current_plan' => 'Yes']
                                    );
                                }
                            }
                            ## Update Pause Status :
                            MemberDeleteProfile::where('id', $isExistPause->id)->update('is_pause_plan', 0);
                        }
                    }
                }

                ## Verification Affiliate Income :
            } elseif (isset($postData['is_verify']) && $postData['is_verify'] == 'Yes') {
                $ids = $postData['id'] ?? [];
                // Convert to array safely
                if (!is_array($ids)) {
                    $ids = explode(',', $ids);   // handles "12" and "12,13"
                }
                $ids = array_filter($ids);
                $memberData = Register::whereIn('id', $ids)->select(['id', 'matri_id', 'affiliate_member_id', 'verification_affiliate_member_id'])->get();
                foreach ($memberData as $key => $member) {
                    $this->addAffiliateMemberIncome($member);
                }
            }

            ## Member Status Change Logs:
            $authenticatedUser = Auth::user();
            DB::table('admin_member_status_change_logs')->insert([
                'status_change_by'   => _adminUserType($authenticatedUser->type),
                'action_by_user_logs' => json_encode($authenticatedUser),
                'change_details'     => json_encode(
                    $request->except(['_token', 'isPost'])
                ),
                'created_on'         => now(),
            ]);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## Admin View Profile :
    public function viewDetails($id = 0)
    {
        ## Extra Js :
        $extraJsArrView = [
            $this->customJsDirectory . $this->directoryName . '/view.js'
        ];
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);

        ## Staff Role Data :
        $viewMemberPermission = _checkPermission($userType, $roleId, 'view_profile');

        ## Member Data:
        $query = Register::withTrashed()->where('id', $id);
        if ($viewMemberPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
            $query->where('staff_assign_id', $userId);
        }
        $registerArr = $query->first();
        if (empty($registerArr) || $registerArr->trashed()) {
            $pageName = 'Member Details';
            return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/userNotFound', compact('registerArr', 'pageName'));
        }

        ## Partner Preferences :
        $registerPartnerArr = $registerArr->partnerPreference;
        ## Profile Complete % :
        $registerArr->completeProfile = ProfileCompletionService::calculate($registerArr);
        ## Match Count
        $matchMakingCountService = app(MatchMakingCountService::class);
        $registerArr->matchMakingCount = $matchMakingCountService->getMatchCount($registerArr);

        ## Button Permission Access :
        if (isset($userType) && $userType == 'Admin') {
            $userType = 'Admin';
        }

        ## Staff Activity:
        if ($userType == 'Staff' || $userType == 'Franchise') {
            AdminCommonActionModel::addStaffFranchiseActivity($id, $userId, 'view_profiles', $userType);
        }

        $dataArr = [
            'pageName' => $this->pageName,
            'resultArr' => $registerArr,
            'registerPartnerArr' => $registerPartnerArr,
            'extraJsArr' => $extraJsArrView,
            'changeStatusUrl' => 'admin.member.changeStatus',
            'redirectUrl' => 'admin.member.index',
            'actionBtnArr' => [
                'match_making' => 1,
                'addComment' => 1,
                'viewComment' => 1,
                'photoApproval' => 1,
                'photoDelete' => 1,
                'idProofDeletebtn' => 1,
                'downloadBiodatabtn' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.member.addForm',
                'edit' => 'admin.member.editForm',
                'view' => 'admin.member.viewDetails',
                'editPlan' => 'admin.member.editPlan',
                'currentPlan' => 'admin.member.currentPlan',
                'viewComment' => 'admin.member.viewComment',
                'addComment' => 'admin.member.addComment',
                'downloadBiodata' => 'admin.member.downloadBiodataPdf',
            ],
        ];

        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            $registerArr->mobile = _getConstant('DISABLE_IN_DEMO_LABEL');
            $registerArr->email = _getConstant('DISABLE_IN_DEMO_LABEL');
            $registerArr->alternate_number = _getConstant('DISABLE_IN_DEMO_LABEL');
        }
        ## Member Field :
        $dataArr['memberFields'] = [
            'Basic Details' => [
                ['key' => 'profile_by', 'label' => 'Profile By', 'value' => $registerArr->profileByData->profileby_name ?? null],
                ['key' => 'fullname', 'label' => 'Full Name', 'value' => $registerArr->fullname ?? null],
                ['key' => 'gender', 'label' => 'Gender', 'value' => $registerArr->gender ?? null],
                ['key' => 'email', 'label' => 'Email', 'value' => $registerArr->email ?? null],
                ['key' => 'mobile', 'label' => 'Mobile Number', 'value' => $registerArr->mobile ?? null],
                ['key' => 'mother_tongue', 'label' => 'Mother Tongue', 'value' => $registerArr->motherTongueData->mtongue_name ?? null],
                ['key' => 'birthdate', 'label' => 'Date of birth', 'value' => $registerArr->birthdate ? \Carbon\Carbon::parse($registerArr->birthdate)->format('d-m-Y') : null],
                ['key' => 'marital_status', 'label' => 'Marital Status', 'value' => $registerArr->maritalStatusData->marital_status_name ?? null],
                ['key' => 'total_children', 'label' => 'Total Children', 'value' => $registerArr->totalChildrenData->total_child_name ?? null],
                ['key' => 'status_children', 'label' => 'Status Children', 'value' => $registerArr->statusChildrenData->status_child_name ?? null],
                ['key' => 'birthplace', 'label' => 'Birth Place', 'value' => $registerArr->birthplace ?? null],
                ['key' => 'birthtime', 'label' => 'Birth Time', 'value' => _birthtimeDisplay($registerArr->birthtime)],
            ],
            'Religious Information' => [
                ['key' => 'religion', 'label' => 'Religion', 'value' => $registerArr->religionData->religion_name ?? null],
                ['key' => 'caste', 'label' => 'Caste', 'value' => $registerArr->casteData->caste_name ?? null],
                ['key' => 'subcaste', 'label' => 'Sub Caste', 'value' => $registerArr->subcaste ?? null],
                ['key' => 'manglik', 'label' => 'Manglik', 'value' => $registerArr->manglikData->manglik_name ?? null],
                ['key' => 'gothra', 'label' => 'Gothra', 'value' => $registerArr->gothra ?? null],
                ['key' => 'moonsign', 'label' => 'Moonsign', 'value' => $registerArr->moonsignData->moonsign_name ?? null],
                ['key' => 'star', 'label' => 'Star', 'value' => $registerArr->starData->star_name ?? null],
                ['key' => 'horoscope', 'label' => 'Horoscope', 'value' => $registerArr->horoscopeData->horoscope_name ?? null],
            ],
            'Location Details' => [
                ['key' => 'country', 'label' => 'Country', 'value' => $registerArr->countryData->country_name ?? null],
                ['key' => 'state', 'label' => 'State', 'value' => $registerArr->stateData->state_name ?? null],
                ['key' => 'city', 'label' => 'City', 'value' => $registerArr->cityData->city_name ?? null],
                ['key' => 'residence_type', 'label' => 'Residence Type', 'value' => $registerArr->residenceTypeData->residence_name ?? null],
                ['key' => 'alternate_number', 'label' => 'Alternative Number', 'value' => $registerArr->alternate_number ?? null],
                ['key' => 'nri_country', 'label' => 'If NRI Originated country', 'value' => $registerArr->nri_country ?? null],
                ['key' => 'address', 'label' => 'Address', 'value' => $registerArr->address ?? null],
            ],
            'Education & Other Details' => [
                ['key' => 'education', 'label' => 'Education', 'value' => implode(', ', $registerArr->education_level_names) ?? null],
                ['key' => 'education_details', 'label' => 'Education Details', 'value' => $registerArr->education_details ?? null],
                ['key' => 'employee_in', 'label' => 'Employee In', 'value' => $registerArr->occupationData->occupation_name ?? null],
                ['key' => 'occupation', 'label' => 'Occupation', 'value' => $registerArr->employeeInData->employee_name ?? null],
                ['key' => 'annual_income', 'label' => 'Annual Income', 'value' => $registerArr->incomeData->annual_income_name ?? null],
                ['key' => 'designation', 'label' => 'Designation', 'value' => $registerArr->designationLevelData->designation_name ?? null],
            ],
            'Physical Information' => [
                ['key' => 'height', 'label' => 'Height', 'value' => _displayHeight($registerArr->height) ?? null],
                ['key' => 'weight', 'label' => 'Weight', 'value' => $registerArr->weight ? $registerArr->weight . ' Kg' : null],
                ['key' => 'eating_habits', 'label' => 'Eating Habits', 'value' => $registerArr->dietData->eating_habit_name ?? null],
                ['key' => 'smoking', 'label' => 'Smoking Habit', 'value' => $registerArr->smokeData->smoking_habit_name ?? null],
                ['key' => 'drinking', 'label' => 'Drinking Habit', 'value' => $registerArr->drinkingData->drinking_habit_name ?? null],
                ['key' => 'body_type', 'label' => 'Body type', 'value' => $registerArr->bodyTypeData->body_type_name ?? null],
                ['key' => 'complexion', 'label' => 'Complexion', 'value' => $registerArr->complexionData->complexion_name ?? null],
                ['key' => 'blood_group_id', 'label' => 'Blood Group', 'value' => $registerArr->bloodGroupData->blood_group_name ?? null],
                ['key' => 'about_me', 'label' => 'About Me', 'value' => $registerArr->about_me_description ?? null],
            ],
            'Family Details' => [
                ['key' => 'family_type', 'label' => 'Family Type', 'value' => $registerArr->familyTypeData->family_type_name ?? null],
                ['key' => 'family_status', 'label' => 'Family Status', 'value' => $registerArr->familyStatusData->family_status_name ?? null],
                ['key' => 'father_name', 'label' => 'Father Name', 'value' => $registerArr->father_name ?? null],
                ['key' => 'father_occupation', 'label' => 'Father Occupation', 'value' => $registerArr->fatherOccupationData->occupation_name ?? null],
                ['key' => 'mother_name', 'label' => 'Mother Name', 'value' => $registerArr->mother_name ?? null],
                ['key' => 'mother_occupation', 'label' => 'Mother Occupation', 'value' => $registerArr->motherOccupationData->occupation_name ?? null],
                ['key' => 'no_of_brothers', 'label' => 'No Of Brothers', 'value' => $registerArr->noOfBrotherData->no_of_bro_sis_name ?? null],
                ['key' => 'no_of_married_brothers', 'label' => 'No Of Married Brothers', 'value' => $registerArr->noOfMarriedBrotherData->married_bro_name ?? null],
                ['key' => 'no_of_sisters', 'label' => 'No Of Sisters', 'value' => $registerArr->noOfSisterData->no_of_bro_sis_name ?? null],
                ['key' => 'no_of_married_sisters', 'label' => 'No Of Married Sisters', 'value' => $registerArr->noOfMarriedSisterData->married_sis_name ?? null],
                ['key' => 'family_details', 'label' => 'Family Details', 'value' => $registerArr->family_details ?? null],
            ],
        ];

        ## Partner Preference Field :
        if ($registerPartnerArr) {
            $partnerFields = [
                'part_religion'       => [ReligionMaster::class, 'religion_name'],
                'part_caste'          => [CasteMaster::class, 'caste_name'],
                'part_country'        => [CountryMaster::class, 'country_name'],
                'part_state'          => [StateMaster::class, 'state_name'],
                'part_marital_status' => [MaritalStatusMaster::class, 'marital_status_name'],
                'part_income'         => [AnnualIncomeMaster::class, 'annual_income_name'],
                'part_education'      => [EducationMaster::class, 'education_name'],
                'part_occupation'     => [OccupationMaster::class, 'occupation_name'],
                'part_mothertongue'   => [MotherTongueMaster::class, 'mtongue_name'],
                'part_manglik'        => [ManglikMaster::class, 'manglik_name'],
            ];
            foreach ($partnerFields as $field => [$model, $column]) {
                $value = $registerPartnerArr->{$field};
                if (!empty($value) && $value !== 'Does Not Matter') {
                    $registerPartnerArr->{$field} = _getLangNamesFromIds($model, $value, $column);
                }
            }
        }
        $dataArr['partnerFields'] = [
            'Partner Preferences' => [
                ['key' => 'part_frm_age', 'label' => 'Partner From Age', 'value' => $registerPartnerArr->part_frm_age ?? null],
                ['key' => 'part_to_age', 'label' => 'Partner To Age', 'value' => $registerPartnerArr->part_to_age ?? null],
                ['key' => 'part_height', 'label' => 'Partner From Height', 'value' => $registerPartnerArr->part_height ?? null],
                ['key' => 'part_height_to', 'label' => 'Partner To Height', 'value' => $registerPartnerArr->part_height_to ?? null],
                ['key' => 'part_religion', 'label' => 'Partner Religion', 'value' => $registerPartnerArr->part_religion ?? null],
                ['key' => 'part_caste', 'label' => 'Partner Caste', 'value' => $registerPartnerArr->part_caste ?? null],
                ['key' => 'part_country', 'label' => 'Partner Country', 'value' => $registerPartnerArr->part_country ?? null],
                ['key' => 'part_state', 'label' => 'Partner State', 'value' => $registerPartnerArr->part_state ?? null],
                ['key' => 'part_income', 'label' => 'Partner Annual Income', 'value' => $registerPartnerArr->part_income ?? null],
                ['key' => 'part_marital_status', 'label' => 'Partner Marital status', 'value' => $registerPartnerArr->part_marital_status ?? null],
                ['key' => 'part_education', 'label' => 'Partner Education', 'value' => $registerPartnerArr->part_education ?? null],
                ['key' => 'part_occupation', 'label' => 'Partner Occupation', 'value' => $registerPartnerArr->part_occupation ?? null],
                ['key' => 'part_mothertongue', 'label' => 'Partner Mother Tongue', 'value' => $registerPartnerArr->part_mothertongue ?? null],
                ['key' => 'part_manglik', 'label' => 'Partner Manglik', 'value' => $registerPartnerArr->part_manglik ?? null],
                ['key' => 'part_art_of_living_teacher', 'label' => 'Partner Manglik', 'value' => $registerPartnerArr->part_art_of_living_teacher ?? null],
                ['key' => 'part_have_art_of_living_program', 'label' => 'Partner Manglik', 'value' => $registerPartnerArr->part_have_art_of_living_program ?? null],
            ],
        ];

        $idProofDetails =  [
            'ID Proof Front' => 'id_proof_front',
            'ID Proof Back' => 'id_proof_back'
        ];
        if ($registerArr->id_proof_status == 'UNAPPROVED' && $userType != 'Admin') {
            $idProof = $idProofDetails;
        } elseif ($userType == 'Admin') {
            $idProof = $idProofDetails;
        } else {
            $idProof = [];
        }
        ## Member Photo Arr :
        $dataArr['memberPhotoArr'] = [
            'Photo Details' => [
                'Photo 1' => 'photo1',
                'Photo 2' => 'photo2',
                'Photo 3' => 'photo3',
                'Photo 4' => 'photo4',
            ],
            'Selfie Photo' => [
                'Selfie Photo' => 'selfie_photo'
            ],
            'ID Proof' => $idProof,
            'Horoscope' => [
                'Horoscope' => 'horoscope_file'
            ],
        ];

        ## Activity View Counts :
        $dataArr['photoRequestCount'] = PhotoRequest::where('sender_member_id', $registerArr->id)->count();
        $dataArr['viewContactCount'] = ViewContactDetail::where('sender_member_id', $registerArr->id)->count();
        $dataArr['viewInterestCount'] = ExpressInterest::where('sender_member_id', $registerArr->id)->count();

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    ## Admin Assign Staff && Franchise :
    public function assignMember(Request $request)
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

        DB::transaction(function () use ($postData, &$responseArr) {
            $authUser   = Auth::user();
            $userType   = _adminUserType($authUser->type);
            $configArr  = _getSiteSetting();

            // Determine assign target
            if (!empty($postData['subAdminId'])) {
                $assignId       = (int) $postData['subAdminId'];
                $assignUserType = 'Staff';
            } elseif (!empty($postData['subAdminFranchiseId'])) {
                $assignId       = (int) $postData['subAdminFranchiseId'];
                $assignUserType = 'Franchise';
            } else {
                return;
            }

            $memberIds = array_map('intval', explode(',', $postData["id"]));
            $now       = _getCurrentDate();
            $action    = 'Assign';

            /* -------------------------------------------------
         | 1) UPSERT ASSIGN HISTORY (Correct keys)
         -------------------------------------------------*/
            foreach ($memberIds as $memberId) {

                AssignHistory::updateOrCreate(
                    [
                        'member_id' => $memberId,
                        'user_type' => $assignUserType,
                    ],
                    [
                        'assign_by'        => $userType,
                        'assign_by_email'  => $configArr['contact_email'],
                        'assign_to'        => $assignId,
                        'assign_date'      => $now,
                        'action'           => $action,
                    ]
                );
            }

            /* -------------------------------------------------
            | 2) Mark previous other assignments as Unassigned
            -------------------------------------------------*/
            AssignHistory::whereIn('member_id', $memberIds)
                ->where('user_type', '!=', $assignUserType)
                ->where('action', 'Assign')
                ->update(['action' => 'Unassigned']);

            /* -------------------------------------------------
            | 3) Bulk update Register table
            -------------------------------------------------*/
            if ($assignUserType === 'Staff') {
                $updateData = [
                    'adminrole_id'        => $assignId,
                    'staff_assign_id'     => $assignId,
                    'staff_assign_date'   => $now,
                    'franchise_assign_id' => 0,
                    'franchised_by'       => 0,
                    'franchise_assign_date' => null,
                ];
            } else { // Franchise

                $updateData = [
                    'adminrole_id'        => 0,
                    'staff_assign_id'     => 0,
                    'staff_assign_date'   => null,
                    'franchised_by'       => $assignId,
                    'franchise_assign_id' => $assignId,
                    'franchise_assign_date' => $now,
                ];
            }

            Register::whereIn('id', $memberIds)->update($updateData);

            ## Notifications :
            foreach ($memberIds as $memberId) {
                AdminCommonActionModel::sendAdminNotification($memberId, '', 'member_assign', $assignUserType, $assignId);
            }

            $responseArr['status'] = 'success';
            $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        });

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
                $assignHistory = AssignHistory::where(['member_id' => $valueId, 'user_type' => $assignUserType])->get();
                ## Update Arr:
                $updateDataArr = array(
                    'assign_by' => $userType,
                    'assign_by_email' => $configArr['contact_email'],
                    'assign_to' => $assignId,
                    'user_type' => $assignUserType,
                    'member_id' => $valueId,
                    'assign_date' => _getCurrentDate(),
                    'action' => $action,
                );
                if (!blank($assignHistory)) {
                    $whereUpdateArr = ['member_id' => $valueId, 'user_type' => $assignUserType];
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
                Register::where($whereUpdate)->update($updateData);
            }
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## Admin Edit Plan :
    public function editPlan($id = 0)
    {
        ## Extra Js :
        $extraJsArrEdit = [
            $this->customJsDirectory . $this->directoryName . '/editPlan.js',
        ];
        ## Member Data:
        $memberData = Register::select('id', 'matri_id', 'fullname', 'gender', 'photo1', 'email', 'mobile')->where('id', $id)->first();

        ## Membership Plan :
        $membershipPlanArr = MembershipPlan::active()->get();

        ## Add On Packages:
        $addOnPackageArr = AddOnPackage::active()->get();

        ## Payment Mode Arr :
        $paymentModeArr = _getStaticArr('paymentMethodArr');

        ## Member's current active plan — needed so the view knows whether
        ## "Assign Add-On Only" mode is available for this member :
        $currentPlanData = Payment::where('member_id', $id)->where('current_plan', 'Yes')->first();

        $dataArr = [
            'pageName' => $this->pageName,
            'memberData' => $memberData,
            'membershipPlanArr' => $membershipPlanArr,
            'paymentModeArr' => $paymentModeArr,
            'extraJsArr' => $extraJsArrEdit,
            'addOnPackageArr' => $addOnPackageArr,
            'currentPlanData' => $currentPlanData,
            'changeStatusUrl' => 'admin.member.changeStatus',
            'redirectUrl' => 'admin.member.index',
            'getPlanDataUrl' => 'admin.member.getPlanData',
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/editPlan', $dataArr);
    }

    ## Get Plan Data :
    public function getPlanData(Request $request, UpgradeMembershipPlanService $service)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html'   => '',
            'data'   => []
        ];

        $assignType = $request->assignType === 'addon_only' ? 'addon_only' : 'new_plan';

        // Add-On Packages selected by user
        $addOnSelectedIds = !empty($request->package_ids)
            ? array_map('intval', (array) $request->package_ids)
            : [];

        // Site config (for default currency + tax display in the view)
        $siteConfigArr = _getSiteSetting();

        if ($assignType === 'addon_only') {

            if (empty($request->memberId)) {
                $responseArr['msg'] = 'Member not found.';
                return response()->json($responseArr, 200);
            }

            // Member must already have an active plan to attach stand-alone add-ons to
            $currentPayment = Payment::where('member_id', $request->memberId)->where('current_plan', 'Yes')->first();
            if (!$currentPayment) {
                $responseArr['msg'] = 'This member does not have an active plan. Please assign a new plan instead.';
                return response()->json($responseArr, 200);
            }

            if (empty($addOnSelectedIds)) {
                $responseArr['msg'] = 'Please select at least one add-on package.';
                return response()->json($responseArr, 200);
            }

            // Used only for display (currency / "on current plan: X") — never fed into pricing
            $membershipPlanArr = MembershipPlan::find($currentPayment->plan_id);
            $addOnPackageArr = AddOnPackage::active()->whereIn('id', $addOnSelectedIds)->get();

            // IMPORTANT: use calculateAddOnAmount(), NOT calculatePlanAmount().
            // calculatePlanAmount() always folds the base plan's amount into the subtotal
            // before taxing it — that's what was causing tax/grand total to be computed
            // on "plan amount + addon amount" even though the plan amount was hidden.
            // calculateAddOnAmount() prices ONLY the selected add-on packages.
            $result = $service->calculateAddOnAmount([
                'package_ids' => $addOnSelectedIds,
                'coupon_code' => $request->coupon_code ?? null,
            ]);

            $planAmount   = 0;
            $planDiscount = 0;
        } else {

            if (empty($request->planId)) {
                $responseArr['msg'] = 'Please select a plan.';
                return response()->json($responseArr, 200);
            }

            // Membership Plan
            $membershipPlanArr = MembershipPlan::active()->find($request->planId);
            if (!$membershipPlanArr) {
                $responseArr['msg'] = 'Selected plan is not available.';
                return response()->json($responseArr, 200);
            }

            $addOnPackageArr = !empty($addOnSelectedIds)
                ? AddOnPackage::active()->whereIn('id', $addOnSelectedIds)->get()
                : collect();

            // All calculation happens inside the service
            $result = $service->calculatePlanAmount($membershipPlanArr, [
                'package_ids' => $addOnSelectedIds,
                'coupon_code' => $request->coupon_code ?? null,
            ]);

            $planAmount   = $result['plan_amount']   ?? 0;
            $planDiscount = $result['plan_discount'] ?? 0;
        }

        $addonAmount   = $result['add_on_amount']  ?? 0;
        $taxAmount     = $result['tax_amount']     ?? 0;
        $taxName       = $result['tax_name']       ?? '';
        $taxPercentage = $result['tax_percentage'] ?? 0;
        $grandTotal    = $result['grand_total']    ?? 0;
        $currencyCode  = $result['currency_code'] ?? ($membershipPlanArr->currency_code ?? ($siteConfigArr['default_currency'] ?? 'USD'));

        // NOTE: paymentArr now carries EVERYTHING the view needs to render,
        // so the view never has to re-derive numbers itself.
        $paymentArr = (object) [
            'planAmount'    => $planAmount,
            'planDiscount'  => $planDiscount,
            'addonAmount'   => $addonAmount,
            'taxName'       => $taxName,
            'taxPercentage' => $taxPercentage,
            'taxAmount'     => $taxAmount,
            'grand_total'   => $grandTotal,
            'currency_code' => $currencyCode,
        ];

        // Member's CURRENT active plan — passed to planData.blade so it can render a
        // "View Current Plan Details" popup, letting the admin see everything (usage,
        // tax, previously approved add-ons) without leaving this page.
        $currentPlanData = null;
        $currentPlanAddOnArr = collect();
        if (!empty($request->memberId)) {
            $currentPlanData = Payment::where('member_id', $request->memberId)->where('current_plan', 'Yes')->first();
            if ($currentPlanData) {
                $currentPlanData->can_chat = $currentPlanData->can_chat ? 'Yes' : 'No';
                $currentPlanAddOnArr = AddOnPayment::where('member_id', $currentPlanData->member_id)
                    ->where('payment_id', $currentPlanData->id)
                    ->where('status', 'APPROVED')
                    ->get();
            }
        }
        $currentPlanFieldsArr = $this->currentPlanFieldsArr();

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/planData', compact(
            'membershipPlanArr',
            'siteConfigArr',
            'addOnPackageArr',
            'addOnSelectedIds',
            'paymentArr',
            'assignType',
            'currentPlanData',
            'currentPlanAddOnArr',
            'currentPlanFieldsArr'
        ))->render();

        $responseArr = [
            'status' => 'success',
            'msg'    => 'Plan data retrieved successfully.',
            'html'   => $html,
            'data'   => [
                'planAmount'  => round($planAmount, 2),
                'addonAmount' => round($addonAmount, 2),
                'totalAmount' => round($grandTotal, 2)
            ]
        ];

        return response()->json($responseArr, 200);
    }

    ## Submit Form :
    public function editPlanUpdate(Request $request, UpgradeMembershipPlanService $assignPlanService)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];
        $postData = $request->all();

        if (empty($postData) || blank($postData)) {
            return response()->json($responseArr, 200);
        }

        if (empty($postData['memberId'])) {
            $responseArr['msg'] = _getConstant('responce_message.DATA_NOT_UPDATED');
            return response()->json($responseArr, 200);
        }

        $assignType = ($postData['assignType'] ?? 'new_plan') === 'addon_only' ? 'addon_only' : 'new_plan';

        // "new_plan" requires a selected plan. "addon_only" does NOT require a plan
        // (it is resolved from the member's current active plan) but does require
        // at least one add-on package.
        if ($assignType === 'new_plan' && empty($postData['plan_id'])) {
            $responseArr['msg'] = _getConstant('responce_message.DATA_NOT_UPDATED');
            return response()->json($responseArr, 200);
        }
        if ($assignType === 'addon_only' && empty($postData['add_on_id'])) {
            $responseArr['msg'] = 'Please select at least one add-on package.';
            return response()->json($responseArr, 200);
        }

        ## Staff Activity:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        if ($userType == 'Staff' || $userType == 'Franchise') {
            AdminCommonActionModel::addStaffFranchiseActivity($postData['memberId'], $userId, 'plan_updates', $userType);
        }

        $member = Register::where('id', $request->memberId)->first();
        if (!$member) {
            $responseArr['msg'] = _getConstant('responce_message.DATA_NOT_UPDATED');
            return response()->json($responseArr, 200);
        }

        $authenticatedUser = Auth::user();

        if ($assignType === 'addon_only') {

            ## Add-On ONLY assignment — attach add-ons to the member's existing active plan :
            $currentPayment = Payment::where('member_id', $member->id)->where('current_plan', 'Yes')->first();
            if (!$currentPayment) {
                $responseArr['msg'] = 'This member does not have an active plan. Please assign a new plan instead.';
                return response()->json($responseArr, 200);
            }

            // assignAddOnOnly() takes the CURRENT PAYMENT'S id (int), not a MembershipPlan —
            // it attaches add-ons directly onto that existing payment record.
            $assignPlan = $assignPlanService->assignAddOnOnly($member, $currentPayment->id, [
                'package_ids'  => $request->add_on_id ?? [],
                'coupon_code'  => $request->coupon_code ?? null,
                'payment_mode' => $request->payment_mode ?? '',
            ]);

            ## Add to admin Personalize Chat:
            $this->adminCommonActionModel->addPersonalizeMemberChat($postData['memberId']);

            ## Log Of Add-On Assignment :
            $insertLogArr = [
                'matri_id'            => $member->matri_id,
                'plan_name'           => $currentPayment->plan_name . ' (Add-On Only)',
                'plan_activate_date'  => _getCurrentDate(),
                'current_plan'        => 'Yes',
                'payment_from'        => $userType,
                'action_by_user_logs' => json_encode($authenticatedUser),
                'created_at'          => _getCurrentDate()
            ];
            // DB::table('admin_plan_assign_logs')->insert($insertLogArr);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = 'Add-on package(s) assigned successfully.';
            $responseArr['redirect'] = route('admin.member.index');
        } else {

            ## New Plan Assignment :
            $plan = MembershipPlan::findOrFail($request->plan_id);
            $assignPlan = $assignPlanService->assign($member, $plan, null, [
                'package_ids'  => $request->add_on_id ?? [],
                'payment_mode' => $request->payment_mode ?? '',
                'payment_note' => $request->payment_note ?? '',
                'assign_by'    => json_encode(['id' => $authenticatedUser->id, 'user_type' => $userType]),
            ]);

            ## Admin Plan Assigned Notification to user:
            app(NotificationService::class)->sendNotification(
                $member,
                $member,
                'admin_plan_assign'
            );

            ## Add to admin Personalize Chat:
            $this->adminCommonActionModel->addPersonalizeMemberChat($postData['memberId']);

            ## Log Of Payment :
            $insertLogArr = [
                'matri_id'            => $member->matri_id,
                'plan_name'           => $plan->plan_name,
                'plan_activate_date'  => _getCurrentDate(),
                'current_plan'        => 'Yes',
                'payment_from'        => $userType,
                'action_by_user_logs' => json_encode($authenticatedUser),
                'created_at'          => _getCurrentDate()
            ];
            // DB::table('admin_plan_assign_logs')->insert($insertLogArr);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
            $responseArr['redirect'] = route('admin.member.index');
        }

        return response()->json($responseArr, 200);
    }

    ## Admin Current Plan : (unchanged from your original — kept here for reference only)
    public function currentPlan($id = 0)
    {
        $resultArr = Payment::where('member_id', $id)->where('current_plan', 'Yes')->first();
        if ($resultArr) {
            $resultArr->can_chat = $resultArr->can_chat ? 'Yes' : 'No';
        }

        $recentPlanArr = Payment::where('member_id', $id)->where('current_plan', 'No')->get();
        foreach ($recentPlanArr as $key => $value) {
            $value->can_chat = $value->can_chat ? 'Yes' : 'No';

            $recentPlanaddOnArr = AddOnPayment::where('member_id', $value->member_id)->where('payment_id', $value->id)->where('status', 'APPROVED')->get();
            $recentPlanArr[$key]->addOnPlanArr = $recentPlanaddOnArr;
        }
        $dataArr = [
            'pageName' => $this->pageName,
            'resultArr' => $resultArr,
            'recentPlanArr' => $recentPlanArr,
            'currentPlanArr' => $this->currentPlanFieldsArr(),
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/currentPlan', $dataArr);
    }

    ## Shared field/label config for rendering a plan's details.
    ## Used by currentPlan() (full page) AND getPlanData() (the popup in planData.blade) :
    private function currentPlanFieldsArr()
    {
        return [
            'plan_name' => ['label' => 'Plan Name', 'type' => 'str'],
            'plan_type' => ['label' => 'Plan Type', 'type' => 'str'],
            'plan_activate_date' => ['label' => 'Plan Activated', 'type' => 'date'],
            'plan_expiry_date' => ['label' => 'Plan Expired', 'type' => 'date'],
            'plan_amount' => ['label' => 'Plan Amount', 'type' => 'str'],
            'plan_discount' => ['label' => 'Plan Discount (%)', 'type' => 'str'],
            'currency_code' => ['label' => 'Plan Currency', 'type' => 'str'],
            'total_validity_days' => ['label' => 'Plan Validity (In Days)', 'type' => 'str'],
            'current_plan' => ['label' => 'Current Plan', 'type' => 'str'],
            'payment_note' => ['label' => 'Payment Note', 'type' => 'str'],
            'payment_mode' => ['label' => 'Payments Mode', 'type' => 'str'],
            'plan_description' => ['label' => 'Plan Description', 'type' => 'str'],
            'can_chat' => ['label' => 'Plan Chat', 'type' => 'str'],
            'view_profile_total' => [
                'label' => 'Plan View Profile Used',
                'type' => 'used',
                'field' => 'view_profile_remaining'
            ],
            'interests_total' => [
                'label' => 'Plan Interest Used',
                'type' => 'used',
                'field' => 'interests_used'
            ],
            'contact_views_total' => [
                'label' => 'Plan Contact Used',
                'type' => 'used',
                'field' => 'contact_views_used'
            ],
            'video_minutes_total' => [
                'label' => 'Plan Video Call Used',
                'type' => 'used',
                'field' => 'video_minutes_used'
            ],
            'audio_minutes_total' => [
                'label' => 'Plan Video Call Used',
                'type' => 'used',
                'field' => 'audio_minutes_used'
            ],
            'discount_detail' => ['label' => 'Coupon Code ', 'type' => 'str'],
            'tax_amount' => ['label' => 'Tax Amount', 'type' => 'str'],
            'tax_name' => ['label' => 'Tax Name', 'type' => 'str'],
            'tax_percentage' => ['label' => 'Tax Percentage', 'type' => 'per'],
            'discount_amount' => ['label' => 'Discount Amount (%)', 'type' => 'str'],
            'grand_total' => ['label' => 'Grand Amount', 'type' => 'str'],
        ];
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
            ## Comment Member Data Arr List:
            $whereArr['member_id'] = $postData['id'];
            $resultDataArr = CommentMaster::with(['staff'])->where('member_id', $postData['id'])->latest()->get();

            ## MemberData List:
            $memberData = Register::select('id', 'fullname', 'email', 'mobile')
                ->where('id', $postData['id'])->first();

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/viewCommentPopup',
                compact('resultDataArr', 'memberData')
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

            ## MemberData List:
            $memberData = Register::select('id', 'fullname', 'email', 'mobile')->where('id', $postData['id'])->first();

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
                compact('commentData', 'memberData')
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
            ## Update Old Followp:
            CommentMaster::where('member_id', $postData['member_id'])->update(['follow_up_status' => 0]);

            ## Insert Data
            $insertArr = [
                'member_id'         => $postData['member_id'],
                'posted_user_type'  => $postData['commented_user_type'],
                'posted_by'         => $postData['posted_by'],
                'comment'           => $postData['comment'],
                'created_at'        => _getCurrentDate(),
                'next_followup_date' => !empty($postData['next_followup_date']) ? Carbon::parse($postData['next_followup_date'])->format('Y-m-d H:i:s') : null,
                'follow_up_status'  => 1,
            ];

            CommentMaster::create($insertArr);

            ## Staff Activity:
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);

            if ($userType == 'Staff' || $userType == 'Franchise') {
                AdminCommonActionModel::addStaffFranchiseActivity($postData['member_id'], $userId, 'add_comments', $userType);
            }

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_UPDATED_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## Add Filter :
    public function getFilter(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $currentDate = _getCurrentDate('Y-m-d');
            $elementArr = array(
                'gender' => array(
                    'display_in' => '2',
                    'type' => 'radio',
                    'value_arr' => ['All' => 'All', 'Male' => 'Male', 'Female' => 'Female'],
                    'value' => 'All'
                ),
                'user_type' => array(
                    'display_in' => '2',
                    'type' => 'radio',
                    'value_arr' => ['All' => 'All', '0' => 'Online', '1' => 'Personalize'],
                    'value' => 'All'
                ),
                'keyword' => array('label' => 'Search with Name, Matri Id, Email, Mobile'),
                'frm_age' => array(
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'value_arr' => _ageRang(),
                    'label' => "Age Range From"
                ),
                'to_age' => array(
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'value_arr' => _ageRang(),
                    'label' => "Age Range To"
                ),
                'height' => array(
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'value_arr' => _heightList(),
                    'label' => "Height Range From"
                ),
                'height_to' => array(
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'value_arr' => _heightList(),
                    'label' => "Height Range To",
                ),
                'registered_from' => array(
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => "Registered From"
                ),
                'registered_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Registered To"
                ),
                'plan_expired_from' => array(
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => "Plan Expired From"
                ),
                'plan_expired_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Plan Expired To"
                ),
                'plan_id' => array(
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Membership Plan',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'MembershipPlan',
                        'key_val' => 'id',
                        'key_disp' => 'plan_name'
                    )
                ),
                'plan_status' => array(
                    'is_register' => 'yes',
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'class' => 'single',
                    'value_arr' => array(
                        'Not Paid' => 'Not Paid',
                        'Paid' => 'Paid',
                        'Expired ' => 'Expired '
                    )
                ),
                'mother_tongue' => array(
                    'class' => 'single not_reset  ',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Mother Tongue',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'MotherTongueMaster',
                        'key_val' => 'id',
                        'key_disp' => 'mtongue_name'
                    )
                ),
                'marital_status' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'MaritalStatusMaster',
                        'key_val' => 'id',
                        'key_disp' => 'marital_status_name'
                    ),
                ),
                'education_level' => array(
                    'is_multiple' => 'yes',
                    'label' => 'Education',
                    'type' => 'dropdown',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'EducationMaster',
                        'key_val' => 'id',
                        'class' => '',
                        'key_disp' => 'education_name'
                    )
                ),
                'income' => array(
                    'type' => 'dropdown',
                    'class' => 'single',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'relation' => array(
                        'rel_model' => 'AnnualIncomeMaster',
                        'key_val' => 'id',
                        'key_disp' => 'annual_income_name'
                    ),
                    'label' => 'Annual Income',
                    'is_register' => 'yes',
                ),
                'religion' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'onchange' => "dropdownChange('religion','Caste','caste_list')",
                    'class' => 'single ',
                    'relation' => array(
                        'rel_model' => 'ReligionMaster',
                        'key_val' => 'id',
                        'class' => '',
                        'key_disp' => 'religion_name'
                    )
                ),
                'caste' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'class' => 'single ',
                    'relation' => array(
                        'rel_model' => 'CasteMaster',
                        'key_val' => 'id',
                        'key_disp' => 'caste_name',
                        'rel_col_name' => 'religion_id',
                        'not_load_add' => 'yes',
                        'cus_rel_col_val' => 'religion'
                    )
                ),
                'country_id' => array(
                    'class' => ' not_reset single',
                    'label' => 'Country',
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'onchange' => "dropdownChange('country_id','state_id','state_list')",
                    'relation' => array(
                        'rel_model' => 'CountryMaster',
                        'key_val' => 'id',
                        'key_disp' => 'country_name'
                    )
                ),
                'state_id' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'StateMaster',
                        'key_val' => 'id',
                        'key_disp' => 'state_name',
                        'not_load_add' => 'yes',
                        'cus_rel_col_name' => 'country_id'
                    ),
                    'label' => 'State',
                    'class' => 'single',
                    'onchange' => "dropdownChange('state_id','city','city_list')"
                ),
                'city' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'CityMaster',
                        'key_val' => 'id',
                        'key_disp' => 'city_name',
                        'not_load_add' => 'yes',
                        'cus_rel_col_name' => 'state_id'
                    ),
                    'label' => 'City'
                ),
                'manglik' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'ManglikMaster',
                        'key_val' => 'id',
                        'key_disp' => 'manglik_name'
                    ),
                    'label' => 'Manglik',
                    'is_register' => 'yes'
                ),
                'occupation' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'class' => 'single required',
                    'relation' => array(
                        'rel_model' => 'OccupationMaster',
                        'key_val' => 'id',
                        'class' => '',
                        'key_disp' => 'occupation_name'
                    ),
                    'label' => 'Occupation'
                ),
                'diet' => array(
                    'label' => 'Eating Habits',
                    'class' => 'single ',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'relation' => array(
                        'rel_model' => 'EatingHabitMaster',
                        'key_val' => 'id',
                        'key_disp' => 'eating_habit_name'
                    )
                ),
                'smoke' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'class' => 'single ',
                    'display_placeholder' => 'No',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'SmokingHabitMaster',
                        'key_val' => 'id',
                        'key_disp' => 'smoking_habit_name'
                    )
                ),
                'drink' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'class' => 'single ',
                    'display_placeholder' => 'No',
                    'label' => 'Drinking Habit',
                    'relation' => array(
                        'rel_model' => 'DrinkingHabitMaster',
                        'key_val' => 'id',
                        'key_disp' => 'drinking_habit_name'
                    ),
                    'is_register' => 'yes'
                ),
                'profileby' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'ProfileByMaster',
                        'key_val' => 'id',
                        'key_disp' => 'profileby_name'
                    ),
                    'label' => 'Profile By',
                    'is_register' => 'yes'
                ),
                'family_type' => array(
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'class' => 'single ',
                    'relation' => array(
                        'rel_model' => 'FamilyTypeMaster',
                        'key_val' => 'id',
                        'key_disp' => 'family_type_name'
                    ),
                    'is_register' => 'yes'
                ),
                'family_status' => array(
                    'is_register' => 'yes',
                    'type' => 'dropdown',
                    'is_multiple' => 'yes',
                    'label' => 'Family Status',
                    'display_placeholder' => 'No',
                    'class' => 'single',
                    'relation' => array(
                        'rel_model' => 'FamilyStatusMaster',
                        'key_val' => 'id',
                        'key_disp' => 'family_status_name'
                    )
                ),
                'staff_id' => array(
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Staff List',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'Staff',
                        'key_val' => 'id',
                        'key_disp' => 'username',
                        'rel_col_name' => 'type',
                        'rel_col_val' => 'Staff'
                    )
                ),
                'franchise_id' => array(
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Franchise List',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'Franchise',
                        'key_val' => 'id',
                        'key_disp' => 'username',
                        'rel_col_name' => 'type',
                        'rel_col_val' => 'Franchise'
                    )
                ),
            );

            ## Check Roles Permission:
            $authUser = Auth::user();
            $userType = _adminUserType($authUser->type);
            if ($userType != 'Admin') {
                unset($elementArr['staff_id']);
                unset($elementArr['franchise_id']);
            }

            $otherData = [
                'columnName' => '*',
                'rowData' => [],
            ];
            $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.member.getAjaxPaginationData',
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

    ## Member PDF Download :
    public function downloadBiodataPdf($memberId)
    {
        $memberId = base64_decode($memberId);
        $memberData = Register::where('id', $memberId)->first();
        $memberData->birthdate = $memberData->birthdate ? Carbon::parse($memberData->birthdate)->format('d-m-Y') : null;

        $dataArr = [
            'resultArr' => $memberData,
            'dataStep1' => [
                'Basic Details' => [
                    'Full Name' => $memberData->fullname,
                    'Gender' => $memberData->gender,
                    'Date of birth' => $memberData->birthdate ? Carbon::parse($memberData->birthdate)->format('d-m-Y') : null,
                    'Marital Status' => $memberData->maritalStatusData->marital_status_name ?? null,
                    'Mother Tongue' => $memberData->motherTongueData->mtongue_name ?? null,
                ],
            ],
            'dataStep2' => [
                'Religious Information' => [
                    'Religion' => $memberData->religionData->religion_name ?? null,
                    'Caste' => $memberData->casteData->caste_name ?? null,
                    'Sub Caste' => $memberData->subcaste ?? null,
                    'Manglik' => $memberData->manglikData->manglik_name ?? null,
                    'Gothra' => $memberData->gothra ?? null,
                    'Moonsign' => $memberData->moonsignData->moonsign_name ?? null,
                    'Star' => $memberData->starData->star_name ?? null
                ],
                'Education & Other Details' => [
                    'Education' => implode(', ', $memberData->education_level_names) ?? null,
                    'Education Details' => $memberData->education_details ?? null,
                    'Occupation' => $memberData->occupationData->occupation_name ?? null,
                    'Annual Income' => $memberData->incomeData->annual_income_name ?? null,
                ],
                'Location Details' => [
                    'Country' => $memberData->countryData->country_name ?? null,
                    'State' => $memberData->stateData->state_name ?? null,
                    'City' => $memberData->cityData->city_name ?? null,
                ],
                'Physical Information' => [
                    'Height' => _displayHeight($memberData->height) ?? null,
                    'Weight' => $memberData->weight ? $memberData->weight . ' Kg' : null,
                    'Eating Habits' => $memberData->dietData->eating_habit_name ?? null,
                    'Smoking Habit' => $memberData->smokeData->smoking_habit_name ?? null,
                    'Drinking Habit' => $memberData->drinkingData->drinking_habit_name ?? null,
                ],
            ]
        ];

        $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/memberPdf';
        $pdf = Pdf::loadView($viewPath, compact('dataArr'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => storage_path(),
                'defaultFont' => 'Poppins',
                'isHtml5ParserEnabled' => true,
            ]);

        $fileName = $memberData->matri_id . '-biodata';
        return $pdf->download($fileName . '.pdf');
    }

    public function sendConfirmationEmail(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData['id'])) {
            $memberId = base64_decode($postData['id']);

            $member = Register::where('id', $memberId)->select(['id', 'matri_id', 'fullname', 'email', 'mobile'])->first();

            ## Send Confirmation Email :
            $plainToken = Str::random(64);
            $member->update([
                'email_verification_token' => hash('sha256', $plainToken), // store hashed
                'email_verification_token_expires_at' => Carbon::now()->addDays(3),
            ]);
            $confirmLink = route('web.confirm.email', ['token' => $plainToken]);

            $replaceArr = [
                'user_name'  => $member->fullname,
                'user_matri_id' => $member->matri_id,
                'user_email' => $member->email,
                'confirmation_url' => $confirmLink
            ];
            app(EmailSendService::class)->send('Email Confirmation', $member->email, $replaceArr, ['memberData' => $member]);

            $responseArr['status'] = 'success';
            $responseArr['msg'] = 'Email sent successfully.';
        }
        return response()->json($responseArr, 200);
    }

    ## Verify Mobile Verification:
    public function verifyMobileEmail(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData['id'])) {
            $memberId = base64_decode($postData['id']);

            if ($postData['type'] == 'email') {
                $affected = Register::where('id', $memberId)->where('email_verify_status', '!=', 'Verify')->update(['email_verify_status' => 'Verify']);
                $message = $affected ? 'Email Verified Successfully.' : 'Email already verified.';
            } else {
                $affected = Register::where('id', $memberId)->where('mobile_verify_status', '!=', 'Yes')->update(['mobile_verify_status' => 'Yes']);
                $message = $affected ? 'Mobile Verified Successfully.' : 'Mobile already verified.';
            }

            $responseArr['status'] = 'success';
            $responseArr['msg'] = $message ?? 'Data updated successfully.';
            $responseArr['memberId'] = $memberId;
            $responseArr['type'] = $postData['type'];
        }
        return response()->json($responseArr, 200);
    }

    public function downloadReport(Request $request)
    {
        $postData = $request->all();

        $query = Register::query();
        if (!blank($postData['filedownloadDate'])) {
            [$fromDate, $toDate] = explode(' - ', $postData['filedownloadDate']);
            $query->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $resultDataArr = $query->orderByDesc('id')->get();
        if (blank($resultDataArr)) {
            return redirect()->route('admin.member.index')->with('error', _getConstant('responce_message.NO_DATA_FOUND'));
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Register> $resultDataArr */
        $csvFileArr = [];
        foreach ($resultDataArr as $value) {
            if (_getConstant('DISABLE_DEMO') === 'Enabled') {
                $value->email = _getConstant('DISABLE_IN_DEMO_LABEL');
                $value->mobile = _getConstant('DISABLE_IN_DEMO_LABEL');
            }
            if (!empty($value->birthdate)) {
                $value->birthdate = _birthdateDisplay($value->birthdate, 0) ?? null;
            }
            $value->marital_status = $value->maritalStatusData->marital_status_name ?? null;
            $value->education_level = implode(', ', $value->education_level_names) ?? null;
            $value->occupation = $value->occupationData->occupation_name ?? null;
            $value->religion = $value->religionData->religion_name ?? null;
            $value->caste = $value->casteData->caste_name ?? null;
            $value->mother_tongue = $value->motherTongueData->mtongue_name ?? null;
            $value->country_id = $value->countryData->country_name ?? null;
            $value->state_id = $value->stateData->state_name ?? null;
            $value->city = $value->cityData->city_name ?? null;
            $value->diet = $value->dietData->eating_habit_name ?? null;
            if (!empty($value->height)) {
                $value->height = _displayHeight($value->height);
            } else {
                $value->height = '';
            }
            $value->income = $value->incomeData->annual_income_name ?? null;
            $value->smoke = $value->smokeData->smoking_habit_name ?? null;
            $value->drink = $value->drinkingData->drinking_habit_name ?? null;

            ## Csv Order WIse Array :
            $csvFileArr[] = [
                'matri_id'        => $value->matri_id,
                'fullname'        => $value->fullname,
                'gender'          => $value->gender,
                'email'           => $value->email,
                'mobile'          => $value->mobile,
                'birthdate'       => $value->birthdate,
                'marital_status'  => $value->marital_status,
                'education_level' => $value->education_level,
                'occupation'      => $value->occupation,
                'income'          => $value->income,
                'religion'        => $value->religion,
                'caste'           => $value->caste,
                'mother_tongue'   => $value->mother_tongue,
                'country'         => $value->country_id,
                'state'           => $value->state_id,
                'city'            => $value->city,
                'height'          => $value->height,
            ];
        }

        ## Heading :
        $heading = [
            'Matri Id',
            'Full Name',
            'Gender',
            'Email',
            'Mobile Number',
            'Date of Birth',
            'Marital Status',
            'Education Name',
            'Occupation Name',
            'Income',
            'Religion',
            'Caste',
            'Mother Tongue',
            'Country Name',
            'State Name',
            'City Name',
            'Height',
        ];

        $currentDate = _getCurrentDate('d-m-Y H:i:s');
        $fileName = 'Member Reports (' . $currentDate . ')';

        if ($postData['downloadFormat'] == 'PDF') {
            $dataArr = [
                'title' => 'Member Reports',
                'resultDataArr' => $resultDataArr
            ];
            $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/pdf_format';
            $pdf = PDF::loadView($viewPath, compact('dataArr'))->setOptions(['defaultFont' => 'Helvetica', 'isRemoteEnabled' => true, 'chroot' => public_path()])->setPaper('A4', 'portrait');
            return $pdf->download($fileName . '.pdf');
        } elseif ($postData['downloadFormat'] == 'CSV') {
            return Excel::download(new CommonAdminExport(collect($csvFileArr), $heading, ['created_at']), $fileName . '.xlsx');
        }
    }

    public function addAffiliateMemberIncome($memberData)
    {
        $rows = [];
        $verificationAffiliateId = null;

        // Verified Profile Commission
        if (!empty($memberData->affiliate_member_id)) {
            $affiliate = AffiliateMember::active()
                ->select('id', 'verify_profile_commission')
                ->find($memberData->affiliate_member_id);

            if ($affiliate && $affiliate->verify_profile_commission > 0) {
                $exists = AffiliateMemberIncome::where([
                    'affiliate_member_id' => $affiliate->id,
                    'member_id'           => $memberData->id,
                    'income_type'         => 'Verified Profile',
                ])->exists();

                if (!$exists) {
                    $rows[] = [
                        'affiliate_member_id' => $affiliate->id,
                        'member_id'           => $memberData->id,
                        'amount'              => $affiliate->verify_profile_commission,
                        'is_transfered'       => 0,
                        'income_type'         => 'Verified Profile',
                        'status'              => 1,
                        'created_at'          => now(),
                    ];
                }
            }
        }

        // On Field Verification Commission
        if (!empty($memberData->verification_affiliate_member_id)) {

            $affiliate = AffiliateMember::active()
                ->select('id', 'on_field_verify_profile_commission')
                ->find($memberData->verification_affiliate_member_id);

            if ($affiliate && $affiliate->on_field_verify_profile_commission > 0) {

                $exists = AffiliateMemberIncome::where([
                    'affiliate_member_id' => $affiliate->id,
                    'member_id'           => $memberData->id,
                    'income_type'         => 'On Field Verification Profile',
                ])->exists();

                if (!$exists) {
                    $rows[] = [
                        'affiliate_member_id' => $affiliate->id,
                        'member_id'           => $memberData->id,
                        'amount'              => $affiliate->on_field_verify_profile_commission,
                        'is_transfered'       => 0,
                        'income_type'         => 'On Field Verification Profile',
                        'status'              => 1,
                        'created_at'          => now(),
                    ];
                }

                // Update Affiliate Assign Member
                $assign = AffiliateMemberAssign::firstOrNew([
                    'affiliate_member_id' => $affiliate->id,
                    'member_id'           => $memberData->id,
                ]);

                $assign->is_verify   = true;
                $assign->status      = 1;
                $assign->verify_date = now();
                $assign->save();

                // Keep ID for updating after successful insert
                $verificationAffiliateId = $affiliate->id;
            }
        }

        // Bulk Insert
        if (!empty($rows)) {
            AffiliateMemberIncome::insert($rows);
        }
    }
    // public function addAffiliateMemberIncome($memberData)
    // {
    //     $rows = [];
    //     ## Verified Profile Commission :
    //     if (!empty($memberData->affiliate_member_id)) {
    //         $affiliate = AffiliateMember::active()->select('id', 'verify_profile_commission')->find($memberData->affiliate_member_id);
    //         if ($affiliate && $affiliate->verify_profile_commission > 0) {
    //             $exists = AffiliateMemberIncome::where([
    //                 'affiliate_member_id' => $affiliate->id,
    //                 'member_id'           => $memberData->id,
    //                 'income_type'         => 'Verified Profile',
    //             ])->exists();
    //             if (!$exists) {
    //                 $rows[] = [
    //                     'affiliate_member_id' => $affiliate->id,
    //                     'member_id'           => $memberData->id,
    //                     'amount'              => $affiliate->verify_profile_commission,
    //                     'is_transfered'       => 0,
    //                     'income_type'         => 'Verified Profile',
    //                     'status'              => 1,
    //                     'created_at'          => now(),
    //                 ];
    //             }
    //         }
    //     }
    //     ## On Field Verification Commission :
    //     if (!empty($memberData->verification_affiliate_member_id)) {
    //         $affiliate = AffiliateMember::active()->select('id', 'on_field_verify_profile_commission')->find($memberData->verification_affiliate_member_id);
    //         if ($affiliate && $affiliate->on_field_verify_profile_commission > 0) {
    //             $exists = AffiliateMemberIncome::where([
    //                 'affiliate_member_id' => $affiliate->id,
    //                 'member_id'           => $memberData->id,
    //                 'income_type'         => 'On Field Verification Profile',
    //             ])->exists();
    //             if (!$exists) {
    //                 $rows[] = [
    //                     'affiliate_member_id' => $affiliate->id,
    //                     'member_id'           => $memberData->id,
    //                     'amount'              => $affiliate->on_field_verify_profile_commission,
    //                     'is_transfered'       => 0,
    //                     'income_type'         => 'On Field Verification Profile',
    //                     'status'              => 1,
    //                     'created_at'          => now(),
    //                 ];
    //             }

    //             ## Update Affiliate Assign Member :
    //             $assign = AffiliateMemberAssign::firstOrNew([
    //                 'affiliate_member_id' => $affiliate->id,
    //                 'member_id' => $memberData->id,
    //             ]);
    //             $assign->is_verify   = true;
    //             $assign->status      = 1;
    //             $assign->verify_date = now();
    //             $assign->save();
    //         }
    //     }

    //     ## Bulk Insert :
    //     if (!empty($rows)) {
    //         AffiliateMemberIncome::insert($rows);
    //     }
    // }

    public function generateAiAboutMe(Request $request, AiGenerateService $aiService): JsonResponse
    {
        try {
            $memberData = Register::where('id', $request->id)->first();

            $aboutMe = $aiService->generate('about_me', $memberData);

            if (!$aboutMe) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.msg_about_me_generate_with_ai_failed_message'),
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $aboutMe,
                'message' => __('messages.msg_about_me_generate_with_ai_success_message'),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.msg_about_me_generate_with_ai_failed_message'),
                'data' => []
            ], 200);
        }
    }
}

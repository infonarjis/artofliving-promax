<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use Illuminate\Http\Request;
## Services
use App\Services\AdminCommonActionModel;
use App\Models\Franchise;
use App\Models\FranchiseActivity;
use App\Models\Register;
use App\Models\Staff;
use App\Models\StaffActivity;
use App\Services\Admin\MatchMakingCountService;
use Illuminate\Support\Facades\Auth;

class PersonalizeMemberController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $customJsDirectory;
    private $pageName;
    private $statusTabArr;
    public function __construct()
    {
        ## Check Admin Access:
        // $this->middleware(
        //     fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        // );
        
        $this->directoryName = '/member';
        $this->searchColumn = ['fullname', 'email', 'matri_id', 'gender', 'mobile'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Manage Personlize Member';
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
        $memberPermission = _checkPermission($userType, $roleId, 'view_member');
        if ($memberPermission == 'No') {
            return redirect()->route('admin.dashboard');
        }

        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('member_register', AdminAlert::STATUS_READ);

        ## Dashboard Data Filter:
        $setDashboardData = $request->session()->get('setDashboardData');
        if (!blank($setDashboardData) && $setDashboardData == 'Yes') {
            session()->forget(['setDashboardData', 'dashboardKey', 'dashboardValue']);
        } else {
            $dashboardKey = $request->session()->get('dashboardKey');
            $dashboardValue = $request->session()->get('dashboardValue');
            $tabMap = [
                'plan_status:Paid' => 'paidTab',
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
        if (isset($authUserType) && $authUserType == 'Admin') {
            $userType = 'Admin';
            $assign = 1;
        } elseif (isset($authUserType) && $authUserType == 'Franchise') {
            $userType = 'Admin';
            $assign = 0;
        } else {
            $assign = 0;
        }
        ## Staff Role Data :
        $addBtnPermission = _checkPermission($userType, $roleId, 'add_member');
        $addBtn = 0;
        if ($addBtnPermission != 'No') {
            $addBtn = 1;
        }
        $deleteBtnPermission = _checkPermission($userType, $roleId, 'delete_member');
        $deleteBtn = 0;
        if ($deleteBtnPermission != 'No') {
            $deleteBtn = 1;
        }
        $approveBtnPermission = _checkPermission($userType, $roleId, 'approve_member');
        $approveBtn = 0;
        if ($approveBtnPermission != 'No') {
            $approveBtn = 1;
        }
        $unApproveBtnPermission = _checkPermission($userType, $roleId, 'unapprove_member');
        $unApproveBtn = 0;
        if ($unApproveBtnPermission != 'No') {
            $unApproveBtn = 1;
        }
        $suspendBtnPermission = _checkPermission($userType, $roleId, 'suspend_member');
        $suspendBtn = 0;
        if ($suspendBtnPermission != 'No') {
            $suspendBtn = 1;
        }
        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.personalizeMember.getAjaxPaginationData',
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
                'downloadBtn' => 1,
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
        $htmlDataArr = [];
        if (isset($postData) && !blank($postData)) {
            $page = 1;
            $limit = 10;
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
            ## Check Search Keyword :
            $whereStr = '';
            $whereArr = [];
            if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
                $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
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

            ## Dashboard Data Filter:
            $dashboardKey = $request->session()->get('dashboardKey');
            $dashboardValue = $request->session()->get('dashboardValue');
            if (isset($dashboardValue) && $dashboardValue != '' && isset($dashboardKey) && $dashboardKey != '') {
                session(['setDashboardData' => 'Yes']);
                $whereArr[$dashboardKey] = $dashboardValue;
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
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $roleId = _adminRoleId($authUser, $userType);
            $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');
            $whereArrStr = '';
            if ($addBtnPermission == 'Own Members' && $userId != '' && $userType == 'Staff') {
                if (blank($whereStr)) {
                    $whereArrStr = "(staff_assign_id = $userId)";
                } else {
                    $whereArrStr .= " AND (staff_assign_id = $userId)";
                }
            }

            ## Franchise member Check:
            if ($userType == 'Franchise' && !blank($userId)) {
                if (blank($whereStr)) {
                    $whereArrStr = "(franchise_assign_id = $userId)";
                } else {
                    $whereArrStr .= " AND (franchise_assign_id = $userId)";
                }
            }

            if (blank($whereStr)) {
                $whereStr = "$whereArrStr";
            } else {
                $whereStr .= "$whereArrStr";
            }

            ## User Type :
            $whereArr['user_type'] = 1;
            ## Tab Wise Count Data :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

            ## Result Arr :
            $resultArr = Register::query()
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

            ## Button Permission Access :
            ## Staff Role Data :
            $matchPermission = _checkPermission($userType, $roleId, 'match_making');
            $matchMakingBtn = 0;
            if ($matchPermission != 'No') {
                $matchMakingBtn = 1;
            }
            $addCommentBtnPermission = _checkPermission($userType, $roleId, 'add_comment');
            $addCommentBtn = 0;
            if ($addCommentBtnPermission != 'No') {
                $addCommentBtn = 1;
            }
            $viewCommentBtnPermission = _checkPermission($userType, $roleId, 'view_comment');
            $viewCommentBtn = 0;
            if ($viewCommentBtnPermission != 'No') {
                $viewCommentBtn = 1;
            }
            $editBtnPermission = _checkPermission($userType, $roleId, 'edit_member');
            $editBtn = 0;
            if ($editBtnPermission != 'No') {
                $editBtn = 1;
            }
            $viewBtnPermission = _checkPermission($userType, $roleId, 'view_profile');
            $viewBtn = 0;
            if ($viewBtnPermission != 'No') {
                $viewBtn = 1;
            }
            $activeToPaidBtn = _checkPermission($userType, $roleId, 'active_to_paid_member') != 'No' ? 1 : 0;

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
                    'match_making' => $matchMakingBtn,
                    'addComment' => $addCommentBtn,
                    'viewComment' => $viewCommentBtn,
                    'confirmEmail' => 1,
                    'view' => $viewBtn,
                    'edit' => $editBtn,
                    'downloadBiodatabtn' => 1,
                    'activeToPaidBtn' => $activeToPaidBtn
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
                            'assign_to_staff' => [
                                'label' => 'Assign to Staff',
                                'type' => 'str',
                            ],
                            'last_login' => [
                                'label' => 'Last Login',
                                'type' => 'date',
                            ]
                        ],
                        'center' => [
                            'user_type' => [
                                'label' => 'User Type',
                                'type' => 'user_type',
                            ],
                            'mobile' => [
                                'label' => 'Mobile No',
                                'type' => 'str',
                            ],
                            'religion_name' => [
                                'label' => 'Religion',
                                'type' => 'str',
                            ],
                            'marital_status_name' => [
                                'label' => 'Marital Status',
                                'type' => 'str',
                            ],
                            'plan_expired_on' => [
                                'label' => 'Plan Expired',
                                'type' => 'date',
                            ],
                            'assign_to_franchise' => [
                                'label' => 'Assign to Franchise',
                                'type' => 'str',
                            ],
                            'created_at' => [
                                'label' => 'Registered On',
                                'type' => 'date',
                            ]
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

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = [];
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            ## User Type :
            $whereArr['user_type'] = 1;
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
}

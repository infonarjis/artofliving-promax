<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Staff;
use App\Services\Admin\MatchMakingCountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeaturedMemberController extends Controller
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
        $this->directoryName = '/member';
        $this->searchColumn = ['fullname', 'email', 'matri_id', 'gender', 'mobile'];
        $this->statusTabArr = [
            'featuredTab' => [
                'isActive' => 1,
                'label' => 'Featured Member',
                'id' => 'featuredData',
                'class' => '',
                'conditionVal' => 'Featured',
                'conditionColumn' => 'fstatus'
            ],
            'unFeaturedTab' => [
                'label' => 'UnFeatured Member',
                'id' => 'unFeaturedData',
                'class' => '',
                'conditionVal' => 'Unfeatured',
                'conditionColumn' => 'fstatus'
            ],
        ];
    }

    /**
     * Display the Paid Active Member list page
     */
    public function index()
    {
        $extraJsArr = ['/custom/js' . $this->directoryName . '/list.js'];
        $staffListArr = Staff::active()->get();

        $dataArr = [
            'pageName' => 'Member Featured Member',
            'ajaxPaginationRequestUrl' => 'admin.featuredMember.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.member.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => $staffListArr,
            'franchiseListArr' => [],
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'suspend' => 1,
                'isSearch' => 1,
                'isAssign' => 0,
                'verifyBtn' => 1,
                'fstatus' => 1,
                'filter' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.member.addForm',
                'edit' => 'admin.member.editForm',
                'view' => 'admin.member.viewDetails',
                'editPlan' => 'admin.member.editPlan',
                'currentPlan' => 'admin.member.currentPlan',
                'viewComment' => 'admin.member.viewComment',
                'addComment' => 'admin.member.addComment',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    /**
     * AJAX Pagination and Filter
     */
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => []
        ];

        $postData = $request->all();
        if (!isset($postData) || blank($postData)) {
            return response()->json($responseArr, 200);
        }

        $page = $postData['page'] ?? 1;
        $limit = $postData['limit'] ?? 10;

        // Order
        $orderList = explode('-', $postData['order'] ?? '');
        $orderBy = $orderList[0] ?? 'id';
        $orderByType = strtoupper($orderList[1] ?? 'DESC');

        // Filters
        $whereArr = [];
        if (!blank($postData['conditionColumn']) && !blank($postData['conditionVal'])) {
            $whereArr['status'] = 'APPROVED';
            $whereArr['plan_status'] = 'Paid';
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }

        // Dashboard filters
        $dashboardKey = $request->session()->get('dashboardKey');
        $dashboardValue = $request->session()->get('dashboardValue');
        if (!blank($dashboardKey) && !blank($dashboardValue)) {
            session(['setDashboardData' => 'Yes']);
            $whereArr[$dashboardKey] = $dashboardValue;
        }

        // Filter string
        $whereStr = '';
        if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
            $whereStr = _getAdminFilterWhereStr($postData);
        }

        // Search keyword
        if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {
            $searchKeyword = $postData['searchKeyword'];
            $searchParts = [];
            foreach ($this->searchColumn as $col) {
                $searchParts[] = "$col like '%$searchKeyword%'";
            }
            $keywordStr = implode(' OR ', $searchParts);
            $whereStr .= blank($whereStr) ? "($keywordStr)" : " AND ($keywordStr)";
        }

        ## Check Roles Permission:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);

        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');
        if ($addBtnPermission == 'Own Members' && $userType == 'Staff') {
            $whereStr .= blank($whereStr) ? "(staff_assign_id = $userId)" : " AND (staff_assign_id = $userId)";
        }

        if ($userType == 'Franchise') {
            $whereStr .= blank($whereStr) ? "(franchise_assign_id = $userId)" : " AND (franchise_assign_id = $userId)";
        }

        // Tab-wise counts
        $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);

        // Data
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

        // Permission buttons
        $matchMakingBtn = _checkPermission($userType, $roleId, 'match_making') != 'No' ? 1 : 0;
        $addCommentBtn = _checkPermission($userType, $roleId, 'add_comment') != 'No' ? 1 : 0;
        $viewCommentBtn = _checkPermission($userType, $roleId, 'view_comment') != 'No' ? 1 : 0;
        $editBtn = _checkPermission($userType, $roleId, 'edit_member') != 'No' ? 1 : 0;
        $viewBtn = _checkPermission($userType, $roleId, 'view_profile') != 'No' ? 1 : 0;
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
                'downloadBiodata' => 'admin.member.downloadBiodataPdf'
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
                    'matri_id' => ['label' => 'Matri Id', 'type' => 'str'],
                ],
                'field' => [
                    'left' => [
                        'fullname' => ['label' => 'Full Name', 'type' => 'str'],
                        'email' => ['label' => 'Email', 'type' => 'str'],
                        'gender' => ['label' => 'Gender', 'type' => 'str'],
                        'birthdate' => ['label' => 'Date of birth', 'type' => 'birthdate'],
                        'plan_name' => ['label' => 'Plan Name', 'type' => 'str'],
                        'username' => ['label' => 'Assign to Staff', 'type' => 'master', 'relation' => 'staffData'],
                        'last_login' => ['label' => 'Last Login', 'type' => 'date']
                    ],
                    'center' => [
                        'user_type' => ['label' => 'User Type', 'type' => 'user_type'],
                        'mobile' => ['label' => 'Mobile No', 'type' => 'str'],
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

        return response()->json($responseArr, 200);
    }

    /**
     * Tab Wise Count
     */
    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        $rawWhereArr = []; // initialize to avoid undefined var warning

        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = ['status' => 'APPROVED', 'plan_status' => 'Paid'];

            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }

            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere;
            }

            $tabWiseCountData[$tabId] = Register::query()
                ->when(!empty($whereArr), fn($q) => $q->where($whereArr))
                ->when(!empty($whereStr), fn($q) => $q->whereRaw($whereStr))
                ->when(!empty($rawWhereArr), fn($q) => $q->whereRaw(implode(' AND ', $rawWhereArr)))
                ->count();
        }

        return $tabWiseCountData;
    }
}

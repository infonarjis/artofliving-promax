<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentsOfLeadGeneration;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadFollowedUpReportController extends Controller
{
    private string $directoryName;
    private array $statusTabArr;

    public function __construct()
    {
        $this->directoryName = '/leadFollowedUpReport';
        $this->statusTabArr = [
            'todayFollowupTab' => [
                'label' => 'Today Followup',
                'id' => 'todayFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Today',
                'isActive' => 1,
                'strWhere' => ''
            ],
            'previousFollowupTab' => [
                'label' => 'Previous Followup',
                'id' => 'previousFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Previous',
                'strWhere' => ''
            ],
            'nextFollowupTab' => [
                'label' => 'Next Followup',
                'id' => 'nextFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Next',
                'strWhere' => ''
            ],
            'pendingFollowupTab' => [
                'label' => 'Pending Followup',
                'id' => 'pendingFollowupdData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Pending',
                'strWhere' => ''
            ],
        ];
    }

    public function index()
    {
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js',
            '/custom/js/leadGeneration/list.js',
        ];

        $dataArr = [
            'pageName' => 'Manage Lead Followed Up Report',
            'ajaxPaginationRequestUrl' =>
            'admin.leadFollowedUpReport.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
            ],
            'actionButtonUrl' => [
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
                'staffUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
                'franchiseAssignbtn' => 'admin.leadGeneration.assignMember',
                'franchiseUnAssignbtn' => 'admin.leadGeneration.unAssignMember',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => __('messages.msg_unexpected_error_occured'),
            'html' => '',
            'data' => [],
        ];

        $page = (int) ($validated['page'] ?? 1);
        $limit = (int) ($validated['limit'] ?? 10);

        ## Validate Request :
        $validated = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'conditionColumn' => ['nullable', 'string', 'in:Today,Previous,Next,Pending'],
            'searchKeyword' => ['nullable', 'string', 'max:255'],
        ]);
        $conditionColumn = $validated['conditionColumn'] ?? null;
        $searchKeyword = trim($validated['searchKeyword'] ?? '');

        ## Base Query :
        $query = $this->baseFollowupQuery()
            ->with([
                'leadGeneration:id,username,gender,interest,email,phone_no_1,country,created_at,staff_assign_id,franchise_assign_id',
                'leadGeneration.staff:id,username',
                'leadGeneration.franchise:id,username,email',
            ]);
        ## Apply Tab Condition :
        if (!empty($conditionColumn)) {
            $this->applyFollowupCondition(
                $query,
                $conditionColumn
            );
        }
        ## Apply Search :
        if ($searchKeyword !== '') {
            $this->applySearchCondition($query, $searchKeyword);
        }

        $query->latest('created_at');

        $resultArr = $query->paginate($limit, ['*'], 'page', $page);
        $htmlDataArr = ['tabCount' => $this->tabWiseCountData($this->statusTabArr, $searchKeyword)];

        $dataArr = (object) [
            'pageName' => 'page',
            'actionButtonUrl' => [
                'viewComment' => 'admin.leadGeneration.viewComment',
                'addComment' => 'admin.leadGeneration.addComment',
            ],
            'actionBtnArr' => [
                'addComment' => 1,
                'viewComment' => 1,
            ],
        ];

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr', 'dataArr'))->render();

        $responseArr['status'] = 'success';
        $responseArr['msg'] = 'Data fetched successfully.';
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = $html;
        return response()->json($responseArr, 200);
    }

    private function baseFollowupQuery()
    {
        $user = Auth::user();

        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);

        $permission = _checkPermission($userType, $roleId, 'view_lead_generation');

        ## Base Query :
        $query = CommentsOfLeadGeneration::query()
            ->where('follow_up_status', 1)
            ->whereNotNull('next_followup_date');

        ## Own Members Restriction :
        if ($permission === 'Own Members' && !empty($staffId) && $userType == 'Staff') {
            $query->whereHas(
                'leadGeneration',
                function ($q) use ($staffId) {
                    $q->where('staff_assign_id', $staffId);
                }
            );
        }
        return $query;
    }

    private function applyFollowupCondition($query, string $conditionColumn)
    {
        $today = now()->toDateString();
        return match ($conditionColumn) {
            'Today' => $query->whereDate('next_followup_date', $today),
            'Previous' => $query->whereDate('next_followup_date', '<', $today),
            'Next' => $query->whereDate('next_followup_date', '>', $today),
            'Pending' => $query->whereHas(
                'leadGeneration',
                function ($q) {
                    $q->whereNotNull(
                        'staff_assign_id'
                    )->where(
                        'commented',
                        0
                    );
                }
            ),
            default => $query,
        };
    }

    ## Apply Keyword Search :
    private function applySearchCondition($query, string $keyword)
    {
        $searchKeyword = '%' . $keyword . '%';
        $query->where(function ($q) use ($searchKeyword) {
            $q->where('comment', 'like', $searchKeyword)
                ->orWhere('posted_user_type', 'like', $searchKeyword)
                ->orWhereHas(
                    'leadGeneration',
                    function ($leadQuery) use ($searchKeyword) {
                        $leadQuery
                            ->where('username', 'like', $searchKeyword)
                            ->orWhere('email', 'like', $searchKeyword)
                            ->orWhere('gender', 'like', $searchKeyword)
                            ->orWhere('phone_no_1', 'like', $searchKeyword);
                    }
                )
                ## Staff :
                ->orWhereHas(
                    'leadGeneration.staff',
                    function ($staffQuery) use ($searchKeyword) {
                        $staffQuery->where('username', 'like', $searchKeyword);
                    }
                )
                ## Franchise :
                ->orWhereHas(
                    'leadGeneration.franchise',
                    function ($franchiseQuery) use ($searchKeyword) {
                        $franchiseQuery->where('username', 'like', $searchKeyword);
                    }
                );
        });
        return $query;
    }

    private function tabWiseCountData(array $tabArr, string $searchKeyword = ''): array
    {
        $tabWiseCountData = [];
        $baseQuery = $this->baseFollowupQuery();
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $conditionColumn = $value['conditionColumn'] ?? null;

            if (empty($tabId) || empty($conditionColumn)) {
                continue;
            }

            $query = clone $baseQuery;

            ## Apply Tab Condition :
            $this->applyFollowupCondition($query, $conditionColumn);

            if ($searchKeyword !== '') {
                $this->applySearchCondition($query, $searchKeyword);
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }
}

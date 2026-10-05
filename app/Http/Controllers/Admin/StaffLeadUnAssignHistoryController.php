<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignHistory;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StaffLeadUnAssignHistoryController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/staffLeadUnAssignHistory';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => '',
            ],
        ];
    }

    public function index()
    {
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js',
        ];

        $dataArr = [
            'pageName' =>
            'Unassigned Lead Generation From Staff',
            'ajaxPaginationRequestUrl' =>
            'admin.staffLeadUnAssignHistory.getAjaxPaginationData',
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
            ],
            'actionButtonUrl' => [
                'add' => 'admin.staffLeadUnAssignHistory.addForm',
                'edit' => 'admin.staffLeadUnAssignHistory.editForm/',
                'staffAssignbtn' => 'admin.leadGeneration.assignMember',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = $this->initializeResponseArray();
        $postData = $request->all();
        if (!$this->isValidPostData($postData)) {
            return response()->json($responseArr, 200);
        }

        [$page, $limit] = $this->getPageAndLimit($postData);

        [$whereArr, $whereStr] = $this->buildWhereConditions($postData);

        ## Main Query :
        $query = AssignHistory::with(['leadGeneration:id,username,email,created_at', 'staff:id,username'])
            ->where('lead_generation_id', '!=', '')
            ->where('user_type', 'Staff')
            ->where('action', 'Unassigned')
            ->whereHas('leadGeneration')
            ->whereHas('staff')
            ## Condition Filter :
            ->when(
                !empty($whereArr),
                function (Builder $query) use ($whereArr) {
                    $query->where($whereArr);
                }
            )
            ## Search Filter :
            ->when(
                !empty($postData['searchKeyword'] ?? ''),
                function (Builder $query) use ($postData) {
                    $this->applySearchFilter($query, $postData['searchKeyword']);
                }
            );

        ## Tab-wise Counts:
        $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $postData, $whereArr);

        ## Pagination :
        $resultArr = $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

        ## Render HTML :
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();
        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'html' => $html,
            'data' => $htmlDataArr,
        ], 200);
    }

    ## Initialize Response Array :
    private function initializeResponseArray()
    {
        return [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];
    }

    ## Validate Post Data :
    private function isValidPostData($postData)
    {
        return isset($postData) && !blank($postData);
    }

    ## Get Page and Limit :
    private function getPageAndLimit($postData)
    {
        $page = isset($postData['page']) && $postData['page'] !== '' ? (int) $postData['page'] : 1;
        $limit = isset($postData['limit']) && $postData['limit'] !== '' ? (int) $postData['limit'] : 10;

        return [$page, $limit];
    }

    ## Build Where Conditions :
    private function buildWhereConditions($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] !== '' && isset($postData['conditionVal']) && $postData['conditionVal'] !== '') {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }

        return [$whereArr, ''];
    }

    ## Apply Search Filter :
    private function applySearchFilter(Builder $query, string $searchKeyword)
    {
        $searchKeyword = trim($searchKeyword);
        $query->where(
            function (Builder $query) use ($searchKeyword) {
                ## Search Lead Username :
                $query->whereHas(
                    'leadGeneration',
                    function (Builder $leadQuery) use ($searchKeyword) {
                        $leadQuery->where('username', 'LIKE', '%' . $searchKeyword . '%');
                    }
                )
                    ## Search Lead Email :
                    ->orWhereHas(
                        'leadGeneration',
                        function (Builder $leadQuery) use (
                            $searchKeyword
                        ) {
                            $leadQuery->where('email', 'LIKE', '%' . $searchKeyword . '%');
                        }
                    );
            }
        );
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $postData = [], $whereArr = [])
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = AssignHistory::query()
                ->where('lead_generation_id', '!=', '')
                ->where('user_type', 'Staff')
                ->where('action', 'Unassigned')
                ->whereHas('leadGeneration')
                ->whereHas('staff');

            ## Tab Conditions :
            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }
            $strWhere = $value['strWhere'] ?? '';
            if (is_array($strWhere) && !empty($strWhere)) {
                $query->where($strWhere);
            }

            ## Condition Filter :
            if (!empty($whereArr)) {
                $query->where($whereArr);
            }

            ## Search Filter :
            if (!empty($postData['searchKeyword'] ?? '')) {
                $this->applySearchFilter($query, $postData['searchKeyword']);
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignHistory;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StaffUnassignHistoryController extends Controller
{
    private $directoryName;
    private $statusTabArr;
    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/staffUnassignHistory';
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
            'pageName' => 'Manage Unassigned Members From Staff',
            'ajaxPaginationRequestUrl' =>
            'admin.staffUnassignHistory.getAjaxPaginationData',
            'changeStatusUrl' =>
            'admin.staffUnassignHistory.changeStatus',
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
                'add' => 'admin.staffUnassignHistory.addForm',
                'edit' => 'admin.staffUnassignHistory.editForm/',
                'staffAssignbtn' => 'admin.member.unAssignMember',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];

        $postData = $request->all();
        if (!$this->isValidPostData($postData)) {
            return response()->json($responseArr, 200);
        }
        [$page, $limit] = $this->getPageAndLimit($postData);

        ## Tab-wise Counts :
        $htmlDataArr['tabCount'] = $this->tabWiseCountData(
            $this->statusTabArr,
            $postData
        );

        ## Main Query :
        $resultArr = AssignHistory::with([
            'register:id,matri_id,fullname,email,created_at',
            'staff:id,username',
        ])
            ->where('member_id', '!=', '')
            ->where('user_type', 'Staff')
            ->where('action', 'Unassigned')
            ## Condition Filter :
            ->when(
                !empty($postData['conditionColumn'])
                    && $postData['conditionVal'] !== '',
                function (Builder $query) use ($postData) {
                    $query->where(
                        $postData['conditionColumn'],
                        $postData['conditionVal']
                    );
                }
            )
            ## Search :
            ->when(
                !empty($postData['searchKeyword']),
                function (Builder $query) use ($postData) {
                    $this->applySearchFilter(
                        $query,
                        $postData['searchKeyword']
                    );
                }
            )
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        ## Render HTML :
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();
        $responseArr['status'] = 'success';
        $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = $html;
        return response()->json($responseArr, 200);
    }

    ## Validate Post Data :
    private function isValidPostData($postData)
    {
        return isset($postData) && !blank($postData);
    }

    ## Get Page and Limit :
    private function getPageAndLimit($postData)
    {
        $page = (isset($postData['page']) && $postData['page'] !== '') ? (int) $postData['page'] : 1;
        $limit = (isset($postData['limit']) && $postData['limit'] !== '') ? (int) $postData['limit'] : 10;
        return [$page, $limit];
    }

    ## Apply Search Filter :
    private function applySearchFilter(Builder $query, $searchKeyword)
    {
        $searchKeyword = trim($searchKeyword);
        $query->where(function (Builder $query) use ($searchKeyword) {
            ## Search Staff Username :
            $query->whereHas(
                'staff',
                function (Builder $staffQuery) use ($searchKeyword) {
                    $staffQuery->where('username', 'LIKE', '%' . $searchKeyword . '%');
                    $staffQuery->where('username', 'LIKE', '%' . $searchKeyword . '%');
                }
            )
                ## Search Member Email
                ->orWhereHas(
                    'register',
                    function (Builder $registerQuery) use ($searchKeyword) {
                        $registerQuery->where('matri_id', 'like', "%{$searchKeyword}%")
                            ->orWhere('fullname', 'like', "%{$searchKeyword}%")
                            ->orWhere('email', 'like', "%{$searchKeyword}%");
                    }
                );
        });
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $postData = [])
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = AssignHistory::query()
                ->where('member_id', '!=', '')
                ->where('user_type', 'Staff')
                ->where('action', 'Unassigned');
            ## Tab Condition :
            if (!empty($value['conditionColumn']) && $value['conditionVal'] !== '') {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }
            ## Array Where Conditions :
            $strWhere = $value['strWhere'] ?? '';
            if (is_array($strWhere) && !empty($strWhere)) {
                $query->where($strWhere);
            }
            ## Search Filter :
            if (!empty($postData['searchKeyword'])) {
                $this->applySearchFilter($query, $postData['searchKeyword']);
            }
            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }
}

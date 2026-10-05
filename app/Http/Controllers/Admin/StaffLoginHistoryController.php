<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffLoginHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StaffLoginHistoryController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/staffLoginHistory';
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

    public function index($matriID = '')
    {
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js',
        ];

        $dataArr = [
            'pageName' => 'Staff Login History',
            'ajaxPaginationRequestUrl' => 'admin.staffLoginHistory.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.staffLoginHistory.changeStatus',
            'extraJsArr' =>    $extraJsArr,
            'memberMatriId' => $matriID,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.staffLoginHistory.addForm',
                'edit' => 'admin.staffLoginHistory.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];

        $postData = $request->all();

        if (!isset($postData) || blank($postData)) {
            return response()->json($responseArr, 200);
        }

        $page = isset($postData['page']) && $postData['page'] !== '' ? (int) $postData['page'] : 1;
        $limit = isset($postData['limit']) && $postData['limit'] !== '' ? (int) $postData['limit'] : 10;

        ## Condition Column Value :
        $whereArr = $this->conditionValue($postData);

        ## Main Query :
        $query = StaffLoginHistory::with(['staff:id,username,email'])
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
            )
            ->when(
                !empty($postData['memberMatriId'] ?? ''),
                function (Builder $query) use ($postData) {
                    $memberId = base64_decode($postData['memberMatriId'], true);
                    if ($memberId !== false && is_numeric($memberId)) {
                        $query->whereHas(
                            'staff',
                            function (Builder $staffQuery) use ($memberId) {
                                $staffQuery->where('id', (int) $memberId);
                            }
                        );
                    }
                }
            );

        $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $postData, $whereArr);

        ## Pagination :
        $resultArr = $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

        ## Render HTML :
        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();
        $responseArr['status'] = 'success';
        $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = $html;
        return response()->json($responseArr, 200);
    }

    ## Apply Search Filter :
    private function applySearchFilter(Builder $query, string $searchKeyword)
    {
        $searchKeyword = trim($searchKeyword);
        $query->where(
            function (Builder $query) use ($searchKeyword) {
                $query->whereHas(
                    'staff',
                    function (Builder $staffQuery) use ($searchKeyword) {
                        $staffQuery->where('username', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%');
                    }
                );
            }
        );
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $postData = [], $whereArr = [])
    {
        $tabWiseCountData = [];

        ## Decode Member ID Once :
        $memberId = null;
        if (!empty($postData['memberMatriId'] ?? '')) {
            $decodedMemberId = base64_decode($postData['memberMatriId'], true);
            if ($decodedMemberId !== false && is_numeric($decodedMemberId)) {
                $memberId = (int) $decodedMemberId;
            }
        }

        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = StaffLoginHistory::query();

            ## Member ID Filter :
            if ($memberId !== null) {
                $query->whereHas(
                    'staff',
                    function (Builder $staffQuery) use ($memberId) {
                        $staffQuery->where('id', $memberId);
                    }
                );
            }

            ## Tab Condition :
            if (!blank($value['conditionColumn'] ?? null)) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }

            ## Additional String Conditions :
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

            ## Count :
            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    ## Condition Value :
    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] !== '' && isset($postData['conditionVal']) && $postData['conditionVal'] !== '') {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }

        return $whereArr;
    }

    public function onSearchKeyword($postData)
    {
        return '';
    }

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data' => [],
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
            return response()->json(
                $responseArr,
                200
            );
        }

        ## Soft Delete :
        if (isset($postData['is_deleted'])) {
            StaffLoginHistory::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);

            StaffLoginHistory::whereIn('id', $ids)->update($updateData);
        }

        $responseArr['status'] = 'success';

        $responseArr['msg'] = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');

        return response()->json(
            $responseArr,
            200
        );
    }
}

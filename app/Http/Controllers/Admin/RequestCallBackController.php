<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestCallBack;
use Illuminate\Http\Request;

class RequestCallBackController extends Controller
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
        
        $this->directoryName = '/requestCallBack';
        $this->searchColumn = ['matri_id', 'name', 'email', 'email'];
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'Member Request Call Back',
            'ajaxPaginationRequestUrl' => 'admin.requestCallBack.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'downloadBtn' => 0
            ],
            'actionButtonUrl' => [
                'add' => 'admin.requestCallBack.addForm',
                'edit' => 'admin.requestCallBack.editForm/',
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
        if (isset($postData) && !blank($postData)) {

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);
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
                    $whereStrFilter .= "created_at between '$fromDate' and '$toDate'";
                }
                if (isset($postData['event_id']) && !blank($postData['event_id'])) {
                    $eventId  = $postData['event_id'];
                    $checkAnd  = '';
                    if (!blank($whereStrFilter)) {
                        $checkAnd = 'AND';
                    }
                    $whereStrFilter .= "$checkAnd ( event_id = '$eventId')";
                }
            }
            if (blank($whereStr)) {
                $whereStr = $whereStrFilter;
            } elseif (blank($whereStrFilter)) {
                $whereStr .= "";
            } else {
                $whereStr .= " AND ($whereStrFilter)";
            }
            session(['whereStrFilter' => $whereStr]);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = RequestCallBack::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
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
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = RequestCallBack::query()
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
        return $whereStr;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (
            isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
            isset($postData['conditionVal']) && $postData['conditionVal'] != ''
        ) {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CallyzerHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CallLogHistoryCallyzerController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct() {
        $this->directoryName = '/callLogHistoryCallyzer';
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
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
        ## Extra Js :
        $extraJsArr = [
            '/custom/js/callLogHistoryCallyzer/list.js'
        ];

        $dataArr = [
            'pageName' => 'Call History',
            'ajaxPaginationRequestUrl' => 'admin.callLogHistoryCallyzer.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'statusTabArr' => $this->statusTabArr,
            'getDateRange' => _getCurrentTo15date()
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

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            
            $getDateRange = _getCurrentTo15date();
            $callFrom = strtotime($getDateRange['call_from']);
            $callTo = strtotime($getDateRange['call_to']);
            if (isset($postData['call_from'])) {
                $callFrom = strtotime($postData['call_from']);
            }
            if (isset($postData['call_to'])) {
                $callTo = strtotime($postData['call_to']);
            }
            
            $payload = [
                "call_from" => $callFrom,
                "call_to" => $callTo,
                "call_types" => ["Missed","Rejected","Incoming","Outgoing"],
                "emp_code" => [],
                "emp_numbers" => [],
                "duration_les_than" => 100,
                "emp_tags" => [],
                "is_exclude_numbers" => true,
                "page_no" => $page,
                "page_size" => $limit
            ];
            $payloadUrl = 'call-log/history';
            $type = 'POST';
            $responseArr = CallyzerHelper::getCallyzerApiRequest($type,$payloadUrl,$payload);
            if($responseArr['message'] == 'Success'){
                $resultArr = $responseArr['result'];
                $resultCount = $responseArr['total_records'];   
                ## Tab Wise Count Data :
                $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr,$resultCount);
            }else{
                $resultArr = [];
                $resultCount = 0;
                ## Tab Wise Count Data :
                $dataArr['tabCount'] = 0;
            }
            $errorMsg = $responseArr['message'] ?? '';

            $resultArr = new LengthAwarePaginator($resultArr, $resultCount, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.callLogHistoryCallyzer.editForm',
                ],
            ]);
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $resultCount) {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = isset($value['id']) ? $value['id'] : $key;
            $tabWiseCountData[$tabId] = $resultCount;
        }
        return $tabWiseCountData;
    }
}
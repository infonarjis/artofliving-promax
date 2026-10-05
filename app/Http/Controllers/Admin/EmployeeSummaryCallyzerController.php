<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CallyzerHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeSummaryCallyzerController extends Controller
{
    private $directoryName;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/employeeSummaryCallyzer';
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js/employeeSummaryCallyzer/list.js'
        ];

        $dataArr = [
            'pageName' => 'Employee Summary Reports',
            'ajaxPaginationRequestUrl' => 'admin.employeeSummaryCallyzer.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'statusTabArr' => [],
            'getDateRange' => _getCurrentTo15date(),
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
                "call_types" => ["Missed", "Rejected", "Incoming", "Outgoing"],
                "emp_numbers" => [],
                "duration_les_than" => 100,
                "emp_tags" => [],
                "is_exclude_numbers" => true,
                "page_no" => $page,
                "page_size" => $limit
            ];

            $payloadUrl = 'call-log/employee-summary';
            $type = 'POST';
            $responseArr = CallyzerHelper::getCallyzerApiRequest($type,$payloadUrl,$payload);
            $resultArr = $responseArr['result'] ?? [];
            $errorMsg = $responseArr['message'] ?? '';

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'errorMsg')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }
}

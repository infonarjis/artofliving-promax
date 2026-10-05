<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CallyzerHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EmployeeDetailCallyzerController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/employeeDetailCallyzer';
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
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'Employee Details',
            'ajaxPaginationRequestUrl' => 'admin.employeeDetailCallyzer.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
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
        if (isset($postData) && !blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            $payload = [
                "emp_numbers" => [],
                "emp_tags" => [],
                "emp_name" => "",
                "emp_codes" => [],
                "page_no" => $page,
                "page_size" => $limit
            ];
            $payloadUrl = 'employee/get';
            $type = 'GET';
            $responseArr = CallyzerHelper::getCallyzerApiRequest($type,$payloadUrl,$payload);
            if ($responseArr['message'] == 'Success') {
                $resultArr = $responseArr['result'];
                $resultCount = $responseArr['total_records'];
            } else {
                $resultArr = [];
                $resultCount = 0;
                ## Tab Wise Count Data :
                $dataArr['tabCount'] = 0;
            }
            $errorMsg = $responseArr['message'] ?? '';

            ## Tab Wise Count Data :
            $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $resultCount);

            $resultArr = new LengthAwarePaginator($resultArr, $resultCount, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.employeeDetailCallyzer.editForm',
                ],
            ]);
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr','errorMsg')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $resultCount)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = isset($value['id']) ? $value['id'] : $key;
            $tabWiseCountData[$tabId] = $resultCount;
        }
        return $tabWiseCountData;
    }
}

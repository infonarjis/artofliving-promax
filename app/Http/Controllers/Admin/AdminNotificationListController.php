<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationListController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {
        $this->directoryName = '/adminNotificationList';
        $this->searchColumn = ['member_id', 'title', 'message'];
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
    public function index($matriID = '')
    {
        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => 'Admin Notification List',
            'ajaxPaginationRequestUrl' => 'admin.adminNotificationList.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
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
                'add' => 'admin.adminNotificationList.addForm',
                'edit' => 'admin.adminNotificationList.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    /**
     * Get Ajax Pagination Data
     */
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        $htmlDataArr = [];
        if (!blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;

            ## Check Roles Permission :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            $notifiUserType = strtolower($userType);

            ## Condition Column Value :
            $whereArr = $this->conditionValue($postData);
            ## Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            ## Check Member Id :
            if (isset($postData['memberMatriId']) && $postData['memberMatriId'] != '') {
                $memberMatriId = $postData['memberMatriId'];
                if (!blank($whereStr)) {
                    $whereStr .= " AND ";
                }
                $whereStr .= "(sender_matri_id = '" . addslashes($memberMatriId) . "')";
            }

            /*
            |--------------------------------------------------------------------------
            | Base Notification Query
            |--------------------------------------------------------------------------
            | Same condition as Blade:
            | approved()
            | forUser($notifiUserType, $userId)
            |--------------------------------------------------------------------------
            */
            $notificationQuery = AdminNotification::query()->approved()->forUser($notifiUserType, $userId);

            ## Tab Wise Counts :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr, $notifiUserType, $userId);

            ## Data :
            $resultArr = (clone $notificationQuery)
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->latest('id')
                ->paginate($limit, ['*'], 'page', $page);

            ## Render HTML :
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
            $responseArr['data'] = $htmlDataArr;
        }

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $whereStr, $notifiUserType, $userId)
    {
        $tabWiseCountData = [];

        ## Base Query :
        $notificationQuery = AdminNotification::query()->approved()->forUser($notifiUserType, $userId);

        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = [];
            $rawWhereArr = [];
            ## Condition Column :
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }

            ## Additional Where :
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere;
            }

            ## Tab Count :
            $tabWiseCountData[$tabId] = (clone $notificationQuery)
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($rawWhereArr), function ($q) use ($rawWhereArr) {
                    foreach ($rawWhereArr as $rawWhere) {
                        $q->whereRaw($rawWhere);
                    }
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->count();
        }
        return $tabWiseCountData;
    }

    ## Condition Column Value :
    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }

        return $whereArr;
    }

    ## Search Keyword :
    public function onSearchKeyword($postData)
    {
        $whereStr = '';
        if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {
            $searchKeyword = addslashes($postData['searchKeyword']);
            foreach ($this->searchColumn as $key => $value) {
                if ($key != 0) {
                    $whereStr .= " OR ";
                }
                $whereStr .= "$value LIKE '%{$searchKeyword}%'";
            }
            if ($whereStr != '') {
                $whereStr = "( $whereStr )";
            }
        }
        return $whereStr;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOnPackage;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class AddOnPackageController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/addOnPackage';
        $this->searchColumn = ['package_category', 'package_title', 'package_amount', 'description'];
        $this->pageName = 'Add On Package';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
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
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.addOnPackage.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.addOnPackage.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
                'view' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.addOnPackage.addForm',
                'edit' => 'admin.addOnPackage.editForm',
                'view' => 'admin.addOnPackage.viewDetails',
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
        if (isset($postData) && !blank($postData)) {

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            ## Tab-Wise Counts :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = AddOnPackage::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.addOnPackage.editForm',
                    'view' => 'admin.addOnPackage.viewDetails',
                ],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    /**
     * Tab Wise Count
     */
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
            $tabWiseCountData[$tabId] = AddOnPackage::query()
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

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
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
            return response()->json($responseArr, 200);
        }

        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            AddOnPackage::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            AddOnPackage::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $packedCategoryArr = array(
            'Allowed View Contacts' => 'Allowed View Contacts',
            'Allowed View Profiles' => 'Allowed View Profiles',
            'Allowed Interest Profiles' => 'Allowed Interest Profiles',
            'Allowed Duration' => 'Allowed Duration',
            'Allowed Video Call' => 'Allowed Video Call',
            'Allowed Audio Call' => 'Allowed Audio Call',
            'WhatsApp Group' => 'WhatsApp Group',
        );
        $siteSetting = _getSiteSetting();
        ## Check Zego-Cloud Setting Enabled:
        if ($siteSetting['zego_video_call_setting'] == 'UNAPPROVED') {
            unset($packedCategoryArr['Allowed Video Call']);
        }
        if ($siteSetting['zego_voice_call_setting'] == 'UNAPPROVED') {
            unset($packedCategoryArr['Allowed Audio Call']);
        }
        $elementArr = array(
            'package_title' => array(
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
            ),
            'package_category' => array(
                'type' => 'dropdown',
                'label' => 'Package',
                'column' => '6',
                'class' => 'select2',
                'value_arr' => $packedCategoryArr,
            ),
            'package_count' => array(
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),
            'package_amount' => array(
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),
            'in_app_purchase_android_id' => [
                'is_required' => 'required',
                'class' => 'required',
                'label' => 'In App Product ID (Android)',
                'column' => '6',
            ],
            'in_app_purchase_android_amount' => [
                'is_required' => 'required',
                'class' => 'required',
                'label' => 'In App Plan Amount (Android)',
                'input_type' => 'number',
                'column' => '6'
            ],

            'in_app_purchase_ios_id' => [
                'is_required' => 'required',
                'class' => 'required',
                'label' => 'In App Product ID (IOS)',
                'column' => '6',
            ],
            'in_app_purchase_ios_amount' => [
                'is_required' => 'required',
                'class' => 'required',
                'label' => 'In App Plan Amount (IOS)',
                'input_type' => 'number',
                'column' => '6'
            ],
            'description' => array('type' => 'textarea', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = AddOnPackage::find($id);
        }
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.addOnPackage.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.addOnPackage.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'package_title',
            'package_category',
            'package_count',
            'package_amount',
            'description',
            'status',
            'in_app_purchase_android_id',
            'in_app_purchase_android_amount',
            'in_app_purchase_ios_id',
            'in_app_purchase_android_amount',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                $updateData['updated_at'] = _getCurrentDate();
                AddOnPackage::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                AddOnPackage::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        $resultArr = AddOnPackage::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

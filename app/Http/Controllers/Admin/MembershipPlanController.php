<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class MembershipPlanController extends Controller
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

        $this->directoryName = '/membershipPlan';
        $this->searchColumn = ['plan_name', 'plan_amount'];
        $this->pageName = 'Manage Membership Plan';
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
            ],
            'personalizedTab' => [
                'label' => 'Personalized Plan',
                'id' => 'personalizedPlanData',
                'class' => '',
                'conditionVal' => '1',
                'conditionColumn' => 'is_personalized',
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
            'ajaxPaginationRequestUrl' => 'admin.membershipPlan.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.membershipPlan.changeStatus',
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
                'add' => 'admin.membershipPlan.addForm',
                'edit' => 'admin.membershipPlan.editForm',
                'view' => 'admin.membershipPlan.viewDetails',
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

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = MembershipPlan::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.membershipPlan.editForm',
                    'view' => 'admin.membershipPlan.viewDetails',
                ],
            ];
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr', 'dataArr'));
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
            $tabWiseCountData[$tabId] = MembershipPlan::query()
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
            MembershipPlan::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            MembershipPlan::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $elementArr = [
            'plan_name' => ['is_required' => 'required', 'class' => 'required', 'column' => '6'],
            'plan_type' => ['type' => 'radio', 'column' => '6', 'value_arr' => ['PAID' => 'PAID', 'FREE' => 'FREE'], 'value' => 'PAID'],
            'currency_code' => [
                'is_required' => 'required',
                'class' => 'required select2',
                'type' => 'dropdown',
                'label' => 'Plan Currency',
                'relation' => ['rel_model' => 'CurrencyMaster', 'key_val' => 'currency_code', 'key_disp' => 'currency_code'],
                'column' => '6',
                // 'form_group_class' => 'currency_code'
            ],
            'plan_amount' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6',
                'form_group_class' => 'plan_amount'
            ],
            'international_currency_code' => [
                'is_required' => 'required',
                'class' => 'required select2',
                'type' => 'dropdown',
                'label' => 'International Plan Currency',
                'relation' => ['rel_model' => 'CurrencyMaster', 'key_val' => 'currency_code', 'key_disp' => 'currency_code'],
                'column' => '6',
                // 'form_group_class' => 'currency_code'
            ],
            'international_plan_amount' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6',
                'form_group_class' => 'plan_amount',
                'value' => '0.00'
            ],

            'in_app_purchase_android_id' => [
                'label' => 'In App Product ID (Android)',
                'column' => '4',
            ],
            'in_app_purchase_android_amount' => [
                'label' => 'In App Plan Amount (Android)',
                'input_type' => 'number',
                'column' => '4',
                'value' => '0.00'
            ],
            'international_in_app_purchase_android_amount' => [
                'label' => 'International In App Plan Amount (Android)',
                'input_type' => 'number',
                'column' => '4',
                'value' => '0.00'
            ],

            'in_app_purchase_ios_id' => [
                'label' => 'In App Product ID (IOS)',
                'column' => '4',
            ],
            'in_app_purchase_ios_amount' => [
                'label' => 'In App Plan Amount (IOS)',
                'input_type' => 'number',
                'column' => '4',
                'value' => '0.00'
            ],
            'international_in_app_purchase_ios_amount' => [
                'label' => 'International In App Plan Amount (IOS)',
                'input_type' => 'number',
                'column' => '4',
                'value' => '0.00'
            ],

            'plan_discount' => [
                'is_required' => 'required',
                'class' => 'required',
                'label' => 'Plan Discount (%)',
                'input_type' => 'number',
                'column' => '6',
                'form_group_class' => 'plan_discount'
            ],
            'validity_days' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'Plan Validity (Days)',
                'column' => '6'
            ],
            'view_profile_limit' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'View Profile Limit',
                'column' => '6'
            ],
            'interests_limit' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'Send Interest Limit',
                'column' => '6'
            ],
            'contact_views_limit' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'Contact View Limit',
                'column' => '6'
            ],
            'video_minutes_limit' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'Video Call Minutes',
                'column' => '6',
                'display_info' => 'Total Video Call Minutes For Plan'
            ],
            'audio_minutes_limit' => [
                'is_required' => 'required',
                'class' => 'required',
                'input_type' => 'number',
                'label' => 'Audio Call Minutes',
                'column' => '6',
                'display_info' => 'Total Audio Call Minutes For Plan'
            ],
            'can_chat' => [
                'type' => 'radio',
                'is_required' => 'required',
                'class' => 'required',
                'value_arr' => ['1' => 'Yes', '0' => 'No'],
                'value' => '0',
                'column' => '6'
            ],
            'is_personalized' => [
                'type' => 'radio',
                'is_required' => 'required',
                'class' => 'required',
                'value_arr' => ['1' => 'Yes', '0' => 'No'],
                'value' => '0',
                'column' => '6'
            ],
            'ai_interest' => [
                'type' => 'radio',
                'label' => 'Send Auto AI Interest',
                'is_required' => 'required',
                'class' => 'required',
                'value_arr' => ['1' => 'Yes', '0' => 'No'],
                'value' => '0',
                'column' => '6'
            ],
            'plan_description' => [
                'type' => 'textarea',
                'column' => '12'
            ],
            'status' => [
                'type' => 'radio',
                'column' => '6',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ],
        ];

        ## Check Zego-Cloud Setting Enabled:
        $siteSetting = _getSiteSetting();
        ## Check Zego-Cloud Setting Enabled:
        if ($siteSetting['zego_video_call_setting'] == 'UNAPPROVED') {
            unset($elementArr['video_minutes_limit']);
        }
        if ($siteSetting['zego_voice_call_setting'] == 'UNAPPROVED') {
            unset($elementArr['audio_minutes_limit']);
        }

        ## AI Mode Check :
        if (_getConstant('AI_MODE') == 'Disabled'){
            unset($elementArr['ai_interest']);
        }

        $mode = ($id != '') ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = MembershipPlan::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.membershipPlan.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.membershipPlan.addEdit',
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
            'plan_name',
            'plan_type',
            'plan_amount',
            'international_currency_code',
            'international_plan_amount',
            'plan_discount',
            'currency_code',
            'validity_days',
            'interests_limit',
            'contact_views_limit',
            'video_minutes_limit',
            'view_profile_limit',
            'ai_interest',
            'audio_minutes_limit',
            'can_chat',
            'is_personalized',
            'plan_description',

            'in_app_purchase_android_id',
            'in_app_purchase_android_amount',
            'international_in_app_purchase_android_amount',
            'in_app_purchase_ios_id',
            'in_app_purchase_ios_amount',
            'international_in_app_purchase_ios_amount',

            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            if (isset($postData['plan_type']) && $postData['plan_type'] == 'FREE') {
                $updateData['plan_amount'] = '0';
                $updateData['international_plan_amount'] = '0';
                $updateData['plan_discount'] = '0';
            }
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                MembershipPlan::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])
                    ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                MembershipPlan::create($updateData);
                return redirect()->route($postData['callbackUrl'])
                    ->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        $resultArr = MembershipPlan::where('id', $id)->first();
        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

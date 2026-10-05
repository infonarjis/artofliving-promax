<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOnPayment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
## Services
use App\Services\AdminFormBuilderService;

class FranchiseSalesReportsController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/franchiseSalesReports';
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
        ];
    }

    ## List :
    public function index()
    {
        session()->forget('whereStrFilter');
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => 'Franchise Sales Reports',
            'ajaxPaginationRequestUrl' => 'admin.franchiseSalesReports.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.franchiseSalesReports.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'filter' => 1
            ],
            'actionButtonUrl' => [
                'add' => 'admin.franchiseSalesReports.addForm',
                'edit' => 'admin.franchiseSalesReports.editForm',
                'viewInvoice' => 'admin.franchiseSalesReports.viewInvoice',
                'filter' => 'admin.franchiseSalesReports.getFilter'
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
            $htmlDataArr = [];
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Filter Conditions :
            if (isset($postData['isFilterApply']) && (int) $postData['isFilterApply'] === 1) {
                ## Created Date Filter :
                if (isset($postData['created_from']) || isset($postData['created_to'])) {
                    $fromDate = $postData['created_from'] ?? null;
                    $toDate   = $postData['created_to'] ?? null;

                    if (!blank($fromDate) && !blank($toDate)) {
                        $whereArr[] = [
                            'type' => 'date_between',
                            'from' => $fromDate,
                            'to'   => $toDate,
                        ];
                    } elseif (!blank($fromDate)) {
                        $whereArr[] = [
                            'type' => 'date_from',
                            'date' => $fromDate,
                        ];
                    } elseif (!blank($toDate)) {
                        $whereArr[] = [
                            'type' => 'date_to',
                            'date' => $toDate,
                        ];
                    }
                }

                ## Plan Name :
                if (isset($postData['plan_name']) && !blank($postData['plan_name'])) {
                    $planName = $postData['plan_name'];

                    if (!is_array($planName)) {
                        $planName = [$planName];
                    }

                    $planName = collect($planName)
                        ->flatten()
                        ->filter(fn($plan) => !blank($plan))
                        ->map(fn($plan) => trim((string) $plan))
                        ->unique()
                        ->values()
                        ->toArray();

                    if (!empty($planName)) {
                        $whereArr[] = [
                            'type'   => 'where_in',
                            'column' => 'payments.plan_name',
                            'values' => $planName,
                        ];
                    }
                }

                ## Franchise :
                if (isset($postData['franchise_id']) && !blank($postData['franchise_id'])) {
                    $franchiseIds = $postData['franchise_id'];

                    if (!is_array($franchiseIds)) {
                        $franchiseIds = [$franchiseIds];
                    }
                    $franchiseIds = collect($franchiseIds)
                        ->flatten()
                        ->filter(fn($id) => !blank($id))
                        ->map(fn($id) => (int) $id)
                        ->filter(fn($id) => $id > 0)
                        ->unique()
                        ->values()
                        ->toArray();

                    if (!empty($franchiseIds)) {
                        $whereArr[] = [
                            'type'   => 'where_in',
                            'column' => 'payments.franchise_id',
                            'values' => $franchiseIds,
                        ];
                    }
                }

                ## Currency Code :
                if (isset($postData['currency_code']) && !blank($postData['currency_code'])) {
                    $currencyCode = $postData['currency_code'];

                    if (is_array($currencyCode)) {
                        $currencyCode = $currencyCode[0] ?? null;
                    }
                    if (!blank($currencyCode)) {
                        $whereArr[] = [
                            'type'   => 'where',
                            'column' => 'payments.currency_code',
                            'value'  => trim($currencyCode),
                        ];
                    }
                }
            }

            ## Default Franchise Condition :
            $whereArr[] = [
                'type'   => 'where_raw',
                'query'  => 'payments.franchise_id != 0',
            ];
            ##  Franchise User Restriction :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            if ($userType === 'Franchise' && !blank($userId)) {
                $whereArr[] = [
                    'type'   => 'where',
                    'column' => 'registers.franchise_assign_id',
                    'value'  => (int) $userId,
                ];
            }

            ## Main Payment Query :
            $resultArr = Payment::query()
                ->from('payments')
                ->where('payments.status', 'SUCCESS')
                ->join(
                    'registers',
                    'registers.id',
                    '=',
                    'payments.member_id'
                )
                ->select('payments.*')
                ->with([
                    'member:id,matri_id,email,fullname,mobile,franchise_assign_id',
                    'member.franchiseData:id,username',
                ]);

            ## Apply Common Filters :
            $resultArr = $this->applyPaymentFilters(
                $resultArr,
                $whereArr
            );

            ## Search Keyword :
            if (isset($postData['searchKeyword']) && !blank($postData['searchKeyword'])) {
                $keyword = trim($postData['searchKeyword']);
                $search  = '%' . $keyword . '%';

                $resultArr->where(function ($q) use ($search) {
                    $q->where('payments.plan_name', 'LIKE', $search)
                        ->orWhere('payments.transaction_id', 'LIKE', $search)
                        ->orWhere('registers.fullname', 'LIKE', $search)
                        ->orWhere('registers.matri_id', 'LIKE', $search)
                        ->orWhere('registers.email', 'LIKE', $search)
                        ->orWhere('registers.mobile', 'LIKE', $search);
                });
            }

            $resultArr = $resultArr->orderByDesc('payments.id')->paginate($limit, ['payments.*'], 'page', $page);

            ## Tab Count :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereArr, $postData);

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.franchiseSalesReports.editForm',
                    'viewInvoice' => 'admin.franchiseSalesReports.viewInvoice',
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

    public function tabWiseCountData($tabArr, $whereArr = [], $postData = [])
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;

            ## Query :
            $query = Payment::query()
                ->from('payments')
                ->where('payments.status', 'SUCCESS')
                ->join(
                    'registers',
                    'registers.id',
                    '=',
                    'payments.member_id'
                );

            ## Tab Condition :
            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }

            ## Common Filters :
            if (!empty($whereArr)) {
                $query = $this->applyPaymentFilters($query, $whereArr);
            }

            ## Search Keyword :
            if (isset($postData['searchKeyword']) && !blank($postData['searchKeyword'])) {
                $search = '%' . trim($postData['searchKeyword']) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('payments.plan_name', 'LIKE', $search)
                        ->orWhere('payments.transaction_id', 'LIKE', $search)
                        ->orWhere('registers.fullname', 'LIKE', $search)
                        ->orWhere('registers.matri_id', 'LIKE', $search)
                        ->orWhere('registers.email', 'LIKE', $search)
                        ->orWhere('registers.mobile', 'LIKE', $search);
                });
            }

            ## Count :
            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    private function applyPaymentFilters($query, array $whereArr)
    {
        foreach ($whereArr as $condition) {
            switch ($condition['type']) {
                case 'where':
                    $query->where($condition['column'], $condition['value']);
                    break;
                case 'where_in':
                    $query->whereIn($condition['column'], $condition['values']);
                    break;
                case 'where_raw':
                    $query->whereRaw($condition['query']);
                    break;
                case 'date_between':
                    $query->whereBetween('payments.plan_activate_date', [$condition['from'], $condition['to']]);
                    break;
                case 'date_from':
                    $query->where('payments.plan_activate_date', '>=', $condition['date']);
                    break;
                case 'date_to':
                    $query->where('payments.plan_activate_date', '<=', $condition['date']);
                    break;
            }
        }
        return $query;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' && isset($postData['conditionVal']) && $postData['conditionVal'] != '') {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
    }

    public function viewInvoice($id)
    {
        $resultArr = Payment::active()->with([
            'member:id,matri_id,email,fullname,mobile,franchise_assign_id',
            'member.franchiseData:id,username'
        ])->where('id', $id)->first();

        if (!$resultArr) {
            return redirect()->route('admin.member.index')->with('error', _getConstant('responce_message.RECORD_NOT_FOUND') ?? 'Invoice not found.');
        }

        $addOnPlan = AddOnPayment::where('payment_id', $resultArr->id)->get();

        $configArr = _getSiteSetting();

        $dataArr = [
            'pageName' => 'View Invoice',
            'resultArr' => $resultArr,
            'addOnPlan' => $addOnPlan,
            'configArr' => $configArr,
            'actionButtonUrl' => [
                'viewInvoice' => 'admin.member.viewInvoice',
            ],
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/viewInvoice', $dataArr);
    }

    public function getFilter(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();

        $currentDate = _getCurrentDate('Y-m-d');
        if (isset($postData) && !blank($postData)) {
            $elementArr = array(
                'created_from' => array(
                    'is_register' => 'yes',
                    'input_type' => 'date',
                    'label' => "Plan Activated From"
                ),
                'created_to' => array(
                    'input_type' => 'date',
                    'is_register' => 'yes',
                    'other' => 'max="' . $currentDate . '"',
                    'label' => "Plan Activated To"
                ),
                'plan_name' => array(
                    'class' => 'single not_reset',
                    'is_register' => 'yes',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Plan Name',
                    'type' => 'dropdown',
                    'relation' => array(
                        'rel_model' => 'MembershipPlan',
                        'key_val' => 'plan_name',
                        'class' => '',
                        'key_disp' => 'plan_name'
                    )
                ),
                'franchise_id' => array(
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Franchise List',
                    'type' => 'dropdown',
                    'is_register' => 'yes',
                    'relation' => array(
                        'rel_model' => 'Franchise',
                        'key_val' => 'id',
                        'key_disp' => 'username',
                        'rel_col_name' => 'type',
                        'rel_col_val' => 'Franchise'
                    )
                ),
                'currency_code' => [
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'type' => 'dropdown',
                    'label' => 'Plan Currency',
                    'relation' => ['rel_model' => 'CurrencyMaster', 'key_val' => 'currency_code', 'key_disp' => 'currency_code'],
                    'column' => '6'
                ],
            );

            $otherData = [
                'rowData' => [],
            ];
            $fromHtml =
                $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
            $dataArr = [
                'elementArr' => $elementArr,
                'formUrl' => 'admin.franchiseSalesReports.getAjaxPaginationData',
                'formId' => 'filterForm',
                'formName' => 'filterForm',
                'formSubmitBtnClass' => 'filterFormSubmitBtn',
                'formSubmitBtnId' => 'filterFormSubmitBtn',
                'fromHtml' => $fromHtml,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/filterPopup', $dataArr);
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }
}

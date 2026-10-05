<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Hash;

class FranchiseController extends Controller
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

        $this->directoryName = '/franchise';
        $this->searchColumn = ['username', 'email', 'mobile'];
        $this->pageName = 'Manage Franchise';
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
                'conditionColumn' => 'franchise.status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'franchise.status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.franchise.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.franchise.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'view' => 1,
                'isSearch' => 1,
                'is_copy_access_link' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.franchise.addForm',
                'edit' => 'admin.franchise.editForm',
                'view' => 'admin.franchise.viewDetails',
                'copyaccessLinkVal' => route('franchise.login')
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

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = Franchise::when(!empty($whereArr), function ($q) use ($whereArr) {
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
                    'edit' => 'admin.franchise.editForm',
                    'view' => 'admin.franchise.viewDetails',
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
            $tabWiseCountData[$tabId] = Franchise::query()
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
            Franchise::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Franchise::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {

        $mode = ($id != '') ? 'edit' : 'add';

        $elementArr = array(
            'username' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'email' => array(
                'is_required' => 'required',
                'input_type' => 'email',
                'isDisableInDemo' => 'Yes',
                'class' => 'required',
                'modeType' => $mode,
                'column' => '6'
            ),
            'password_decrypted' => array(
                'is_required' => 'required',
                'type' => 'password',
                'label' => 'Password',
                'class' => 'required',
                'mode' => $mode,
                'column' => '6'
            ),
            'mobile' => array(
                'is_required' => 'required',
                'type' => 'mobile',
                'isDisableInDemo' => 'Yes',
                'class' => 'required single',
                'modeType' => $mode,
                'column' => '6',
                'maxLength' => '15',
                'type_num_alph' => 'tel',
            ),
            'commission' => array(
                'is_required' => 'required',
                'input_type' => 'number',
                'other' => "min='0' max='100'",
                'column' => '6'
            ),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = Franchise::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.franchise.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.franchise.addEdit',
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
            'username',
            'email',
            'mobile',
            'commission',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);

        if (!empty($updateData)) {
            ## Check Duplicate :
            if (_getConstant('DISABLE_DEMO') != 'Enabled') {
                $duplicate = Franchise::where(function ($q) use ($request) {
                    $q->where('email', $request->email)
                        ->orWhere('mobile', $request->mobile);
                })->select('id', 'email', 'mobile')->first();
                if ($duplicate) {
                    if ($duplicate->email === $request->email) {
                        return redirect()->route($postData['callbackUrl'])->with('error', 'Email already exists.');
                    }
                    if ($duplicate->mobile === $request->mobile) {
                        return redirect()->route($postData['callbackUrl'])->with('error', 'Mobile number already exists.');
                    }
                }
            }

            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $this->updateRecord($updateData, $postData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $refCode = _createReferalCode(12);
                $updateData['referral_code'] = $refCode;
                $this->insertRecord($updateData, $postData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    public function updateRecord($updateData, $postData)
    {
        $mobileNumber = $this->mobileNumberAdd($postData);
        if (isset($mobileNumber) && $mobileNumber != '') {
            $updateData['mobile'] = $mobileNumber;
        }
        if (isset($postData['password_decrypted']) && $postData['password_decrypted'] != '') {
            $updateData['password'] = Hash::make($postData['password_decrypted']);
        }
        if (isset($postData['password_decrypted']) && $postData['password_decrypted'] != '') {
            $updateData['password_decrypted'] = $postData['password_decrypted'];
        }
        $whereArr = ['id' => $postData['id']];
        Franchise::where($whereArr)->update($updateData);
    }

    public function insertRecord($updateData, $postData)
    {
        $mobileNumber = $this->mobileNumberAdd($postData);
        if (isset($mobileNumber) && $mobileNumber != '') {
            $updateData['mobile'] = $mobileNumber;
        }
        if (isset($postData['password_decrypted']) && $postData['password_decrypted'] != '') {
            $updateData['password'] = Hash::make($postData['password_decrypted']);
            $updateData['password_decrypted'] = $postData['password_decrypted'];
        }
        $updateData['created_at'] = _getCurrentDate();
        Franchise::create($updateData);
    }

    public function mobileNumberAdd($postData)
    {
        return $postData['mobile_country_code'] . '-' . $postData['mobile'];
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        $resultArr = Franchise::where('id', $id)->first();
        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

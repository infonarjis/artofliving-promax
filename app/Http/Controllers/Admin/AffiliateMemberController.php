<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\AffiliateMember;
use App\Services\AdminCommonActionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

## Services
use App\Services\AdminFormBuilderService;

class AffiliateMemberController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $customJsDirectory;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/affiliateMember';
        $this->searchColumn = ['fullname', 'mobile', 'email'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Affiliate Member';
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
        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('affiliate_member', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.affiliateMember.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.affiliateMember.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.affiliateMember.addForm',
                'edit' => 'admin.affiliateMember.editForm',
                'view' => 'admin.affiliateMember.viewDetails',
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
        if (!empty($postData)) {
            $dataArr = [];
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = AffiliateMember::when(!empty($whereArr), function ($q) use ($whereArr) {
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
                    'edit' => 'admin.affiliateMember.editForm',
                    'view' => 'admin.affiliateMember.viewDetails',
                ],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
            $responseArr['data'] = $htmlDataArr;
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
            $tabWiseCountData[$tabId] = AffiliateMember::query()
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
            AffiliateMember::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            AffiliateMember::whereIn('id', $ids)->update($updateData);
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
            'gender' => array(
                'type' => 'radio',
                'value_arr' => ['Male' => 'Male', 'Female' => 'Female'],
                'column' => '12',
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
                'label' => 'Gender'
            ),
            'fullname' => array(
                'is_required' => 'required',
                'label' => 'Full Name',
                'class' => 'required',
                'column' => '6'
            ),
            'email' => array(
                'input_type' => 'email',
                'is_required' => 'required',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'check_duplicate' => 'Yes',
                'class' => 'required',
                'column' => '6'
            ),
            'mobile' => array(
                'is_required' => 'required',
                'type' => 'mobile',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'check_duplicate' => 'Yes',
                'class' => 'required',
                'column' => '6',
                'maxLength' => '15',
                'type_num_alph' => 'tel',
            ),
            'password' => array(
                'is_required' => 'required',
                'type' => 'password',
                'is_register' => 'yes',
                'label' => 'Password',
                'class' => 'required',
                'mode' => $mode
            ),
            'verify_profile' => array(
                'type' => 'radio',
                'value_arr' => ['No', 'Yes'],
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
            ),
            'verify_profile_commission' => array(
                'is_required' => 'required',
                'label' => 'Verify Profile Commission Amount',
                'column' => '6',
                'input_type' => 'number',
                'other' => 'min="0"',
                'class' => 'required'
            ),
            'paid_profile' => array(
                'type' => 'radio',
                'value_arr' => ['No', 'Yes'],
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
            ),
            'paid_profile_commission' => array(
                'is_required' => 'required',
                'label' => 'Paid Profile Commission Amount',
                'column' => '6',
                'input_type' => 'number',
                'other' => 'min="0"',
                'class' => 'required'
            ),
            // 'on_field_verify_profile' => array(
            //     'type' => 'radio',
            //     'value_arr' => ['No', 'Yes'],
            //     'is_required' => 'required',
            //     'class' => 'required',
            //     'is_register' => 'yes',
            // ),
            // 'on_field_verify_profile_commission' => array(
            //     'is_required' => 'required',
            //     'label' => 'On Field Profile Commission Amount',
            //     'column' => '6',
            //     'input_type' => 'number',
            //     'other' => 'min="0"',
            //     'class' => 'required'
            // ),
            'bank_name' => array(
                'label' => 'Bank Name',
                'class' => '',
                'column' => '6'
            ),
            'bank_account_holder_name' => array(
                'label' => 'Bank Account Holder Name',
                'class' => '',
                'column' => '6'
            ),
            'bank_account_type' => array('type' => 'radio', 'value_arr' => array('Savings account' => 'Savings account', 'Current account' => 'Current account')),
            'bank_account_number' => array(
                'label' => 'Bank Account Number',
                'class' => '',
                'column' => '6'
            ),
            'bank_ifsc_code' => array(
                'label' => 'Bank IFSC Code',
                'class' => '',
                'column' => '6'
            ),
            'upi_id' => array(
                'label' => 'UPI Id',
                'class' => '',
                'column' => '6'
            ),
            'country_id' => array(
                'class' => ' not_reset select2 ',
                'label' => 'Country Name',
                'type' => 'dropdown',
                'column' => '6',
                'is_required' => 'required',
                'onchange' => "dropdownChange('country_id','state_id','state_list')",
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'id',
                    'class' => '',
                    'key_disp' => 'country_name'
                )
            ),
            'state_id' => array(
                'label' => 'State Name',
                'column' => '6',
                'class' => ' not_reset select2 ',
                'type' => 'dropdown',
                'is_required' => 'required',
                'onchange' => "dropdownChange('state_id','city_id','state_list')",
                'relation' => array(
                    'rel_model' => 'StateMaster',
                    'key_val' => 'id',
                    'key_disp' => 'state_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'country_id'
                ),
                'onchange' => "dropdownChange('state_id','city_id','city_list')"
            ),
            'city_id' => array(
                'type' => 'dropdown',
                'label' => 'City Name',
                'class' => 'select2',
                'is_required' => 'required',
                'relation' => array(
                    'rel_model' => 'CityMaster',
                    'key_val' => 'id',
                    'key_disp' => 'city_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'state_id',
                    'rel_col_name' => 'state_id',
                ),
                'label' => 'City Name',
                'column' => '6'
            ),
            'image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.AFFILIATE_MEMBER_PHOTOS_URL',
                'other' => 'data-width="200" data-height="200"',
                'crop_image' => 'Yes'
            ),
            'status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = AffiliateMember::find($id);
        }
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.affiliateMember.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.affiliateMember.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();
        $updateArr = array(
            'gender',
            'fullname',
            'email',
            'mobile',
            'verify_profile',
            'verify_profile_commission',
            'paid_profile',
            'paid_profile_commission',
            // 'on_field_verify_profile',
            // 'on_field_verify_profile_commission',
            'bank_account_number',
            'bank_account_holder_name',
            'upi_id',
            'bank_account_type',
            'bank_name',
            'bank_ifsc_code',
            'country_id',
            'state_id',
            'city_id',
            'image',
            'status'
        );
        $updateData = _getRequestData($updateArr, $postData);

        if (_getConstant('DISABLE_DEMO') != 'Enabled') {

            ## Check Duplicate Email / Mobile :
            $duplicateQuery = AffiliateMember::where(function ($q) use ($updateData) {
                $q->where('email', $updateData['email'])
                    ->orWhere('mobile', $updateData['mobile']);
            });

            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $duplicateQuery->where('id', '!=', $postData['id']);
            }

            $duplicateRecord = $duplicateQuery->first();
            if ($duplicateRecord) {
                if ($duplicateRecord->email === $updateData['email']) {
                    return back()->withInput()->with('error', 'Email already exists.');
                }
                return back()->withInput()->with('error', 'Mobile number already exists.');
            }
        }

        ## Check If File Exit Or Not And Validation:
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (!empty($value['name'])) {
                    $path = _getConstant($postData[$key . '_path']);
                    $oldValue = '';
                    if (!blank($postData[$key . '_val'])) {
                        $oldValue = $postData[$key . '_val'];
                    }
                    $uploadedFiles = UploadHelper::uploadFile($request->file($key), $path, $oldValue);
                    $updateData[$key] = $uploadedFiles;
                }
            }
        }

        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                if (isset($postData['password']) && $postData['password'] != '') {
                    $updateData['password'] = Hash::make($postData['password']);
                }
                $whereArr = ['id' => $postData['id']];
                AffiliateMember::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                /* ---------- Generate Referral Code ---------- */
                $refferalCode = _createReferalCode(12);
                $referralLink = route('web.register.referral', [
                    'type' => 'affiliate',
                    'code' => $refferalCode,
                ]);

                /* ---------- QR Folder (public storage) ---------- */
                $qrFolder = storage_path('app/public/' . _getConstant('upload_path.AFFILIATE_QR_CODE_IMG'));
                if (!File::exists($qrFolder)) {
                    File::makeDirectory($qrFolder, 0755, true);
                }
                /* ---------- QR File ---------- */
                $qrFileName = 'qr_' . time() . '_' . rand(100, 999) . '.svg';
                $qrFullPath = $qrFolder . '/' . $qrFileName;

                /* ---------- Generate QR SVG ---------- */
                QrCode::format('svg')->size(300)->margin(2)->generate($referralLink, $qrFullPath);

                $updateData['created_at'] = _getCurrentDate();
                $updateData['password'] = Hash::make($postData['password']);
                $updateData['referral_code'] = $refferalCode;
                $updateData['qr_image'] = $qrFileName;
                AffiliateMember::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {

        $resultArr = AffiliateMember::with([
            'country:id,country_name',
            'state:id,state_name',
            'city:id,city_name',
        ])->where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

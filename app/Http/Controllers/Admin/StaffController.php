<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffCommission;
use App\Models\StaffHolidayMaster;
use App\Models\StaffLeave;
use App\Models\StaffPayHeadMaster;
use App\Models\StaffSalaryDetail;
use App\Models\StaffSalaryMaster;
use App\Models\StaffSalaryPayHead;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $duplicateKeyCheck;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly('Staff') ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staff';
        $this->searchColumn = ['username', 'email', 'mobile'];
        $this->duplicateKeyCheck = ['email', 'mobile'];
        $this->pageName = 'Manage Staff';
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
                'conditionColumn' => 'staff.status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'staff.status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Check Staff Access :
        if ($redirect = $this->checkStaffLoginAccess()) {
            return $redirect;
        }

        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.staff.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.staff.changeStatus',
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
                'add' => 'admin.staff.addForm',
                'edit' => 'admin.staff.editForm',
                'view' => 'admin.staff.viewDetails',
                'copyaccessLinkVal' => route('staff.login')
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

            // Tab-wise counts :
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data :
            $resultArr = Staff::with(['staffRole:id,role_name'])->when(!empty($whereArr), function ($q) use ($whereArr) {
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
                    'edit' => 'admin.staff.editForm',
                    'view' => 'admin.staff.viewDetails',
                    'paySlip' => 'admin.staff.paySlip',
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
            $tabWiseCountData[$tabId] = Staff::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
        }

        return $tabWiseCountData;
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
            Staff::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Staff::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        ## Check Staff Access :
        if ($redirect = $this->checkStaffLoginAccess($id)) {
            return $redirect;
        }

        $mode = ($id != '') ? 'edit' : 'add';
        ## Birthdate :
        $currentDate = _getCurrentDate('Y-m-d');
        $date = strtotime($currentDate . ' -18 year');
        $maxBirthdate = date('Y-m-d', $date);
        $elementArr = array(
            'gender' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['Male' => 'Male', 'Female' => 'Female'],
                'is_required' => 'required',
                'class' => 'required',
                'is_register' => 'yes',
                'label' => 'Gender'
            ),
            'role_id' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'column' => '6',
                'relation' => array(
                    'rel_model' => 'StaffRole',
                    'key_val' => 'id',
                    'key_disp' => 'role_name'
                ),
                'class' => 'required select2'
            ),
            'username' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'mobile' => array(
                'is_required' => 'required',
                'type' => 'mobile',
                'class' => 'required single',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'column' => '6'
            ),
            'email' => array(
                'is_required' => 'required',
                'input_type' => 'email',
                'class' => 'required',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
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
            'birthdate' => array(
                'is_required' => 'required',
                'placeholder' => 'Date of birth',
                'input_type' => 'date',
                'label' => 'Date of birth',
                'class' => 'required',
                'is_register' => 'yes',
                'other' => 'max="' . $maxBirthdate . '"'
            ),
            'marital_status' => array(
                'is_required' => 'required',
                'label' => 'Marital Status',
                'type' => 'dropdown',
                'is_register' => 'yes',
                'relation' => array(
                    'rel_model' => 'MaritalStatusMaster',
                    'key_val' => 'marital_status_name',
                    'key_disp' => 'marital_status_name'
                ),
                'class' => 'select2'
            ),
            'basic_salary' => array(
                'is_required' => 'required',
                'label' => 'Basic Salary',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),

            'blood_group' => array(
                'type' => 'dropdown',
                'is_register' => 'yes',
                'label' => 'Blood Group',
                'relation' => array(
                    'rel_model' => 'BloodGroupMaster',
                    'key_val' => 'id',
                    'key_disp' => 'blood_group_name'
                ),
                'class' => 'select2'
            ),

            'address' => array(
                'type' => 'textarea',
                'label' => 'Address',
                'column' => '12'
            ),

            'country_id' => array(
                'type' => 'dropdown',
                'label' => 'Country',
                'class' => 'select2 single',
                'onchange' => "dropdownChange('country_id','state_id','state_list')",
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'id',
                    'key_disp' => 'country_name'
                ),
                'column' => '6'
            ),

            'state_id' => array(
                'type' => 'dropdown',
                'label' => 'State',
                'class' => 'single select2',
                'onchange' => "dropdownChange('state_id','city','city_list')",
                'relation' => array(
                    'rel_model' => 'StateMaster',
                    'key_val' => 'id',
                    'key_disp' => 'state_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'country_id'
                ),
                'column' => '6'
            ),

            'city' => array(
                'type' => 'dropdown',
                'label' => 'City',
                'class' => 'select2',
                'relation' => array(
                    'rel_model' => 'CityMaster',
                    'key_val' => 'id',
                    'key_disp' => 'city_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'state_id'
                ),
                'column' => '6'
            ),

            'telephone_no' => array(
                'label' => 'Telephone No.',
                'column' => '6',
                'type_num_alph' => 'tel',
                'input_type' => 'tel',
                'maxlength' => '15'
            ),

            'identity_document' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'label' => 'Identity Document',
                'value_arr' => _getStaticArr('documentType'),
                'class' => 'required select2',
                'column' => '6'
            ),

            'identity_number' => array(
                'is_required' => 'required',
                'label' => 'Identity No.',
                'class' => 'required',
                'column' => '6'
            ),

            'id_number' => array(
                'label' => 'ID Number',
                'column' => '6'
            ),

            'employee_type' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'label' => 'Employee Type',
                'value_arr' => _getStaticArr('staffEmployeeType'),
                'class' => 'required select2',
                'column' => '6'
            ),

            'joining_date' => array(
                'is_required' => 'required',
                'input_type' => 'date',
                'label' => 'Joining Date',
                'class' => 'required',
                'column' => '6'
            ),

            'designation' => array(
                'is_required' => 'required',
                'label' => 'Designation',
                'class' => 'required',
                'column' => '6'
            ),

            'department' => array(
                'is_required' => 'required',
                'label' => 'Department',
                'class' => 'required',
                'column' => '6'
            ),

            'pan_no' => array(
                'is_required' => 'required',
                'label' => 'PAN No.',
                'class' => 'required',
                'column' => '6'
            ),

            'nationality' => array(
                'label' => 'Nationality',
                'column' => '6'
            ),

            'bank_name' => array(
                'label' => 'Bank Name',
                'column' => '6'
            ),

            'bank_account_no' => array(
                'label' => 'Bank Account No.',
                'column' => '6',
                'type_num_alph' => 'tel',
                'input_type' => 'tel',
                'maxlength' => '20'
            ),

            'bank_ifsc_code' => array(
                'label' => 'IFSC Code',
                'column' => '6'
            ),

            'pf_account_no' => array(
                'is_required' => 'required',
                'label' => 'PF Account No.',
                'class' => 'required',
                'column' => '6'
            ),

            'uan_no' => array(
                'label' => 'UAN No.',
                'column' => '6'
            ),

            'commission_applicable' => array(
                'is_required' => 'required',
                'type' => 'radio',
                'label' => 'Commission Applicable',
                'value_arr' => array(
                    'No' => 'No',
                    'Yes' => 'Yes'
                ),
                'class' => 'required',
                'column' => '6'
            ),

            'commission_value' => array(
                'is_required' => 'required',
                'label' => 'Commission Value',
                'input_type' => 'number',
                'class' => 'required',
                'column' => '6'
            ),

            'total_sick_leave' => array(
                'is_required' => 'required',
                'label' => 'Total Sick Leave',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),

            'monthly_applied_sick_leave' => array(
                'is_required' => 'required',
                'label' => 'Monthly Applied Sick Leave',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),

            'total_paid_leave' => array(
                'is_required' => 'required',
                'label' => 'Total Paid Leave',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),

            'monthly_applied_paid_leave' => array(
                'is_required' => 'required',
                'label' => 'Monthly Applied Paid Leave',
                'class' => 'required',
                'input_type' => 'number',
                'column' => '6'
            ),
            'profile_image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.STAFF_IMAGE_URL',
                'class' => 'required',
                'other' => 'data-width="200" data-height="200"',
                'crop_image' => 'Yes'
            ),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = Staff::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.staff.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.staff.addEdit',
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
            'gender',
            'role_id',
            'username',
            'email',
            'mobile',
            'birthdate',
            'marital_status',
            'status',
            'basic_salary',
            'blood_group',
            'address',
            'country_id',
            'state_id',
            'city',
            'telephone_no',
            'identity_document',
            'identity_number',
            'id_number',
            'employee_type',
            'joining_date',
            'designation',
            'department',
            'pan_no',
            'nationality',
            'bank_name',
            'bank_account_no',
            'bank_ifsc_code',
            'pf_account_no',
            'uan_no',
            'commission_applicable',
            'commission_value',
            'total_sick_leave',
            'monthly_applied_sick_leave',
            'total_paid_leave',
            'monthly_applied_paid_leave',
        );
        $updateData = _getRequestData($updateArr, $postData);
        // Handle empty integer fields
        foreach (['state_id', 'city'] as $field) {
            if (isset($updateData[$field]) && $updateData[$field] === '') {
                $updateData[$field] = 0;
            }
        }
        ## Check If File Exit Or Not And Validation: Date : 03-08-2023
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
            ## Check Duplicate :
            $duplicateCount = '';
            if (_getConstant('DISABLE_DEMO') != 'Enabled') {
                $duplicateCount = $this->checkDuplicate($request);
            }
            if (!blank($duplicateCount)) {
                return redirect()->route($postData['callbackUrl'])->with('error', "Duplicate Data found for $duplicateCount");
            }
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $this->updateRecord($updateData, $postData);
                $authUser = Auth::user();
                $userType = _adminUserType($authUser->type);
                if ($userType == 'Staff') {
                    return redirect()->back()->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
                } else {
                    return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
                }
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
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
            $updateData['password_decrypted'] = $postData['password_decrypted'];
        }
        $whereArr = ['id' => $postData['id']];
        Staff::where($whereArr)->update($updateData);
    }

    public function insertRecord($updateData, $postData)
    {
        $mobileNumber = $this->mobileNumberAdd($postData);
        if (isset($mobileNumber) && $mobileNumber != '') {
            $updateData['mobile'] = $mobileNumber;
        }
        if (!empty($postData['password_decrypted'])) {
            $updateData['password'] = Hash::make($postData['password_decrypted']);
            $updateData['password_decrypted'] = $postData['password_decrypted'];
        }

        $updateData['created_at'] = _getCurrentDate();
        $staff = Staff::create($updateData);
        $staff->update([
            'staff_prefix' => 'STF-' . $staff->id,
        ]);
    }

    public function mobileNumberAdd($postData)
    {
        if (isset($postData['mobile_country_code']) && isset($postData['mobile'])) {
            return $postData['mobile_country_code'] . '-' . $postData['mobile'];
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        ## Check Staff Access :
        if ($redirect = $this->checkStaffLoginAccess($id)) {
            return $redirect;
        }

        $resultArr = Staff::with([
            'staffRole:id,role_name'
        ])->where('id', $id)->first();
        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    ## Check Duplicate :
    private function checkDuplicate(Request $request): string
    {
        if (empty($this->duplicateKeyCheck)) {
            return '';
        }
        // Prepare mobile with country code if present
        $mobile = null;
        if ($request->filled('mobile_country_code') && $request->filled('mobile')) {
            $mobile = $request->mobile_country_code . '-' . $request->mobile;
        }

        $query = Staff::query();

        // Exclude current record during update
        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        // Build OR conditions for duplicate fields
        $query->where(function ($q) use ($request, $mobile) {
            foreach ($this->duplicateKeyCheck as $field) {
                $value = $field === 'mobile' ? $mobile : $request->input($field);

                if (!blank($value)) {
                    $q->orWhere($field, $value);
                }
            }
        });

        $duplicateRecord = $query->first();

        if (!$duplicateRecord) {
            return '';
        }

        // Detect exactly which fields are duplicated
        $duplicateFields = [];

        foreach ($this->duplicateKeyCheck as $field) {
            $value = $field === 'mobile' ? $mobile : $request->input($field);

            if (!blank($value) && $duplicateRecord->$field === $value) {
                $duplicateFields[] = _createLabel($field);
            }
        }

        return implode(', ', $duplicateFields);
    }

    ## Salary Slip :
    public function paySlip($id = null)
    {
        ## Check Staff Access :
        if ($redirect = $this->checkStaffLoginAccess()) {
            return $redirect;
        }

        if ($id != null) {
            ## Extra Js :
            $extraJsArr = [];

            $monthYear = _getCurrentDate('Y-m');
            $isExistSalary = StaffSalaryMaster::where('staff_id', $id)->where('month_year', $monthYear)->first();
            if (blank($isExistSalary)) {
                $dataArr = $this->getSalaryData($monthYear, $id);
            } else {
                $dataArr = $this->getEditSalaryData($monthYear, $isExistSalary, $id);
                $dataArr['isEdit'] = 1;
            }
            return view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/paySlip',
                [
                    'pageName' => 'Staff Pay Slip',
                    'ajaxPaginationRequestUrl' => 'admin.staff.paySlipAjax',
                    'changeStatusUrl' => '',
                    'extraJsArr' => $extraJsArr,
                    'dataArr' => $dataArr,
                ]
            );
        } else {
            return redirect()->back();
        }
    }

    ## Salary Slip :
    public function paySlipAjax(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $id = $postData['staff_id'];
            $monthYear = $postData['month_year'];
            $isExistSalary = StaffSalaryMaster::where('staff_id', $id)->where('month_year', $monthYear)->first();
            if (blank($isExistSalary)) {
                $dataArr = $this->getSalaryData($monthYear, $id);
                $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/paySlipAjax', compact('dataArr'));
            } else {
                $dataArr = $this->getEditSalaryData($monthYear, $isExistSalary, $id);
                $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/paySlipAjaxEdit', compact('dataArr'));
            }
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function getSalaryData($monthYear, $id)
    {
        $total_days = date('t', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $total_sundays = count(array_filter(range(1, $total_days), fn($d) => date('w', strtotime("$year-$month-$d")) == 0));
        $start_date = date('Y-m-01', strtotime($monthYear)); // First day of month
        $end_date = date('Y-m-t', strtotime($monthYear));   // Last day of month
        $year_first_date = date('Y-01-01', strtotime($monthYear));
        $previous_month_end = date('Y-m-t', strtotime('-1 month', strtotime($monthYear)));

        $staffData = Staff::find($id);
        $dataArr['staff_data'] = $staffData;
        $dataArr['staff_pay_head_data'] = StaffPayHeadMaster::where('status', 'APPROVED')->get();
        $dataArr['total_holi_day'] = StaffHolidayMaster::where('status', 'APPROVED')
            ->whereBetween('holiday_date', [$start_date, $end_date])
            ->count();

        $sick_leave = StaffLeave::where([
            'status'     => 'APPROVED',
            'leave_type' => 'Sick Leave',
            'staff_id'   => $id,
        ])
            ->where(function ($query) use ($start_date, $end_date) {

                $query->whereBetween('leave_start_date', [$start_date, $end_date])

                    ->orWhereBetween('leave_end_date', [$start_date, $end_date])

                    ->orWhereRaw(
                        '? BETWEEN leave_start_date AND leave_end_date',
                        [$start_date]
                    );
            })
            ->selectRaw("
                SUM(
                    DATEDIFF(
                        LEAST(leave_end_date, ?),
                        GREATEST(leave_start_date, ?)
                    ) + 1
                ) AS total_leave_days
            ", [$end_date, $start_date])
            ->first();
        $sick_leave->total_leave_days = $sick_leave->total_leave_days ?? '0';
        $dataArr['sick_leave'] = $sick_leave;
        $paid_leave = StaffLeave::where([
            'status'     => 'APPROVED',
            'leave_type' => 'Paid Leave',
            'staff_id'   => $id,
        ])
            ->where(function ($query) use ($start_date, $end_date) {

                $query->whereBetween('leave_start_date', [$start_date, $end_date])

                    ->orWhereBetween('leave_end_date', [$start_date, $end_date])

                    ->orWhereRaw(
                        '? BETWEEN leave_start_date AND leave_end_date',
                        [$start_date]
                    );
            })
            ->selectRaw("
                SUM(
                    DATEDIFF(
                        LEAST(leave_end_date, ?),
                        GREATEST(leave_start_date, ?)
                    ) + 1
                ) AS total_leave_days
            ", [$end_date, $start_date])
            ->first();
        $paid_leave->total_leave_days = $paid_leave->total_leave_days ?? '0';
        $dataArr['paid_leave'] = $paid_leave;
        ## Old Leave Calculation :
        $total_sick_leave = StaffLeave::where([
            'status'     => 'APPROVED',
            'leave_type' => 'Sick Leave',
            'staff_id'   => $id,
        ])
            ->where(function ($query) use ($year_first_date, $previous_month_end) {

                $query->whereBetween('leave_start_date', [$year_first_date, $previous_month_end])

                    ->orWhereBetween('leave_end_date', [$year_first_date, $previous_month_end])

                    ->orWhereRaw(
                        '? BETWEEN leave_start_date AND leave_end_date',
                        [$year_first_date]
                    );
            })
            ->selectRaw("
                SUM(
                    DATEDIFF(
                        LEAST(leave_end_date, ?),
                        GREATEST(leave_start_date, ?)
                    ) + 1
                ) AS total_leave_days
            ", [$previous_month_end, $year_first_date])
            ->first();
        $total_sick_leave->total_leave_days = $total_sick_leave->total_leave_days ?? '0';
        $dataArr['total_sick_leave'] = $total_sick_leave;
        $total_paid_leave = StaffLeave::where([
            'status'     => 'APPROVED',
            'leave_type' => 'Paid Leave',
            'staff_id'   => $id,
        ])
            ->where(function ($query) use ($year_first_date, $previous_month_end) {

                $query->whereBetween('leave_start_date', [$year_first_date, $previous_month_end])

                    ->orWhereBetween('leave_end_date', [$year_first_date, $previous_month_end])

                    ->orWhereRaw(
                        '? BETWEEN leave_start_date AND leave_end_date',
                        [$year_first_date]
                    );
            })
            ->selectRaw("
                SUM(
                    DATEDIFF(
                        LEAST(leave_end_date, ?),
                        GREATEST(leave_start_date, ?)
                    ) + 1
                ) AS total_leave_days
            ", [$previous_month_end, $year_first_date])
            ->first();
        $total_paid_leave->total_leave_days = $total_paid_leave->total_leave_days ?? '0';
        $dataArr['total_paid_leave'] = $total_paid_leave;
        $dataArr['remaining_sick_leave'] = $staffData->total_sick_leave - $total_sick_leave->total_leave_days;
        $dataArr['remaining_paid_leave'] = $staffData->total_paid_leave - $total_paid_leave->total_leave_days;
        $dataArr['salary_month_year'] = $monthYear;
        $dataArr['total_days'] = $total_days;
        $dataArr['total_sundays'] = $total_sundays;
        $dataArr['total_working_days'] = $total_days - $total_sundays - $dataArr['total_holi_day'];
        ## Attendance Calculation :
        $attendanceData = StaffAttendance::where('staff_id', $id)->whereDate('punch_in', '>=', $start_date)->whereDate('punch_in', '<=', $end_date)->get();
        $total_seconds = 0;
        foreach ($attendanceData as $row) {
            if (!empty($row->punch_out)) {
                $in_time  = strtotime($row->punch_in);
                $out_time = strtotime($row->punch_out);
                $diff = $out_time - $in_time;
                $total_seconds += $diff;
            }
        }
        $dataArr['total_hours'] = round($total_seconds / 3600);
        ## Get Total Commission Earned:
        $total_commission_earned = StaffCommission::where([
            'staff_id' => $id,
            'currency' => 'INR',
        ])
            ->whereBetween('created_at', [
                $start_date . ' 00:00:00',
                $end_date . ' 23:59:59'
            ])
            ->sum('commssion_amount');
        $total_commission_earned = $total_commission_earned ?? '0';
        $dataArr['total_commission_earned'] = $total_commission_earned;

        return $dataArr;
    }

    public function getEditSalaryData($monthYear, $isExistSalary, $id)
    {
        $start_date = date('Y-m-01', strtotime($monthYear)); // First day of month
        $end_date = date('Y-m-t', strtotime($monthYear));   // Last day of month
        $staffData = Staff::find($id);
        $dataArr['staff_data'] = $staffData;
        $dataArr['staff_pay_head_data'] = StaffPayHeadMaster::where('status', 'APPROVED')->get();

        ## Get Total Commission Earned:
        $total_commission_earned = StaffCommission::where([
            'staff_id' => $id,
            'currency' => 'INR',
        ])
            ->whereBetween('created_at', [
                $start_date . ' 00:00:00',
                $end_date . ' 23:59:59'
            ])
            ->sum('commssion_amount');
        $total_commission_earned = $total_commission_earned ?? '0';
        $dataArr['total_commission_earned'] = $total_commission_earned;

        $dataArr['salary_month_year'] = $monthYear;
        $dataArr['salary_data'] = $isExistSalary;
        $dataArr['staff_salary_pay_heads'] = StaffSalaryPayHead::where('staff_salary_id', $isExistSalary->id)->get();
        $dataArr['staff_salary_details'] = StaffSalaryDetail::where('staff_salary_id', $isExistSalary->id)->first();

        return $dataArr;
    }

    ## Save Salary Slip :
    public function saveSalarySlip(Request $request)
    {
        $postData = $request->all();
        if ($postData) {
            $staffSalaryPayHeadArr = [];
            $total_earnings = 0;
            $total_deductions = $postData['total_unpaid_leaves_amount'];
            if (isset($postData['pay_head_id']) && !empty($postData['pay_head_id'])) {
                foreach ($postData['pay_head_id'] as $key => $value) {
                    $payHeadData = StaffPayHeadMaster::find($key);
                    if (!empty($payHeadData)) {
                        $staffSalaryPayHeadArr[] = [
                            'staff_id' => $postData['staff_id'],
                            'pay_head_id' => $key,
                            'pay_head_title' => $payHeadData->title,
                            'pay_head_type' => $payHeadData->pay_head_type,
                            'pay_head_amount' => $value,
                            'month_year' => $postData['month_year'],
                        ];
                        if ($payHeadData->pay_head_type == 'Earning') {
                            $total_earnings += $value;
                        } elseif ($payHeadData->pay_head_type == 'Deduction') {
                            $total_deductions += $value;
                        }
                    }
                }
            }
            $total_net_payable_salary = $postData['basic_salary'] + $total_earnings - $total_deductions;
            $salaryMasterData = [
                'staff_id' => $postData['staff_id'],
                'total_days' => $postData['total_days'],
                'working_days' => $postData['working_days'],
                'payable_days' => $postData['payable_days'],
                'month_year' => $postData['month_year'],
                'basic_salary' => $postData['basic_salary'],
                'total_earning' => $total_earnings,
                'total_deduction' => $total_deductions,
                'total_net_payable_salary' => $total_net_payable_salary,
                'salary_pay_date' => $postData['salary_pay_date'],
            ];
            $StaffSalaryMaster = StaffSalaryMaster::create($salaryMasterData);
            $staff_salary_id = $StaffSalaryMaster->id;
            if (!empty($staffSalaryPayHeadArr)) {
                foreach ($staffSalaryPayHeadArr as $index => $payHeadData) {
                    $payHeadData['staff_salary_id'] = $staff_salary_id;
                    StaffSalaryPayHead::create($payHeadData);
                }
            }

            $salaryDetailsArr = [
                'staff_salary_id' => $staff_salary_id,
                'staff_id' => $postData['staff_id'],
                'month_year' => $postData['month_year'],
                'basic_salary' => $postData['basic_salary'],
                'total_earning' => $total_earnings,
                'total_deduction' => $total_deductions,
                'total_net_payable_salary' => $total_net_payable_salary,
                'total_days' => $postData['total_days'],
                'working_days' => $postData['working_days'],
                'total_holi_day' => $postData['total_holi_day'],
                'total_present_days' => $postData['total_present_days'],
                'attendance_leaves' => $postData['attendance_leaves'],
                'total_working_hours' => $postData['total_working_hours'],
                'total_attendance_hour' => $postData['total_attendance_hour'],
                'overtime_shortfall_hrs' => $postData['overtime_shortfall_hrs'],
                'total_paid_leave' => $postData['total_paid_leave'],
                'total_sick_leave' => $postData['total_sick_leave'],
                'total_annual_leave' => $postData['total_annual_leave'],
                'applicable_paid_leave' => $postData['applicable_paid_leave'],
                'applicable_sick_leave' => $postData['applicable_sick_leave'],
                'total_applicable_this_month_leaves' => $postData['total_applicable_this_month_leaves'],
                'applied_paid_leave' => $postData['applied_paid_leave'],
                'applied_sick_leave' => $postData['applied_sick_leave'],
                'total_leaves' => $postData['total_leaves'],
                'remaining_paid_leave' => $postData['remaining_paid_leave'],
                'remaining_sick_leave' => $postData['remaining_sick_leave'],
                'total_remain_leaves' => $postData['total_remain_leaves'],
                'deductable_paid_leaves' => $postData['deductable_paid_leaves'],
                'deductable_sick_leaves' => $postData['deductable_sick_leaves'],
                'total_deductable_leaves' => $postData['total_deductable_leaves'],
                'perday_salary' => $postData['perday_salary'],
                'payable_days' => $postData['payable_days'],
                'net_payable_salary' => $postData['net_payable_salary'],
                'total_unpaid_leaves' => $postData['total_unpaid_leaves'],
                'total_unpaid_leaves_amount' => $postData['total_unpaid_leaves_amount'],
            ];

            StaffSalaryDetail::create($salaryDetailsArr);
            return redirect()->back()->with('success', 'Data added successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    ## Update Salary Slip :
    public function updateSalarySlip(Request $request)
    {
        $postData = $request->all();
        if (!empty($postData) && isset($postData['staff_salary_id']) && $postData['staff_salary_id'] != '') {
            $salaryData = StaffSalaryMaster::find($postData['staff_salary_id']);
            if (!empty($salaryData)) {
                $staffSalaryPayHeadArr = [];
                $total_earnings = 0;
                $total_deductions = $postData['total_unpaid_leaves_amount'];
                if (isset($postData['pay_head_id']) && !empty($postData['pay_head_id'])) {
                    foreach ($postData['pay_head_id'] as $key => $value) {
                        $payHeadData = StaffPayHeadMaster::find($key);
                        if (!empty($payHeadData)) {
                            $staffSalaryPayHeadArr[] = [
                                'staff_salary_id' => $postData['staff_salary_id'],
                                'staff_id' => $postData['staff_id'],
                                'pay_head_id' => $key,
                                'pay_head_title' => $payHeadData->title,
                                'pay_head_type' => $payHeadData->pay_head_type,
                                'pay_head_amount' => $value,
                                'month_year' => $postData['month_year'],
                            ];
                            if ($payHeadData->pay_head_type == 'Earning') {
                                $total_earnings += $value;
                            } elseif ($payHeadData->pay_head_type == 'Deduction') {
                                $total_deductions += $value;
                            }
                        }
                    }
                }
                $total_net_payable_salary = $salaryData->basic_salary + $total_earnings - $total_deductions;
                $salaryMasterData = [
                    'total_earning' => $total_earnings,
                    'total_deduction' => $total_deductions,
                    'total_net_payable_salary' => $total_net_payable_salary,
                    'salary_pay_date' => $postData['salary_pay_date'],
                    'updated_at' => _getCurrentDate()
                ];
                StaffSalaryMaster::where('id', $postData['staff_salary_id'])->update($salaryMasterData);
                if (!empty($staffSalaryPayHeadArr)) {
                    StaffSalaryPayHead::where('staff_salary_id', $postData['staff_salary_id'])->forceDelete();
                    foreach ($staffSalaryPayHeadArr as $index => $payHeadData) {
                        StaffSalaryPayHead::create($payHeadData);
                    }
                }

                $salaryDetailsArr = [
                    'total_earning' => $total_earnings,
                    'total_deduction' => $total_deductions,
                    'total_net_payable_salary' => $total_net_payable_salary,
                    'updated_at' => _getCurrentDate()
                ];

                StaffSalaryDetail::where('staff_salary_id', $postData['staff_salary_id'])->update($salaryDetailsArr);
                return redirect()->back()->with('success', 'Salary slip saved successfully.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong!');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function downloadSalarySlip($id = null)
    {
        if ($id != null) {
            $isExistSalary = StaffSalaryMaster::find($id);
            if (!empty($isExistSalary)) {
                $dataArr['staff_data'] = Staff::find($isExistSalary->staff_id);
                $dataArr['salary_data'] = $isExistSalary;
                $dataArr['staff_salary_pay_heads'] = StaffSalaryPayHead::where('staff_salary_id', $id)->get();
                $dataArr['staff_salary_details'] = StaffSalaryDetail::where('staff_salary_id', $id)->first();
                $fileName = $dataArr['staff_data']->staff_prefix . '-' . 'Pay-Slip-' . $dataArr['salary_data']->month_year;
                $dataArr['fileName'] = $fileName;
                ini_set('memory_limit', '512M');
                set_time_limit(300);
                $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/salary_slip', $dataArr)->render();
                $pdf = Pdf::loadHTML($html);

                return $pdf->download($fileName . '.pdf');
            } else {
                return redirect()->back()->with('error', 'Something went wrong!');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function salarySlipIndex()
    {
        ## Check Staff Access :
        if ($redirect = $this->checkStaffLoginAccess()) {
            return $redirect;
        }

        ## Extra Js :
        $extraJsArr = ['/custom/js/commonList.js'];
        $dataArr = [
            'pageName' => 'Staff Salary Slips',
            'ajaxPaginationRequestUrl' => 'admin.staff.getAjaxPaginationDataSalarySlip',
            'changeStatusUrl' => '',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'view' => 0,
            ],
            'actionButtonUrl' => [],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function checkStaffLoginAccess($id = null)
    {
        $authUser = Auth::user();
        // If user is not logged in
        if (!$authUser) {
            return redirect()->route('admin.login');
        }
        // Invalid/empty ID
        $userType = _adminUserType($authUser->type);
        if (empty($id) && $userType != 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        $userType = _adminUserType($authUser->type);
        // Staff can access only their own profile
        if ($userType === 'Staff' && (int) $authUser->id !== (int) $id) {
            return redirect()->route('admin.dashboard');
        }
        return null;
    }
}

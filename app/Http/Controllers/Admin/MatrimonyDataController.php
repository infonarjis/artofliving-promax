<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Helpers\UploadHelper;
use App\Services\AdminFormBuilderService;
use App\Models\MatrimonyData;
use App\Services\AiMatrimonyDataService;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\CountryMaster;
use App\Models\MotherTongueMaster;
use App\Models\ReligionMaster;
use App\Models\StateMaster;

class MatrimonyDataController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $customJsDirectory;
    private $statusTabArr;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderServic
    ) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderServic;
        $this->directoryName = '/matrimonyData';
        $this->searchColumn = ['pagename', 'title', 'matrimony_name', 'search_type'];
        $this->pageName = 'Manage Matrimony Data';
        $this->customJsDirectory = '/custom/js';
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

    public function index()
    {
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.matrimonyData.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.matrimonyData.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'view' => 1,
                'isSearch' => 1
            ],
            'actionButtonUrl' => [
                'add' => 'admin.matrimonyData.addForm',
                'edit' => 'admin.matrimonyData.editForm',
                'view' => 'admin.matrimonyData.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => []
        ];

        $postData = $request->all();

        if (!blank($postData)) {

            $page  = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = MatrimonyData::query();

            $query->where('lang_code', _getDefaultLanguage());

            ## Condition Filter :
            if (isset($postData['conditionColumn']) && $postData['conditionVal'] != '') {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            ## Search Filter:
            if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '') {
                $searchKeyword = $postData['searchKeyword'];
                $query->where(function ($q) use ($searchKeyword) {
                    foreach ($this->searchColumn as $column) {
                        $q->orWhere($column, 'LIKE', "%$searchKeyword%");
                    }
                });
            }

            $resultArr = $query->orderBy('id', 'DESC')->paginate($limit, ['*'], 'page', $page);

            $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $postData);

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count  :
    public function tabWiseCountData($tabArr, $postData)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $query = MatrimonyData::query();
            $query->where('lang_code', _getDefaultLanguage());
            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }
            if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '') {
                $query->where(function ($q) use ($postData) {
                    foreach ($this->searchColumn as $column) {
                        $q->orWhere($column, 'LIKE', '%' . $postData['searchKeyword'] . '%');
                    }
                });
            }
            $tabWiseCountData[$tabId] = $query->count();
        }
        return $tabWiseCountData;
    }

    ## Change Status :
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
            MatrimonyData::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            MatrimonyData::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Add / Edit Form :
    public function addEditForm($id = '')
    {
        $elementArr = array(
            'pagename' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'slug' => array('check_duplicate' => 'Yes', 'column' => '6'),
            'matrimony_description' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '12'
            ),
            'banner_img' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.COMMUNITY_BANNER_IMAGE_URL',
                'class' => 'required',
                'label' => 'Banner image'
            ),
            'search_type' => array(
                'is_required' => 'required',
                'class' => ' not_reset select2 ',
                'column' => '6',
                'type' => 'dropdown',
                'onchange' => "dropdownChangeCom('matrimony_name_val','search_type')",
                'value_arr' => array(
                    'Religion' => 'Religion',
                    'Caste' => 'Caste',
                    'Mother-Tongue' => 'Mother Tongue',
                    'Country' => 'Country',
                    'State' => 'State',
                    'City' => 'City'
                )
            ),
            'matrimony_name' => array(
                'input_type' => 'hidden'
            ),
            'matrimony_name_val' => array(
                'is_required' => 'required',
                'label' => 'Matrimony Name',
                'type' => 'dropdown',
                'class' => 'select2',
                'column' => '6',
                'relation' => array(
                    'rel_model' => 'MatrimonyData',
                    'key_val' => 'matrimony_name',
                    'key_disp' => 'matrimony_name',
                    'rel_col_name' => 'search_type',
                    'not_load_add' => 'yes'
                )
            ),
            'meta_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'meta_keyword' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '6'
            ),
            'meta_description' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '12'
            ),
            'match_type' => array(
                'display_in' => '2',
                'type' => 'radio',
                'value_arr' => ['0' => 'Auto', '1' => 'Manually'],
                'value' => '0',
                'column' => '12'
            ),
            'matri_id_groom' => array(
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'column' => '6',
                'is_multiple' => 'yes',
                'class' => 'single',
                'form_group_class' => ' matri_id_groom',
                'relation' => array(
                    'rel_model' => 'Register',
                    'key_val' => 'matri_id',
                    'key_disp' => 'matri_id',
                    'rel_col_name' => 'gender',
                    'rel_col_val' => 'Male'
                )
            ),
            'matri_id_bride' => array(
                'type' => 'dropdown',
                'display_placeholder' => 'No',
                'column' => '6',
                'is_multiple' => 'yes',
                'class' => 'single',
                'form_group_class' => ' matri_id_bride',
                'relation' => array(
                    'rel_model' => 'Register',
                    'key_val' => 'matri_id',
                    'key_disp' => 'matri_id',
                    'rel_col_name' => 'gender',
                    'rel_col_val' => 'Female'
                )
            ),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js'
        ];


        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = MatrimonyData::find($id);
        }

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'columnName' => '*',
            'callbackUrl' => 'admin.matrimonyData.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.matrimonyData.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'languageDataArr' => _getActiveLanguage(),
            'id' => $id,
            'mode' => $mode,
            'rowData' => $rowData,
            'extraJsArr' => $extraJsArrAdd
        ]);
    }

    ## Add / Update Data :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);
        $mode = $request->input('mode');
        $duplicate = $this->checkDuplicate($request, $slug);

        if ($duplicate) {
            return redirect()->route($postData['callbackUrl'])
                ->with('error', "Duplicate Data found for $duplicate");
        }

        $updateData = $request->only([
            'pagename',
            'title',
            'slug',
            'matrimony_description',
            'search_type',
            // 'matrimony_name',
            'meta_keyword',
            'meta_title',
            'meta_description',
            'match_type',
            'matri_id_groom',
            'matri_id_bride',
            'banner_img',
            'status'
        ]);

        $updateData['slug'] = $slug;

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

        ## Slug :
        $updateData['slug'] = $request->slug ?: Str::slug($request->title);

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

        $defaultLanguage = _getDefaultLanguage();
        try {
            if ($request->matrimony_name_val != '') {
                $updateData['matrimony_name'] = $request->matrimony_name_val;
            }
            // ================= ADD =================
            if ($mode === 'add') {
                // Create new translated row
                $updateData['lang_id']   = $request->id;
                $updateData['lang_code'] = $request->lang_code;
                MatrimonyData::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }

            // ================= DEFAULT LANGUAGE UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = MatrimonyData::find($request->id);
                if (!$model) {
                    return redirect()->route($postData['callbackUrl'])->with('error', 'Record not found.');
                }
                $model->update($updateData);
            } else {
                // ================= OTHER LANGUAGE =================
                $existingLangRow = MatrimonyData::where([
                    'lang_id'   => $request->id,
                    'lang_code' => $request->lang_code,
                ])->first();
                if ($existingLangRow) {
                    unset($updateData['slug']);
                    // Update existing translated row
                    $existingLangRow->update($updateData);
                } else {
                    // Create new translated row
                    $updateData['lang_id']   = $request->id;
                    $updateData['lang_code'] = $request->lang_code;
                    $updateData['status']    = 'APPROVED';
                    MatrimonyData::create($updateData);
                }
            }
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Throwable $e) {
            return redirect()->route($postData['callbackUrl'])->with('error', $e->getMessage());
        }
    }

    ## Duplicate Check :
    private function checkDuplicate($request, $slug)
    {
        $query = MatrimonyData::where('slug', $slug)
            ->where('title', $request->title);

        if ($request->id) {
            $query->where('id', '!=', $request->id);
        }
        return $query->exists() ? 'Title, Slug' : '';
    }

    ## View Details :
    public function viewDetails($id = 0)
    {
        $resultArr = MatrimonyData::find($id);
        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    public function getMatrimonyList(Request $request)
    {
        $currentLanguage = App::getLocale();
        $label = '';
        $data  = [];
        switch ($request->get_list) {
            case 'religion_lists':
                $label = _getLang('field_lbl_select_religion');
                $data = ReligionMaster::getDropdown($currentLanguage);
                break;
            case 'caste_dropdown':
                $label = _getLang('field_lbl_select_caste');
                $data = CasteMaster::active()
                    ->where('lang_code', $currentLanguage)
                    ->pluck('caste_name', 'id')
                    ->toArray();
                break;
            case 'mothertongue_lists':
                $label = _getLang('field_lbl_select_mother_tongue');
                $data = MotherTongueMaster::getDropdown($currentLanguage);
                break;
            case 'country_lists':
                $label = _getLang('field_lbl_select_country');
                $data = CountryMaster::getDropdown($currentLanguage);

                $countryList = CountryMaster::getDropdown($currentLanguage);
                $data = [];
                foreach ($countryList as $key => $value) {
                    $data[$value['id']] = $value['country_name'];
                }
                // $topCountries = $countryList->where('is_top_country', 1);
                // $allCountries = $countryList->where('is_top_country', 0);

                break;
            case 'state_listsm':
                $label = _getLang('field_lbl_select_state');
                $data = StateMaster::active()
                    ->where('lang_code', $currentLanguage)
                    ->pluck('state_name', 'id')
                    ->toArray();
                break;
            case 'city_listsm':
                $label = _getLang('field_lbl_select_city');
                $data = CityMaster::active()
                    ->where('lang_code', $currentLanguage)
                    ->pluck('city_name', 'id')
                    ->toArray();
                break;
            default:
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Invalid Request'
                ]);
        }
        $htmlCode = '<option value="">Select ' . $label . '</option>';
        $ids = [];
        if (!empty($request->current_val)) {
            $ids = explode(',', $request->current_val);
        }
        foreach ($data as $key => $val) {
            $selected = '';
            if (in_array($key, $ids)) {
                $selected = 'selected';
            }
            $htmlCode .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
        }
        return response()->json([
            'status' => 'success',
            'html' => $htmlCode
        ]);
    }

    ## Language Data :
    public function getLangData(Request $request)
    {
        $id = $request->id;
        $langCode = $request->langCode;

        $defaultLanguage = _getDefaultLanguage();

        if ($defaultLanguage == $langCode) {
            $data = MatrimonyData::where('id', $id)->first();
        } else {
            $data = MatrimonyData::where('lang_id', $id)
                ->where('lang_code', $langCode)
                ->first();
        }
        return response()->json($data);
    }

    ## Generate Ai Matrimony Pages :
    public function generatePages(Request $request)
    {
        $topic = trim($request->input('topic', ''));

        if (empty($topic)) {
            return response()->json([
                'success' => false,
                'message' => 'Topic is required.',
            ], 422);
        }

        try {
            $service = new AiMatrimonyDataService();
            $data    = $service->generatePage($topic);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            Log::error('generatePages error', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate page content: ' . $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\WeddingPlanner;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class WeddingVendorsController extends Controller
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

        $this->directoryName = '/weddingVendors';
        $this->searchColumn = ['planner_name'];
        $this->pageName = 'Manage Vendor';
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
            'ajaxPaginationRequestUrl' => 'admin.weddingVendors.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.weddingVendors.changeStatus',
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
                'add' => 'admin.weddingVendors.addForm',
                'edit' => 'admin.weddingVendors.editForm',
                'view' => 'admin.weddingVendors.viewDetails',
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
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = WeddingPlanner::when(!empty($whereArr), function ($q) use ($whereArr) {
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
                    'edit' => 'admin.weddingVendors.editForm',
                    'view' => 'admin.weddingVendors.viewDetails',
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
            $tabWiseCountData[$tabId] = WeddingPlanner::query()
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
            WeddingPlanner::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            WeddingPlanner::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $mode = ($id != '') ? 'edit' : 'add';
        $minimumValue = "min='0'";
        $elementArr = array(
            'category_id' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'column' => '6',
                'relation' => array(
                    'rel_model' => 'VendorCategory',
                    'key_val' => 'id',
                    'key_disp' => 'category_name'
                ),
                'label' => 'Category Name',
                'column' => '6',
                'class' => 'required'
            ),
            'planner_name' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'title' => array(
                'is_required' => 'required',
                'label' => 'Venue Title',
                'class' => 'required',
                'column' => '6'
            ),
            'capacity' => array(
                'is_required' => 'required',
                'label' => 'Capacity People',
                'column' => '6',
                'input_type' => 'number',
                'other' => $minimumValue,
                'class' => 'required'
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
                'type_num_alph' => 'tel'
            ),
            'currency' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'column' => '6',
                'relation' => array(
                    'rel_model' => 'CurrencyMaster',
                    'key_val' => 'currency_code',
                    'key_disp' => 'currency_name'
                ),
                'class' => 'required'
            ),
            'start_rate_range' => array(
                'is_required' => 'required',
                'input_type' => 'number',
                'column' => '6',
                'other' => $minimumValue,
                'class' => 'required'
            ),
            'end_rate_range' => array(
                'is_required' => 'required',
                'input_type' => 'number',
                'other' => $minimumValue,
                'class' => 'required',
                'column' => '6'
            ),
            'address' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
            ),
            'country_id' => array(
                'class' => ' not_reset select2 required',
                'is_required' => 'required',
                'label' => 'Country Name',
                'type' => 'dropdown',
                'column' => '6',
                'onchange' => "dropdownChange('country_id','state_id','state_list')",
                'relation' => array(
                    'rel_model' => 'CountryMaster',
                    'key_val' => 'id',
                    'class' => 'required',
                    'key_disp' => 'country_name'
                )
            ),
            'state_id' => array(
                'class' => ' not_reset select2 required',
                'is_required' => 'required',
                'label' => 'State Name',
                'type' => 'dropdown',
                'column' => '6',
                'onchange' => "dropdownChange('state_id','city_id','city_list')",
                'relation' => array(
                    'rel_model' => 'StateMaster',
                    'key_val' => 'id',
                    'key_disp' => 'state_name',
                    'not_load_add' => 'yes',
                    'cus_rel_col_name' => 'country_id'
                )
            ),
            'city_id' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'label' => 'City Name',
                'class' => 'select2 required',
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
            'description' => array('type' => 'textarea', 'class' => 'page-editor', 'column' => '12'),
            'website' => array('input_type' => 'url', 'column' => '6'),
            'facebook_link' => array('input_type' => 'url', 'column' => '6'),
            'twitter_link' => array('input_type' => 'url', 'column' => '6'),
            'google_link' => array('input_type' => 'url', 'column' => '6'),
            'image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.WEDDING_PLANNER_IMAGE_URL',
            ),
            'image_2' => array(
                'type' => 'file',
                'path_value' => 'upload_path.WEDDING_PLANNER_IMAGE_URL',
            ),
            'image_3' => array(
                'type' => 'file',
                'path_value' => 'upload_path.WEDDING_PLANNER_IMAGE_URL',
            ),
            'image_4' => array(
                'type' => 'file',
                'path_value' => 'upload_path.WEDDING_PLANNER_IMAGE_URL',
            ),
            'image_5' => array(
                'type' => 'file',
                'path_value' => 'upload_path.WEDDING_PLANNER_IMAGE_URL',
            ),
            'status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $thirdPartyCssArr = [
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css'
        ];

        $thirdPartyJsArr = [
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/colors/trumbowyg.colors.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/emoji/trumbowyg.emoji.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontfamily/trumbowyg.fontfamily.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontsize/trumbowyg.fontsize.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/history/trumbowyg.history.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/indent/trumbowyg.indent.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.25.1/dist/plugins/lineheight/trumbowyg.lineheight.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.25.0/dist/plugins/pasteimage/trumbowyg.pasteimage.min.js',
        ];

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = WeddingPlanner::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.weddingVendors.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.weddingVendors.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'thirdPartyCssArr' => $thirdPartyCssArr,
            'thirdPartyJsArr' => $thirdPartyJsArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'category_id',
            'planner_name',
            'title',
            'capacity',
            'email',
            'mobile',
            'currency',
            'start_rate_range',
            'end_rate_range',
            'address',
            'country_id',
            'state_id',
            'city_id',
            'description',
            'website',
            'facebook_link',
            'twitter_link',
            'google_link',
            'image',
            'image2',
            'image3',
            'image4',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
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
        $updateData['description'] = $postData['description'];

        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                WeddingPlanner::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                WeddingPlanner::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        $resultArr = WeddingPlanner::with([
            'category:id,category_name',
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

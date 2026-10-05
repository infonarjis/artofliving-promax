<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Services\AdminFormBuilderService;
use App\Helpers\UploadHelper;
use App\Models\CmsPage;
use App\Models\SiteContent;
use Illuminate\Support\Str;

class CmsPagesController extends Controller
{
    private $adminFormBuilderService;
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

        $this->directoryName = '/cmsPages';
        $this->searchColumn = ['page_title', 'seo_title', 'seo_keywords'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Manage CMS Page';

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

    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.cmsPages.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.cmsPages.changeStatus',
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
                'add' => 'admin.cmsPages.addForm',
                'edit' => 'admin.cmsPages.editForm',
                'view' => 'admin.cmsPages.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Ajax Pagination
    |--------------------------------------------------------------------------
    */
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

            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = CmsPage::where('lang_code', _getDefaultLanguage());

            $whereArr = $this->conditionValue($postData);

            foreach ($whereArr as $key => $val) {
                $query->where($key, $val);
            }

            if (!empty($postData['searchKeyword'])) {

                $keyword = $postData['searchKeyword'];

                $query->where(function ($q) use ($keyword) {

                    foreach ($this->searchColumn as $col) {
                        $q->orWhere($col, 'like', "%$keyword%");
                    }
                });
            }

            $resultCount = $query->count();

            $resultArr = $query
                ->orderBy('id', 'DESC')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $postData);

            $resultArr = new LengthAwarePaginator(
                $resultArr,
                $resultCount,
                $limit,
                $page,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page'
                ]
            );

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }

        return response()->json($responseArr, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Tab Wise Count
    |--------------------------------------------------------------------------
    */

    public function tabWiseCountData($tabArr, $postData)
    {
        $tabWiseCountData = [];

        foreach ($tabArr as $key => $value) {

            $tabId = $value['id'] ?? $key;

            $query = CmsPage::where('lang_code', _getDefaultLanguage());

            if (!blank($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function onSearchKeyword($postData)
    {
        $whereStr = '';

        if (isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' && !empty($this->searchColumn)) {

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

    /*
    |--------------------------------------------------------------------------
    | Condition
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */
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
            CmsPage::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            CmsPage::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Add/Edit Form :
    public function addEditForm($id = '')
    {
        $mode = ($id != '') ? 'edit' : 'add';

        $rowData = [];

        if ($mode == 'edit') {
            $rowData = CmsPage::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'columnName' => '*',
            'rowData' => $rowData,
            'callbackUrl' => 'admin.cmsPages.index'
        ];

        $elementArr = array(
            'page_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'page_content' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required page-editor',
                'column' => '12'
            ),
            'seo_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'seo_description' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
            ),
            'seo_keywords' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
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

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.cmsPages.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'mode' => $mode,
            'id' => $id,
            'languageDataArr' => _getActiveLanguage(),
            'extraJsArr' => $extraJsArrAdd,
            'thirdPartyJsArr' => $thirdPartyJsArr,
            'thirdPartyCssArr' => $thirdPartyCssArr
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    public function addEdit(Request $request)
    {
        $request->validate([
            'page_title' => 'required|string|max:255',
            'status'     => 'required',
            'lang_code'  => 'required|string',
            'callbackUrl' => 'required',
        ]);

        $defaultLanguage = _getDefaultLanguage();
        $callbackUrl     = $request->input('callbackUrl');
        $mode            = $request->input('mode');

        $updateData = $request->only([
            'page_title',
            'page_content',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'status',
        ]);

        ## Generate slug :
        if (!empty($updateData['page_title'])) {
            $updateData['page_url'] = Str::slug($updateData['page_title']);
        }
        ## Add New CMS Page :
        if ($mode === 'add') {
            $updateData['lang_id']   = $request->id;
            $updateData['lang_code'] = $request->lang_code;
            CmsPage::create($updateData);
            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_ADDED_SUCCESS'));
        }
        ## Update Default Language :
        if ($request->lang_code === $defaultLanguage) {
            $cmsPage = CmsPage::find($request->id);

            if (!$cmsPage) {
                return redirect()->route($callbackUrl)->with('error', 'Record not found.');
            }

            $cmsPage->update($updateData);
            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }
        ## Add / Update Translation :
        CmsPage::updateOrCreate(
            [
                'lang_id'   => $request->id,
                'lang_code' => $request->lang_code,
            ],
            array_merge($updateData, [
                'status' => $updateData['status'] ?? 'APPROVED',
            ])
        );

        CmsPage::clearCache();

        return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    /*
    |--------------------------------------------------------------------------
    | View Details
    |--------------------------------------------------------------------------
    */

    public function viewDetails($id = 0)
    {
        $resultArr = CmsPage::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Language Data
    |--------------------------------------------------------------------------
    */

    public function getLangData(Request $request)
    {
        $postData = $request->all();

        $id = $postData['id'];
        $langCode = $postData['langCode'];

        $defaultLanguage = _getDefaultLanguage();

        if ($defaultLanguage != $langCode) {
            $row = CmsPage::where(['lang_code' => $langCode, 'lang_id' => $id])->first();
        } else {
            $row = CmsPage::where(['lang_code' => $langCode, 'id' => $id])->first();
        }

        $responseArr = [
            'lang_id' => $row->id ?? $id,
            'lang_code' => $langCode,
            'page_title' => $row->page_title ?? '',
            'page_content' => $row->page_content ?? '',
            'seo_title' => $row->seo_title ?? '',
            'seo_description' => $row->seo_description ?? '',
            'seo_keywords' => $row->seo_keywords ?? '',
        ];

        return response()->json($responseArr, 200);
    }


    ## Homepage Text Setting :
    public function aboutUsPageAddEditForm()
    {
        $elementArr = array(
            'about_us_image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.OTHER_IMAGE_URL',
                'class' => 'required',
                'label' => 'Banner'
            ),
            'about_us_title' => array('is_required' => 'required', 'label' => 'Title', 'column' => '6'),
            'about_us_small_desc' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '12'
            ),
            'about_us_desc' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required page-editor',
                'column' => '12'
            ),
            'about_us_brow_sec1' => array('is_required' => 'required', 'label' => 'Browse Section 1', 'column' => '6'),
            'about_us_brow_sec2' => array('is_required' => 'required', 'label' => 'Browse Section 2', 'column' => '6'),
            'about_us_brow_sec3' => array('is_required' => 'required', 'label' => 'Browse Section 3', 'column' => '6'),
        );

        $mode = 'edit';

        $rowData = SiteContent::where([
            'id' => '1',
            'lang_code' => _getDefaultLanguage()
        ])->first();

        $otherData = [
            'mode' => $mode,
            'id' => '1',
            'rowData' => $rowData,
            'callbackUrl' => 'admin.aboutUsPageAddEditForm'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js'
        ];

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

        $dataArr = [
            'pageName' => 'Update About Us Page',
            'elementArr' => $elementArr,
            'formUrl' => 'admin.aboutUsPageAddEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'languageDataArr' => _getActiveLanguage(),
            'mode' => $mode,
            'id' => '1',
            'rowData' => $rowData,
            'extraJsArr' => $extraJsArrAdd,
            'thirdPartyJsArr' => $thirdPartyJsArr,
            'thirdPartyCssArr' => $thirdPartyCssArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/aboutUsPageAddEdit', $dataArr);
    }

    ## Submit Form :
    public function aboutUsPageAddEdit(Request $request)
    {
        $postData = $request->all();

        $defaultLang = _getDefaultLanguage();
        $langCode = $postData['lang_code'];

        $updateData = [
            'about_us_title' => $postData['about_us_title'] ?? '',
            'about_us_small_desc' => $postData['about_us_small_desc'] ?? '',
            'about_us_desc' => $postData['about_us_desc'] ?? '',
            'about_us_brow_sec1' => $postData['about_us_brow_sec1'] ?? '',
            'about_us_brow_sec2' => $postData['about_us_brow_sec2'] ?? '',
            'about_us_brow_sec3' => $postData['about_us_brow_sec3'] ?? '',
            'status' => $postData['status'] ?? 'APPROVED'
        ];

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

        $updateData['about_us_desc'] = $postData['about_us_desc'];
        if ($langCode == $defaultLang) {
            SiteContent::where('id', $postData['id'])->update($updateData);
        } else {
            $check = SiteContent::where([
                'lang_id' => $postData['id'],
                'lang_code' => $langCode
            ])->first();
            $updateData['lang_code'] = $langCode;
            $updateData['lang_id'] = $postData['id'];
            if ($check) {
                SiteContent::where('id', $check->id)->update($updateData);
            } else {
                SiteContent::create($updateData);
            }
        }

        return redirect()->back()->with(
            'success',
            _getConstant('responce_message.DATA_UPDATED_SUCCESS')
        );
    }

    ## Language Code:
    public function getLangDataAboutUs(Request $request)
    {
        $postData = $request->all();

        $id = $postData['id'];
        $langCode = $postData['langCode'];

        $defaultLanguage = _getDefaultLanguage();

        if ($defaultLanguage == $langCode) {

            $row = SiteContent::where([
                'id' => $id,
                'lang_code' => $langCode
            ])->first();
        } else {

            $row = SiteContent::where([
                'lang_id' => $id,
                'lang_code' => $langCode
            ])->first();
        }

        $aboutUsImgUrl = '';
        if ($row !== null && _checkStorageFileExists('upload_path.OTHER_IMAGE_URL', $row->about_us_image)) {
            $aboutUsImgUrl = _assetUrl('upload_path.OTHER_IMAGE_URL') . $row->about_us_image;
        }

        $responseArr = [
            'lang_id' => $row->id ?? $id,
            'lang_code' => $langCode,
            'about_us_title' => $row->about_us_title ?? '',
            'about_us_small_desc' => $row->about_us_small_desc ?? '',
            'about_us_desc' => $row->about_us_desc ?? '',
            'about_us_brow_sec1' => $row->about_us_brow_sec1 ?? '',
            'about_us_brow_sec2' => $row->about_us_brow_sec2 ?? '',
            'about_us_brow_sec3' => $row->about_us_brow_sec3 ?? '',
            'about_us_image' => $aboutUsImgUrl
        ];

        return response()->json($responseArr, 200);
    }
}

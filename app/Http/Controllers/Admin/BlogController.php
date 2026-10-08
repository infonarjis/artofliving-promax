<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\BlogMaster;
use App\Helpers\UploadHelper;
use App\Services\AdminFormBuilderService;
use Exception;

class BlogController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;
    private $customJsDirectory;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/blog';
        $this->pageName = 'Manage Blog';
        $this->customJsDirectory = '/custom/js';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'isActive' => 1,
                'conditionColumn' => '',
                'conditionVal' => '',
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'APPROVED',
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'UNAPPROVED',
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
            'ajaxPaginationRequestUrl' => 'admin.blog.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.blog.changeStatus',
            'extraJsArr' => $extraJsArr,
            'statusTabArr' => $this->statusTabArr,

            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'view' => 1,
                'isSearch' => 1,
            ],

            'actionButtonUrl' => [
                'add' => 'admin.blog.addForm',
                'edit' => 'admin.blog.editForm',
                'view' => 'admin.blog.viewDetails',
            ],
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Ajax Pagination :
    public function getAjaxPaginationData(Request $request)
    {
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
        $searchKeyword = $request->searchKeyword;

        $defaultLang = _getDefaultLanguage();

        $query = BlogMaster::query()->where('lang_code', $defaultLang);

        ## Status Filter :
        if ($request->conditionColumn && $request->conditionVal) {
            $query->where($request->conditionColumn, $request->conditionVal);
        }

        ## Search :
        if (!empty($searchKeyword)) {
            $query->where('title', 'LIKE', "%{$searchKeyword}%");
        }

        ## Pagination :
        $resultArr = $query
            ->orderBy('id', 'DESC')
            ->paginate($limit, ['*'], 'page', $page);

        ## Tab Counts :
        $dataArr['tabCount'] = [
            'allData' => BlogMaster::where('lang_code', $defaultLang)->count(),
            'approvedData' => BlogMaster::where('status', 'APPROVED')->where('lang_code', $defaultLang)->count(),
            'unapprovedData' => BlogMaster::where('status', 'UNAPPROVED')->where('lang_code', $defaultLang)->count(),
        ];

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
            compact('resultArr')
        )->render();

        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'data' => $dataArr,
            'html' => $html
        ]);
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
            BlogMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            BlogMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Add/Edit Form
    public function addEditForm($id = '')
    {
        $elementArr = array(
            'title' => array('is_required' => 'required', 'class' => 'required'),
            'content' => array('is_required' => 'required', 'type' => 'textarea', 'class' => 'required  page-editor'),
            'blog_image' => array(
                'type' => 'file',
                'class' => 'required',
                'is_required' => 'required',
                'path_value' => 'upload_path.BLOG_IMAGE_URL',
            ),
            'seo_title' => array('class' => '', 'column' => '12'),
            'seo_description' => array(
                'type' => 'textarea',
                'class' => '',
                'column' => '6'
            ),
            'seo_keywords' => array(
                'class' => '',
                'column' => '6'
            ),
            'status' => array('type' => 'radio', 'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')),
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

        $mode = ($id != '') ? 'edit' : 'add';

        $rowData = [];
        if ($mode == 'edit') {
            $rowData = BlogMaster::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'columnName' => '*',
            'rowData' => $rowData,
            'callbackUrl' => 'admin.blog.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.blog.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'languageDataArr' => _getActiveLanguage(),
            'rowData' => $rowData,
            'mode' => $mode,
            'id' => $id,
            'extraJsArr' => $extraJsArrAdd,
            'thirdPartyJsArr' => $thirdPartyJsArr,
            'thirdPartyCssArr' => $thirdPartyCssArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Add / Edit Submit :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateData = $request->only([
            'title',
            'content',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'status'
        ]);

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
            // ================= DEFAULT LANGUAGE UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = BlogMaster::find($request->id);

                if (!$model) {
                    return redirect()->route($postData['callbackUrl'])->with('error', 'Record not found.');
                }

                $model->update($updateData);
            } else {
                // ================= OTHER LANGUAGE =================
                $existingLangRow = BlogMaster::where([
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
                    BlogMaster::create($updateData);
                }
            }
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($postData['callbackUrl'])->with('error', $e->getMessage());
        }
    }

    ## View Details :
    public function viewDetails($id)
    {
        $resultArr = BlogMaster::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    ## Language Code:
    public function getLangData(Request $request)
    {
        $postData = $request->all();
        $id = $postData['id'];
        $langCode = $postData['langCode'];

        $defaultLanguage = _getDefaultLanguage();
        $whereArr = ['lang_code' => $langCode, 'id' => $id];
        if ($defaultLanguage != $langCode) {
            $whereArr = ['lang_code' => $langCode, 'lang_id' => $id];
        }
        $returnDataArr = BlogMaster::where($whereArr)->first();

        $blogImageUrl = '';
        if ($returnDataArr !== null && _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $returnDataArr->blog_image)) {
            $blogImageUrl = _assetUrl('upload_path.BLOG_IMAGE_URL') . $returnDataArr->blog_image;
        }
        $responseArr = [
            'lang_id' => $returnDataArr->id ?? $id,
            'lang_code' => $langCode,
            'title' => $returnDataArr->title ?? '',
            'content' => $returnDataArr->content ?? '',
            'blog_image' => $blogImageUrl,
        ];
        return response()->json($responseArr, 200);
    }
}

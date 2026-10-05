<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\SuccessStory;
use App\Services\AdminFormBuilderService;
use Exception;

class SuccessStoryController extends Controller
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
        $this->directoryName = '/successStory';
        $this->searchColumn = ['bridename', 'groomname', 'brideid'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Success Stories';
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

    // List Page
    public function index()
    {
        $extraJsArr = [$this->customJsDirectory . $this->directoryName . '/list.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.successStory.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.successStory.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => ['add' => 1, 'delete' => 1, 'approve' => 1, 'unapprove' => 1, 'edit' => 1, 'view' => 1, 'isSearch' => 1],
            'actionButtonUrl' => [
                'add' => 'admin.successStory.addForm',
                'edit' => 'admin.successStory.editForm',
                'view' => 'admin.successStory.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    // AJAX Pagination
    public function getAjaxPaginationData(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $searchKeyword = $request->input('searchKeyword');
        $conditionColumn = $request->input('conditionColumn');
        $conditionVal = $request->input('conditionVal');

        $query = SuccessStory::query();

        // Default language
        $query->where('lang_code', _getDefaultLanguage());

        if ($conditionColumn && $conditionVal) {
            $query->where($conditionColumn, $conditionVal);
        }

        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                foreach ($this->searchColumn as $i => $col) {
                    if ($i === 0) {
                        $q->where($col, 'like', "%$searchKeyword%");
                    } else {
                        $q->orWhere($col, 'like', "%$searchKeyword%");
                    }
                }
            });
        }

        $resultCount = $query->count();
        $results = $query->orderBy('id', 'desc')->forPage($page, $limit)->get();

        // Tab counts
        $tabCount = [];
        foreach ($this->statusTabArr as $tab) {
            $tabQuery = SuccessStory::where('lang_code', _getDefaultLanguage());
            if (!empty($postData['searchKeyword'])) {
                $keyword = $postData['searchKeyword'];
                $tabQuery->where(function ($q) use ($keyword) {
                    foreach ($this->searchColumn as $col) {
                        $q->orWhere($col, 'like', "%$keyword%");
                    }
                });
            }
            if (!empty($tab['conditionColumn'])) {
                $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
            }
            $tabCount[$tab['id']] = $tabQuery->count();
        }

        $resultArr = new LengthAwarePaginator($results, $resultCount, $limit, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();

        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'data' => ['tabCount' => $tabCount],
            'html' => $html
        ]);
    }

    // Change Status
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
            SuccessStory::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            SuccessStory::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    // Add/Edit Form
    public function addEditForm($id = null)
    {
        $mode = $id ? 'edit' : 'add';
        $rowData = $id ? SuccessStory::find($id) : null;

        $currentDate = _getCurrentDate('Y-m-d');
        $elementArr = [
            'bridename' => ['is_required' => 'required', 'class' => 'required', 'label' => "Bride's Name", 'column' => '6'],
            'brideid' => ['is_required' => 'required', 'class' => 'required', 'label' => "Bride's ID", 'column' => '6'],
            'groomname' => ['is_required' => 'required', 'class' => 'required', 'label' => "Groom's Name", 'column' => '6'],
            'groomid' => ['is_required' => 'required', 'class' => 'required', 'label' => "Groom's Id", 'column' => '6'],
            'marriagedate' => ['input_type' => 'date', 'is_required' => 'required', 'class' => 'required', 'label' => 'Your Marriage Date', 'column' => '6', 'other' => 'max="' . $currentDate . '"',],
            'story_type' => ['type' => 'dropdown', 'value_arr' => ['Photo Story' => 'Photo Story', 'Video Story' => 'Video Story'], 'is_required' => 'required', 'class' => 'required', 'label' => 'Story Type'],
            'video_type' => ['type' => 'radio', 'value_arr' => ['youtube' => 'Youtube Link', 'video' => 'Upload Video'], 'label' => 'Video Type', 'form_group_class' => 'video_type'],
            'video_link' => ['input_type' => 'link', 'label' => 'Youtube Link', 'form_group_class' => 'video_link'],
            'wedding_photo' => ['is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.SUCCESS_STORY_IMAGE_URL', 'label' => 'Upload Your Wedding Photo', 'form_group_class' => 'wedding_photo'],
            'wedding_video_file' => ['is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.SUCCESS_STORY_IMAGE_URL', 'label' => 'Upload Your Wedding Video', 'form_group_class' => 'wedding_video_file','max_file_upload' => '50','extension'=>'MP4, MOV, AVI, WEBM','file_type' => 'video'],
            'wedding_video_thumbnail' => ['is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.SUCCESS_STORY_IMAGE_URL', 'label' => 'Upload Your Wedding Video Thumbnail', 'form_group_class' => 'wedding_video_thumbnail'],
            'successmessage' => ['type' => 'textarea', 'label' => 'Success Message'],
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
            'status' => ['type' => 'radio', 'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']],
        ];

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.successStory.index'
        ]);

        $extraJsArr = [$this->customJsDirectory . $this->directoryName . '/addEdit.js'];

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'fromHtml' => $fromHtml,
            'formUrl' => 'admin.successStory.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'rowData' => $rowData,
            'extraJsArr' => $extraJsArr,
            'mode' => $mode,
            'id' => $id,
            'languageDataArr' => _getActiveLanguage()
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    // Submit Add/Edit
    // Submit Add/Edit
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $mode = $postData['mode'] ?? 'add';
        $callbackUrl = $postData['callbackUrl'] ?? 'admin.successStory.index';

        $data = $request->only([
            'story_type',
            'bridename',
            'brideid',
            'groomname',
            'groomid',
            'marriagedate',
            'video_link',
            'successmessage',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'status'
        ]);

        // File upload :
        if ($request->hasFile('wedding_photo')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $data['wedding_photo'] = UploadHelper::uploadFile(
                $request->file('wedding_photo'),
                $path
            );
        }

        if ($request->hasFile('wedding_video_file')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $data['wedding_video_file'] = UploadHelper::uploadFile(
                $request->file('wedding_video_file'),
                $path,
                '',
                '',
                0
            );
        }
        if ($request->hasFile('wedding_video_thumbnail')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $data['wedding_video_thumbnail'] = UploadHelper::uploadFile(
                $request->file('wedding_video_thumbnail'),
                $path
            );
        }
        $defaultLanguage = _getDefaultLanguage();
        try {
            ## Add :
            if ($mode === 'add') {
                $data['lang_code'] = $defaultLanguage;
                $data['status'] = $data['status'] ?? 'APPROVED';
                SuccessStory::create($data);
            }
            ## EDIT ;
            else {
                if (!$request->id) {
                    return redirect()->route($callbackUrl)->with('error', 'Success story ID is required for edit.');
                }
                // DEFAULT LANGUAGE
                if ($request->lang_code === $defaultLanguage) {
                    $model = SuccessStory::find($request->id);
                    if (!$model) {
                        return redirect()->route($callbackUrl)->with('error', 'Success story not found.');
                    }
                    $model->update($data);
                }
                // OTHER LANGUAGE
                else {
                    $existingLangRow = SuccessStory::where(['lang_id'   => $request->id, 'lang_code' => $request->lang_code])->first();
                    if ($existingLangRow) {
                        // Update existing translated row
                        $existingLangRow->update($data);
                    } else {
                        // Create new translated row
                        $updateData = $data;
                        $updateData['lang_id'] = $request->id;
                        $updateData['lang_code'] = $request->lang_code;
                        $updateData['status'] = 'APPROVED';
                        SuccessStory::create($updateData);
                    }
                }
            }

            return redirect()->route($callbackUrl)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } catch (Exception $e) {
            return redirect()->route($callbackUrl)->with('error', $e->getMessage());
        }
    }

    ## View Details :
    public function viewDetails($id)
    {
        $story = SuccessStory::find($id);
        if (!$story) {
            return redirect()->route('admin.successStory.index')->with('error', 'Record not found.');
        }
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $story,
        ]);
    }

    public function getLangData(Request $request)
    {
        $id = $request->id;
        $langCode = $request->langCode;
        $defaultLanguage = _getDefaultLanguage();

        // Always get base record (default language)
        $baseData = SuccessStory::where([
            'id' => $id,
            'lang_code' => $defaultLanguage
        ])->first();

        // Try to get translation
        $translatedData = SuccessStory::where([
            'lang_id' => $id,
            'lang_code' => $langCode
        ])->first();

        // Use translated if exists, otherwise fallback to base
        $data = $translatedData ?? $baseData;

        $weddingPhotoUrl = '';
        if (!empty($data) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $data->wedding_photo)) {
            $weddingPhotoUrl = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $data->wedding_photo;
        }

        return response()->json([
            // IMPORTANT: lang_id logic
            'lang_id'        => $translatedData->id ?? $id,
            'lang_code'      => $langCode,
            'bridename'      => $data->bridename ?? '',
            'groomname'      => $data->groomname ?? '',
            'successmessage' => $data->successmessage ?? '',
            'weddingphoto'   => $weddingPhotoUrl,
        ]);
    }
}

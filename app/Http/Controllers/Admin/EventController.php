<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegister;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class EventController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/event';
        $this->searchColumn = ['title', 'description'];
        $this->pageName = 'Manage Event';
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
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.event.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.event.changeStatus',
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
                'add' => 'admin.event.addForm',
                'edit' => 'admin.event.editForm',
                'view' => 'admin.event.viewDetails',
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

            $resultArr = Event::when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);
            foreach($resultArr as $key=>$value){
                $whereCount = ['event_id' => $value->id];
                $resultArr[$key]->totalEventCount = EventRegister::where($whereCount)->count();
            }
            
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));
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
            $tabWiseCountData[$tabId] = Event::query()
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
            Event::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Event::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $mode = ($id != '') ? 'edit' : 'add';

        $currentDate = _getCurrentDate('Y-m-d');

        $elementArr = array(
            'title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'venue' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
            ),
            'contact_number' => array(
                'is_required' => 'required',
                'type' => 'mobile',
                'class' => 'required single',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'column' => '6'
            ),
            'contact_email' => array(
                'is_required' => 'required',
                'input_type' => 'email',
                'class' => 'required',
                'modeType' => $mode,
                'isDisableInDemo' => 'Yes',
                'column' => '6'
            ),
            'description' => array('is_required' => 'required', 'type' => 'textarea', 'class' => 'page-editor required', 'column' => '12'),
            'event_date' => array(
                'input_type' => 'date',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6',
                'other' => 'min="' . $currentDate . '"'
            ),
            'event_time' => array(
                'input_type' => 'time',
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6'
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
                'class' => 'required select2 '
            ),
            'ticket_price' => array(
                'is_required' => 'required',
                'input_type' => 'number',
                'other' => "min='0'",
                'class' => 'required',
                'column' => '6'
            ),
            'total_tickets' => array(
                'is_required' => 'required',
                'input_type' => 'number',
                'other' => "min='0'",
                'class' => 'required',
                'column' => '6'
            ),
            'map_address' => array('is_required' => 'required', 'type' => 'textarea', 'class' => 'required', 'column' => '12'),
            'event_youtube_link' => array('label' => 'Youtube Link', 'input_type' => 'url', 'column' => '6'),
            'event_facebook_link' => array('label' => 'Facebook Link', 'input_type' => 'url', 'column' => '6'),
            'event_instagram_link' => array('label' => 'Instagram Link', 'input_type' => 'url', 'column' => '6'),
            'event_twitter_link' => array('label' => 'Twitter Link', 'input_type' => 'url', 'column' => '6'),
            'event_pinterest_link' => array('label' => 'Pinterest Link', 'input_type' => 'url', 'column' => '6'),
            'image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.EVENT_IMAGE_URL',
                'class' => 'required',
                'display_note' => 'Image size must be 1296 x 486 pixels'
            ),
            'image_2' => array(
                'type' => 'file',
                'path_value' => 'upload_path.EVENT_IMAGE_URL',
                'display_note' => 'Image size must be 1296 x 486 pixels'
            ),
            'image_3' => array(
                'type' => 'file',
                'path_value' => 'upload_path.EVENT_IMAGE_URL',
                'display_note' => 'Image size must be 1296 x 486 pixels'
            ),
            'image_4' => array(
                'type' => 'file',
                'path_value' => 'upload_path.EVENT_IMAGE_URL',
                'display_note' => 'Image size must be 1296 x 486 pixels'
            ),
            'status' => array(
                'type' => 'radio',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/addEdit.js'
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
            $rowData = Event::find($id);
        }
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.event.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.event.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArr,
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
            'title',
            'description',
            'event_date',
            'event_time',
            'contact_number',
            'contact_email',
            'total_tickets',
            'venue',
            'currency',
            'ticket_price',
            'map_address',
            'event_facebook_link',
            'event_twitter_link',
            'event_youtube_link',
            'event_instagram_link',
            'event_pinterest_link',
            'image',
            'image2',
            'image3',
            'image4',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
        $updateData['map_address'] = $postData['map_address'];
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
                Event::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                Event::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Detials:
    public function viewDetails($id = 0)
    {
        $resultArr = Event::where('id', $id)->first();
        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}

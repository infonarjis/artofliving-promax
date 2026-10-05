<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\NdaOtherDocMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\AdminFormBuilderService;

class NdaAndOtherDocsController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;

    private string $directoryName;
    private array $searchColumn;
    private string $pageName;
    private array $statusTabArr;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderService
    ) {
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/ndaAndOtherDocs';

        $this->searchColumn = [
            'title',
            'status',
        ];

        $this->pageName = 'Manage NDA and Other Documents';

        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => '',
            ],

            'sendTab' => [
                'label' => 'Sent list',
                'id' => 'sentData',
                'class' => '',
                'conditionVal' => 'admin',
                'conditionColumn' => 'type',
                'strWhere' => '',
            ],

            'ReceivedTab' => [
                'label' => 'Received list',
                'id' => 'receivedData',
                'class' => '',
                'conditionColumn' => 'type',
                'conditionVal' => 'staff',
                'strWhere' => '',
            ],
        ];
    }

    /**
     * List
     */
    public function index()
    {
        session()->forget('whereStrFilter');

        $userType = $this->getUserType();

        if ($userType === 'Staff') {
            $this->statusTabArr['sendTab']['conditionVal'] = 'staff';
            $this->statusTabArr['ReceivedTab']['conditionVal'] = 'admin';
        }

        $dataArr = [
            'pageName' => $this->pageName,

            'ajaxPaginationRequestUrl' =>
            'admin.ndaAndOtherDocs.getAjaxPaginationData',

            'changeStatusUrl' =>
            'admin.ndaAndOtherDocs.changeStatus',

            'extraJsArr' => [
                '/custom/js/commonList.js',
            ],

            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'view' => 0,
                'filter' => $userType === 'Admin' ? 1 : 0,
            ],

            'actionButtonUrl' => [
                'add' => 'admin.ndaAndOtherDocs.addForm',
                'edit' => '',
                'view' => '',
                'filter' => 'admin.ndaAndOtherDocs.getFilter',
            ],

            'statusTabArr' => $this->statusTabArr,
        ];

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH')
                . $this->directoryName
                . '/index',
            $dataArr
        );
    }

    /**
     * Get Ajax Pagination Data
     */
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant(
                'responce_message.SOMETHING_WENT_WRONG'
            ),
            'html' => '',
            'data' => [],
        ];

        $page = max((int) $request->input('page', 1), 1);

        $limit = (int) $request->input('limit', 10);

        if ($limit <= 0) {
            $limit = 10;
        }

        $userType = $this->getUserType();

        /**
         * Update tab condition based on user type
         */
        if ($userType === 'Staff') {
            $this->statusTabArr['sendTab']['conditionVal'] = 'staff';
            $this->statusTabArr['ReceivedTab']['conditionVal'] = 'admin';
        }

        /**
         * Tab condition
         */
        $whereArr = $this->conditionValue(
            $request->all()
        );

        /**
         * Search keyword
         */
        $searchKeyword = trim(
            $request->input('searchKeyword', '')
        );

        /**
         * Staff restriction
         */
        $staffId = null;

        if ($userType === 'Staff') {
            $staffId = auth()
                ->guard('staff')
                ->id();

            $whereArr['staff_id'] = $staffId;
        }

        /**
         * Filter
         */
        $staffIds = [];

        if (
            $request->input('isFilterApply') == 1
            && !blank($request->input('staff_id'))
        ) {
            $staffIds = $request->input('staff_id');

            if (!is_array($staffIds)) {
                $staffIds = [$staffIds];
            }

            $staffIds = array_filter(
                array_map('intval', $staffIds)
            );

            if (!empty($staffIds)) {
                session([
                    'whereStrFilter' => [
                        'staff_ids' => $staffIds,
                    ],
                ]);
            }
        } else {
            session()->forget('whereStrFilter');
        }

        /**
         * Tab-wise counts
         */
        $htmlDataArr['tabCount'] = $this->tabWiseCountData(
            $this->statusTabArr,
            $searchKeyword,
            $staffIds
        );

        /**
         * Main Query
         */
        $query = NdaOtherDocMaster::with('staff')
            ->when(
                !empty($whereArr),
                function ($query) use ($whereArr) {
                    $query->where($whereArr);
                }
            )

            /**
             * Search
             */
            ->when(
                $searchKeyword !== '',
                function ($query) use ($searchKeyword) {
                    $this->applySearch(
                        $query,
                        $searchKeyword
                    );
                }
            )

            /**
             * Filter by Staff
             */
            ->when(
                !empty($staffIds),
                function ($query) use ($staffIds) {
                    $query->whereIn(
                        'staff_id',
                        $staffIds
                    );
                }
            )

            /**
             * Staff can only see their own documents
             */
            ->when(
                $userType === 'Staff',
                function ($query) use ($staffId) {
                    $query->where(
                        'staff_id',
                        $staffId
                    );
                }
            )

            ->orderByDesc('id');

        $resultArr = $query->paginate(
            $limit,
            ['*'],
            'page',
            $page
        );

        $dataArr = (object) [
            'pageName' => 'page',

            'actionButtonUrl' => [
                'edit' => 'admin.ndaAndOtherDocs.editForm',
                'view' => '',
            ],
        ];

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH')
                . $this->directoryName
                . '/ajaxResultData',
            compact(
                'resultArr',
                'dataArr'
            )
        );

        $responseArr['status'] = 'success';

        $responseArr['msg'] = _getConstant(
            'responce_message.DATA_GET_SUCCESS'
        );

        $responseArr['data'] = $htmlDataArr;

        $responseArr['html'] = (string) $html;

        return response()->json(
            $responseArr,
            200
        );
    }

    /**
     * Apply Search
     *
     * Safe query-builder based search.
     */
    private function applySearch(
        $query,
        string $searchKeyword
    ) {
        $query->where(function ($searchQuery) use (
            $searchKeyword
        ) {
            foreach ($this->searchColumn as $column) {
                $searchQuery->orWhere(
                    $column,
                    'LIKE',
                    '%' . $searchKeyword . '%'
                );
            }
        });

        return $query;
    }

    /**
     * Tab Wise Count
     */
    public function tabWiseCountData(
        array $tabArr,
        string $searchKeyword = '',
        array $staffIds = []
    ): array {
        $tabWiseCountData = [];

        $userType = $this->getUserType();

        $staffId = null;

        if ($userType === 'Staff') {
            $staffId = auth()
                ->guard('staff')
                ->id();
        }

        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;

            $query = NdaOtherDocMaster::query();

            /**
             * Tab condition
             */
            if (
                !empty($value['conditionColumn'])
                && array_key_exists(
                    'conditionVal',
                    $value
                )
                && $value['conditionVal'] !== ''
            ) {
                $query->where(
                    $value['conditionColumn'],
                    $value['conditionVal']
                );
            }

            /**
             * Staff restriction
             */
            if ($userType === 'Staff') {
                $query->where(
                    'staff_id',
                    $staffId
                );
            }

            /**
             * Search
             */
            if ($searchKeyword !== '') {
                $this->applySearch(
                    $query,
                    $searchKeyword
                );
            }

            /**
             * Staff filter
             */
            if (!empty($staffIds)) {
                $query->whereIn(
                    'staff_id',
                    $staffIds
                );
            }

            $tabWiseCountData[$tabId] = $query->count();
        }

        return $tabWiseCountData;
    }

    /**
     * Search Keyword
     *
     * Kept for compatibility if used somewhere else.
     */
    public function onSearchKeyword($postData)
    {
        return trim(
            $postData['searchKeyword'] ?? ''
        );
    }

    /**
     * Condition Value
     */
    public function conditionValue($postData): array
    {
        $whereArr = [];

        if (
            isset($postData['conditionColumn'])
            && $postData['conditionColumn'] !== ''
            && isset($postData['conditionVal'])
            && $postData['conditionVal'] !== ''
        ) {
            $conditionColumn = $postData['conditionColumn'];

            $conditionVal = $postData['conditionVal'];

            /**
             * Whitelist allowed condition columns.
             *
             * This prevents a user from passing any arbitrary
             * database column name.
             */
            $allowedColumns = [
                'type',
                'status',
            ];

            if (
                in_array(
                    $conditionColumn,
                    $allowedColumns,
                    true
                )
            ) {
                $whereArr[$conditionColumn] = $conditionVal;
            }
        }

        return $whereArr;
    }

    /**
     * Popup Add/Edit Form
     */
    public function addEditForm($id = '')
    {
        $elementArr = [
            'title' => [
                'is_required' => 'required',
                'class' => 'required',
                'column' => '12',
            ],

            'doc_image' => [
                'type' => 'file',
                'is_required' => 'required',
                'class' => 'required',
                'path_value' =>
                'upload_path.NDA_AND_OTHER_DOCS_RECEIPT_URL',
            ],
        ];

        $userType = $this->getUserType();

        if ($userType === 'Admin') {
            $elementArr['staff_id'] = [
                'type' => 'dropdown',
                'is_required' => 'required',
                'class' => 'required',

                'relation' => [
                    'rel_model' => 'Staff',
                    'key_val' => 'id',
                    'key_disp' => 'username',
                ],

                'column' => '12',
            ];
        }

        $mode = $id !== '' ? 'edit' : 'add';

        $rowData = [];

        if ($mode === 'edit') {
            $rowData = NdaOtherDocMaster::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.ndaAndOtherDocs.index',
        ];

        $fromHtml =
            $this->adminFormBuilderService
            ->generateFormElement(
                $elementArr,
                $otherData
            );

        $dataArr = [
            'pageName' =>
            $this->pageName
                . ' '
                . ucwords($mode),

            'elementArr' => $elementArr,

            'formUrl' =>
            'admin.ndaAndOtherDocs.addEdit',

            'formId' => 'addEditForm',

            'formName' => 'addEditForm',

            'formSubmitBtnClass' => 'formSubmitBtn',

            'formSubmitBtnId' => 'formSubmitBtn',

            'fromHtml' => $fromHtml,
        ];

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH')
                . $this->directoryName
                . '/addEdit',
            $dataArr
        );
    }

    /**
     * Submit Add/Edit Form
     */
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = [
            'title',
            'staff_id',
        ];

        $updateData = _getRequestData(
            $updateArr,
            $postData
        );

        /**
         * File Upload
         */
        foreach ($request->allFiles() as $key => $file) {
            if (!$file) {
                continue;
            }

            $pathKey = $postData[$key . '_path'] ?? null;

            if (!$pathKey) {
                continue;
            }

            $path = _getConstant($pathKey);

            $oldValue = $postData[$key . '_val'] ?? '';

            $uploadedFiles = UploadHelper::uploadFile(
                $file,
                $path,
                $oldValue
            );

            $updateData[$key] = $uploadedFiles;
        }

        if (empty($updateData)) {
            return redirect()
                ->route(
                    $postData['callbackUrl']
                )
                ->with(
                    'error',
                    _getConstant(
                        'responce_message.DATA_NOT_UPDATED'
                    )
                );
        }

        $userType = $this->getUserType();

        if ($userType === 'Staff') {
            $updateData['staff_id'] =
                auth()
                ->guard('staff')
                ->id();

            $updateData['type'] = 'staff';
        } else {
            $updateData['type'] = 'admin';
        }

        /**
         * Update
         */
        if (
            isset($postData['mode'])
            && $postData['mode'] === 'edit'
        ) {
            NdaOtherDocMaster::where(
                'id',
                $postData['id']
            )->update($updateData);

            return redirect()
                ->route(
                    $postData['callbackUrl']
                )
                ->with(
                    'success',
                    _getConstant(
                        'responce_message.DATA_UPDATED_SUCCESS'
                    )
                );
        }

        /**
         * Insert
         */
        if (
            isset($postData['mode'])
            && $postData['mode'] === 'add'
        ) {
            $updateData['created_at'] =
                _getCurrentDate();

            NdaOtherDocMaster::create(
                $updateData
            );

            return redirect()
                ->route(
                    $postData['callbackUrl']
                )
                ->with(
                    'success',
                    'Data added successfully.'
                );
        }

        return redirect()
            ->route(
                $postData['callbackUrl']
            )
            ->with(
                'error',
                _getConstant(
                    'responce_message.DATA_NOT_UPDATED'
                )
            );
    }

    /**
     * Change Status
     */
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',

            'msg' => _getConstant(
                'responce_message.SOMETHING_WENT_WRONG'
            ),

            'data' => [],
        ];

        $ids = $request->input('id', []);

        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter(
            array_map(
                'intval',
                $ids
            )
        );

        if (empty($ids)) {
            $responseArr['msg'] =
                'Invalid IDs supplied.';

            return response()->json(
                $responseArr,
                200
            );
        }

        /**
         * Soft Delete
         */
        if ($request->has('is_deleted')) {
            NdaOtherDocMaster::whereIn(
                'id',
                $ids
            )->delete();
        } else {
            $updateData = _getRequestData(
                _getStaticArr('statusUpdateArr'),
                $request->all()
            );

            unset($updateData['id']);

            NdaOtherDocMaster::whereIn(
                'id',
                $ids
            )->update($updateData);
        }

        $responseArr['status'] = 'success';

        $responseArr['msg'] =
            _getConstant(
                'responce_message.RECORD_UPDATED_SUCCESS'
            );

        return response()->json(
            $responseArr,
            200
        );
    }

    /**
     * Get Filter
     */
    public function getFilter(Request $request)
    {
        $responseArr = [
            'status' => 'error',

            'msg' => _getConstant(
                'responce_message.SOMETHING_WENT_WRONG'
            ),

            'html' => '',

            'data' => [],
        ];

        if ($request->all()) {
            $elementArr = [
                'staff_id' => [
                    'class' => 'single not_reset',
                    'is_multiple' => 'yes',
                    'display_placeholder' => 'No',
                    'label' => 'Staff List',
                    'type' => 'dropdown',

                    'relation' => [
                        'rel_model' => 'Staff',
                        'key_val' => 'id',
                        'key_disp' => 'username',
                        'rel_col_name' => 'type',
                        'rel_col_val' => 'Staff',
                    ],
                ],
            ];

            $otherData = [
                'rowData' => [],
            ];

            $fromHtml =
                $this->adminFormBuilderService
                ->generateFormElement(
                    $elementArr,
                    $otherData
                );

            $dataArr = [
                'elementArr' => $elementArr,

                'formUrl' =>
                'admin.ndaAndOtherDocs.getAjaxPaginationData',

                'formId' => 'filterForm',

                'formName' => 'filterForm',

                'formSubmitBtnClass' =>
                'filterFormSubmitBtn',

                'formSubmitBtnId' =>
                'filterFormSubmitBtn',

                'fromHtml' => $fromHtml,
            ];

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH')
                    . $this->directoryName
                    . '/filterPopup',
                $dataArr
            );

            $responseArr['status'] = 'success';

            $responseArr['msg'] =
                _getConstant(
                    'responce_message.DATA_GET_SUCCESS'
                );

            $responseArr['html'] =
                (string) $html;
        }

        return response()->json(
            $responseArr,
            200
        );
    }

    /**
     * Get Current User Type
     */
    private function getUserType(): string
    {
        $authUserType = Auth::user()->type;

        return _adminUserType(
            $authUserType
        );
    }
}

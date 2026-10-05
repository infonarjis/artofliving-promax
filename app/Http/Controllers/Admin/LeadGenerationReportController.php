<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentsOfLeadGeneration;
use Illuminate\Http\Request;

class LeadGenerationReportController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        $this->directoryName = '/leadGenerationReport';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
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
            'pageName' => 'Manage Lead Generation Report',
            'ajaxPaginationRequestUrl' => 'admin.leadGenerationReport.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.leadGenerationReport.addForm',
                'edit' => 'admin.leadGenerationReport.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $limit   = $request->input('limit', 10);
        $page    = $request->input('page', 1);
        $keyword = $request->input('searchKeyword');

        $query = $this->baseQuery();

        // Apply column condition filter
        if ($request->filled(['conditionColumn', 'conditionVal'])) {
            $query->where(
                $request->conditionColumn,
                $request->conditionVal
            );
        }

        // Apply search
        $this->applySearch($query, $keyword);

        // Use paginate() because commonPagination expects lastPage()
        $resultArr = $query
            ->orderByDesc('id')
            ->paginate($limit, ['*'], 'page', $page);

        // Tab count
        $tabCounts = $this->getTabCounts($keyword);

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
            compact('resultArr')
        )->render();

        return response()->json([
            'status' => 'success',
            'msg'    => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'html'   => $html,
            'data'   => [
                'tabCount' => $tabCounts
            ],
        ]);
    }

    /**
     * Base query with required joins
     */
    private function baseQuery()
    {
        return CommentsOfLeadGeneration::query()
            ->with([
                'leadGeneration:id,username,interest,email,staff_assign_id,franchise_assign_id',
                'leadGeneration.staff:id,username,email',
                'leadGeneration.franchise:id,username,email',
            ]);
    }

    /**
     * Apply keyword search across multiple columns safely
     */
    private function applySearch($query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $query->where(function ($q) use ($keyword) {
            $q->whereHas('leadGeneration', function ($q) use ($keyword) {
                $q->where('interest', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('interest', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })
                ->orWhereHas('staff', function ($q) use ($keyword) {
                    $q->where('email', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%");
                })
                ->orWhereHas('franchise', function ($q) use ($keyword) {
                    $q->where('email', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%");
                });
        });
    }

    /**
     * Get tab counts without running many queries
     */
    private function getTabCounts(?string $keyword): array
    {
        $query = $this->baseQuery();
        $this->applySearch($query, $keyword);

        return [
            'allData' => (clone $query)->count(),
        ];
    }
}

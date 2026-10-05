<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentMaster;
use App\Models\Staff;
use Illuminate\Http\Request;

class FollowedUpReportController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/followedUpReport';

        $this->statusTabArr = [
            'todayFollowupTab' => [
                'label' => 'Today Followup',
                'id' => 'todayFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Today',
                'isActive' => 1,
                'strWhere' => ''
            ],
            'previousFollowupTab' => [
                'label' => 'Previous Followup',
                'id' => 'previousFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Previous',
                'strWhere' => ''
            ],
            'nextFollowupTab' => [
                'label' => 'Next Followup',
                'id' => 'nextFollowupData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Next',
                'strWhere' => ''
            ],
            'pendingFollowupTab' => [
                'label' => 'Pending Followup',
                'id' => 'pendingFollowupdData',
                'class' => '',
                'conditionVal' => 'Yes',
                'conditionColumn' => 'Pending',
                'strWhere' => ''
            ],
        ];
    }

    /**
     * List page
     */
    public function index()
    {
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => 'Manage Followed Up Member Report',
            'ajaxPaginationRequestUrl' => 'admin.followedUpReport.getAjaxPaginationData',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => Staff::active()->get(),
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
                'staffAssign' => 1,
                'staffUnAssign' => 1,
                'franchiseAssign' => 1,
                'franchiseUnAssign' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.followedUpReport.addForm',
                'edit' => 'admin.followedUpReport.editForm/',
                'staffAssignbtn' => 'admin.member.assignMember',
                'staffUnAssignbtn' => 'admin.member.unAssignMember',
                'franchiseAssignbtn' => 'admin.member.assignMember',
                'franchiseUnAssignbtn' => 'admin.member.unAssignMember',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    /**
     * AJAX Pagination
     */
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => __('messages.msg_unexpected_error_occured'),
            'html'   => '',
            'data'   => [],
        ];

        $postData = $request->all();
        if (!empty($postData)) {

            $page  = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = CommentMaster::with(['register.staffData', 'register.countryData'])
                ->where('follow_up_status', 1)
                ->whereNotNull('next_followup_date');

            $currentDate = now()->format('Y-m-d');

            // Filter by tab
            if (!empty($postData['conditionColumn'])) {
                switch ($postData['conditionColumn']) {
                    case 'Today':
                        $query->whereDate('next_followup_date', $currentDate);
                        break;
                    case 'Previous':
                        $query->whereDate('next_followup_date', '<', $currentDate);
                        break;
                    case 'Next':
                        $query->whereDate('next_followup_date', '>', $currentDate);
                        break;
                    case 'Pending':
                        $query->where('follow_up_status', 1)
                            ->whereHas('register', function ($q) {
                                $q->whereNotNull('staff_assign_id')
                                    ->where('commented', 0);
                            });
                        break;
                }
            }

            // Keyword search
            if (!empty($postData['searchKeyword'])) {
                $keyword = trim($postData['searchKeyword']);
                $query->where(function ($q) use ($keyword) {
                    // CommentMaster fields
                    $q->where('comment', 'like', "%{$keyword}%")
                        ->orWhere('posted_user_type', 'like', "%{$keyword}%")
                        // Register fields
                        ->orWhereHas('register', function ($qr) use ($keyword) {
                            $qr->where(function ($q) use ($keyword) {
                                $q->where('fullname', 'like', "%{$keyword}%")
                                    ->orWhere('matri_id', 'like', "%{$keyword}%")
                                    ->orWhere('email', 'like', "%{$keyword}%")
                                    ->orWhere('mobile', 'like', "%{$keyword}%");
                            });
                        })
                        // Staff fields
                        ->orWhereHas('staff', function ($qs) use ($keyword) {
                            $qs->where('username', 'like', "%{$keyword}%");
                        });
                });
            }

            $query->orderBy('created_at', 'desc');

            $resultArr = $query->paginate($limit, ['*'], 'page', $page);

            // Tab-wise count
            $htmlDataArr['tabCount'] = $this->tabWiseCountData(
                $this->statusTabArr,
                $postData ?? []
            );

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'viewComment' => 'admin.member.viewComment',
                    'addComment' => 'admin.member.addComment'
                ],
                'actionBtnArr' => [
                    'addComment' => 1,
                    'viewComment' => 1,
                ],
            ];

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            )->render();

            $responseArr['status'] = 'success';
            $responseArr['msg'] = 'Data fetched successfully.';
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = $html;
        }

        return response()->json($responseArr, 200);
    }

    /**
     * Calculate tab-wise count
     */
    private function tabWiseCountData($tabArr, $postData = [])
    {
        $tabWiseCountData = [];

        $start = now()->startOfDay();
        $end   = now()->endOfDay();
        $searchKeyword = $postData['searchKeyword'] ?? '';
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $base = CommentMaster::query()->where('follow_up_status', 1)->whereNotNull('next_followup_date');
            if (!empty($searchKeyword)) {
                $keyword = trim($searchKeyword);
                $base->where(function ($q) use ($keyword) {
                    // Comment fields
                    $q->where('comment', 'like', "%{$keyword}%")
                        ->orWhere('posted_user_type', 'like', "%{$keyword}%")
                        // Member/Register fields
                        ->orWhereHas('register', function ($qr) use ($keyword) {
                            $qr->where(function ($q) use ($keyword) {
                                $q->where('fullname', 'like', "%{$keyword}%")
                                    ->orWhere('matri_id', 'like', "%{$keyword}%")
                                    ->orWhere('email', 'like', "%{$keyword}%")
                                    ->orWhere('mobile', 'like', "%{$keyword}%");
                            });
                        })
                        // Staff fields
                        ->orWhereHas('staff', function ($qs) use ($keyword) {
                            $qs->where('username', 'like', "%{$keyword}%");
                        });
                });
            }
            
            switch ($value['conditionColumn']) {
                case 'Today':
                    $tabWiseCountData[$tabId] = (clone $base)
                        ->whereBetween('next_followup_date', [$start, $end])
                        ->count();
                    break;
                case 'Previous':
                    $tabWiseCountData[$tabId] = (clone $base)
                        ->where('next_followup_date','<',$start)
                        ->count();
                    break;
                case 'Next':
                    $tabWiseCountData[$tabId] = (clone $base)
                        ->where('next_followup_date','>',$end)
                        ->count();
                    break;
                case 'Pending':
                    $tabWiseCountData[$tabId] = (clone $base)
                        ->whereHas('register', function ($q) {
                            $q->whereNotNull('staff_assign_id')
                                ->where('commented', 0);
                        })
                        ->count();
                    break;
            }
        }
        return $tabWiseCountData;
    }
}

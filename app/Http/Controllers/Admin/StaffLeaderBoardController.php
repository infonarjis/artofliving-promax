<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Staff;
use App\Models\StaffAssignApprovedMember;
use App\Models\StaffAssignWpPlanMember;
use App\Models\StaffAttendance;
use App\Models\StaffDailyRevenue;
use App\Models\StaffHolidayMaster;
use App\Models\StaffKpi;
use App\Models\StaffKpiAssignMember;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

## Services
use Illuminate\Support\Facades\Auth;

class StaffLeaderBoardController extends Controller
{
    private $directoryName;
    private $pageName;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/staffLeaderBoard';
        $this->pageName = 'Staff Leaderboard';
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [];

        $monthYear = _getCurrentDate('Y-m');

        $staffList = $this->getScoreCardData($monthYear);

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index',
            [
                'pageName' => $this->pageName,
                'ajaxPaginationRequestUrl' => 'admin.staffLeaderBoard.getAjaxPaginationData',
                'downloadPdfUrl' => 'admin.staffLeaderBoard.downloadPdf',
                'downloadCsvUrl' => 'admin.staffLeaderBoard.downloadCsv',
                'changeStatusUrl' => '',
                'extraJsArr' => $extraJsArr,
                'monthYear' => $monthYear,
                'staffList' => $staffList
            ]
        );
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
            $monthYear = $postData['month_year'];
            $staffList = $this->getScoreCardData($monthYear);
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('staffList'));
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    public function getScoreCardData($monthYear = '')
    {
        $start_date = date('Y-m-01', strtotime($monthYear));
        $end_date = date('Y-m-t', strtotime($monthYear));

        $staffList = Staff::get();

        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);

        if ($userType == 'Staff') {
            $this->pageName = 'My Leaderboard';
            $staffList = Staff::where('id', $authUser->id)->get();
        }

        $staffIds = $staffList->pluck('id')->toArray();

        $staff_kpi_assign_member_all = StaffKpiAssignMember::whereIn('staff_id', $staffIds)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->selectRaw('staff_id, COUNT(*) as total')
            ->groupBy('staff_id')
            ->pluck('total', 'staff_id');

        $staff_kpis_all = StaffKpi::whereIn('staff_id', $staffIds)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->selectRaw('staff_id, COUNT(*) as total')
            ->groupBy('staff_id')
            ->pluck('total', 'staff_id');

        $register_member_count_all = Register::whereIn('created_by_id', $staffIds)
            ->where('created_by', 'Staff')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->selectRaw('created_by_id, COUNT(*) as total')
            ->groupBy('created_by_id')
            ->pluck('total', 'created_by_id');

        $staff_revenue_count_all = StaffDailyRevenue::whereIn('staff_id', $staffIds)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->selectRaw('staff_id, COUNT(*) as total')
            ->groupBy('staff_id')
            ->pluck('total', 'staff_id');
        $dataListArr = [];
        foreach ($staffList as $key => $staff) {

            ## KPI
            $staff_kpi_assign_member = $staff_kpi_assign_member_all[$staff->id] ?? 0;
            $staff_kpis = $staff_kpis_all[$staff->id] ?? 0;

            $staff_kpi_percentage = 0;

            if ($staff_kpis > 0 && $staff_kpi_assign_member > 0) {

                $staff_kpi_percentage =
                    ($staff_kpis * 100)
                    / $staff_kpi_assign_member;

                $staff_kpi_percentage = min(
                    $staff_kpi_percentage,
                    100
                );
            }

            ## Revenue
            $register_member_count = $register_member_count_all[$staff->id] ?? 0;
            $staff_revenue_count = $staff_revenue_count_all[$staff->id] ?? 0;

            $staff_revenue_percentage = 0;

            if ($staff_revenue_count > 0 && $register_member_count > 0) {

                $staff_revenue_percentage =
                    ($staff_revenue_count * 100)
                    / $register_member_count;

                $staff_revenue_percentage = min(
                    $staff_revenue_percentage,
                    100
                );
            }

            $kpi_weight = _getConstant('payroll.STAFF_LEADERBOARD_KPI_PER') / 100;      // 70%
            $revenue_weight = _getConstant('payroll.STAFF_LEADERBOARD_REVENUE_PER') / 100;  // 30%

            $kpi_score = $staff_kpi_percentage * $kpi_weight;
            $revenue_score = $staff_revenue_percentage * $revenue_weight;

            $total_score = round(
                $kpi_score +
                    $revenue_score,
                2
            );

            $rating_text =
                $total_score >= 90 ? "⭐️⭐️⭐️⭐️⭐️ Outstanding" : ($total_score >= 80 ? "⭐️⭐️⭐️⭐️ Exceeds Expectations" : ($total_score >= 70 ? "⭐️⭐️⭐️ Meets Expectations" : ($total_score >= 60 ? "⭐️⭐️ Needs Improvement" :
                            "⚠️ Underperforming")));

            $staffList[$key]->kpi_score = $kpi_score;
            $staffList[$key]->revenue_score = $revenue_score;
            $staffList[$key]->total_score = $total_score;
            $staffList[$key]->rating_text = $rating_text;
            $staffList[$key]->start_date = $start_date;
            $staffList[$key]->end_date = $end_date;
            $staffList[$key]->profile_image = _assetUrl('upload_path.STAFF_IMAGE_URL') . '/' . $staff->profile_image;
        }
        $staffList = $staffList->sortByDesc('total_score')->values();
        return $staffList;
    }

    public function downloadPdf($monthYear)
    {
        if ($monthYear) {
            if ($monthYear) {
                $staffList = $this->getScoreCardData($monthYear);
                $resultDataCsv = [];
                foreach ($staffList as $key => $staff) {
                    if ($staff->total_score >= 90) {
                        $rating_text = "Outstanding";
                    } elseif ($staff->total_score >= 80) {
                        $rating_text = "Exceeds Expectations";
                    } elseif ($staff->total_score >= 70) {
                        $rating_text = "Meets Expectations";
                    } elseif ($staff->total_score >= 60) {
                        $rating_text = "Needs Improvement";
                    } else {
                        $rating_text = "Underperforming";
                    }
                    $resultDataCsv[] = [
                        'Username' => $staff->username,
                        'Rank' => $key + 1,
                        'Date Range' => $staff->start_date . ' to ' . $staff->end_date,
                        'KPI Score (' . _getConstant('payroll.STAFF_LEADERBOARD_KPI_PER') . '%)' => number_format($staff->kpi_score, 2),
                        'Revenue Score (' . _getConstant('payroll.STAFF_LEADERBOARD_REVENUE_PER') . '%)' => number_format($staff->revenue_score, 2),
                        'Total Score (%)' => number_format($staff->total_score, 2),
                        'Rating' => $rating_text,
                    ];
                }
                $filename = 'staff_leaderboard_' . $monthYear;
                $dataTableColm = [
                    'Username',
                    'Rank',
                    'Date Range',
                    'KPI Score (' . _getConstant('payroll.STAFF_LEADERBOARD_KPI_PER') . '%)',
                    'Revenue Score (' . _getConstant('payroll.STAFF_LEADERBOARD_REVENUE_PER') . '%)',
                    'Total Score (%)',
                    'Rating'
                ];
                $dataArr['salary_month_year'] = $monthYear;
                $dataArr['dataTableColm'] = $dataTableColm;
                $dataArr['resultDataCsv'] = $resultDataCsv;
                $dataArr['reportTitle'] = 'Staff Leaderboard Report';
                ini_set('memory_limit', '512M');
                set_time_limit(300);
                $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
                $pdf = Pdf::loadHTML($html);

                return $pdf->download($filename . '.pdf');
            } else {
                return back();
            }
        } else {
            return back();
        }
    }

    public function downloadCsv($monthYear = null)
    {
        if ($monthYear) {
            $staffList = $this->getScoreCardData($monthYear);
            $resultDataCsv = [];
            foreach ($staffList as $key => $staff) {
                if ($staff->total_score >= 90) {
                    $rating_text = "Outstanding";
                } elseif ($staff->total_score >= 80) {
                    $rating_text = "Exceeds Expectations";
                } elseif ($staff->total_score >= 70) {
                    $rating_text = "Meets Expectations";
                } elseif ($staff->total_score >= 60) {
                    $rating_text = "Needs Improvement";
                } else {
                    $rating_text = "Underperforming";
                }
                $resultDataCsv[] = [
                    'Username' => $staff->username,
                    'Rank' => $key + 1,
                    'Date Range' => $staff->start_date . ' to ' . $staff->end_date,
                    'KPI Score (' . _getConstant('payroll.STAFF_LEADERBOARD_KPI_PER') . '%)' => number_format($staff->kpi_score, 2),
                    'Revenue Score (' . _getConstant('payroll.STAFF_LEADERBOARD_REVENUE_PER') . '%)' => number_format($staff->revenue_score, 2),
                    'Total Score (%)' => number_format($staff->total_score, 2),
                    'Rating' => $rating_text,
                ];
            }
            $filename = 'staff_leaderboard_' . $monthYear;
            $dataTableColm = [
                'Username',
                'Rank',
                'Date Range',
                'KPI Score (' . _getConstant('payroll.STAFF_LEADERBOARD_KPI_PER') . '%)',
                'Revenue Score (' . _getConstant('payroll.STAFF_LEADERBOARD_REVENUE_PER') . '%)',
                'Total Score (%)',
                'Rating'
            ];
            $array[] = $dataTableColm;
            foreach ($resultDataCsv as $key => $row) {
                $line = array();
                foreach ($dataTableColm as $item) {
                    $line[] = $row[$item];
                }
                $array[] = $line;
            }
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"$filename" . ".csv\"");
            header("Pragma: no-cache");
            header("Expires: 0");
            $handle = fopen('php://output', 'w');
            foreach ($array as $array) {
                fputcsv($handle, $array);
            }
            fclose($handle);
        } else {
            return back();
        }
    }
}

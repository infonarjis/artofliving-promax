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

class StaffScoreCardController extends Controller
{
    private $directoryName;
    private $pageName;

    public function __construct() {
## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/staffScoreCard';
        $this->pageName = 'Staff Scorecards';
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
                'ajaxPaginationRequestUrl' => 'admin.staffScoreCard.getAjaxPaginationData',
                'downloadPdfUrl' => 'admin.staffScoreCard.downloadPdf',
                'downloadCsvUrl' => 'admin.staffScoreCard.downloadCsv',
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

    public function getScoreCardData($monthYear = '') {
        $total_days = date('t', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));
        $total_sundays = count(array_filter(range(1, $total_days), fn($d) => date('w', strtotime("$year-$month-$d")) == 0));
        $start_date = date('Y-m-01', strtotime($monthYear)); // First day of month
        $end_date = date('Y-m-t', strtotime($monthYear));   // Last day of month
        $total_holi_day = StaffHolidayMaster::where('status', 'APPROVED')
            ->whereBetween('holiday_date', [$start_date, $end_date])
            ->count();
        $staffList = Staff::get();
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        if ($userType == 'Staff') {
            $this->pageName = 'My Scorecards';
            $staffList = Staff::where('id', $authUser->id)->get();
        }
        foreach ($staffList as $key => $staff) {
            $attendanceData = StaffAttendance::where('staff_id', $staff->id)->whereDate('punch_in', '>=', $start_date)->whereDate('punch_in', '<=', $end_date)->get();
            $total_seconds = 0;
            foreach ($attendanceData as $row) {
                if (!empty($row->punch_out)) {
                    $in_time  = strtotime($row->punch_in);
                    $out_time = strtotime($row->punch_out);
                    $diff = $out_time - $in_time;
                    $total_seconds += $diff;
                }
            }
            $total_attendance_hour = round($total_seconds / 3600);
            $total_working_days = $total_days - $total_sundays - $total_holi_day;
            $total_working_hours = $total_working_days * _getConstant('payroll.STAFF_WORKING_HOUR');
            $attendance_percentage_20 = 0;
            if ($total_working_hours > 0) {
                $attendance_percentage_20 = ($total_attendance_hour * _getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER')) / $total_working_hours;
                if ($attendance_percentage_20 > _getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER')) {
                    $attendance_percentage_20 = _getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER');
                }
            }
            ## Total KPI Calculation :
            $staff_kpi_assign_member = StaffKpiAssignMember::where('staff_id', $staff->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_kpis = StaffKpi::where('staff_id', $staff->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_kpi_percentage_35 = 0;
            if ($staff_kpis > 0 && $staff_kpi_assign_member > 0) {
                $staff_kpi_percentage_35 = ($staff_kpis * _getConstant('payroll.STAFF_SCORECARD_KPI_PER')) / $staff_kpi_assign_member;
                if ($staff_kpi_percentage_35 > _getConstant('payroll.STAFF_SCORECARD_KPI_PER')) {
                    $staff_kpi_percentage_35 = _getConstant('payroll.STAFF_SCORECARD_KPI_PER');
                }
            }

            ## Staff Daily Revenue Calculation :
            $register_member_count = Register::where('created_by_id', $staff->id)->where('created_by', 'Staff')->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_revenue_count = StaffDailyRevenue::where('staff_id', $staff->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_revenue_percentage_20 = 0;
            if ($staff_revenue_count > 0 && $register_member_count > 0) {
                $staff_revenue_percentage_20 = ($staff_revenue_count * _getConstant('payroll.STAFF_SCORECARD_REVENUE_PER')) / $register_member_count;
                if ($staff_revenue_percentage_20 > _getConstant('payroll.STAFF_SCORECARD_REVENUE_PER')) {
                    $staff_revenue_percentage_20 = _getConstant('payroll.STAFF_SCORECARD_REVENUE_PER');
                }
            }

            ## Staff Profile Approved Calculation :
            $staff_assign_approved_member = StaffAssignApprovedMember::where('staff_id', $staff->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_profile_approved_percentage_10 = 0;
            if ($staff_assign_approved_member > 0 && $staff_kpi_assign_member > 0) {
                $staff_profile_approved_percentage_10 = ($staff_assign_approved_member * _getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER')) / $staff_kpi_assign_member;
                if ($staff_profile_approved_percentage_10 > _getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER')) {
                    $staff_profile_approved_percentage_10 = _getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER');
                }
            }

            ## Staff WP Plan Assign Calculation :
            $staff_wp_plan_assign_count = StaffAssignWpPlanMember::where('staff_id', $staff->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->count();
            $staff_wp_plan_assign_percentage_15 = 0;
            if ($staff_wp_plan_assign_count > 0 && $staff_kpi_assign_member > 0) {
                $staff_wp_plan_assign_percentage_15 = ($staff_wp_plan_assign_count * _getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER')) / $staff_kpi_assign_member;
                if ($staff_wp_plan_assign_percentage_15 > _getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER')) {
                    $staff_wp_plan_assign_percentage_15 = _getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER');
                }
            }
            $total_score = round($attendance_percentage_20 + $staff_kpi_percentage_35 + $staff_revenue_percentage_20 + $staff_profile_approved_percentage_10 + $staff_wp_plan_assign_percentage_15);
            if ($total_score >= 90) {
                $rating_text = "Outstanding";
            } elseif ($total_score >= 80) {
                $rating_text = "Exceeds Expectations";
            } elseif ($total_score >= 70) {
                $rating_text = "Meets Expectations";
            } elseif ($total_score >= 60) {
                $rating_text = "Needs Improvement";
            } else {
                $rating_text = "Underperforming";
            }
            $staffList[$key]->attendance_percentage_20 = $attendance_percentage_20;
            $staffList[$key]->staff_kpi_percentage_35 = $staff_kpi_percentage_35;
            $staffList[$key]->staff_revenue_percentage_20 = $staff_revenue_percentage_20;
            $staffList[$key]->staff_profile_approved_percentage_10 = $staff_profile_approved_percentage_10;
            $staffList[$key]->staff_wp_plan_assign_percentage_15 = $staff_wp_plan_assign_percentage_15;
            $staffList[$key]->total_score = $total_score;
            $staffList[$key]->rating_text = $rating_text;
            $staffList[$key]->profile_image = _assetUrl('upload_path.STAFF_IMAGE_URL') . '/' . $staff->profile_image;
        }
        return $staffList;
    }

    public function downloadPdf($monthYear) {
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
                        'Attendance Score ('._getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER').'%)' => number_format($staff->attendance_percentage_20,2),
                        'KPI Score ('._getConstant('payroll.STAFF_SCORECARD_KPI_PER').'%)' => number_format($staff->staff_kpi_percentage_35,2),
                        'Revenue Score ('._getConstant('payroll.STAFF_SCORECARD_REVENUE_PER').'%)' => number_format($staff->staff_revenue_percentage_20,2),
                        'Profile Approved Score ('._getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER').'%)' => number_format($staff->staff_profile_approved_percentage_10,2),
                        'WhatsApp Plan Assign Score ('._getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER').'%)' => number_format($staff->staff_wp_plan_assign_percentage_15,2),
                        'Total Score (%)' => number_format($staff->total_score,2),
                        'Rating' => $rating_text,
                    ];
                }
                $filename = 'staff_scorecards_' . $monthYear;
                $dataTableColm = [
                    'Username',
                    'Attendance Score ('._getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER').'%)',
                    'KPI Score ('._getConstant('payroll.STAFF_SCORECARD_KPI_PER').'%)',
                    'Revenue Score ('._getConstant('payroll.STAFF_SCORECARD_REVENUE_PER').'%)',
                    'Profile Approved Score ('._getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER').'%)',
                    'WhatsApp Plan Assign Score ('._getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER').'%)',
                    'Total Score (%)',
                    'Rating'
                ];
                $dataArr['sr_no'] = 'no';
                $dataArr['salary_month_year'] = $monthYear;
                $dataArr['dataTableColm'] = $dataTableColm;
                $dataArr['resultDataCsv'] = $resultDataCsv;
                $dataArr['reportTitle'] = 'Staff Scorecards Report';
                ini_set('memory_limit', '512M');
                set_time_limit(300);
                $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/reportPdf', $dataArr)->render();
                $pdf = Pdf::loadHTML($html);

                return $pdf->download($filename.'.pdf');
            } else {
                return back();
            }
        } else {
            return back();
        }
    }

    public function downloadCsv($monthYear = null) {
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
                    'Attendance Score ('._getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER').'%)' => number_format($staff->attendance_percentage_20,2),
                    'KPI Score ('._getConstant('payroll.STAFF_SCORECARD_KPI_PER').'%)' => number_format($staff->staff_kpi_percentage_35,2),
                    'Revenue Score ('._getConstant('payroll.STAFF_SCORECARD_REVENUE_PER').'%)' => number_format($staff->staff_revenue_percentage_20,2),
                    'Profile Approved Score ('._getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER').'%)' => number_format($staff->staff_profile_approved_percentage_10,2),
                    'WhatsApp Plan Assign Score ('._getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER').'%)' => number_format($staff->staff_wp_plan_assign_percentage_15,2),
                    'Total Score (%)' => number_format($staff->total_score,2),
                    'Rating' => $rating_text,
                ];
            }
            $filename = 'staff_scorecards_' . $monthYear;
            $dataTableColm = [
                'Username',
                'Attendance Score ('._getConstant('payroll.STAFF_SCORECARD_ATTENDANCE_PER').'%)',
                'KPI Score ('._getConstant('payroll.STAFF_SCORECARD_KPI_PER').'%)',
                'Revenue Score ('._getConstant('payroll.STAFF_SCORECARD_REVENUE_PER').'%)',
                'Profile Approved Score ('._getConstant('payroll.STAFF_SCORECARD_PROFILE_APPROVED_PER').'%)',
                'WhatsApp Plan Assign Score ('._getConstant('payroll.STAFF_SCORECARD_WP_PLAN_ASSIGN_PER').'%)',
                'Total Score (%)',
                'Rating'
            ];
            $array[] = $dataTableColm;
			foreach ($resultDataCsv as $key => $row){
				$line = array();
				foreach ($dataTableColm as $item){									
					$line[] = $row[$item]; 
				}
				$array[] = $line;
			}
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=\"$filename".".csv\"");
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

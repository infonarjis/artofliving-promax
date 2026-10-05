<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseActivity;
use App\Models\FranchiseLoginHistory;
use App\Models\Payment;
use App\Models\Register;
use Illuminate\Http\Request;
## Services:
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FranchiseDashboardController extends Controller
{
    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
    }

    public function index()
    {
        ## Remove Old Session Activity Type:
        session()->forget(['franchise_activity_type']);
        ## Button Permission Access :
        $authUserType = Auth::user();
        $userType = _adminUserType($authUserType->type);
        ## Total Payment History :
        $paymentHistory = Payment::where('status', 'SUCCESS')->sum('grand_total');
        $totalPayment = _defaultCurrency() . ' ' . $paymentHistory;
        ## Extra Js :
        $extraJsArrAdd = [
            '/custom/js/franchiseDashboard/list.js'
        ];

        $dataArr = [
            'pageName' => 'Franchise Dashboard',
            'totalPayment' => $totalPayment,
            'extraJsArr' => $extraJsArrAdd,
            'userType' => $userType,
            'franchiseListArr' => Franchise::active()->get(),
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/franchiseDashboard/index', $dataArr);
    }

    public function dashboardData(Request $request)
    {
        $responseArr['code'] = 300;
        $responseArr['status'] = 'error';
        $responseArr['message'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            if (isset($postData['checkCount']) && $postData['checkCount'] > '0') {
                ## Remove Old Session Activity Type:
                session()->forget(['franchise_activity_type']);
                $dataArr = [
                    'activity_type' => $postData['activity_type'],
                    'franchise_id' => $postData['franchise_id'],
                    'start_date' => $postData['start_date'],
                    'end_date' => $postData['end_date']
                ];
                ## Set Session:
                session([
                    'franchise_activity_type' => $dataArr
                ]);
                $responseArr['code'] = 200;
                $responseArr['status'] = 'success';
                $responseArr['message'] = 'Data update succesfully';
                $responseArr['redirectUrl'] = route('admin.member.index');
            }
        }
        return response()->json($responseArr, 200);
    }

    public function getGraphData(Request $request)
    {
        $responseArr['code'] = 300;
        $responseArr['status'] = 'error';
        $responseArr['message'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {

            if (!blank($postData['franchise_id'])) {
                $franchiseDataArr = Franchise::select(['id', 'username', 'email', 'last_login'])->where('id', $postData['franchise_id'])->first();
                $resultData['franchise_name'] = $franchiseDataArr->username;
                $resultData['franchise_email'] = $franchiseDataArr->email;
                $resultData['franchise_last_login'] = _displayDate($franchiseDataArr->last_login, 'j F, Y h:i A');
            } else {
                $resultData['franchise_name'] = '';
            }

            $activityTypes = [
                'view_profiles',
                'add_profiles',
                'edit_profiles',
                'plan_updates',
                'upload_photos',
                'add_comments',
            ];
            $activityQuery = FranchiseActivity::query()
                ->selectRaw('activity_type, COUNT(*) as total')
                ->whereIn('activity_type', $activityTypes)
                ->when(!blank($postData['franchise_id']), function ($q) use ($postData) {
                    $q->where('franchise_id', $postData['franchise_id']);
                })
                ->when(!blank($postData['start_date']) && !blank($postData['end_date']), function ($q) use ($postData) {
                    $q->whereBetween('created_at', [
                        $postData['start_date'],
                        $postData['end_date']
                    ]);
                })
                ->groupBy('activity_type')
                ->pluck('total', 'activity_type');
            $resultData['viewProfileCount']  = $activityQuery['view_profiles'] ?? 0;
            $resultData['addProfileCount']   = $activityQuery['add_profiles'] ?? 0;
            $resultData['editprofileCount']  = $activityQuery['edit_profiles'] ?? 0;
            $resultData['memberPaymentCount'] = $activityQuery['plan_updates'] ?? 0;
            $resultData['uploadPhotoCount']  = $activityQuery['upload_photos'] ?? 0;
            $resultData['addCommentCount']   = $activityQuery['add_comments'] ?? 0;

            ## Assigned Member Count :
            $resultData['assignedMemberCount'] = Register::query()
                ->whereNotNull('franchise_assign_id')
                ->where('franchise_assign_id', '!=', 0)
                ->when(!blank($postData['franchise_id']), function ($q) use ($postData) {
                    $q->where('franchise_assign_id', $postData['franchise_id']);
                })
                ->when(!blank($postData['start_date']) && !blank($postData['end_date']), function ($q) use ($postData) {
                    $q->whereBetween('created_at', [
                        $postData['start_date'],
                        $postData['end_date']
                    ]);
                })
                ->count();
            ## Unassigned Member Count :
            $resultData['pendingAssignMemberCount'] = Register::query()
                ->where(function ($q) {
                    $q->whereNull('franchise_assign_id')
                        ->orWhere('franchise_assign_id', 0);
                })
                ->when(!blank($postData['start_date']) && !blank($postData['end_date']), function ($q) use ($postData) {
                    $q->whereBetween('created_at', [
                        $postData['start_date'],
                        $postData['end_date']
                    ]);
                })
                ->count();


            ## Franchise Commissoin Reports:
            $start = Carbon::now()->startOfMonth()->subMonths(11);
            $end   = Carbon::now()->endOfMonth();

            $monthlyRows = Payment::query()
                ->selectRaw("DATE_FORMAT(plan_activate_date, '%Y-%m') as ym, SUM(franchise_comm_amt) as total")
                ->where('status', 'APPROVED')
                ->when(!blank($postData['franchise_id']), function ($q) use ($postData) {
                    $q->where('franchise_id', $postData['franchise_id']);
                })
                ->whereBetween('plan_activate_date', [$start, $end])
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');

            $recentMonthsName = [];
            $monthlyPayment   = [];
            for ($i = 0; $i < 12; $i++) {
                $month = $start->copy()->addMonths($i);
                $key   = $month->format('Y-m');

                $recentMonthsName[] = $month->format('M-y');
                $monthlyPayment[]   = number_format($monthlyRows[$key] ?? 0, 2, '.', '');
            }

            $resultData['recentMonthsName'] = $recentMonthsName;
            $resultData['monthlyPayment']   = $monthlyPayment;

            ## Total Franchise Payment History :
            $totalFranchiseAmount = Payment::query()
                ->where('status', 'APPROVED')
                ->when(!blank($postData['franchise_id']), function ($q) use ($postData) {
                    $q->where('franchise_id', $postData['franchise_id']);
                })
                ->sum('franchise_comm_amt');

            $resultData['totalFranchiseCommAmount'] = _defaultCurrency() . ' ' . number_format($totalFranchiseAmount, 2, '.', '');

            ## Total Time Spend:
            $totalSeconds = FranchiseLoginHistory::query()
                ->when(!blank($postData['franchise_id']), function ($q) use ($postData) {
                    $q->where('franchise_id', $postData['franchise_id']);
                })
                ->when(!blank($postData['start_date']) && !blank($postData['end_date']), function ($q) use ($postData) {
                    $q->whereBetween('login_at', [
                        $postData['start_date'],
                        $postData['end_date']
                    ]);
                })
                ->sum(DB::raw('TIMESTAMPDIFF(SECOND, login_at, logout_at)'));
            $hours   = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;
            $resultData['totalTimeSpend'] = "{$hours}h {$minutes}m {$seconds}s";

            $responseArr['code'] = 200;
            $responseArr['status'] = 'success';
            $responseArr['message'] = 'Data get successful';
            $responseArr['data'] = $resultData;
        }
        return response()->json($responseArr, 200);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Register;
use App\Models\Staff;
use App\Models\StaffActivity;
use App\Models\StaffLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    private $directoryName;
    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->directoryName = '/staffDashboard';
    }

    public function index()
    {
        ## Remove Old Session Activity Type:
        session()->forget(['staff_activity_type']);
        session()->forget(['franchise_activity_type']);

        ## Button Permission Access :
        $authUserType = Auth::user();
        $userType = _adminUserType($authUserType->type);
        ## Total Payment History
        $whereArr = ['status' => 'APPROVED'];
        $paymentHistory = Payment::where('status', 'SUCCESS')->sum('grand_total');
        $totalPayment = _defaultCurrency() . ' ' . $paymentHistory;
        ## Extra Js :
        $extraJsArrAdd = [
            '/custom/js/staffDashboard/list.js'
        ];

        $dataArr = [
            'pageName' => 'Staff Dashboard',
            'totalPayment' => $totalPayment,
            'extraJsArr' => $extraJsArrAdd,
            'userType' => $userType,
            'staffListArr' => Staff::active()->get(),
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
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
                session()->forget(['staff_activity_type']);
                $dataArr = [
                    'activity_type' => $postData['activity_type'],
                    'staff_id' => $postData['staff_id'],
                    'start_date' => $postData['start_date'],
                    'end_date' => $postData['end_date']
                ];
                ## Set Session:
                session([
                    'staff_activity_type' => $dataArr
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
            if (!blank($postData['staff_id'])) {
                $staffDataArr = Staff::select(['id', 'username', 'email', 'last_login'])->where('id', $postData['staff_id'])->first();
                $resultData['staff_name'] = $staffDataArr->username;
                $resultData['staff_email'] = $staffDataArr->email;
                $resultData['staff_last_login'] = _displayDate($staffDataArr->last_login, 'j F, Y h:i A');
            } else {
                $resultData['staff_name'] = '';
            }

            $activityTypes = [
                'view_profiles',
                'add_profiles',
                'edit_profiles',
                'plan_updates',
                'upload_photos',
                'add_comments',
            ];
            $activityQuery = StaffActivity::query()
                ->selectRaw('activity_type, COUNT(*) as total')
                ->whereIn('activity_type', $activityTypes)
                ->when(!blank($postData['staff_id']), function ($q) use ($postData) {
                    $q->where('staff_id', $postData['staff_id']);
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
                ->whereNotNull('staff_assign_id')
                ->where('staff_assign_id', '!=', 0)
                ->when(!blank($postData['staff_id']), function ($q) use ($postData) {
                    $q->where('staff_assign_id', $postData['staff_id']);
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
                    $q->whereNull('staff_assign_id')
                        ->orWhere('staff_assign_id', 0);
                })
                ->when(!blank($postData['start_date']) && !blank($postData['end_date']), function ($q) use ($postData) {
                    $q->whereBetween('created_at', [
                        $postData['start_date'],
                        $postData['end_date']
                    ]);
                })
                ->count();

            ## Total Time Spend:
            $totalSeconds = StaffLoginHistory::query()
                ->when(!blank($postData['staff_id']), function ($q) use ($postData) {
                    $q->where('staff_id', $postData['staff_id']);
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

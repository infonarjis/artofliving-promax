<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Payment;
use App\Models\Register;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    private $authUser;
    private string $userType;
    private int $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authUser = Auth::user();
            $this->userType = _adminUserType($this->authUser->type);
            $this->userId   = $this->authUser->id;
            return $next($request);
        });
    }

    /* ===============================================================
       Dashboard Main
    =============================================================== */
    public function index()
    {
        $paymentQuery = Payment::active();
        if ($this->userType === 'Franchise') {
            $paymentQuery->where('franchise_id', $this->userId);
        }
        if ($this->userType === 'Staff') {
            $paymentQuery->whereHas('member',fn($q) => $q->where('staff_assign_id', $this->userId));
        }
        $totalPayment = _defaultCurrency() . ' ' .number_format($paymentQuery->sum('grand_total'), 2);
        if ($this->userType === 'Staff') {
            $today = Carbon::today()->toDateString();
            $todayPunch = StaffAttendance::where('staff_id', $this->userId)
                ->whereDate('punch_in', $today)
                ->whereNull('punch_out')
                ->first();
        }
        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . '/dashboard/index',
            [
                'pageName'     => 'Dashboard',
                'totalPayment' => $totalPayment,
                'extraJsArr'   => ['/custom/js/dashboard/list.js'],
                'userType'     => $this->userType,
                'todayPunch'   => $todayPunch ?? null,
            ]
        );
    }

    /* ===============================================================
       Graph Data (Fully Optimized)
    =============================================================== */

    public function getGraphData(Request $request)
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $addBtnPermission = _checkPermission($userType, $roleId, 'view_member');

        /* ================= MEMBER STATS (1 QUERY) ================= */
        $stats = Register::query()
            ->when(
                $this->userType === 'Franchise',
                fn($q) => $q->where('franchise_assign_id', $this->userId)
            )
            ->when(
                $userType === 'Staff',
                function ($q) use ($addBtnPermission, $userId) {
                    // Own Members permission
                    if ($addBtnPermission === 'Own Members' && !blank($userId)) {
                        $q->where('staff_assign_id', $userId);
                    }
                }
            )
            ->selectRaw("
                COUNT(*) AS total,
                SUM(gender = 'Male') AS male,
                SUM(gender = 'Female') AS female,
                SUM(status = 'APPROVED') AS approved,
                SUM(status = 'UNAPPROVED') AS unapproved,
                SUM(status = 'Suspended') AS suspended,
                SUM(plan_status = 'Paid') AS paid,
                SUM(plan_status = 'Expired') AS expired,
                SUM(plan_status = 'Not Paid') AS not_paid
            ")
            ->first();

        /* ================= PHOTO STATS (1 QUERY INSTEAD OF 4) ================= */

        $photoStats = Register::active()
            ->when(
                $this->userType === 'Franchise',
                fn($q) => $q->where('franchise_assign_id', $this->userId)
            )
            ->when(
                $userType === 'Staff',
                function ($q) use ($addBtnPermission, $userId) {
                    // Own Members permission
                    if ($addBtnPermission === 'Own Members' && !blank($userId)) {
                        $q->where('staff_assign_id', $userId);
                    }
                }
            )
            ->selectRaw("
                SUM(photo1_status = 'UNAPPROVED' AND photo1 != '') AS photo1,
                SUM(photo2_status = 'UNAPPROVED' AND photo2 != '') AS photo2,
                SUM(photo3_status = 'UNAPPROVED' AND photo3 != '') AS photo3,
                SUM(photo4_status = 'UNAPPROVED' AND photo4 != '') AS photo4
            ")
            ->first();

        /* ================= LAST 12 MONTH RANGE ================= */

        $today = Carbon::today();
        $rangeStart = $today->copy()->subMonths(11)->startOfMonth();
        $rangeEnd   = $today->copy()->endOfMonth();

        /* ================= MONTHLY PAYMENTS ================= */

        $paymentRows = Payment::active()
            ->when(
                $this->userType === 'Franchise',
                fn($q) => $q->where('franchise_id', $this->userId)
            )
            ->when(
                $this->userType === 'Staff',
                fn($q) => $q->whereHas(
                    'member',
                    fn($mq) => $mq->where('staff_assign_id', $this->userId)
                )
            )
            ->whereBetween('plan_activate_date', [$rangeStart, $rangeEnd])
            ->selectRaw("DATE_FORMAT(plan_activate_date, '%Y-%m') AS ym,
                         SUM(grand_total) AS total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        /* ================= MONTHLY MEMBER STATUS ================= */

        $memberRows = Register::query()
            ->when(
                $userType === 'Franchise',
                function ($q) use ($addBtnPermission, $userId) {
                    // Own Members permission
                    if ($addBtnPermission === 'Own Members' && !blank($userId)) {
                        $q->where('franchise_assign_id', $userId);
                    }
                }
            )
            ->when(
                $userType === 'Staff',
                function ($q) use ($addBtnPermission, $userId) {
                    // Own Members permission
                    if ($addBtnPermission === 'Own Members' && !blank($userId)) {
                        $q->where('staff_assign_id', $userId);
                    }
                }
            )
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') AS ym,
                SUM(status = 'APPROVED') AS approved,
                SUM(status = 'UNAPPROVED') AS unapproved,
                SUM(status = 'Suspended') AS suspended
            ")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        ## BUILD CHART ARRAYS :
        $months = [];
        $monthlyPayments = [];
        $approvedMembers = [];
        $unapprovedMembers = [];
        $suspendedMembers = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $ym = $month->format('Y-m');
            $months[] = $month->shortMonthName;
            $monthlyPayments[] = (float) ($paymentRows[$ym] ?? 0);
            $approvedMembers[] = (int) ($memberRows[$ym]->approved ?? 0);
            $unapprovedMembers[] = (int) ($memberRows[$ym]->unapproved ?? 0);
            $suspendedMembers[] = (int) ($memberRows[$ym]->suspended ?? 0);
        }

        /* ================= FINAL RESPONSE ================= */

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'admin_id' => $this->userId,
                'user_type' => $this->userType,
                'allMemberCount' => (int) $stats->total,
                'maleMemberCount' => (int) $stats->male,
                'femaleMemberCount' => (int) $stats->female,
                'approvedMemberCount' => (int) $stats->approved,
                'unapprovedMemberCount' => (int) $stats->unapproved,
                'suspendedMemberCount' => (int) $stats->suspended,
                'paidMemberCount' => (int) $stats->paid,
                'expiredMemberCount' => (int) $stats->expired,
                'notPaidMemberCount' => (int) $stats->not_paid,
                'photo1' => (int) $photoStats->photo1,
                'photo2' => (int) $photoStats->photo2,
                'photo3' => (int) $photoStats->photo3,
                'photo4' => (int) $photoStats->photo4,
                'recentMonthsName' => $months,
                'monthlyPayment' => $monthlyPayments,
                'totalApprovedMembers' => $approvedMembers,
                'totalUnapprovedMembers' => $unapprovedMembers,
                'totalSuspendedMembers' => $suspendedMembers
            ]
        ]);
    }

    public function dashboardData(Request $request)
    {
        $responseArr['code'] = 300;
        $responseArr['status'] = 'error';
        $responseArr['message'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            if (session()->has('dashboardKey') && session()->has('dashboardValue')) {
                session()->forget(['dashboardKey', 'dashboardValue', 'setDashboardData']);
            }
            session([
                'dashboardKey' => $postData['dashboardKey'],
                'dashboardValue' => $postData['dashboardValue']
            ]);
            $responseArr['code'] = 200;
            $responseArr['status'] = 'success';
            $responseArr['message'] = 'Data update succesfully';
            $responseArr['redirectUrl'] = route('admin.member.index');
        }
        return response()->json($responseArr, 200);
    }

    /**
     * Mark all notifications as read
     */
    public function notificationUnread(Request $request)
    {
        ## Update Notification:
        AdminNotification::query()->update([
            'is_read' => 1,
            'updated_at' => _getCurrentDate(),
        ]);

        return response()->json([
            'code'    => 200,
            'status'  => 'success',
            'message' => _getConstant('responce_message.DATA_UPDATED_SUCCESS'),
        ]);
    }

    /* CLEAR CACHE  */
    public function clearCache()
    {
        Cache::flush();

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('clear-compiled');
        Artisan::call('optimize:clear');

        // Re-cache config and routes if needed
        Artisan::call('config:cache');
        Artisan::call('route:cache');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'All cache cleared successfully!'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Register;
use Illuminate\Support\Facades\Auth;

class AffiliateAssignMemberController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();

        /* ---------- Base Query Factory (VERY IMPORTANT) ---------- */
        $baseQuery = function () use ($affiliateUser) {
            return Register::where('affiliate_member_id', $affiliateUser->id);
        };

        /* ---------- Apply Filters ---------- */
        $query = $baseQuery();

        if ($range = request('range')) {
            if ($range == '7days') {
                $query->whereDate('created_at', '>=', now()->subDays(7));
            } elseif ($range == 'month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            }
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($plan = request('plan')) {
            $query->where('plan_id', $plan);
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%$search%")
                    ->orWhere('matri_id', 'like', "%$search%")
                    ->orWhere('plan_name', 'like', "%$search%");
            });
        }

        /* ---------- Stats (filter aware) ---------- */
        $totalMemberCount = (clone $query)->count();
        $verifiedCount    = (clone $query)->where('status', 'APPROVED')->count();
        $premiumCount     = (clone $query)->where('plan_status', 'Paid')->count();

        /* ---------- Table Data ---------- */
        $resultData = (clone $query)
            ->select('id', 'matri_id', 'fullname', 'plan_id', 'plan_status', 'plan_name', 'status', 'created_at')
            ->orderByDesc('id')
            ->paginate(10);

        $plans = MembershipPlan::active()
            ->select(['id', 'plan_name'])
            ->get();

        /* ---------- AJAX ---------- */
        if (request()->ajax()) {
            return view(
                _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.assignMember.ajax_result',
                compact('resultData')
            )->render();
        }

        $pageName = 'Referral Members';

        return view(
            _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.assignMember.index',
            compact(
                'pageName',
                'affiliateUser',
                'resultData',
                'plans',
                'totalMemberCount',
                'verifiedCount',
                'premiumCount'
            )
        );
    }
}

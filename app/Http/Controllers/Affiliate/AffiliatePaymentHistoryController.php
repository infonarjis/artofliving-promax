<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateMemberIncomeTransaction;
use Illuminate\Support\Facades\Auth;

class AffiliatePaymentHistoryController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();

        /* ---------- ONE BASE QUERY ---------- */
        $query = AffiliateMemberIncomeTransaction::where('affiliate_member_id', $affiliateUser->id);

        /* ---------- Proper Search (VERY IMPORTANT) ---------- */
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {

                // Search in income table
                $q->where('plan_name', 'like', "%{$search}%")

                  // Search in member table via relation
                  ->orWhereHas('member', function ($m) use ($search) {
                      $m->where('fullname', 'like', "%{$search}%")
                        ->orWhere('matri_id', 'like', "%{$search}%");
                  });
            });
        }

        /* ---------- Pagination ---------- */
        $resultData = $query
            ->orderByDesc('id')
            ->paginate(10);

        /* ---------- AJAX ---------- */
        if (request()->ajax()) {
            return view(
                _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.paymentHistory.ajax_result',
                compact('resultData')
            )->render();
        }

        $pageName = 'Payment History';

        return view(
            _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.paymentHistory.index',
            compact(
                'pageName',
                'affiliateUser',
                'resultData',
            )
        );
    }
}
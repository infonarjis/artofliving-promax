<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateMemberIncome;
use Illuminate\Support\Facades\Auth;

class AffiliateIncomeListController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();

        /* ---------- ONE BASE QUERY ---------- */
        $query = AffiliateMemberIncome::with([
            'member:id,matri_id,fullname'
        ])->where('affiliate_member_id', $affiliateUser->id);

        /* ---------- Stats (filter aware) ---------- */
        $onFieldVerifyProfile    = (clone $query)->where('income_type', 'On Field Verification Profile')->count();
        $verifyProfileCommission     = (clone $query)->where('income_type', 'Verified Profile')->count();
        $paidProfileCommission     = (clone $query)->where('income_type', 'Paid Member')->count();

        $onFieldVerifyAmount    = (clone $query)->where('income_type', 'On Field Verification Profile')->sum('amount');
        $verifyProfileAmount     = (clone $query)->where('income_type', 'Verified Profile')->sum('amount');
        $paidProfileAmount     = (clone $query)->where('income_type', 'Paid Member')->sum('amount');

        /* ---------- Pagination ---------- */
        $resultData = $query
            ->orderByDesc('id')
            ->paginate(10);

        /* ---------- AJAX ---------- */
        if (request()->ajax()) {
            return view(
                _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.incomeList.ajax_result',
                compact('resultData')
            )->render();
        }

        $pageName = 'Referral Members';

        return view(
            _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.incomeList.index',
            compact(
                'pageName',
                'affiliateUser',
                'resultData',
                'onFieldVerifyProfile',
                'verifyProfileCommission',
                'paidProfileCommission',
                'onFieldVerifyAmount',
                'verifyProfileAmount',
                'paidProfileAmount',
            )
        );
    }
}
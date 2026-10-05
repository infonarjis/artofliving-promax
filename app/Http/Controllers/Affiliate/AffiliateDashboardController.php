<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateMemberIncome;
use App\Models\AffiliateMemberIncomeTransaction;
use App\Models\AffiliateReferralClick;
use App\Models\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliateDashboardController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();
        $affiliateId = $affiliateUser->id;

        /* ---------- Base Query (Reuse) ---------- */
        $registerBaseQuery = Register::where('affiliate_member_id', $affiliateId);

        /* ---------- Latest 5 Referral Members ---------- */
        $refferalMembers = (clone $registerBaseQuery)
            ->select('id', 'matri_id', 'fullname', 'plan_id', 'plan_status', 'plan_name', 'status', 'created_at')
            ->latest('id')
            ->take(5)
            ->get();

        /* ---------- Total Referral Members Count ---------- */
        $refferalMembersCount = (clone $registerBaseQuery)->count();

        /* ---------- Total Referral Clicks ---------- */
        $affiliateVisitClick = AffiliateReferralClick::where('affiliate_member_id', $affiliateId)->count();

        /* ---------- Total Earnings (Transferred Only) ---------- */
        $totalEarn = AffiliateMemberIncome::where('affiliate_member_id', $affiliateId)
            ->sum('amount');
        
        // Income not yet added to a daily transaction
        $unsettledIncome = AffiliateMemberIncome::where(
            'affiliate_member_id',
            $affiliateId
        )
            ->where('is_transfered', 0)
            ->sum('amount');

        // All daily transaction batches
        $transactions = AffiliateMemberIncomeTransaction::where(
            'affiliate_member_id',
            $affiliateId
        );

        $totalTransactionAmount = (clone $transactions)->sum('amount');

        $pendingTransactionAmount = (clone $transactions)
            ->where('is_transfered', 0)
            ->sum('amount');

        $transferredAmount = (clone $transactions)
            ->where('is_transfered', 1)
            ->sum('amount');

        // Dashboard totals
        $totalEarnings = $unsettledIncome + $totalTransactionAmount;

        $pendingTransfer = $unsettledIncome + $pendingTransactionAmount;

        $awaitingSettlement = $unsettledIncome;

        $readyForAdminTransfer = $pendingTransactionAmount;
        // $totalEarn = AffiliateMemberIncome::where('affiliate_member_id', $affiliateId)
        //     ->where('is_transfered', 1)
        //     ->sum('amount');

        $pageName = 'Dashboard';
        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.dashboard.index', compact('affiliateUser', 'pageName', 'refferalMembers', 'refferalMembersCount', 'affiliateVisitClick','totalEarnings','pendingTransfer','transferredAmount','awaitingSettlement','readyForAdminTransfer'));
    }

    public function requestSettlement()
    {
        $affiliateMemberId = auth('affiliate')->id();

        if (!$affiliateMemberId) {
            return redirect()->back()->with('error', 'Please login first.');
        }

        try {
            DB::transaction(function () use ($affiliateMemberId) {

                // Get income records not yet included in a settlement
                $incomeRecords = AffiliateMemberIncome::where(
                    'affiliate_member_id',
                    $affiliateMemberId
                )
                    ->where('is_transfered', 0)
                    ->lockForUpdate()
                    ->get();

                if ($incomeRecords->isEmpty()) {
                    throw new \Exception('No income available for settlement.');
                }

                // Calculate total unsettled income
                $totalAmount = $incomeRecords->sum('amount');

                if ($totalAmount <= 0) {
                    throw new \Exception('Settlement amount must be greater than zero.');
                }

                // Create settlement transaction
                AffiliateMemberIncomeTransaction::create([
                    'affiliate_member_id' => $affiliateMemberId,
                    'amount'              => $totalAmount,
                    'is_transfered'       => 0,
                ]);

                // Mark income records as included in this settlement
                AffiliateMemberIncome::whereIn(
                    'id',
                    $incomeRecords->pluck('id')
                )->update([
                    'is_transfered' => 1,
                ]);
            });

            return redirect()->back()->with(
                'success',
                'Settlement request submitted successfully.'
            );

        } catch (\Exception $e) {

            return redirect()->back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}

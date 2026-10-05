<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateReferralClick;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliateVisitorClicksController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();

        $baseQuery = AffiliateReferralClick::where('affiliate_member_id', $affiliateUser->id);

        /* ---------------- FILTER ---------------- */
        if (request('filter')) {
            switch (request('filter')) {
                case 'today':
                    $baseQuery->whereDate('clicked_at', today());
                    break;
                case 'yesterday':
                    $baseQuery->whereDate('clicked_at', today()->subDay());
                    break;
                case 'last7':
                    $baseQuery->whereBetween('clicked_at', [now()->subDays(7), now()]);
                    break;
                case 'month':
                    $baseQuery->whereMonth('clicked_at', now()->month);
                    break;
            }
        }

        /* ---------------- SEARCH ---------------- */
        if (request('search')) {
            $search = request('search');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%$search%")
                    ->orWhere('browser', 'like', "%$search%")
                    ->orWhere('device', 'like', "%$search%");
            });
        }

        /* ---------------- FIRST CLICK PER IP TABLE ---------------- */
        $firstClicks = DB::table('affiliate_referral_clicks')
            ->select('ip_address', DB::raw('MIN(clicked_at) as first_click_time'))
            ->where('affiliate_member_id', $affiliateUser->id)
            ->groupBy('ip_address');

        /* ---------------- MAIN ANALYTICS QUERY ---------------- */
        $resultData = $baseQuery
            ->joinSub($firstClicks, 'fc', function ($join) {
                $join->on('affiliate_referral_clicks.ip_address', '=', 'fc.ip_address');
            })
            ->selectRaw('
        DATE(clicked_at) as click_date,
        referral_url,
        affiliate_referral_clicks.ip_address,
        browser,
        device,
        COUNT(*) as total_clicks,
        MAX(clicked_at) as last_click_time,
        fc.first_click_time
    ')
            ->groupBy(
                'click_date',
                'referral_url',
                'affiliate_referral_clicks.ip_address',
                'browser',
                'device',
                'fc.first_click_time'
            )
            ->orderByDesc('last_click_time')
            ->paginate(10);

        /* -------------------- STATS -------------------- */
        $totalClicks = AffiliateReferralClick::where('affiliate_member_id', $affiliateUser->id)->count();

        $uniqueVisitors = AffiliateReferralClick::where('affiliate_member_id', $affiliateUser->id)
            ->distinct('ip_address')
            ->count('ip_address');

        $topBrowser = AffiliateReferralClick::selectRaw('browser, COUNT(*) as total')
            ->where('affiliate_member_id', $affiliateUser->id)
            ->groupBy('browser')
            ->orderByDesc('total')
            ->first();

        $pageName = 'Visitor Clicks';
        if (request()->ajax()) {
            return view(
                _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.visitorClick.ajax_result',
                compact('pageName','affiliateUser','resultData','totalClicks','uniqueVisitors','topBrowser')
            )->render();
        }

        return view(
            _getConstant('dir_path.AFFILIATE_DIR_PATH') . '.visitorClick.index',
            compact('pageName','affiliateUser','resultData','totalClicks','uniqueVisitors','topBrowser')
        );
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddOnPackage;
use App\Models\MembershipPlan;
use App\Models\SiteSetting;
use App\Models\OfflinePayment;
use App\Services\UpgradeMembershipPlanService;
use Illuminate\Http\JsonResponse;
use Throwable;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $offlinePayment = OfflinePayment::active()->first();

        $standardPlans = MembershipPlan::active()
            ->standard()
            ->paid()
            ->get()
            ->each(fn($plan) => $this->applyDiscount($plan));

        // $personalizePlans = MembershipPlan::active()
        //     ->personalized()
        //     ->paid()
        //     ->get()
        //     ->each(fn($plan) => $this->applyDiscount($plan));
        $personalizePlans = [];

        return view('web.membershipPlan.index', compact(
            'offlinePayment',
            'standardPlans',
            'personalizePlans'
        ));
    }

    private function applyDiscount(MembershipPlan $plan): void
    {
        $originalAmount = (float) $plan->plan_amount;
        $discountPercent = (float) $plan->plan_discount;

        $discountValue = $discountPercent > 0
            ? ($originalAmount * $discountPercent) / 100
            : 0;

        $plan->plan_discount_amount = $originalAmount - $discountValue;
    }

    public function checkout($id)
    {
        $planDetail = MembershipPlan::active()->findOrFail($id);
        if ($planDetail) {
            $this->applyDiscount($planDetail);
        }
        if (blank($planDetail)) {
            return redirect()->back();
        }
        
        $packages = AddOnPackage::active()->get();
        $siteSetting = SiteSetting::select('tax_applicable', 'service_tax')->first();

        return view('web.membershipPlan.checkout', compact('planDetail', 'packages', 'siteSetting'));
    }

    public function calculatePrice(Request $request, UpgradeMembershipPlanService $service): JsonResponse
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:membership_plans,id',
            ]);
            $plan = MembershipPlan::active()->findOrFail($request->plan_id);
            $result = $service->calculatePlanAmount($plan, [
                'package_ids' => $request->package_ids ?? [],
                'coupon_code' => $request->coupon_code ?? null,
            ]);
            return response()->json([
                'success'         => true,
                'currency'        => $plan->currency_code,
                'plan_amount'     => round($result['plan_amount'], 2),
                'plan_discount'   => round($result['plan_discount'], 2),
                'addon_total'     => round($result['add_on_amount'], 2),
                'coupon_discount' => round($result['discount_amount'], 2),
                'tax_amount'      => round($result['tax_amount'], 2),
                'grand_total'     => round($result['grand_total'], 2),

                'coupon_applied'  => $result['coupon_applied'],
                'coupon_code'     => $result['coupon_code'],
                'message'         => $result['coupon_message'],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function calculateAddOnPreview(Request $request, UpgradeMembershipPlanService $assignPlanService)
    {
        $request->validate([
            'package_ids' => 'nullable|array',
            'coupon_code' => 'nullable|string',
        ]);

        $calculated = $assignPlanService->calculateAddOnAmount([
            'package_ids' => $request->package_ids ?? [],
            'coupon_code' => $request->coupon_code ?? null,
        ]);

        return response()->json($calculated);
    }
}

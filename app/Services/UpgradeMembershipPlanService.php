<?php

namespace App\Services;

use App\Models\AddOnPackage;
use App\Models\CouponCode;
use App\Models\AddOnPayment;
use App\Models\AffiliateMember;
use App\Models\AffiliateMemberIncome;
use App\Models\Franchise;
use App\Models\MembershipPayment;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\PlanUpgradeHistory;
use App\Models\Register;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpgradeMembershipPlanService
{
    /**
     * Method 1 (existing/unchanged): purchasing a new plan while one is
     * active immediately merges remaining benefits into the new plan
     * (see assign()).
     */
    public const MODE_IMMEDIATE = 'immediate';

    /**
     * Method 2 (new): Airtel/recharge-style. Purchasing a new plan while
     * one is active queues it; it activates automatically, fresh, once
     * the active plan expires (see queuePlan() / activateNextQueuedPlan()).
     */
    public const MODE_QUEUED = 'queued';

    /**
     * ---------------------------------------------------------
     * Calculate full billing details (plan + addons + coupon + tax)
     * ---------------------------------------------------------
     */
    public function calculatePlanAmount(MembershipPlan $plan, array $data = []): array
    {
        $currencyCode = $plan->currency_code;
        $originalPlanAmount = (float) $plan->plan_amount;
        $user = auth()->user();
        // If user is logged in and mobile matches, use international amount
        if ($user && str_starts_with($user->mobile, '+1-')) {
            $currencyCode = $plan->international_currency_code;
            $originalPlanAmount = (float) $plan->international_plan_amount;
        }
        /*
        |--------------------------------------------------------------------------
        | Plan Discount
        |--------------------------------------------------------------------------
        */
        $planDiscount = 0;
        if ($plan->plan_discount > 0) {
            $planDiscount =  ($originalPlanAmount * $plan->plan_discount) / 100;
        }
        $planAmountAfterDiscount = $originalPlanAmount - $planDiscount;

        /*
        |--------------------------------------------------------------------------
        | Addons
        |--------------------------------------------------------------------------
        */
        $addOnAmount = 0;

        $addOnInterest = 0;
        $addOnViewProfile = 0;
        $addOnViewContact = 0;
        $addOnDuration = 0;
        $addOnVideoCall = 0;
        $addOnAudioCall = 0;
        if (!empty($data['package_ids'])) {
            $packageIds = is_string($data['package_ids']) ? json_decode($data['package_ids'], true) : $data['package_ids'];
            $packageIds = array_filter($packageIds);
            $packages = AddOnPackage::active()->whereIn('id', $packageIds)->get();

            foreach ($packages as $package) {
                $addOnAmount += $package->package_amount;
                match ($package->package_category) {
                    'Allowed Interest Profiles' => $addOnInterest += $package->package_count,
                    'Allowed View Profiles' => $addOnViewProfile += $package->package_count,
                    'Allowed Contacts' => $addOnViewContact += $package->package_count,
                    'Allowed Duration' => $addOnDuration += $package->package_count,
                    'Allowed Video' => $addOnVideoCall += $package->package_count,
                    'Allowed Audio' => $addOnAudioCall += $package->package_count,
                    default => null
                };
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Subtotal
        |--------------------------------------------------------------------------
        */
        $subTotal = $planAmountAfterDiscount + $addOnAmount;

        /*
        |--------------------------------------------------------------------------
        | Coupon
        |--------------------------------------------------------------------------
        */
        $couponApplied = false;
        $couponCode = '';
        $couponId = null;
        $couponMessage = '';

        $discountAmount = 0;
        $discountDetail = '';
        if (!empty($data['coupon_code'])) {
            $coupon = CouponCode::active()
                ->validDate()
                ->where('plan_id', $plan->id)
                ->whereRaw(
                    'LOWER(coupon_code)=?',
                    [strtolower(trim($data['coupon_code']))]
                )
                ->first();
            if (!$coupon) {
                $couponMessage = __('messages.msg_invalid_coupon_code');
            } else {
                /*
                |--------------------------------------------------------------------------
                | Total usage count
                |--------------------------------------------------------------------------
                */
                $totalCouponUsed = $coupon->totalUsedCount();
                /*
                |--------------------------------------------------------------------------
                | Current user usage count
                |--------------------------------------------------------------------------
                */
                $userCouponUsed = 0;
                if (auth()->guard('web')->check()) {
                    $memberId = auth()->guard('web')->id();
                    $userCouponUsed = $coupon->userUsedCount($memberId);
                }
                if (!empty($coupon->total_use_limit) && $totalCouponUsed >= $coupon->total_use_limit) {
                    $couponMessage = __('messages.msg_coupon_usage_limit_exceeded');
                } elseif (!empty($coupon->per_user_use_limit) && $userCouponUsed >= $coupon->per_user_use_limit) {
                    $couponMessage = __('messages.msg_coupon_already_used');
                } else {
                    $couponApplied = true;
                    $couponCode = $coupon->coupon_code;
                    $couponId = $coupon->id;
                    /*
                    |--------------------------------------------------------------------------
                    | Flat / Percentage Discount
                    |--------------------------------------------------------------------------
                    */
                    if (isset($coupon->discount_type) && $coupon->discount_type == 'Flat') {
                        $discountAmount = $coupon->discount_amount;
                    } else {
                        $discountAmount = ($subTotal * $coupon->discount_amount) / 100;
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | Max Discount
                    |--------------------------------------------------------------------------
                    */
                    if (!empty($coupon->max_discount_amount) && $discountAmount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    }

                    $discountAmount = min($discountAmount, $subTotal);
                    $discountDetail = $coupon->coupon_code;
                    $couponMessage = __('messages.msg_coupon_code_applied_successfully');
                    $subTotal -= $discountAmount;
                }
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Tax
        |--------------------------------------------------------------------------
        */
        $taxAmount = 0;
        $taxName = '';
        $taxPercentage = 0;
        $config = _getSiteSetting();
        if ($user && str_starts_with($user->mobile, '+1-')) {
            $config['tax_applicable'] = 'No';
        }
        if (!empty($config['tax_applicable']) && $config['tax_applicable'] == 'Yes') {
            $taxName = $config['tax_name'];
            $taxPercentage = (float) $config['service_tax'];
            $taxAmount = ($subTotal * $taxPercentage) / 100;
        }
        /*
        |--------------------------------------------------------------------------
        | Grand Total
        |--------------------------------------------------------------------------
        */
        $grandTotal = $subTotal + $taxAmount;
        return [
            'currency_code' => $currencyCode,
            'plan_amount' => $originalPlanAmount,
            'plan_discount' => $planDiscount,
            'plan_amount_after_discount' => $planAmountAfterDiscount,
            'add_on_amount' => $addOnAmount,
            'add_on_interest' => $addOnInterest,
            'add_on_view_profile' => $addOnViewProfile,
            'addon_contact_views' => $addOnViewContact,
            'add_on_duration' => $addOnDuration,
            'add_on_video_call' => $addOnVideoCall,
            'add_on_audio_call' => $addOnAudioCall,
            'discount_amount' => $discountAmount,
            'discount_detail' => $discountDetail,
            'coupon_applied' => $couponApplied,
            'coupon_code' => $couponCode,
            'coupon_id' => $couponId,
            'coupon_message' => $couponMessage,
            'tax_applicable' => $config['tax_applicable'],
            'tax_name' => $taxName,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * ---------------------------------------------------------
     * Find the member's current, still-billable active plan.
     * Returns null if the member has no plan or it's not marked SUCCESS.
     * ---------------------------------------------------------
     */
    public function getActiveMembership(Register $user): ?Payment
    {
        return Payment::where('member_id', $user->id)
            ->where('current_plan', 'Yes')
            ->where('status', 'SUCCESS')
            ->first();
    }

    public function isCarryForwardEnabled(): bool
    {
        $carriedForward = 'No';
        return ($carriedForward ?? 'Yes') !== 'No';
    }

    /**
     * ---------------------------------------------------------
     * Calculate what should be carried forward from the member's
     * current active plan into the new plan being purchased.
     *
     * Rules:
     *  - No previous payment (fresh purchase) -> nothing carried forward.
     *  - Previous plan already expired -> nothing carried forward
     *    (an expired plan has no "remaining" benefit by definition).
     *  - Otherwise -> whatever is unused on the previous plan right now.
     * ---------------------------------------------------------
     */
    public function calculateRemainingBenefits(?Payment $currentPayment): array
    {
        $empty = [
            'remaining_days' => 0,
            'remaining_view_profile' => 0,
            'remaining_interest' => 0,
            'remaining_contact_views' => 0,
            'remaining_video_minutes' => 0,
            'remaining_audio_minutes' => 0,
        ];

        if (!$currentPayment) {
            return $empty;
        }
    
        if (!$this->isCarryForwardEnabled()) {
            return $empty;
        }

        $today = Carbon::now()->startOfDay();
        $expiry = Carbon::parse($currentPayment->plan_expiry_date)->endOfDay();

        if ($today->greaterThan($expiry)) {
            // Plan already lapsed -- nothing to carry forward.
            return $empty;
        }

        return [
            'remaining_days' => max(0, $today->diffInDays($expiry, false)),
            'remaining_view_profile' => $currentPayment->view_profile_remaining,
            'remaining_interest' => $currentPayment->interests_remaining,
            'remaining_contact_views' => $currentPayment->contact_views_remaining,
            'remaining_video_minutes' => $currentPayment->video_minutes_remaining,
            'remaining_audio_minutes' => $currentPayment->audio_minutes_remaining,
        ];
    }

    /**
     * ---------------------------------------------------------
     * Assign plan to user and create payment history.
     * If the member has an active plan, its unused validity and feature
     * balances are merged (carried forward) into the new plan.
     * ---------------------------------------------------------
     */
    public function assign(
        Register $user,
        MembershipPlan $plan,
        ?MembershipPayment $membershipPayment = null,
        array $data = []
    ): Payment {

        $calculated = $this->calculatePlanAmount($plan, $data);

        return DB::transaction(function () use ($user, $plan, $membershipPayment, $data, $calculated) {

            /*
            |--------------------------------------------------------------------------
            | Validate Existing Membership
            |--------------------------------------------------------------------------
            | Locked for the life of this transaction so a concurrent add-on
            | purchase (assignAddOnOnly) against the same active payment can't
            | race with this upgrade/renewal.
            */
            $currentPayment = Payment::where('member_id', $user->id)
                ->where('current_plan', 'Yes')
                ->where('status', 'SUCCESS')
                ->lockForUpdate()
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Calculate + Merge Remaining Benefits
            |--------------------------------------------------------------------------
            */
            $remaining = $this->calculateRemainingBenefits($currentPayment);

            $startDate = Carbon::now();

            /*
            |--------------------------------------------------------------------------
            | Carry Forward Remaining Validity
            |--------------------------------------------------------------------------
            | New expiry = New Plan Duration + Add-on Duration + Remaining Days
            | from the previous plan.
            */
            $endDate = $startDate->copy()
                ->addDays($plan->validity_days + $calculated['add_on_duration'] + $remaining['remaining_days']);

            // Update payment status if exists
            if ($membershipPayment) {
                $membershipPayment->update([
                    'status' => 'paid',
                    'transaction_id' => $data['transaction_id'] ?? null
                ]);
            }

            ## Check Member Is Assign Franchise Or Not :
            $franchisedBy = $user->franchised_by;
            $franchiseCommPer = 0;
            $franchiseCommAmt = 0;
            if (!empty($franchisedBy)) {
                $franchiseData = Franchise::active()->find($franchisedBy);
                if ($franchiseData) {
                    $franchiseCommPer = $franchiseData->commission ?? 0;
                    if ($franchiseCommPer > 0 && $calculated['plan_amount'] > 0) {
                        $franchiseCommAmt = round(($calculated['plan_amount'] * $franchiseCommPer) / 100, 2);
                    }
                }
            }

            // ---------------- Create Payment Record ----------------
            $paymentRecord = Payment::create([
                'member_id' => $user->id,
                'plan_id' => $plan->id,
                'previous_payment_id' => $currentPayment?->id,
                'plan_name' => $plan->plan_name,
                'plan_type' => $plan->plan_type,
                'plan_discount' => $plan->plan_discount,
                'plan_description' => $plan->plan_description,

                'plan_activate_date' => $startDate,
                'plan_expiry_date' => $endDate,

                'plan_validity_days' => $plan->validity_days,
                'addon_validity_days' => $calculated['add_on_duration'],
                'carried_forward_days' => $remaining['remaining_days'],
                'total_validity_days' => $plan->validity_days + $calculated['add_on_duration'] + $remaining['remaining_days'],

                'plan_view_profile' => $plan->view_profile_limit,
                'addon_view_profile' => $calculated['add_on_view_profile'],
                'carried_forward_view_profile' => $remaining['remaining_view_profile'],
                'view_profile_total' => $plan->view_profile_limit + $calculated['add_on_view_profile'] + $remaining['remaining_view_profile'],

                'plan_interest' => $plan->interests_limit,
                'addon_interest' => $calculated['add_on_interest'],
                'carried_forward_interest' => $remaining['remaining_interest'],
                'interests_total' => $plan->interests_limit + $calculated['add_on_interest'] + $remaining['remaining_interest'],

                'plan_contact_views' => $plan->contact_views_limit,
                'addon_contact_views' => $calculated['addon_contact_views'],
                'carried_forward_contact_views' => $remaining['remaining_contact_views'],
                'contact_views_total' => $plan->contact_views_limit + $calculated['addon_contact_views'] + $remaining['remaining_contact_views'],

                'plan_video_minutes' => $plan->video_minutes_limit,
                'addon_video_minutes' => $calculated['add_on_video_call'],
                'carried_forward_video_minutes' => $remaining['remaining_video_minutes'],
                'video_minutes_total' => $plan->video_minutes_limit + $calculated['add_on_video_call'] + $remaining['remaining_video_minutes'],

                'plan_audio_minutes' => $plan->audio_minutes_limit,
                'addon_audio_minutes' => $calculated['add_on_audio_call'],
                'carried_forward_audio_minutes' => $remaining['remaining_audio_minutes'],
                'audio_minutes_total' => $plan->audio_minutes_limit + $calculated['add_on_audio_call'] + $remaining['remaining_audio_minutes'],

                'can_chat' => $plan->can_chat,
                'is_personalized' => $plan->is_personalized,

                // if Franchise member:
                'franchise_id' => $franchisedBy,
                'franchise_comm_per' => $franchiseCommPer,
                'franchise_comm_amt' => $franchiseCommAmt,

                // Corrected billing fields
                'plan_amount' => $calculated['plan_amount'] - $calculated['plan_discount'],
                'currency_code' => $calculated['currency_code'],
                'discount_detail' => $calculated['discount_detail'],
                'discount_amount' => $calculated['discount_amount'],
                // 'coupon_id' => $calculated['coupon_id'] ?? null,
                'tax_name' => $calculated['tax_name'],
                'tax_percentage' => $calculated['tax_percentage'],
                'tax_amount' => $calculated['tax_amount'],
                'grand_total' => $calculated['grand_total'],

                'current_plan' => 'Yes',
                'is_renewal' => $currentPayment ? 'Yes' : 'No',
                'transaction_id' => $data['transaction_id'] ?? null,
                'payment_mode' => $data['payment_mode'] ?? '',
                'payment_note' => $data['payment_note'] ?? '',
                'assign_by' => $data['assign_by'] ?? '',
                'status' => "SUCCESS"
            ]);

            // ---------------- Addon Records ----------------
            if (!empty($data['package_ids'])) {
                $packageIds = is_string($data['package_ids'])
                    ? json_decode($data['package_ids'], true)
                    : $data['package_ids'];

                $addOnPackages = AddOnPackage::active()
                    ->whereIn('id', $packageIds)
                    ->get();

                foreach ($addOnPackages as $package) {
                    AddOnPayment::create([
                        'member_id' => $user->id,
                        'payment_id' => $paymentRecord->id,
                        'add_on_id' => $package->id,
                        'package_title' => $package->package_title,
                        'package_category' => $package->package_category,
                        'package_count' => $package->package_count,
                        'package_amount' => $package->package_amount,
                        'description' => $package->description,
                        'updated_at' => _getCurrentDate(),
                        'created_at' => _getCurrentDate(),
                    ]);
                }
            }

            if (!empty($user->affiliate_member_id)) {
                $affiliate = AffiliateMember::active()->select('id', 'paid_profile_commission')->find($user->affiliate_member_id);
                if ($affiliate && $affiliate->paid_profile_commission > 0) {
                    AffiliateMemberIncome::create([
                        'affiliate_member_id' => $affiliate->id,
                        'member_id'           => $user->id,
                        'amount'              => $affiliate->paid_profile_commission,
                        'is_transfered'       => 0,
                        'income_type'         => 'Paid Member',
                        'status'              => 1,
                        'created_at'          => now(),
                    ]);
                }
            }

            // ---------------- Deactivate old plans SAFELY ----------------
            Payment::where('member_id', $user->id)
                ->where('id', '!=', $paymentRecord->id)
                ->update(['current_plan' => 'No']);

            /*
            |--------------------------------------------------------------------------
            | Maintain Membership History
            |--------------------------------------------------------------------------
            | Only recorded when this was an actual upgrade/renewal (i.e. there
            | was a previous active plan to carry forward from).
            */
            if ($currentPayment) {
                PlanUpgradeHistory::create([
                    'member_id' => $user->id,
                    'previous_payment_id' => $currentPayment->id,
                    'new_payment_id' => $paymentRecord->id,
                    'previous_plan_id' => $currentPayment->plan_id,
                    'previous_plan_name' => $currentPayment->plan_name,
                    'new_plan_id' => $plan->id,
                    'new_plan_name' => $plan->plan_name,
                    'carried_forward_days' => $remaining['remaining_days'],
                    'carried_forward_view_profile' => $remaining['remaining_view_profile'],
                    'carried_forward_interest' => $remaining['remaining_interest'],
                    'carried_forward_contact_views' => $remaining['remaining_contact_views'],
                    'carried_forward_video_minutes' => $remaining['remaining_video_minutes'],
                    'carried_forward_audio_minutes' => $remaining['remaining_audio_minutes'],
                    'upgrade_date' => $startDate,
                ]);
            }

            // ---------------- Update User ----------------
            $updateUserData = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->plan_name,
                'plan_expired_on' => $endDate,
                'plan_status' => 'Paid',
            ];
            // If plan is personalized, also set user_type
            if ($plan->is_personalized) {
                $updateUserData['user_type'] = '1';
            }else{
                $updateUserData['user_type'] = '0';
            }
            // Update user in a single query
            $user->update($updateUserData);

            ## Send Notification :
            app(NotificationService::class)->sendNotification(
                $user,
                $user,
                'membership_purchase_success'
            );

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Membership Activated', $user, []);

            // Common Email Data
            $commonReplaceArr = [
                'user_name'     => $user->fullname,
                'user_matri_id' => $user->matri_id,
                'user_email'    => $user->email,
            ];
            // Send Membership Activated Email :
            app(EmailSendService::class)->send('Membership Activated', $user->email, $commonReplaceArr, ['memberData' => $user]);

            // Payment Receipt :
            $config = _getSiteSetting();
            $receiptReplaceArr = array_merge($commonReplaceArr, [
                'invoice_id'           => $config['invoice_prefix'] . $paymentRecord->id,
                'invoice_amount'       => $calculated['grand_total'],
                'invoice_download_url' => route(
                    'web.currentPlan.downloadInvoice',
                    $paymentRecord->id
                ),
            ]);
            app(EmailSendService::class)->send('Payment Receipt', $user->email, $receiptReplaceArr, ['memberData' => $user]);

            return $paymentRecord;
        });
    }

    /**
     * ---------------------------------------------------------
     * Single dynamic entry point for purchasing a plan.
     *
     * Routes to Method 1 (immediate merge, unchanged) or Method 2
     * (queued/recharge-style) based on config -- never on plan-specific
     * conditionals, so this stays reusable for every plan.
     * ---------------------------------------------------------
     */
    public function purchasePlan(
        Register $user,
        MembershipPlan $plan,
        ?MembershipPayment $membershipPayment = null,
        array $data = []
    ): Payment {
        if ($this->resolveActivationMode($plan) === self::MODE_QUEUED) {
            return $this->queuePlan($user, $plan, $membershipPayment, $data);
        }

        return $this->assign($user, $plan, $membershipPayment, $data);
    }

    /**
     * A plan can force its own mode via `membership_plans.activation_mode`.
     * Otherwise it inherits the global `plan_purchase_mode` site setting.
     * Defaults to the existing (immediate) behavior if nothing is set.
     */
    public function resolveActivationMode(MembershipPlan $plan): string
    {
        if (!empty($plan->activation_mode) && in_array($plan->activation_mode, [self::MODE_IMMEDIATE, self::MODE_QUEUED], true)) {
            return $plan->activation_mode;
        }

        $config = _getSiteSetting();

        return ($config['plan_purchase_mode'] ?? null) === self::MODE_QUEUED
            ? self::MODE_QUEUED
            : self::MODE_IMMEDIATE;
    }

    /**
     * ---------------------------------------------------------
     * Method 2: Purchase a plan recharge-style.
     *
     * - No active, unexpired plan -> activates immediately (delegates to
     *   assign(), whose carry-forward math naturally comes out to zero
     *   with nothing to merge from, so behavior is identical to a fresh
     *   purchase).
     * - Active, unexpired plan exists -> the new plan is stored as
     *   'Queued' and does NOT touch the running plan at all. It carries
     *   no benefits forward -- it will start completely fresh from its
     *   own activation date, per the recharge model.
     * ---------------------------------------------------------
     */
    public function queuePlan(
        Register $user,
        MembershipPlan $plan,
        ?MembershipPayment $membershipPayment = null,
        array $data = []
    ): Payment {
        return DB::transaction(function () use ($user, $plan, $membershipPayment, $data) {

            $activePayment = Payment::where('member_id', $user->id)
                ->where('plan_state', 'Active')
                ->where('status', 'SUCCESS')
                ->lockForUpdate()
                ->first();

            $activeIsUsable = $activePayment
                && !empty($activePayment->plan_expiry_date)
                && Carbon::now()->startOfDay()->lessThanOrEqualTo(
                    Carbon::parse($activePayment->plan_expiry_date)->endOfDay()
                );

            // Nothing running (or it already lapsed) -- nothing to queue
            // behind, so just activate now via the unchanged Method 1 flow.
            if (!$activeIsUsable) {
                return $this->assign($user, $plan, $membershipPayment, $data);
            }

            $calculated = $this->calculatePlanAmount($plan, $data);

            if ($membershipPayment) {
                $membershipPayment->update([
                    'status' => 'paid',
                    'transaction_id' => $data['transaction_id'] ?? null
                ]);
            }

            ## Franchise commission is credited at purchase time, same as Method 1
            $franchisedBy = $user->franchised_by;
            $franchiseCommPer = 0;
            $franchiseCommAmt = 0;
            if (!empty($franchisedBy)) {
                $franchiseData = Franchise::active()->find($franchisedBy);
                if ($franchiseData) {
                    $franchiseCommPer = $franchiseData->commission ?? 0;
                    if ($franchiseCommPer > 0 && $calculated['plan_amount'] > 0) {
                        $franchiseCommAmt = round(($calculated['plan_amount'] * $franchiseCommPer) / 100, 2);
                    }
                }
            }

            $nextQueueOrder = (int) Payment::where('member_id', $user->id)
                ->where('plan_state', 'Queued')
                ->max('queue_order') + 1;

            // No merge here -- totals are just this plan + its own add-ons.
            $queuedPayment = Payment::create([
                'member_id' => $user->id,
                'plan_id' => $plan->id,
                'previous_payment_id' => $activePayment->id,
                'plan_name' => $plan->plan_name,
                'plan_type' => $plan->plan_type,
                'plan_discount' => $plan->plan_discount,
                'plan_description' => $plan->plan_description,

                'plan_activate_date' => null,
                'plan_expiry_date' => null,

                'plan_validity_days' => $plan->validity_days,
                'addon_validity_days' => $calculated['add_on_duration'],
                'carried_forward_days' => 0,
                'total_validity_days' => $plan->validity_days + $calculated['add_on_duration'],

                'plan_view_profile' => $plan->view_profile_limit,
                'addon_view_profile' => $calculated['add_on_view_profile'],
                'carried_forward_view_profile' => 0,
                'view_profile_total' => $plan->view_profile_limit + $calculated['add_on_view_profile'],

                'plan_interest' => $plan->interests_limit,
                'addon_interest' => $calculated['add_on_interest'],
                'carried_forward_interest' => 0,
                'interests_total' => $plan->interests_limit + $calculated['add_on_interest'],

                'plan_contact_views' => $plan->contact_views_limit,
                'addon_contact_views' => $calculated['addon_contact_views'],
                'carried_forward_contact_views' => 0,
                'contact_views_total' => $plan->contact_views_limit + $calculated['addon_contact_views'],

                'plan_video_minutes' => $plan->video_minutes_limit,
                'addon_video_minutes' => $calculated['add_on_video_call'],
                'carried_forward_video_minutes' => 0,
                'video_minutes_total' => $plan->video_minutes_limit + $calculated['add_on_video_call'],

                'plan_audio_minutes' => $plan->audio_minutes_limit,
                'addon_audio_minutes' => $calculated['add_on_audio_call'],
                'carried_forward_audio_minutes' => 0,
                'audio_minutes_total' => $plan->audio_minutes_limit + $calculated['add_on_audio_call'],

                'can_chat' => $plan->can_chat,
                'is_personalized' => $plan->is_personalized,

                'franchise_id' => $franchisedBy,
                'franchise_comm_per' => $franchiseCommPer,
                'franchise_comm_amt' => $franchiseCommAmt,

                'plan_amount' => $calculated['plan_amount'] - $calculated['plan_discount'],
                'currency_code' => $plan->currency_code,
                'discount_detail' => $calculated['discount_detail'],
                'discount_amount' => $calculated['discount_amount'],
                'coupon_id' => $calculated['coupon_id'] ?? null,
                'tax_name' => $calculated['tax_name'],
                'tax_percentage' => $calculated['tax_percentage'],
                'tax_amount' => $calculated['tax_amount'],
                'grand_total' => $calculated['grand_total'],

                'current_plan' => 'No',
                'is_renewal' => 'Yes',
                'plan_state' => 'Queued',
                'queue_order' => $nextQueueOrder,
                'queued_at' => Carbon::now(),

                'transaction_id' => $data['transaction_id'] ?? null,
                'payment_mode' => $data['payment_mode'] ?? '',
                'payment_note' => $data['payment_note'] ?? '',
                'assign_by' => $data['assign_by'] ?? '',
                'status' => 'SUCCESS',
            ]);

            // ---------------- Addon Records (paid now, benefits apply on activation) ----------------
            if (!empty($data['package_ids'])) {
                $packageIds = is_string($data['package_ids'])
                    ? json_decode($data['package_ids'], true)
                    : $data['package_ids'];

                $addOnPackages = AddOnPackage::active()->whereIn('id', array_filter($packageIds))->get();

                foreach ($addOnPackages as $package) {
                    AddOnPayment::create([
                        'member_id' => $user->id,
                        'payment_id' => $queuedPayment->id,
                        'add_on_id' => $package->id,
                        'package_title' => $package->package_title,
                        'package_category' => $package->package_category,
                        'package_count' => $package->package_count,
                        'package_amount' => $package->package_amount,
                        'description' => $package->description,
                        'updated_at' => _getCurrentDate(),
                        'created_at' => _getCurrentDate(),
                    ]);
                }
            }

            if (!empty($user->affiliate_member_id)) {
                $affiliate = AffiliateMember::active()->select('id', 'paid_profile_commission')->find($user->affiliate_member_id);
                if ($affiliate && $affiliate->paid_profile_commission > 0) {
                    AffiliateMemberIncome::create([
                        'affiliate_member_id' => $affiliate->id,
                        'member_id'           => $user->id,
                        'amount'              => $affiliate->paid_profile_commission,
                        'is_transfered'       => 0,
                        'income_type'         => 'Paid Member',
                        'status'              => 1,
                        'created_at'          => now(),
                    ]);
                }
            }

            PlanUpgradeHistory::create([
                'member_id' => $user->id,
                'previous_payment_id' => $activePayment->id,
                'new_payment_id' => $queuedPayment->id,
                'previous_plan_id' => $activePayment->plan_id,
                'previous_plan_name' => $activePayment->plan_name,
                'new_plan_id' => $plan->id,
                'new_plan_name' => $plan->plan_name,
                'type' => 'Queue',
                'status' => 'Queued',
                'carried_forward_days' => 0,
                'carried_forward_view_profile' => 0,
                'carried_forward_interest' => 0,
                'carried_forward_contact_views' => 0,
                'carried_forward_video_minutes' => 0,
                'carried_forward_audio_minutes' => 0,
                'upgrade_date' => Carbon::now(),
            ]);

            app(SmsSendService::class)->sendTemplate('Plan Queued', $user, []);
            app(EmailSendService::class)->send('Plan Queued', $user->email, [
                'user_name'     => $user->fullname,
                'user_matri_id' => $user->matri_id,
                'user_email'    => $user->email,
                'plan_name'     => $plan->plan_name,
                'activates_on'  => _displayDate($activePayment->plan_expiry_date, 'j F, Y'),
            ], ['memberData' => $user]);

            return $queuedPayment;
        });
    }

    /**
     * ---------------------------------------------------------
     * Activate the next queued plan for a member, typically called when
     * their currently active plan has just expired. Marks $expiredPayment
     * as Expired (if given and still Active), then activates the earliest
     * queued payment (by queue_order) fresh from "now" -- no carry-forward,
     * matching the recharge model.
     *
     * Returns the newly activated Payment, or null if there was nothing
     * queued to activate.
     * ---------------------------------------------------------
     */
    public function activateNextQueuedPlan(Register $user, ?Payment $expiredPayment = null): ?Payment
    {
        return DB::transaction(function () use ($user, $expiredPayment) {

            if ($expiredPayment) {
                $locked = Payment::where('id', $expiredPayment->id)
                    ->where('plan_state', 'Active')
                    ->lockForUpdate()
                    ->first();

                if ($locked) {
                    $locked->update(['plan_state' => 'Expired', 'current_plan' => 'No']);
                }
            }

            $nextPlan = Payment::where('member_id', $user->id)
                ->where('plan_state', 'Queued')
                ->orderBy('queue_order')
                ->orderBy('queued_at')
                ->lockForUpdate()
                ->first();

            if (!$nextPlan) {
                // Nothing queued -- the member's membership genuinely lapses.
                $user->update(['plan_status' => 'Expired']);
                return null;
            }

            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($nextPlan->total_validity_days);

            $nextPlan->update([
                'plan_activate_date' => $startDate,
                'plan_expiry_date' => $endDate,
                'plan_state' => 'Active',
                'current_plan' => 'Yes',
                'activated_at' => $startDate,
            ]);

            // Belt-and-braces: make sure nothing else is left marked current.
            Payment::where('member_id', $user->id)
                ->where('id', '!=', $nextPlan->id)
                ->update(['current_plan' => 'No']);

            $updateUserData = [
                'plan_id' => $nextPlan->plan_id,
                'plan_name' => $nextPlan->plan_name,
                'plan_expired_on' => $endDate,
                'plan_status' => 'Paid',
            ];
            $planModel = MembershipPlan::find($nextPlan->plan_id);
            if ($planModel && $planModel->is_personalized) {
                $updateUserData['user_type'] = '1';
            }
            $user->update($updateUserData);

            PlanUpgradeHistory::create([
                'member_id' => $user->id,
                'previous_payment_id' => $expiredPayment?->id,
                'new_payment_id' => $nextPlan->id,
                'previous_plan_id' => $expiredPayment?->plan_id,
                'previous_plan_name' => $expiredPayment?->plan_name,
                'new_plan_id' => $nextPlan->plan_id,
                'new_plan_name' => $nextPlan->plan_name,
                'type' => 'Activate',
                'status' => 'Activated',
                'carried_forward_days' => 0,
                'carried_forward_view_profile' => 0,
                'carried_forward_interest' => 0,
                'carried_forward_contact_views' => 0,
                'carried_forward_video_minutes' => 0,
                'carried_forward_audio_minutes' => 0,
                'upgrade_date' => $startDate,
            ]);

            app(NotificationService::class)->sendNotification($user, $user, 'payment_received');
            app(SmsSendService::class)->sendTemplate('Plan Activated', $user, []);
            app(EmailSendService::class)->send('Plan Activated', $user->email, [
                'user_name'     => $user->fullname,
                'user_matri_id' => $user->matri_id,
                'user_email'    => $user->email,
                'plan_name'     => $nextPlan->plan_name,
                'expires_on'    => _displayDate($endDate, 'j F, Y'),
            ], ['memberData' => $user]);

            return $nextPlan->fresh();
        });
    }

    /**
     * ---------------------------------------------------------
     * Scheduler entry point (see ActivateQueuedMembershipPlans command).
     * Finds every Active plan whose expiry date has passed and activates
     * that member's next queued plan, if any. Dynamic and plan-agnostic --
     * it just walks whatever is due, regardless of which plan it is.
     * ---------------------------------------------------------
     */
    public function processExpiredPlans(): int
    {
        $activatedCount = 0;

        $duePayments = Payment::where('plan_state', 'Active')
            ->where('status', 'SUCCESS')
            ->whereNotNull('plan_expiry_date')
            ->whereDate('plan_expiry_date', '<', Carbon::today())
            ->get();

        foreach ($duePayments as $payment) {
            $user = Register::find($payment->member_id);
            if (!$user) {
                continue;
            }

            $activated = $this->activateNextQueuedPlan($user, $payment);
            if ($activated) {
                $activatedCount++;
            }
        }

        return $activatedCount;
    }

    /**
     * All plans currently queued for a member, in activation order.
     */
    public function getQueuedPlans(Register $user)
    {
        return Payment::where('member_id', $user->id)
            ->where('plan_state', 'Queued')
            ->orderBy('queue_order')
            ->get();
    }

    /**
     * Cancel a plan that hasn't activated yet (e.g. member changes their
     * mind). Does not touch the currently active plan. Refund handling, if
     * any, is left to the caller/payment gateway integration.
     */
    public function cancelQueuedPlan(Register $user, int $queuedPaymentId): Payment
    {
        $queued = Payment::where('id', $queuedPaymentId)
            ->where('member_id', $user->id)
            ->where('plan_state', 'Queued')
            ->first();

        if (!$queued) {
            throw new Exception(__('messages.msg_no_queued_plan_found'));
        }

        $queued->update(['plan_state' => 'Cancelled']);

        return $queued->fresh();
    }

    /**
     * ---------------------------------------------------------
     * Calculate add-on-only billing (no base plan involved)
     * ---------------------------------------------------------
     */
    public function calculateAddOnAmount(array $data = []): array
    {
        $addOnAmount = 0;
        $addOnInterest = 0;
        $addOnViewProfile = 0;
        $addOnViewContact = 0;
        $addOnDuration = 0;
        $addOnVideoCall = 0;
        $addOnAudioCall = 0;

        if (!empty($data['package_ids'])) {
            $packageIds = is_string($data['package_ids']) ? json_decode($data['package_ids'], true) : $data['package_ids'];
            $packageIds = array_filter($packageIds);
            $packages = AddOnPackage::active()->whereIn('id', $packageIds)->get();

            foreach ($packages as $package) {
                $addOnAmount += $package->package_amount;
                match ($package->package_category) {
                    'Allowed Interest Profiles' => $addOnInterest += $package->package_count,
                    'Allowed View Profiles' => $addOnViewProfile += $package->package_count,
                    'Allowed Contacts' => $addOnViewContact += $package->package_count,
                    'Allowed Duration' => $addOnDuration += $package->package_count,
                    'Allowed Video' => $addOnVideoCall += $package->package_count,
                    'Allowed Audio' => $addOnAudioCall += $package->package_count,
                    default => null
                };
            }
        }

        $subTotal = $addOnAmount;

        /* ---------------- Coupon ---------------- */
        $couponApplied = false;
        $couponCode = '';
        $couponId = null;
        $couponMessage = '';
        $discountAmount = 0;
        $discountDetail = '';

        if (!empty($data['coupon_code'])) {
            $coupon = CouponCode::active()
                ->validDate()
                ->whereRaw('LOWER(coupon_code)=?', [strtolower(trim($data['coupon_code']))])
                ->first();

            if (!$coupon) {
                $couponMessage = __('messages.msg_invalid_coupon_code');
            } else {
                $totalCouponUsed = $coupon->totalUsedCount();
                $userCouponUsed = 0;
                if (auth()->guard('web')->check()) {
                    $userCouponUsed = $coupon->userUsedCount(auth()->guard('web')->id());
                }

                if (!empty($coupon->total_use_limit) && $totalCouponUsed >= $coupon->total_use_limit) {
                    $couponMessage = __('messages.msg_coupon_usage_limit_exceeded');
                } elseif (!empty($coupon->per_user_use_limit) && $userCouponUsed >= $coupon->per_user_use_limit) {
                    $couponMessage = __('messages.msg_coupon_already_used');
                } else {
                    $couponApplied = true;
                    $couponCode = $coupon->coupon_code;
                    $couponId = $coupon->id;

                    $discountAmount = $coupon->discount_type == 'Flat'
                        ? $coupon->discount_amount
                        : ($subTotal * $coupon->discount_amount) / 100;

                    if (!empty($coupon->max_discount_amount) && $discountAmount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    }

                    $discountAmount = min($discountAmount, $subTotal);
                    $discountDetail = $coupon->coupon_code;
                    $couponMessage = __('messages.msg_coupon_code_applied_successfully');
                    $subTotal -= $discountAmount;
                }
            }
        }

        /* ---------------- Tax ---------------- */
        $taxAmount = 0;
        $taxName = '';
        $taxPercentage = 0;
        $config = _getSiteSetting();
        if (!empty($config['tax_applicable']) && $config['tax_applicable'] == 'Yes') {
            $taxName = $config['tax_name'];
            $taxPercentage = (float) $config['service_tax'];
            $taxAmount = ($subTotal * $taxPercentage) / 100;
        }

        $grandTotal = $subTotal + $taxAmount;

        return [
            'add_on_amount' => $addOnAmount,
            'add_on_interest' => $addOnInterest,
            'add_on_view_profile' => $addOnViewProfile,
            'addon_contact_views' => $addOnViewContact,
            'add_on_duration' => $addOnDuration,
            'add_on_video_call' => $addOnVideoCall,
            'add_on_audio_call' => $addOnAudioCall,
            'discount_amount' => $discountAmount,
            'discount_detail' => $discountDetail,
            'coupon_applied' => $couponApplied,
            'coupon_code' => $couponCode,
            'coupon_id' => $couponId,
            'coupon_message' => $couponMessage,
            'tax_applicable' => $config['tax_applicable'] ?? 'No',
            'tax_name' => $taxName,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * ---------------------------------------------------------
     * Buy add-ons only, applied on top of the user's CURRENT plan
     * (unrelated to the upgrade/renew carry-forward flow above --
     * this tops up an already-active plan rather than replacing it).
     * ---------------------------------------------------------
     */
    public function assignAddOnOnly(Register $user, int $currentPaymentId, array $data = []): Payment
    {
        $currentPayment = Payment::where('id', $currentPaymentId)
            ->where('member_id', $user->id)
            ->where('current_plan', 'Yes')
            ->first();

        if (!$currentPayment) {
            throw new Exception(__('messages.msg_no_active_plan_found'));
        }

        if (empty($data['package_ids'])) {
            throw new Exception(__('messages.msg_no_addon_selected'));
        }

        $calculated = $this->calculateAddOnAmount($data);

        return DB::transaction(function () use ($user, $currentPayment, $data, $calculated) {

            // Extend expiry only if a duration add-on was purchased
            $newExpiryDate = $calculated['add_on_duration'] > 0
                ? Carbon::parse($currentPayment->plan_expiry_date)->addDays($calculated['add_on_duration'])
                : $currentPayment->plan_expiry_date;

            $currentPayment->update([
                'addon_interest'      => $currentPayment->addon_interest + $calculated['add_on_interest'],
                'interests_total'     => $currentPayment->interests_total + $calculated['add_on_interest'],

                'addon_view_profile'      => $currentPayment->addon_view_profile + $calculated['add_on_view_profile'],
                'view_profile_total'     => $currentPayment->view_profile_total + $calculated['add_on_view_profile'],

                'addon_contact_views' => $currentPayment->addon_contact_views + $calculated['addon_contact_views'],
                'contact_views_total' => $currentPayment->contact_views_total + $calculated['addon_contact_views'],

                'addon_video_minutes' => $currentPayment->addon_video_minutes + $calculated['add_on_video_call'],
                'video_minutes_total' => $currentPayment->video_minutes_total + $calculated['add_on_video_call'],

                'addon_audio_minutes' => $currentPayment->addon_audio_minutes + $calculated['add_on_audio_call'],
                'audio_minutes_total' => $currentPayment->audio_minutes_total + $calculated['add_on_audio_call'],

                'addon_validity_days' => $currentPayment->addon_validity_days + $calculated['add_on_duration'],
                'total_validity_days' => $currentPayment->total_validity_days + $calculated['add_on_duration'],
                'plan_expiry_date'    => $newExpiryDate,
            ]);

            $user->update(['plan_expired_on' => $newExpiryDate]);

            // ---------------- Log each add-on against the SAME payment ----------------
            $packageIds = is_string($data['package_ids']) ? json_decode($data['package_ids'], true) : $data['package_ids'];
            $packageIds = array_filter($packageIds);
            $addOnPackages = AddOnPackage::active()->whereIn('id', $packageIds)->get();

            foreach ($addOnPackages as $package) {
                AddOnPayment::create([
                    'member_id'        => $user->id,
                    'payment_id'       => $currentPayment->id,
                    'add_on_id'        => $package->id,
                    'package_title'    => $package->package_title,
                    'package_category' => $package->package_category,
                    'package_count'    => $package->package_count,
                    'package_amount'   => $package->package_amount,
                    'description'      => $package->description,
                    'coupon_id'        => $calculated['coupon_id'] ?? null,
                    'discount_amount'  => $calculated['discount_amount'] ?? 0,
                    'tax_amount'       => $calculated['tax_amount'] ?? 0,
                    'grand_total'      => $calculated['grand_total'] ?? 0,
                    'transaction_id'   => $data['transaction_id'] ?? null,
                    'payment_mode'     => $data['payment_mode'] ?? '',
                    'updated_at'       => _getCurrentDate(),
                    'created_at'       => _getCurrentDate(),
                ]);
            }

            app(SmsSendService::class)->sendTemplate('AddOn Purchased', $user, []);

            app(EmailSendService::class)->send('AddOn Purchased', $user->email, [
                'user_name'     => $user->fullname,
                'user_matri_id' => $user->matri_id,
                'user_email'    => $user->email,
            ], ['memberData' => $user]);

            return $currentPayment->fresh();
        });
    }
}

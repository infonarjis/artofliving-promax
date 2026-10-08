<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddOnPackage;
use App\Models\MembershipPlan;
use App\Models\OfflinePayment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Api\ApiResponseService;
use App\Services\UpgradeMembershipPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MembershipPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $currencyCode = null;
            $authUser = auth()->guard('api')->user();
            $currencyCode = $authUser->country_id == 101 ? 'INR' : 'USD';

            $membershipPlan = MembershipPlan::active()->paid()
                ->when($currencyCode, function ($query) use ($currencyCode) {
                    $query->where('currency_code', $currencyCode);
                })
                ->get();

            $offlinePayment = OfflinePayment::active()->first();

            // Payment Gateway :
            $gateway = PaymentMethod::active()->latest()->first();
            $paymentMethod = [
                'in_app_payment' => 'Inactive',
            ];
            if ($gateway) {
                $paymentMethod['payment_name'] = $gateway->name;
                $paymentMethod['client_id'] = $gateway->client_id;
                $paymentMethod['client_secret'] = $gateway->client_secret;
                $paymentMethod['payment_mode'] = $gateway->payment_mode;

                if ($gateway->app_payment_source === 'IN_APP') {
                    $paymentMethod['in_app_payment'] = 'Active';
                }
            }

            $dataArr = [
                'membership_plans' => $membershipPlan,
                'offline_payment' => $offlinePayment,
                'payment_methods' => $paymentMethod
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Membership plan list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
    public function addOnPackageList(Request $request): JsonResponse
    {
        try {
            $addPackages = AddOnPackage::active()->get();

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $addPackages);
        } catch (Throwable $e) {
            Log::error('Membership plan list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function checkout(Request $request): JsonResponse
    {
        try {
            ## Check Validation :
            $validator = Validator::make($request->all(), [
                'plan_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $currencyCode = $authUser->country_id == 101 ? 'INR' : 'USD';

            $resultList = MembershipPlan::active()
                ->when($currencyCode, function ($query) use ($currencyCode) {
                    $query->where('currency_code', $currencyCode);
                })
                ->where('id', $request->plan_id)
                ->first();

            ## Admin Packages :
            $addPackages = AddOnPackage::active()->get();

            $resultArr = [
                'resultList'  => $resultList,
                'add_on_packages' => $addPackages
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $resultArr);
        } catch (Throwable $e) {
            Log::error('Membership plan checkout API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function calculatePrice(Request $request, UpgradeMembershipPlanService $service): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'plan_id'       => 'required|integer|exists:membership_plans,id',
                'package_ids'   => 'nullable|string',
                'coupon_code'   => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $plan = MembershipPlan::active()->findOrFail($request->plan_id);

            $packageIds = $request->filled('package_ids')
                ? array_filter(array_map('intval', explode(',', $request->input('package_ids'))))
                : [];
            $couponCode = trim((string) $request->input('coupon_code', ''));
            $result = $service->calculatePlanAmount($plan, [
                'package_ids' => $packageIds,
                'coupon_code' => $couponCode,
            ]);

            if ($couponCode !== '' && !$result['coupon_applied']) {
                return ApiResponseService::error($result['coupon_message']);
            }

            $dataArr = [
                'success'         => true,
                'currency'        => $plan->currency_code,
                'plan_amount'     => round($result['plan_amount'], 2),
                'plan_discount'   => round($result['plan_discount'], 2),
                'addon_total'     => round($result['add_on_amount'], 2),
                'coupon_discount' => round($result['discount_amount'], 2),
                'tax_amount'      => round($result['tax_amount'], 2),
                'grand_total'     => round($result['grand_total'], 2),

                'tax_applicable'  => $result['tax_applicable'],
                'tax_name'        => $result['tax_name'],
                'tax_percentage'  => $result['tax_percentage'],

                'coupon_applied'  => $result['coupon_applied'],
                'coupon_code'     => $result['coupon_code'],
                'message'         => $result['coupon_message'],
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Membership plan calculate price API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Assign Membership Plan
    public function assignPlan(Request $request, UpgradeMembershipPlanService $assignPlanService)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plan_id'           => 'nullable|string',
                'package_ids'       => 'nullable|string',
                'coupon_code'       => 'nullable|string|max:100',
                'transaction_id'    => 'nullable|string|max:255',
                'purchase_type'     => 'nullable',
                'payment_gateway'     => 'nullable',
                // 'current_plan_id'   => 'required_if:purchase_type,addon|integer|exists:payments,id',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $data = $validator->validated();

            $authUser = auth()->guard('api')->user();

            $packageIds = $request->filled('package_ids')
                ? array_values(array_filter(array_map('intval', preg_split('/\s*,\s*/', $request->package_ids))))
                : [];
            $couponCode = trim($data['coupon_code'] ?? '');
            $paymentMode = trim($data['payment_gateway'] ?? 'Paypal');

            $assignPlan = [];
            if (isset($data['purchase_type']) && $data['purchase_type'] == 'addon') {
                ## Check Member Plan Status :
                $currentPlan = Payment::select('id', 'member_id', 'current_plan')->where(['member_id' => $authUser->id, 'current_plan' => 'Yes',])->first();

                $assignPlan = $assignPlanService->assignAddOnOnly(
                    $authUser,
                    (int) $currentPlan->id,
                    [
                        'package_ids'    => $packageIds,
                        'coupon_code'    => $couponCode,
                        'transaction_id' => $data['transaction_id'] ?? null,
                        'payment_gateway'   => $paymentMode,
                    ]
                );
            } else {
                $plan = MembershipPlan::find($data['plan_id']);
                $assignPlan = $assignPlanService->assign(
                    $authUser,
                    $plan,
                    null,
                    [
                        'package_ids'   => $packageIds,
                        'coupon_code'   => $couponCode,
                        'transaction_id' => $data['transaction_id'] ?? null,
                        'payment_mode'  => $paymentMode,
                    ]
                );
            }

            return ApiResponseService::success(_getLangApi($request, 'lbl_payment_successful_msg'), $assignPlan);
        } catch (Throwable $e) {
            Log::error('Assign membership plan API failed.', [
                'member_id' => optional($authUser ?? null)->id,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

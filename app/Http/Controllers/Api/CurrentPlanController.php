<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddOnPackage;
use App\Models\AddOnPayment;
use App\Models\Payment;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CurrentPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $currentPlan = Payment::active()->current()->where('member_id', $memberId)->first();

            $addOnPlan = collect();
            if ($currentPlan) {
                $currentPlan->download_invoice = route('web.currentPlan.downloadInvoice', $currentPlan->id);

                $addOnPlan = AddOnPayment::where('member_id', $memberId)->where('payment_id', $currentPlan->id)->get();
            }

            $dataArr = [
                'current_plan' => $currentPlan,
                'add_on_plan' => $addOnPlan,
                'is_addon_package_enabled' => AddOnPackage::active()->exists(),
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Current plan list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function history(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $query = Payment::where('member_id', $memberId)->orderBy('id', 'desc');
            $resultCount = (clone $query)->count();
            $resultList = $query
                ->forPage($page, $limit)
                ->get()
                ->transform(function ($payment) {
                    $payment->download_invoice = route('web.currentPlan.downloadInvoice',$payment->id);

                    return $payment;
                });

            $dataArr = [
                'resultCount' => $resultCount,
                'resultList' => $resultList,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Old plan history list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

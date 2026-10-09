<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestCallBack;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RequestCallBackController extends Controller
{

    public function submit(Request $request): JsonResponse
    {
        try {
            $authUser = auth()->guard('api')->user();

            $rules = [
                'mobile'       => ['required', 'digits_between:7,15'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $data   = $validator->validated();

            RequestCallBack::create([
                'member_id' => $authUser->id       ?? null,
                'matri_id'  => $authUser->matri_id ?? null,
                'name'      => $authUser->fullname ?? $data['fullname'],
                'email'     => $authUser->email    ?? $data['email'],
                'mobile'    => $data['mobile'],
            ]);

            return ApiResponseService::success(_getLangApi($request, 'lbl_request_call_back_msg'));
        } catch (Throwable $e) {

            Log::error('report spam profile API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

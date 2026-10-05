<?php

namespace App\Services\Api;

use Illuminate\Contracts\Validation\Validator;

class ApiResponseService
{
    public static function unauthorized()
    {
        return response()->json([
            'code' => 200,
            'status' => 'error',
            'message' => 'Unauthenticated.',
            'data' => []
        ], 200);
    }

    public static function validationError(Validator $validator)
    {
        return response()->json([
            'code' => 200,
            'status' => 'error',
            'message' => $validator->errors()->first(),
            'data' => []
        ], 200);
    }

    public static function success(string $message = '', $data = [], int $code = 200)
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error(string $message = '', $data = [], int $code = 200)
    {
        return response()->json([
            'code' => $code,
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    // public static function customResponse(int $code = 400, $status = '', string $message = '', $data = [])
    // {
    //     return response()->json([
    //         'code' => $code,
    //         'status' => $status,
    //         'message' => $message,
    //         'data' => $data
    //     ], $code);
    // }

    // public static function serverError()
    // {
    //     return response()->json([
    //         'code' => 500,
    //         'status' => 'error',
    //         'message' => _getLang('msg_something_went_wrong'),
    //         'data' => []
    //     ], 500);
    // }
}

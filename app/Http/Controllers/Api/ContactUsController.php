<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactInqury;
use App\Services\Api\ApiResponseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ContactUsController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'          => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    'regex:/^[\pL\s\'.-]+$/u',
                ],
                'email'         => 'required|email:rfc|max:255',
                'country_code'  => [
                    'required',
                    'regex:/^\+\d{1,5}$/',
                ],
                'mobile_number' => 'required|digits_between:7,15',
                'subject'       => 'required|string|min:3|max:255',
                'message'       => 'required|string|min:10|max:1000',
            ],
            [
                // Name
                'name.required' => __('messages.msg_full_name_required'),
                'name.string'   => __('messages.msg_full_name_must_be_valid_string'),
                'name.min'      => __('messages.msg_full_name_min_length'),
                'name.max'      => __('messages.msg_full_name_max_length'),
                'name.regex'    => __('messages.msg_full_name_only_letters'),

                // Email
                'email.required' => __('messages.msg_email_required'),
                'email.email'    => __('messages.msg_email_valid_format'),
                'email.max'      => __('messages.msg_email_max_length'),

                // Country Code
                'country_code.required' => __('messages.msg_country_code_required'),
                'country_code.regex'    => __('messages.msg_country_code_invalid'),

                // Mobile Number
                'mobile_number.required'       => __('messages.msg_mobile_number_required'),
                'mobile_number.digits_between' => __('messages.msg_mobile_number_between_7_to_15_digits'),

                // Subject
                'subject.required' => __('messages.msg_subject_required'),
                'subject.string'   => __('messages.msg_subject_must_be_valid_string'),
                'subject.min'      => __('messages.msg_subject_min_length'),
                'subject.max'      => __('messages.msg_subject_max_length'),

                // Message
                'message.required' => __('messages.msg_message_required'),
                'message.string'   => __('messages.msg_message_must_be_valid_string'),
                'message.min'      => __('messages.msg_message_min_length'),
                'message.max'      => __('messages.msg_message_max_length'),
            ]
        );

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        try {
            $postData = $validator->validated();

            // Prevent duplicate inquiry within 5 minutes
            $isDuplicate = ContactInqury::where('email', strtolower(trim($postData['email'])))
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if ($isDuplicate) {
                return ApiResponseService::error(_getLangApi($request, 'msg_duplicate_inquiry'));
            }

            $countryCode = trim($postData['country_code']);
            $mobileNumber = trim($postData['mobile_number']);

            ContactInqury::create([
                'name'          => ucwords(strtolower(trim($postData['name']))),
                'email'         => strtolower(trim($postData['email'])),
                'mobile_number' => "{$countryCode}-{$mobileNumber}",
                'subject'       => trim($postData['subject']),
                'message'       => trim($postData['message']),
                'created_at'    => Carbon::now(),
            ]);
            
            return ApiResponseService::success(_getLangApi($request, 'msg_inquiry_submitted_successfully'));

        } catch (Throwable $e) {

            Log::error('Contact inquiry submission  API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}

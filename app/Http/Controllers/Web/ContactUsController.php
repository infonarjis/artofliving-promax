<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactInqury;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function index()
    {
        return view( _getConstant('dir_path.WEB_DIR_PATH') . '.contactUs.index',[]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|min:3|max:255|regex:/^[a-zA-Z\s]+$/',
            'email'         => 'required|email:rfc,dns|max:255',
            'country_code' => [
                'required',
                'string',
                'exists:country_master,country_code',
            ],
            'mobile_number' => 'required|digits_between:7,15',
            'subject'       => 'required|string|min:3|max:255',
            'message'      => 'required|string|min:10|max:1000',
        ], [
            // Name
            'name.required'      => __('messages.msg_full_name_required'),
            'name.string'        => __('messages.msg_full_name_must_be_valid_string'),
            'name.min'           => __('messages.msg_full_name_min_length'),
            'name.max'           => __('messages.msg_full_name_max_length'),
            'name.regex'         => __('messages.msg_full_name_only_letters'),

            // Email
            'email.required'     => __('messages.msg_email_required'),
            'email.email'        => __('messages.msg_email_valid_format'),
            'email.max'          => __('messages.msg_email_max_length'),

            // Country Code
            'country_code.required' => __('messages.msg_country_code_required'),

            // Mobile Number
            'mobile_number.required'       => __('messages.msg_mobile_number_required'),
            'mobile_number.digits_between' => __('messages.msg_mobile_number_between_7_to_15_digits'),

            // Subject
            'subject.required' => __('messages.msg_subject_required'),
            'subject.string'   => __('messages.msg_subject_must_be_valid_string'),
            'subject.min'      => __('messages.msg_subject_min_length'),
            'subject.max'      => __('messages.msg_subject_max_length'),

            // message
            'message.required' => __('messages.msg_message_required'),
            'message.string'   => __('messages.msg_message_must_be_valid_string'),
            'message.min'      => __('messages.msg_message_min_length'),
            'message.max'      => __('messages.msg_message_max_length'),
        ]);

        try {
            // Prevent duplicate inquiry within 5 minutes
            $isDuplicate = ContactInqury::where('email', strtolower(trim($validated['email'])))
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if ($isDuplicate) {
                return response()->json([
                    'status'  => false,
                    'message' => __('messages.msg_duplicate_inquiry'),
                ], 429);
            }

            $countryCode = trim($validated['country_code']);
            $mobileNumber = trim($validated['mobile_number']);

            ContactInqury::create([
                'name'          => ucwords(strtolower(trim($validated['name']))),
                'email'         => strtolower(trim($validated['email'])),
                'mobile_number' => "{$countryCode}-{$mobileNumber}",
                'subject'       => trim($validated['subject']),
                'message'       => trim($validated['message']),
                'created_at'    => Carbon::now(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_inquiry_submitted_successfully'),
            ], 200);

            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_inquiry_submitted_successfully'),
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_something_went_wrong'),
            ], 500);
        }
    }
}

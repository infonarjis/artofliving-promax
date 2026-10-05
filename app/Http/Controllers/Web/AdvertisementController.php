<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CaptchaHelper;
use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvertisementInquiry;
use App\Services\EmailSendService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AdvertisementController extends Controller
{
    public function index()
    {
        $captchaCode = CaptchaHelper::generate('advertisement_captcha');

        return view(_getConstant('dir_path.WEB_DIR_PATH').'.advertisement.index', [
            'captchaCode' => $captchaCode,
        ]);
    }

    public function refreshCaptcha()
    {
        return response()->json([
            'success'     => true,
            'captchaCode' => CaptchaHelper::generate('advertisement_captcha'),
        ]);
    }

    public function submitInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'link'            => 'required|url|max:255',
            'country_code' => [
                'required',
                'string',
                'exists:country_master,country_code',
            ],
            'mobile'          => 'required|digits_between:8,15',
            'email'           => 'required|email:rfc,dns',
            'contact_person'  => 'required|string|max:255',
            'captcha_code'    => 'required',
            'image'           => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!CaptchaHelper::validate($request->captcha_code, 'advertisement_captcha')) {
            return response()->json([
                'status'      => false,
                'captchaCode' => CaptchaHelper::generate('advertisement_captcha'),
                'errors'      => [
                    'captcha_code' => [__('messages.msg_invalid_captcha')],
                ],
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Upload image
            $path = _getConstant('upload_path.ADVERTISE_IMAGE_URL');
            $imageName = UploadHelper::uploadFile($request->file('image'), $path);

            if (!$imageName) {
                throw new \Exception('Image upload failed');
            }

            $inquiry = AdvertisementInquiry::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'mobile'         => $request->country_code . $request->mobile,
                'link'           => $request->link,
                'contact_person' => $request->contact_person,
                'image'          => $imageName,
                'status'         => 'PENDING',
            ]);

            ## Send email to admin :
            $adminEmail = _getSiteSetting('contact_email');
            app(EmailSendService::class)->send(
                'Advertisement Inquiry',
                $adminEmail,
                [
                    'applicant_name'           => $inquiry->name,
                    'applicant_email'          => $inquiry->email,
                    'applicant_mobile'         => $inquiry->mobile,
                    'applicant_link'           => $inquiry->link,
                    'applicant_contact_person' => $inquiry->contact_person
                ]
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_advertisement_inquiry_submitted_successfully')
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('messages.msg_something_went_wrong')
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Services\Api\ApiResponseService;
use App\Services\MyProfileSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MemberDeleteProfile;
use App\Models\RegisterPartner;
use App\Services\AdminCommonActionModel;
use App\Services\AiGenerateService;
use App\Services\EmailSendService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MyProfileController extends Controller
{
    /**
     * Get authenticated member profile
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $member = auth()->guard('api')->user();
            $currentLanguage = $request->header('lang_code', _getDefaultLanguage());
            App::setLocale(session('locale', $currentLanguage));
            
            if (!$member) {
                return ApiResponseService::unauthorized();
            }

            $memberId = $member->id;

            // Refresh data from DB (optional) :
            $member = Register::find($memberId);
            if (!$member) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_member_not_found')
                );
            }

            ## Photos Full Url :
            $member->photo1 = _checkImageUrlApi('upload_path.MEMBER_PHOTOS_URL', $member->photo1, $member->gender);
            $member->photo2 = _checkImageUrlApi('upload_path.MEMBER_PHOTOS_URL', $member->photo2, $member->gender);
            $member->photo3 = _checkImageUrlApi('upload_path.MEMBER_PHOTOS_URL', $member->photo3, $member->gender);
            $member->photo4 = _checkImageUrlApi('upload_path.MEMBER_PHOTOS_URL', $member->photo4, $member->gender);
            ## Id Proof Full Url :
            $member->id_proof_front = _checkImageUrlApi('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_front);
            $member->id_proof_back = _checkImageUrlApi('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_back);
            ## Horoscope Full Url :
            $member->horoscope_file = _checkImageUrlApi('upload_path.MEMBER_HOROSCOPE_URL', $member->horoscope_file);

            $member->age = _birthdateDisplay($member->birthdate, 0);
            $member->height_str = _displayHeight($member->height);

            $member->partner_preference = $member->partnerPreference;
            $member->download_biodata = route('web.myProfile.downloadBiodataPdf', [$member->id]);

            ## Dyanmic View :
            $member->myProfileTab = MyProfileSectionService::getSections($member, $currentLanguage);
            ## Partner Sections :
            $member->partnerPreferenceTab = MyProfileSectionService::partnerSection($member->partnerPreference, $currentLanguage);

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $member);
        } catch (Throwable $e) {

            Log::error('Get my profile API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Edit Profiles:
    public function editProfile(Request $request): JsonResponse
    {
        try {
            $memberId = auth()->guard('api')->id();

            $validator = Validator::make($request->all(), [
                'form_type' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $member = Register::findOrFail($memberId);

            $form_type = $request->form_type;
            switch ($form_type) {
                case 'basic_details':
                    $validator = Validator::make($request->all(), [
                        'mother_tongue' => 'required',
                        'marital_status' => 'required',
                        'profileby' => 'required'
                    ], [
                        'mother_tongue.required' => 'Please select mother tongue.',
                        'marital_status.required' => 'Please select marital status.',
                        'profileby.required' => 'Please select profile by.',
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'profileby' => $request->profileby,
                        'mother_tongue' => $request->mother_tongue,
                        'birthplace' => $request->birthplace,
                        'birthtime' => $request->birthtime,
                        'marital_status' => $request->marital_status,
                        'total_children' => $request->total_children,
                        'status_children' => $request->status_children,
                    ]);
                    break;
                case 'religious_information':
                    $validator = Validator::make($request->all(), [
                        'religion' => 'required',
                        'caste' => 'required'
                    ], [
                        'religion.required' => 'Please select religion.',
                        'caste.required' => 'Please enter caste.'
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'religion' => $request->religion,
                        'caste' => $request->caste,
                        'subcaste' => $request->subcaste,
                        'manglik' => $request->manglik,
                        'gothra' => $request->gothra,
                        'moonsign' => $request->moonsign,
                        'star' => $request->star,
                        'horoscope' => $request->horoscope
                    ]);
                    break;
                case 'location_details':
                    $rules = [
                        'country_id' => 'required',
                        'state_id' => 'required',
                        'city' => 'required',
                    ];

                    $messages = [
                        'country_id.required' => 'Please select country.',
                        'state_id.required' => 'Please select state.',
                        'city.required' => 'Please enter city.',
                    ];

                    $validator = Validator::make(
                        $request->all(),
                        $rules,
                        $messages
                    );

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'country_id' => $request->country_id,
                        'state_id' => $request->state_id,
                        'city' => $request->city,
                        'alternate_number' => $request->alternate_number,
                        'address' => $request->address,
                        'nri_country' => $request->nri_country,
                        'residence_type' => $request->residence_type
                    ]);
                    break;
                case 'education_details':
                    $validator = Validator::make($request->all(), [
                        'education_level' => 'required',
                        'occupation' => 'required',
                        'employee_in' => 'required',
                        'income' => 'required',
                        'designation_level' => 'required',
                    ], [
                        'education_level.required' => 'Please select education level.',
                        'occupation.required' => 'Please select occupation.',
                        'employee_in.required' => 'Please enter employee in.',
                        'income.required' => 'Please enter annual income.',
                        'designation_level.required' => 'Please enter designation.',
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'education_level' => $request->education_level,
                        'education_details' => $request->education_details,
                        'occupation' => $request->occupation,
                        'employee_in' => $request->employee_in,
                        'income' => $request->income,
                        'designation_level' => $request->designation_level
                    ]);
                    break;
                case 'physical_information':
                    $validator = Validator::make($request->all(), [
                        'height' => 'required',
                        'weight' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'height' => $request->height,
                        'weight' => $request->weight,
                        'diet' => $request->diet,
                        'smoke' => $request->smoke,
                        'drink' => $request->drink,
                        'complexion' => $request->complexion,
                        'body_type' => $request->body_type,
                        'about_me_description' => $request->about_me_description,
                        'blood_group_id' => $request->blood_group_id,
                    ]);
                    break;
                case 'art_of_living_association':
                    $validator = Validator::make($request->all(), [
                        'Yesart_of_living_teacher' => 'required',
                        'have_art_of_living_program' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $member->update([
                        'Yesart_of_living_teacher' => $request->Yesart_of_living_teacher,
                        'teacher_code' => $request->teacher_code,
                        'teaching_courses' => $request->teaching_courses,
                        'have_art_of_living_program' => $request->have_art_of_living_program,
                        'teacher_name' => $request->teacher_name,
                        'teacher_mobile_no' => $request->teacher_mobile_no,
                        'art_of_living_program' => $request->art_of_living_program,
                        'no_of_years_in_artofliving' => $request->no_of_years_in_artofliving,
                    ]);

                    break;
                case 'family_details':
                    $validator = Validator::make($request->all(), [
                        'father_name' => 'required',
                        'father_occupation' => 'required',
                        'mother_name' => 'required',
                        'mother_occupation' => 'required'
                    ], [
                        'father_name.required' => 'Please select father name.',
                        'father_occupation.required' => 'Please select father occupation.',
                        'mother_name.required' => 'Please enter mother name.',
                        'mother_occupation.required' => 'Please enter mother occupation.',
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $member->update([
                        'family_type' => $request->family_type,
                        'family_status' => $request->family_status,
                        'father_name' => $request->father_name,
                        'father_occupation' => $request->father_occupation,
                        'mother_name' => $request->mother_name,
                        'mother_occupation' => $request->mother_occupation,
                        'no_of_brother' => $request->no_of_brother,
                        'no_of_married_brother' => $request->no_of_married_brother,
                        'no_of_sister' => $request->no_of_sister,
                        'no_of_married_sister' => $request->no_of_married_sister,
                        'family_details' => $request->family_details
                    ]);

                    break;
                case 'partner_preference':
                    RegisterPartner::updateOrCreate(
                        ['member_id' => $memberId],
                        [
                            'part_frm_age' => $request->part_frm_age,
                            'part_to_age' => $request->part_to_age,
                            'part_height' => $request->part_height,
                            'part_height_to' => $request->part_height_to,
                            'part_religion' => $request->part_religion ?? null,
                            'part_caste' => $request->part_caste ?? null,
                            'part_country' => $request->part_country ?? null,
                            'part_state' => $request->part_state ?? null,
                            'part_marital_status' => $request->part_marital_status ?? null,
                            'part_income' => $request->part_income ?? null,
                            'part_education' => $request->part_education ?? null,
                            'part_occupation' => $request->part_occupation ?? null,
                            'part_mothertongue' => $request->part_mothertongue ?? null,
                            'part_manglik' => $request->part_manglik ?? null,
                            'part_diet' => $request->part_diet ?? null,
                            'part_smoke' => $request->part_smoke ?? null,
                            'part_drink' => $request->part_drink ?? null,
                            'part_art_of_living_teacher' => $request->part_art_of_living_teacher ?? null,
                            'part_have_art_of_living_program' => $request->part_have_art_of_living_program ?? null,
                        ]
                    );
                    break;
                case 'upload_photos':
                    $validator = Validator::make($request->all(), [
                        [
                            'photo1' => $member->photo1 ? 'nullable' : 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
                            'photo2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                            'photo3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                            'photo4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                        ],
                        [
                            'photo1.required' => __('messages.msg_please_upload_profile_photo'),
                            'image' => __('messages.msg_valid_image'),
                            'mimes' => __('messages.msg_image_type'),
                            'max' => __('messages.msg_image_max_size'),
                        ],
                        [
                            'photo1' => __('messages.attr_photo1'),
                            'photo2' => __('messages.attr_photo2'),
                            'photo3' => __('messages.attr_photo3'),
                            'photo4' => __('messages.attr_photo4')
                        ]
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
                    // $updateData = [
                    //     'photo_visibility' => $request->photo_visibility
                    // ];
                    $updateData = [];
                    foreach ($photos as $photo) {
                        if ($request->hasFile($photo)) {
                            $path = _getConstant('upload_path.MEMBER_PHOTOS_URL');
                            $blurPath = _getConstant('upload_path.MEMBER_BLUR_PHOTOS_URL');
                            $oldValue = $member->$photo ?? '';
                            $filename = UploadHelper::uploadFile($request->file($photo), $path, $oldValue, '', 1, $blurPath);
                            if ($filename) {
                                $updateData[$photo] = $filename;
                                $updateData[$photo . '_status'] = 'UNAPPROVED';
                                $updateData[$photo . '_uploaded_on'] = now();
                            }
                        }
                    }
                    $member->update($updateData);

                    ## Send Admin Notification :
                    AdminCommonActionModel::sendAdminNotification($member->id, $member->matri_id, 'photo_upload');

                    break;
                case 'upload_id_proof':
                    $rules = [
                        'id_proof_type' => 'required',
                    ];

                    if (!$member->id_proof_front) {
                        $rules['id_proof_front'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                    }

                    if (!$member->id_proof_back) {
                        $rules['id_proof_back'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                    }

                    $messages = [
                        'id_proof_type.required'  => __('messages.msg_select_id_proof_type'),
                        'id_proof_front.required' => __('messages.msg_upload_id_proof_front'),
                        'id_proof_back.required'  => __('messages.msg_upload_id_proof_back'),

                        'id_proof_front.image' => __('messages.msg_valid_image'),
                        'id_proof_back.image'  => __('messages.msg_valid_image'),

                        'id_proof_front.mimes' => __('messages.msg_image_type'),
                        'id_proof_back.mimes'  => __('messages.msg_image_type'),

                        'id_proof_front.max' => __('messages.msg_image_max_size_5mb'),
                        'id_proof_back.max'  => __('messages.msg_image_max_size_5mb'),
                    ];

                    $attributes = [
                        'id_proof_type'  => __('messages.attr_id_proof_type'),
                        'id_proof_front' => __('messages.attr_id_proof_front'),
                        'id_proof_back'  => __('messages.attr_id_proof_back'),
                    ];

                    $validator = Validator::make(
                        $request->all(),
                        $rules,
                        $messages,
                        $attributes
                    );

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $idProof = ['id_proof_front', 'id_proof_back'];
                    $updateData = [];
                    foreach ($idProof as $id_type) {
                        if ($request->hasFile($id_type)) {
                            $path = _getConstant('upload_path.MEMBER_IDPROOF_URL');
                            $oldValue = $member->$id_type ?? '';
                            $filename = UploadHelper::uploadFile($request->file($id_type), $path, $oldValue);
                            if ($filename) {
                                $updateData[$id_type] = $filename;
                            }
                        }
                    }
                    $updateData['id_proof_type'] = $request->id_proof_type;
                    $updateData['id_proof_status'] = 'UNAPPROVED';
                    $updateData['id_proof_uploaded_on'] = now();
                    $member->update($updateData);

                    ## Send Admin Notification :
                    AdminCommonActionModel::sendAdminNotification($member->id, $member->matri_id, 'photo_upload');

                    break;
                case 'upload_horoscope':
                    $validator = Validator::make($request->all(), [
                        'horoscope_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $horoscopeFile = ['horoscope_file'];
                    $updateData = [];
                    foreach ($horoscopeFile as $file) {
                        if ($request->hasFile($file)) {
                            $path = _getConstant('upload_path.MEMBER_HOROSCOPE_URL');
                            $oldValue = $member->$file ?? '';
                            $filename = UploadHelper::uploadFile($request->file($file), $path, $oldValue);
                            if ($filename) {
                                $updateData[$file] = $filename;
                            }
                        }
                    }
                    $updateData['horoscope_status'] = 'UNAPPROVED';
                    $updateData['horoscope_uploaded_on'] = now();
                    $member->update($updateData);

                    ## Send Admin Notification :
                    AdminCommonActionModel::sendAdminNotification($member->id, $member->matri_id, 'horoscope_upload');
                    break;
            }

            auth()->guard('api')->setUser($member->fresh());

            return ApiResponseService::success(_getLangApi($request, 'msg_profile_update_successfully'));
        } catch (Throwable $e) {

            Log::error('Edit profile API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function removePhoto(Request $request): JsonResponse
    {
        try {

            $memberId = auth()->guard('api')->id();

            $validator = Validator::make($request->all(), [
                'photo_key' => 'required|in:photo2,photo3,photo4'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $member = Register::findOrFail($memberId);

            $photoKey = $request->photo_key;

            if (blank($member->$photoKey)) {
                return ApiResponseService::error(__('messages.msg_photo_not_found'));
            }

            // Delete original photo
            $path = _getConstant('upload_path.MEMBER_PHOTOS_URL');
            // Delete blur photo if exists
            $blurPath = _getConstant('upload_path.MEMBER_BLUR_PHOTOS_URL');
            UploadHelper::deleteFile($path, $member->$photoKey, 'public', $blurPath);

            $member->update([
                $photoKey => null,
                $photoKey . '_status' => 'UNAPPROVED',
                $photoKey . '_uploaded_on' => null,
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_photo_removed_successfully'));
        } catch (Throwable $e) {

            Log::error('Remove photos API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function removeIdProof(Request $request): JsonResponse
    {
        try {
            $memberId = auth()->guard('api')->id();

            $validator = Validator::make($request->all(), [
                'id_proof_key' => 'required|in:id_proof_front,id_proof_back',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $member = Register::findOrFail($memberId);

            $key = $request->id_proof_key;

            $path = _getConstant('upload_path.MEMBER_IDPROOF_URL');

            // Delete selected ID proof file
            UploadHelper::deleteFile($path, $member->$key);

            // Clear selected proof
            $member->$key = null;

            // Check whether the other ID proof still exists
            $otherKey = $key === 'id_proof_front'
                ? 'id_proof_back'
                : 'id_proof_front';

            $hasOtherProof = !blank($member->$otherKey);

            $member->id_proof_status = 'UNAPPROVED';

            // Only clear uploaded date if BOTH proofs are removed
            if (!$hasOtherProof) {
                $member->id_proof_uploaded_on = null;
            }

            $member->save();

            return ApiResponseService::success(
                _getLangApi($request, 'msg_id_proof_removed_successfully')
            );
        } catch (Throwable $e) {
            Log::error('Remove id proof API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    public function removeHoroscope(Request $request): JsonResponse
    {
        try {
            $memberId = auth()->guard('api')->id();

            $member = Register::findOrFail($memberId);

            if (blank($member->horoscope_file)) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_file_not_found')
                );
            }

            $path = _getConstant('upload_path.MEMBER_HOROSCOPE_URL');

            UploadHelper::deleteFile($path, $member->horoscope_file);

            $member->update([
                'horoscope_file' => null,
                'horoscope_status' => 'UNAPPROVED',
                'horoscope_uploaded_on' => null,
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_horoscope_removed_successfully'));
        } catch (Throwable $e) {

            Log::error('Remove horoscope API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function generateAiAboutMe(Request $request, AiGenerateService $aiService): JsonResponse
    {
        try {
            $member = auth()->guard('api')->user();

            $aboutMe = $aiService->generate('about_me', $member);

            if (!$aboutMe) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_about_me_generate_with_ai_failed_message')
                );
            }

            return ApiResponseService::success(
                _getLangApi($request, 'msg_about_me_generate_with_ai_success_message'),
                ['about_me' => $aboutMe]
            );
        } catch (Throwable $e) {

            Log::error('Edit Profile Ai about me API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function deleteRequest(Request $request): JsonResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|min:10|max:500',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            // Prevent duplicate pending request
            $alreadyPending = MemberDeleteProfile::where('sender', $memberId)
                ->where('admin_action_status', 0)
                ->exists();

            if ($alreadyPending) {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_you_already_have_a_pending_delete_request')
                );
            }

            MemberDeleteProfile::create([
                'sender'  => $memberId,
                'reason'  => $request->reason,
                'sent_on' => _getCurrentDate(),
            ]);

            ## Send Admin Notification :
            AdminCommonActionModel::sendAdminNotification($authUser->id, $authUser->matri_id, 'photo_upload');

            return ApiResponseService::success(_getLangApi($request, 'msg_your_delete_profile_request_has_been_sent_to_admin'));
        } catch (Throwable $e) {

            Log::error('delete request API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function sendConfirmationEmail(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\Register|null $authUser */
            $authUser = auth()->guard('api')->user();

            if ($authUser->email_verify_status == 'Verify') {
                return ApiResponseService::error(
                    _getLangApi($request, 'msg_your_email_is_already_verified')
                );
            }

            ## Send Confirmation Email :
            $plainToken = Str::random(64);
            $authUser->update([
                'email_verification_token' => hash('sha256', $plainToken), // store hashed
                'email_verification_token_expires_at' => Carbon::now()->addDays(3),
            ]);
            $confirmLink = route('web.confirm.email', ['token' => $plainToken]);

            $replaceArr = [
                'user_name'  => $authUser->fullname,
                'user_matri_id' => $authUser->matri_id,
                'user_email' => $authUser->email,
                'confirmation_url' => $confirmLink
            ];
            app(EmailSendService::class)->send('Email Confirmation', $authUser->email, $replaceArr, ['memberData' => $authUser]);

            return ApiResponseService::success(_getLangApi($request, 'msg_verification_email_sent_successfully'));
        } catch (Throwable $e) {

            Log::error('Send confirmation email API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function updateLatLog(Request $request): JsonResponse
    {
        try {
            $memberId = auth()->guard('api')->id();

            $validator = Validator::make($request->all(), [
                'latitude'          => 'sometimes|nullable',
                'longitude'         => 'sometimes|nullable',
                'android_device_id' => 'sometimes|nullable|string',
                'ios_device_id'     => 'sometimes|nullable|string'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $member = Register::findOrFail($memberId);

            $updateData = [];
            // Update latitude and longitude only if provided
            if ($request->filled('latitude')) {
                $updateData['latitude'] = $request->latitude;
            }
            if ($request->filled('longitude')) {
                $updateData['longitude'] = $request->longitude;
            }
            // Update device details according to user agent
            if ($request->filled('android_device_id')) {
                $updateData['android_device_id'] = $request->android_device_id ?? '';
                $updateData['ios_device_id']     = '';
                $updateData['web_device_id']     = '';
            } elseif ($request->filled('ios_device_id')) {
                $updateData['ios_device_id']     = $request->ios_device_id ?? '';
                $updateData['android_device_id'] = '';
                $updateData['web_device_id']     = '';
            }

            if (!empty($updateData)) {
                $member->update($updateData);
            }
            return ApiResponseService::success(
                _getLangApi($request, 'msg_profile_update_successfully')
            );
        } catch (Throwable $e) {
            Log::error('Lat & Long Update API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AffiliateMember;
use App\Models\Franchise;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Services\AdminCommonActionModel;
use App\Services\AiGenerateService;
use App\Services\Api\ApiResponseService;
use App\Services\EmailSendService;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            ## Normalize Request Data :
            $request->merge([
                'fullname' => trim((string) $request->fullname),
                'email'    => strtolower(trim((string) $request->email)),
                'mobile'   => trim((string) $request->mobile),
                'country_code' => trim((string) $request->country_code),
            ]);

            ## Validation :
            $validator = Validator::make(
                $request->all(),
                [
                    'gender' => ['required', 'in:Male,Female'],
                    'fullname' => ['required', 'string', 'min:3', 'max:150', "regex:/^[a-zA-Z\s'.-]+$/"],
                    'country_code' => [
                        'required',
                        'string',
                        Rule::exists('country_master', 'country_code')->where('status', 'APPROVED'),
                    ],
                    'mobile' => [
                        'required',
                        'digits_between:8,15',
                        function ($attribute, $value, $fail) use ($request) {
                            $fullMobile = $request->country_code . '-' . $value;
                            if (Register::where('mobile', $fullMobile)->exists()) {
                                $fail(__('messages.msg_mobile_number_already_registered'));
                            }
                        },
                    ],
                    'email' => ['required', 'email:rfc,dns', 'unique:registers,email'],
                    'password' => ['required', 'string', 'min:8'],
                    'birthdate' => [
                        'required',
                        'date_format:Y-m-d',
                        'before_or_equal:' . Carbon::now()->subYears(18)->toDateString(),
                    ],
                    'profileby' => 'required',
                    'marital_status' => 'required',
                    'religion' => 'required',
                    'caste' => 'required'
                ],
                [
                    'fullname.regex' => __('messages.msg_full_name_only_letters'),
                    'birthdate.before_or_equal' => __('messages.msg_you_must_be_at_least_18_years_old'),
                    'birthdate.date_format' => __('messages.msg_invalid_birthdate'),
                ]
            );

            ## Validation Response :
            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            ## Create Registration :
            $register = DB::transaction(function () use ($request) {

                ## Verify referrals from DB :
                $franchisedBy = 0;
                $franchiseAssignId = 0;
                $affiliateMemberId = 0;
                if ($request->filled('franchised_by')) {
                    $franchise = Franchise::active()->where('id', $request->franchised_by)->first();
                    if ($franchise) {
                        $franchisedBy = $franchise->id;
                        $franchiseAssignId = $franchise->id;
                    }
                }
                if ($request->filled('affiliate_member_id')) {
                    $affiliate = AffiliateMember::active()->where('id', $request->affiliate_member_id)->first();
                    if ($affiliate) {
                        $affiliateMemberId = $affiliate->id;
                    }
                }

                $fullMobile = $request->country_code . '-' . $request->mobile;

                $register = Register::create([
                    'fullname' => $request->fullname,
                    'email' => $request->email,
                    'mobile' => $fullMobile,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                    'birthdate' => $request->birthdate,
                    'profileby' => $request->profileby,
                    'marital_status' => $request->marital_status,
                    'religion' => $request->religion,
                    'caste' => $request->caste,
                    'total_children' => $request->total_children,
                    'status_children' => $request->status_children,

                    'registered_from' => 'Android',

                    'franchised_by' => $franchisedBy,
                    'franchise_assign_id' => $franchiseAssignId,
                    'franchise_assign_date' => $franchiseAssignId ? _getCurrentDate() : null,
                    'affiliate_member_id' => $affiliateMemberId,

                    'ip' => $request->ip(),
                    'agent' => $request->userAgent(),
                ]);

                ## Generate Matri ID :
                $siteSetting = _getSiteSetting();
                $register->update([
                    'matri_id' => ($siteSetting['matri_prefix'] ?? '') . $register->id,
                ]);

                ## Create Partner Preference :
                RegisterPartner::create([
                    'member_id' => $register->id,
                ]);
                ## Send Email / SMS After Successful Commit :
                DB::afterCommit(function () use ($register) {
                    try {
                        $this->sendEmailSMS($register);
                    } catch (Throwable $e) {
                        Log::error('Registration Email/SMS Failed', [
                            'member_id' => $register->id,
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ]);
                    }
                });

                return $register;
            });

            return ApiResponseService::success(_getLangApi($request, 'msg_your_profile_successfully_registered'), ['member_id' => $register->id]);
        } catch (Throwable $e) {

            Log::error('Register submit first basic step API failed.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ApiResponseService::error(
                _getLangApi(
                    $request,
                    'msg_something_went_wrong'
                )
            );
        }
    }

    public function submitSteps(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'step'      => 'required|integer|min:1|max:9',
            'member_id' => 'required|exists:registers,id',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        DB::beginTransaction();
        try {
            $step = (int)$request->step;
            $register = Register::find($request->member_id);
            switch ($step) {
                case 1:
                    $validator = Validator::make($request->all(), [
                        'mother_tongue' => 'required',
                    ]);
                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $register->update([
                        'register_step'   => 1,

                        'mother_tongue' => $request->mother_tongue,
                        'subcaste' => $request->subcaste,
                        'manglik' => $request->manglik,
                        'gothra' => $request->gothra,
                        'moonsign' => $request->moonsign,
                        'star' => $request->star,
                        'horoscope' => $request->horoscope,
                        'birthplace' => $request->birthplace,
                        'birthtime' => $request->birthtime
                    ]);
                    break;
                case 2:
                    $validator = Validator::make($request->all(), [
                        'country_id' => ['required', 'integer', 'exists:country_master,id'],
                        'state_id' => ['required', 'integer', 'exists:state_master,id'],
                        'city' => ['required', 'string', 'max:150'],
                    ]);
                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $register->update([
                        'register_step' => 2,
                        'country_id' => $request->country_id,
                        'state_id' => $request->state_id,
                        'city' => $request->city,
                        'alternate_number' => $request->alternate_number,
                        'address' => $request->address,
                        'nri_country' => $request->nri_country,
                        'residence_type' => $request->residence_type
                    ]);
                    break;
                case 3:
                    $validator = Validator::make($request->all(), [
                        'education_level' => ['required', 'exists:education_master,id'],
                        'occupation' => ['required', 'exists:occupation_master,id'],
                        'employee_in' => ['required', 'exists:employee_master,id'],
                        'income' => ['required', 'exists:annual_income_master,id'],
                        'designation_level' => ['required', 'exists:designation_master,id'],
                    ]);
                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $register->update([
                        'register_step' => 3,
                        'education_level' => $request->education_level,
                        'education_details' => $request->education_details,
                        'occupation' => $request->occupation,
                        'employee_in' => $request->employee_in,
                        'income' => $request->income,
                        'designation_level' => $request->designation_level
                    ]);
                    break;
                case 4:
                    $validator = Validator::make($request->all(), [
                        'height' => 'required',
                        'weight' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $register->update([
                        'register_step' => 4,
                        'height' => $request->height,
                        'weight' => $request->weight,
                        'diet' => $request->diet,
                        'smoke' => $request->smoke,
                        'drink' => $request->drink,
                        'complexion' => $request->complexion,
                        'body_type' => $request->body_type,
                        'blood_group_id' => $request->blood_group_id,
                        'about_me_description' => $request->about_me_description,
                    ]);
                    break;
                case 5:
                    $validator = Validator::make($request->all(), [
                        'father_name' => 'required',
                        'father_occupation' => 'required',
                        'mother_name' => 'required',
                        'mother_occupation' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }
                    $register->update([
                        'register_step' => 5,
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
                case 6:
                    $imageRule = 'image|mimes:jpeg,jpg,png,webp,heic,heif|max:5120';
                    $validator = Validator::make($request->all(), [
                        'selfie_photo' => $register->selfie_photo ? "nullable|{$imageRule}" : "required|{$imageRule}",
                        'photo1' => $register->photo1 ? "nullable|{$imageRule}" : "required|{$imageRule}",
                        'photo2' => "nullable|{$imageRule}",
                        'photo3' => "nullable|{$imageRule}",
                        'photo4' => "nullable|{$imageRule}",
                        'photo_visibility' => "required",
                    ]);

                    if ($validator->fails()) {
                        return ApiResponseService::validationError($validator);
                    }

                    $updateData = [];
                    $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
                    foreach ($photos as $photo) {
                        if ($request->hasFile($photo)) {
                            $filename = UploadHelper::uploadFile(
                                $request->file($photo),
                                _getConstant('upload_path.MEMBER_PHOTOS_URL'),
                                $register->$photo ?? '',
                                '',
                                1,
                                _getConstant('upload_path.MEMBER_BLUR_PHOTOS_URL')
                            );

                            if ($filename) {
                                $updateData[$photo] = $filename;
                                $updateData[$photo . '_status'] = 'UNAPPROVED';
                                $updateData[$photo . '_uploaded_on'] = now();
                            }
                        }
                    }

                    ## Selfie Photo:
                    if ($request->hasFile('selfie_photo')) {
                        $filename = UploadHelper::uploadFile(
                            $request->file('selfie_photo'),
                            _getConstant('upload_path.SELFIE_PHOTOS_URL'),
                            $register->selfie_photo ?? '',
                            '',
                            1
                        );

                        if ($filename) {
                            $updateData['selfie_photo'] = $filename;
                            $updateData['selfie_photo_status'] = 'UNAPPROVED';
                            $updateData['selfie_photo_uploaded_on'] = now();
                        }
                        ## Send Admin Notification :
                        AdminCommonActionModel::sendAdminNotification($register->id, $register->matri_id, 'selfie_upload');
                    }

                    $updateData['photo_visibility'] = $request->photo_visibility;
                    $updateData['register_step'] = 7;

                    $register->update($updateData);
                    break;
                case 7:
                    $rules = [
                        'id_proof_type' => 'required',
                    ];

                    if (!$register->id_proof_front) {
                        $rules['id_proof_front'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                    }

                    if (!$register->id_proof_back) {
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

                    $updateData = [];
                    $documents = [
                        'id_proof_front',
                        'id_proof_back'
                    ];

                    foreach ($documents as $document) {
                        if ($request->hasFile($document)) {
                            $filename = UploadHelper::uploadFile(
                                $request->file($document),
                                _getConstant('upload_path.MEMBER_IDPROOF_URL'),
                                $register->$document ?? ''
                            );
                            if ($filename) {
                                $updateData[$document] = $filename;
                            }
                        }
                    }

                    $updateData['id_proof_type'] = $request->id_proof_type;
                    $updateData['id_proof_status'] = 'UNAPPROVED';
                    $updateData['id_proof_uploaded_on'] = now();
                    $updateData['register_step'] = 8;

                    $register->update($updateData);
                    break;
                case 8:
                    RegisterPartner::updateOrCreate(
                        ['member_id' => $register->id],
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
                        ]
                    );
                    $register->update(['register_step' => 9]);
                    break;
                default:
                    return ApiResponseService::error('Invalid Step');
            }

            DB::commit();
            return ApiResponseService::success(
                _getLangApi($request, 'msg_profile_update_successfully')
            );
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('register step submit API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error($e->getMessage());
        }
    }


    public function generateAiAboutMe(Request $request, AiGenerateService $aiService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'member_id' => 'required|exists:registers,id',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $member = Register::findOrFail($request->member_id);

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
            // Log::error('AI About Me Error: ' . $e->getMessage());
            Log::error('register AI About Me API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_about_me_generate_with_ai_failed_message')
            );
        }
    }

    public function sendEmailSMS($register)
    {
        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('Registration', $register, []);

        ## Send Email :
        $replaceArr = [
            'user_name'  => $register->fullname,
            'user_matri_id' => $register->matri_id,
            'user_email' => $register->email,
        ];
        app(EmailSendService::class)->send('Registration', $register->email, $replaceArr, ['memberData' => $register]);

        ## Send Confirmation Email :
        $plainToken = Str::random(64);
        $register->update([
            'email_verification_token' => hash('sha256', $plainToken), // store hashed
            'email_verification_token_expires_at' => Carbon::now()->addDays(3),
        ]);
        $confirmLink = route('web.confirm.email', ['token' => $plainToken]);

        $replaceArr = [
            'user_name'  => $register->fullname,
            'user_matri_id' => $register->matri_id,
            'user_email' => $register->email,
            'confirmation_url' => $confirmLink
        ];
        app(EmailSendService::class)->send('Email Confirmation', $register->email, $replaceArr, ['memberData' => $register]);

        ## Send Admin Notification :
        AdminCommonActionModel::sendAdminNotification($register->id, $register->matri_id, 'new_registration');
    }
}

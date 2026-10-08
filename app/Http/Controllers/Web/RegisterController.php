<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\CaptchaHelper;
use App\Models\AnnualIncomeMaster;
use App\Models\Register;
use App\Models\CountryMaster;
use App\Models\DesignationMaster;
use App\Models\EducationMaster;
use App\Models\EmployeeMaster;
use App\Models\FamilyStatusMaster;
use App\Models\FamilyTypeMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MarriedBroMaster;
use App\Models\MarriedSisMaster;
use App\Models\MoonsignMaster;
use App\Models\MotherTongueMaster;
use App\Models\NoOfBroSisMaster;
use App\Models\OccupationMaster;
use App\Models\ProfileByMaster;
use App\Models\RegisterPartner;
use App\Models\ReligionMaster;
use App\Models\ResidenceMaster;
use App\Models\StatusChildMaster;
use App\Models\TotalChildMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\UploadHelper;
use App\Models\AffiliateMember;
use App\Models\AffiliateReferralClick;
use App\Models\BloodGroupMaster;
use App\Models\BodyTypeMaster;
use App\Models\ComplexionMaster;
use App\Models\CourseDetailMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\Franchise;
use App\Models\HoroscopeMaster;
use App\Models\SmokingHabitMaster;
use App\Models\StarMaster;
use App\Services\AdminCommonActionModel;
use App\Services\AiGenerateService;
use App\Services\EmailSendService;
use App\Services\SmsSendService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;
use Throwable;

class RegisterController extends Controller
{
    public function index($type = null, $code = null)
    {
        $currentLanguage = App::getLocale();
        $captchaCode = CaptchaHelper::generate('register_captcha');

        $minBirthdate = now()->subYears(80)->format('Y-m-d');
        $maxBirthdate = now()->subYears(18)->format('Y-m-d');

        // Clear session after success
        session()->forget('member_id');

        // Guard invalid URLs
        if (!in_array($type, ['franchise', 'affiliate']) || blank($code)) {
            $type = null;
            $code = null;
        }

        ## Refferal Code:
        $franchisedBy = null;
        $franchiseAssignId = null;
        $affiliateReferralId = null;
        if ($type && $code) {
            if ($type === 'franchise') {
                $franchise = Franchise::active()->where('referral_code', $code)->first();
                if ($franchise) {
                    $franchisedBy = $franchise->id;
                    $franchiseAssignId = $franchise->id;
                }
            }
            if ($type === 'affiliate') {
                $affiliate = AffiliateMember::active()->where('referral_code', $code)->first();
                if ($affiliate) {
                    $affiliateReferralId = $affiliate->id;
                    ## Visitor Clicks / Referral Analytics :
                    $agent = new Agent();
                    AffiliateReferralClick::create([
                        'affiliate_member_id' => $affiliate->id,
                        'referral_code' => $code,
                        'ip_address' => request()->ip(),
                        'browser' => $agent->browser(),
                        'device' => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
                        'referral_url' => url()->current(),
                        'clicked_at' => now(),
                    ]);
                }
            }
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.register.index', [
            'title' => 'Register',
            'captchaCode' => $captchaCode,
            'minBirthdate' => $minBirthdate,
            'maxBirthdate' => $maxBirthdate,
            'profileByList' => ProfileByMaster::getDropdown($currentLanguage),
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'totalChildrenList' => TotalChildMaster::getDropdown($currentLanguage),
            'statusChildrenList' => StatusChildMaster::getDropdown($currentLanguage),

            'franchisedBy' => $franchisedBy,
            'franchiseAssignId' => $franchiseAssignId,
            'affiliateReferralId' => $affiliateReferralId,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'fullname' => trim($request->fullname),
            'email' => strtolower(trim($request->email)),
            'mobile' => trim($request->mobile),
        ]);

        $validator = Validator::make($request->all(), [
            'gender' => 'required|in:Male,Female',
            'profileby' => 'required',
            'fullname' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[a-zA-Z\s\'.-]+$/'
            ],
            'country_code' => [
                'required',
                'string',
                Rule::exists('country_master', 'country_code')->where('status', 'APPROVED'),
            ],
            'mobile' => [
                'required',
                'digits_between:8,15',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($request) {
                    $fullMobile = trim($request->country_code) . '-' . trim($value);
                    if (Register::where('mobile', $fullMobile)->exists()) {
                        $fail(__('messages.msg_mobile_number_already_registered'));
                    }
                }
            ],
            'email' => 'required|email:rfc,dns|unique:registers,email',
            'password' => 'required|min:8|confirmed',
            'birthdate' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'),

            'marital_status' => ['required', 'integer', 'exists:marital_status_masters,id'],
            'religion' => ['required', 'integer', 'exists:religion_master,id'],
            'caste' => ['required', 'integer', 'exists:caste_master,id'],
            'terms' => 'accepted',

            //  secure hidden inputs
            'franchised_by' => 'nullable|integer',
            'affiliate_member_id' => 'nullable|integer',
            'captcha_code' => 'required|string'
        ], [
            'fullname.regex' => 'Full name should contain only letters.',
            'birthdate.before' => 'You must be at least 18 years old.',
            'terms.accepted' => 'Please accept terms & conditions.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        if (!CaptchaHelper::validate($request->captcha_code, 'register_captcha')) {
            return $this->captchaError();
        }

        return DB::transaction(function () use ($request) {

            $siteSetting = _getSiteSetting();

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

            ## Create Register :
            $fullMobile = trim($request->country_code) . '-' . trim($request->mobile);
            $register = Register::create([
                'fullname' => trim($request->fullname),
                'email' => strtolower(trim($request->email)),
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
                'registered_from' => 'Website',
                'ip' => $request->ip(),
                'agent' => $request->userAgent(),

                'franchised_by' => $franchisedBy,
                'franchise_assign_id' => $franchiseAssignId,
                'franchise_assign_date' => $franchiseAssignId ? _getCurrentDate() : null,
                'affiliate_member_id' => $affiliateMemberId,
            ]);

            $register->update([
                'matri_id' => $siteSetting['matri_prefix'] . $register->id
            ]);

            ## Add Partner Preference:
            RegisterPartner::updateOrCreate(
                ['member_id' => $register->id],
                ['member_id' => $register->id]
            );

            DB::afterCommit(function () use ($register) {
                try {
                    ## Send Registration Email & SmS:
                    $this->sendEmailSMS($register);
                } catch (Throwable $e) {
                    Log::error('Registration Email/SMS Failed', [
                        'member_id' => $register->id,
                        'error' => $e->getMessage()
                    ]);
                }
            });

            session()->put('member_id', $register->id);

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_registration_successful_please_complete_your_profile'),
                'redirect' => route('web.register.nextStep')
            ]);
        });
    }

    private function captchaError()
    {
        return response()->json([
            'status' => false,
            'message' => __('messages.msg_invalid_captcha'),
            'refresh_captcha' => true,
            'captchaCode' => CaptchaHelper::generate('register_captcha')
        ]);
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

    public function nextStep()
    {
        $memberId = session('member_id');
        if (!$memberId) {
            return redirect()->route('web.register.index');
        }
        $member = Register::with('partnerPreference')->where('id', $memberId)->first();

        $currentLanguage = App::getLocale();

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.register.registerStep.index', [
            'title' => 'Register',
            'member' => $member,

            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),

            'manglikList' => ManglikMaster::getDropdown($currentLanguage),
            'moongsignList' => MoonsignMaster::getDropdown($currentLanguage),
            'starList' => StarMaster::getDropdown($currentLanguage),
            'horoscopeList' => HoroscopeMaster::getDropdown($currentLanguage),

            'topCountries' => $topCountries,
            'allCountries' => $allCountries,
            'residenceList' => ResidenceMaster::getDropdown($currentLanguage),

            'educationList' => EducationMaster::getDropdown($currentLanguage),
            'occupationList' => OccupationMaster::getDropdown($currentLanguage),
            'employeeInList' => EmployeeMaster::getDropdown($currentLanguage),
            'incomeList' => AnnualIncomeMaster::getDropdown($currentLanguage),
            'designationList' => DesignationMaster::getDropdown($currentLanguage),

            'familyTypeList' => FamilyTypeMaster::getDropdown($currentLanguage),
            'familyStatusList' => FamilyStatusMaster::getDropdown($currentLanguage),
            'noOfBroSisList' => NoOfBroSisMaster::getDropdown($currentLanguage),
            'noOfMarriedBrotherList' => MarriedBroMaster::getDropdown($currentLanguage),
            'noOfMarriedSisterList' => MarriedSisMaster::getDropdown($currentLanguage),

            'eatingHabitList' => EatingHabitMaster::getDropdown($currentLanguage),
            'smokingHabitList' => SmokingHabitMaster::getDropdown($currentLanguage),
            'drinkingHabitList' => DrinkingHabitMaster::getDropdown($currentLanguage),
            'bodyTypeList' => BodyTypeMaster::getDropdown($currentLanguage),
            'complextionList' => ComplexionMaster::getDropdown($currentLanguage),
            'bloodGroupList' => BloodGroupMaster::getDropdown($currentLanguage),

            'idProofTypeList' => _getStaticArr('idProofTypeArr'),
            'courseList' => CourseDetailMaster::getDropdown($currentLanguage),
            'yearList' => _yearFormat(),
        ]);
    }

    public function submitSteps(Request $request)
    {
        $memberId = session('member_id');
        if (!$memberId) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_session_expired')
            ]);
        }

        $request->validate([
            'step' => 'required|integer|min:1|max:9'
        ]);

        $register = Register::findOrFail($memberId);
        $step = $request->step;

        switch ($step) {
            case 1:
                $request->validate([
                    'mother_tongue' => 'required',
                ], [
                    'mother_tongue.required' => 'Please select your mother tongue.',
                ]);
                $register->update([
                    'register_step' => $step,
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
                $request->validate(
                    [
                        'country_id' => ['required', 'integer', 'exists:country_master,id'],
                        'state_id' => ['required', 'integer', 'exists:state_master,id'],
                        'city' => ['required', 'string', 'max:150'],
                    ],
                    [
                        'country_id.required' => 'Please select country.',
                        'state_id.required' => 'Please select state.',
                        'city.required' => 'Please enter city.',
                    ]
                );
                $register->update([
                    'register_step' => $step,
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
                $request->validate([
                    'education_level' => ['required', 'exists:education_master,id'],
                    'occupation' => ['required', 'exists:occupation_master,id'],
                    'employee_in' => ['required', 'exists:employee_master,id'],
                    'income' => ['required', 'exists:annual_income_master,id'],
                    'designation_level' => ['required', 'exists:designation_master,id'],
                ], [
                    'education_level.required' => 'Please select education level.',
                    'occupation.required' => 'Please select occupation.',
                    'employee_in.required' => 'Please enter employee in.',
                    'income.required' => 'Please enter annual income.',
                    'designation_level.required' => 'Please enter designation.',
                ]);
                $register->update([
                    'register_step' => $step,
                    'education_level' => $request->education_level,
                    'education_details' => $request->education_details,
                    'occupation' => $request->occupation,
                    'employee_in' => $request->employee_in,
                    'income' => $request->income,
                    'designation_level' => $request->designation_level
                ]);
                break;
            case 4:
                $request->validate([
                    'height' => 'required',
                    'weight' => 'required'
                ], [
                    'height.required' => 'Please select your height.',
                    'weight.required' => 'Please enter your weight.'
                ]);
                $register->update([
                    'register_step' => $step,
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
            case 5:
                $request->validate([
                    'Yesart_of_living_teacher' => 'required',
                    'have_art_of_living_program' => 'required',
                ]);

                $register->update([
                    'register_step' => $step,
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
            case 6:
                $request->validate([
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

                $register->update([
                    'register_step' => $step,
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
            case 7:
                $imageRule = 'image|mimes:jpeg,jpg,png,webp,heic,heif|max:5120';
                $request->validate(
                    [
                        'selfie_photo' => $register->selfie_photo ? "nullable|{$imageRule}" : "required|{$imageRule}",
                        'photo1' => $register->photo1 ? "nullable|{$imageRule}" : "required|{$imageRule}",
                        'photo2' => "nullable|{$imageRule}",
                        'photo3' => "nullable|{$imageRule}",
                        'photo4' => "nullable|{$imageRule}",
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
                        'photo4' => __('messages.attr_photo4'),
                    ]
                );

                $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
                $updateData = [
                    'register_step' => $step,
                    'photo_visibility' => $request->photo_visibility
                ];
                foreach ($photos as $photo) {
                    if ($request->hasFile($photo)) {
                        $path = _getConstant('upload_path.MEMBER_PHOTOS_URL');
                        $blurPath = _getConstant('upload_path.MEMBER_BLUR_PHOTOS_URL');
                        $oldValue = $register->$photo ?? '';
                        $filename = UploadHelper::uploadFile($request->file($photo), $path, $oldValue, '', 1, $blurPath);
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
                $register->update($updateData);
                break;
            case 8:
                $rules = [
                    'id_proof_type' => 'required'
                ];
                if (!$register->id_proof_front) {
                    $rules['id_proof_front'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                }
                if (!$register->id_proof_back) {
                    $rules['id_proof_back'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                }
                $request->validate(
                    $rules,
                    [
                        'id_proof_type.required' => __('messages.msg_select_id_proof_type'),
                        'id_proof_front.required' => __('messages.msg_upload_id_proof_front'),
                        'id_proof_back.required' => __('messages.msg_upload_id_proof_back'),
                        'image' => __('messages.msg_valid_image'),
                        'mimes' => __('messages.msg_image_type'),
                        'max' => __('messages.msg_image_max_size_5mb'),
                    ],
                    [
                        'id_proof_type' => __('messages.attr_id_proof_type'),
                        'id_proof_front' => __('messages.attr_id_proof_front'),
                        'id_proof_back' => __('messages.attr_id_proof_back'),
                    ]
                );

                $idProof = ['id_proof_front', 'id_proof_back'];
                $updateData = [];
                foreach ($idProof as $id_type) {
                    if ($request->hasFile($id_type)) {
                        $path = _getConstant('upload_path.MEMBER_IDPROOF_URL');
                        $oldValue = $register->$id_type ?? '';
                        $filename = UploadHelper::uploadFile($request->file($id_type), $path, $oldValue);
                        if ($filename) {
                            $updateData[$id_type] = $filename;
                        }
                    }
                }
                $updateData['id_proof_type'] = $request->id_proof_type;
                $updateData['id_proof_status'] = 'UNAPPROVED';
                $updateData['id_proof_uploaded_on'] = now();
                $updateData['register_step'] = $step;
                $register->update($updateData);
                break;
            case 9:
                RegisterPartner::updateOrCreate(
                    ['member_id' => $memberId],
                    [
                        'part_frm_age'          => $request->part_frm_age,
                        'part_to_age'           => $request->part_to_age,

                        'part_height'           => $request->part_height,
                        'part_height_to'        => $request->part_height_to,

                        'part_religion'         => implode(',', (array) $request->part_religion),
                        'part_caste'            => implode(',', (array) $request->part_caste),

                        'part_country'          => implode(',', (array) $request->part_country),
                        'part_state'            => implode(',', (array) $request->part_state),

                        'part_marital_status'   => implode(',', (array) $request->part_marital_status),
                        'part_income'           => implode(',', (array) $request->part_income),
                        'part_education'        => implode(',', (array) $request->part_education),
                        'part_occupation'       => implode(',', (array) $request->part_occupation),
                        'part_mothertongue'     => implode(',', (array) $request->part_mothertongue),
                        'part_manglik'          => implode(',', (array) $request->part_manglik),
                        'part_art_of_living_teacher' => implode(',', (array) $request->part_art_of_living_teacher),
                        'part_have_art_of_living_program' => implode(',', (array) $request->part_have_art_of_living_program),
                    ]
                );

                $register->update([
                    'register_step' => $step,
                ]);

                break;
        }
        return response()->json([
            'status' => true,
            'message' => __('messages.msg_profile_update_successfully')
        ]);
    }

    public function successPage()
    {
        if (!session()->has('member_id')) {
            return redirect()->route('web.register.index');
        }

        // Clear session after success
        session()->forget('member_id');

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.register.registerStep.success', [
            'title' => 'Register Success'
        ]);
    }

    public function generateAiAboutMe(Request $request, AiGenerateService $aiService): JsonResponse
    {
        try {
            $memberId = session('member_id');
            if (!$memberId) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_session_expired')
                ]);
            }

            $member = Register::findOrFail($memberId);

            $aboutMe = $aiService->generate('about_me', $member);

            if (!$aboutMe) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_about_me_generate_with_ai_failed_message'),
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => $aboutMe,
                'message' => __('messages.msg_about_me_generate_with_ai_success_message'),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_about_me_generate_with_ai_failed_message'),
            ]);
        }
    }

    public function refreshCaptcha()
    {
        return response()->json([
            'success' => true,
            'captchaCode' => CaptchaHelper::generate('register_captcha')
        ]);
    }
}

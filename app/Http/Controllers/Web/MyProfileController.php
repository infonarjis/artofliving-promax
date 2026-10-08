<?php

namespace App\Http\Controllers\Web;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AnnualIncomeMaster;
use App\Models\BloodGroupMaster;
use App\Models\BodyTypeMaster;
use App\Models\CasteMaster;
use App\Models\ComplexionMaster;
use App\Models\CountryMaster;
use App\Models\CourseDetailMaster;
use App\Models\DesignationMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\EducationMaster;
use App\Models\EmployeeMaster;
use App\Models\FamilyStatusMaster;
use App\Models\FamilyTypeMaster;
use App\Models\HoroscopeMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MarriedBroMaster;
use App\Models\MarriedSisMaster;
use App\Models\MoonsignMaster;
use App\Models\MotherTongueMaster;
use App\Models\NoOfBroSisMaster;
use App\Models\OccupationMaster;
use App\Models\ProfileByMaster;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Models\ReligionMaster;
use App\Models\ResidenceMaster;
use App\Models\SmokingHabitMaster;
use App\Models\StarMaster;
use App\Models\StateMaster;
use App\Models\StatusChildMaster;
use App\Models\TotalChildMaster;
use App\Services\AdminCommonActionModel;
use App\Services\MyProfileSectionService;
use App\Services\AiGenerateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Throwable;

class MyProfileController extends Controller
{
    public function index()
    {
        $memberId = auth()->guard('web')->id();
        $currentLanguage = App::getLocale();

        $member = Register::where('id', $memberId)->first();
        $registerPartner = $member->partnerPreference;

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.myProfile.index', [
            'memberDetailSections' => MyProfileSectionService::getSections($member, $currentLanguage),
            'memberPartnerSection' => MyProfileSectionService::partnerSection($registerPartner,$currentLanguage)
        ]);
    }

    public function editProfile($section)
    {
        $memberId = auth()->guard('web')->id();
        $currentLanguage = App::getLocale();
        $member = Register::where('id', $memberId)->first();

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.myProfile.editProfile.index', [
            'section' => $section,
            'member' => $member,
            'profileByList' => ProfileByMaster::getDropdown($currentLanguage),
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
            'motherTongueList' => MotherTongueMaster::getDropdown($currentLanguage),
            'maritalStatusList' => MaritalStatusMaster::getDropdown($currentLanguage),
            'totalChildrenList' => TotalChildMaster::getDropdown($currentLanguage),
            'statusChildrenList' => StatusChildMaster::getDropdown($currentLanguage),

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

    public function updateProfile(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        $request->validate([
            'form_type' => 'required'
        ]);

        $member = Register::findOrFail($memberId);

        $form_type = $request->form_type;
        switch ($form_type) {
            case 'basic_details':
                $request->validate([
                    'mother_tongue' => 'required',
                    'marital_status' => 'required',
                    'profileby' => 'required'
                ], [
                    'mother_tongue.required' => 'Please select mother tongue.',
                    'marital_status.required' => 'Please select marital status.',
                    'profileby.required' => 'Please select profile by.',
                ]);
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
                $request->validate([
                    'religion' => 'required',
                    'caste' => 'required'
                ], [
                    'religion.required' => 'Please select religion.',
                    'caste.required' => 'Please enter caste.'
                ]);
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
                $request->validate(
                    [
                        'country_id' => 'required',
                        'state_id' => 'required',
                        'city' => 'required',
                    ],
                    [
                        'country_id.required' => 'Please select country.',
                        'state_id.required' => 'Please select state.',
                        'city.required' => 'Please enter city.',
                    ]
                );
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
                $request->validate([
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
                $request->validate([
                    'height' => 'required',
                    'weight' => 'required',
                ], [
                    'height.required' => 'Please select your height.',
                    'weight.required' => 'Please enter your weight.',
                ]);
                $member->update([
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
            case 'art_of_living_association':
                    $request->validate([
                        'Yesart_of_living_teacher' => 'required',
                        'have_art_of_living_program' => 'required',
                    ]);
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
                        'part_frm_age'        => $request->part_frm_age,
                        'part_to_age'         => $request->part_to_age,

                        'part_height'         => $request->part_height,
                        'part_height_to'      => $request->part_height_to,

                        'part_religion'       => implode(',', (array) $request->part_religion),
                        'part_caste'          => implode(',', (array) $request->part_caste),

                        'part_country'        => implode(',', (array) $request->part_country),
                        'part_state'          => implode(',', (array) $request->part_state),

                        'part_marital_status' => implode(',', (array) $request->part_marital_status),
                        'part_income'         => implode(',', (array) $request->part_income),
                        'part_education'      => implode(',', (array) $request->part_education),
                        'part_occupation'     => implode(',', (array) $request->part_occupation),
                        'part_mothertongue'   => implode(',', (array) $request->part_mothertongue),
                        'part_manglik'        => implode(',', (array) $request->part_manglik),
                        'part_art_of_living_teacher' => implode(',', (array) $request->part_art_of_living_teacher),
                        'part_have_art_of_living_program' => implode(',', (array) $request->part_have_art_of_living_program),
                    ]
                );
                break;
            case 'upload_photos':
                $imageRule = 'image|mimes:jpeg,jpg,png,webp,heic,heif|max:5120';
                $request->validate(
                    [
                        'photo1' => $member->photo1 ? "nullable|{$imageRule}" : "required|{$imageRule}",
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
                    'photo_visibility' => $request->photo_visibility
                ];
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
                    'id_proof_type' => 'required'
                ];
                if (!$member->id_proof_front) {
                    $rules['id_proof_front'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
                }
                if (!$member->id_proof_back) {
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

        auth()->guard('web')->setUser($member->fresh());

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_profile_update_successfully')
        ]);
    }

    public function removePhoto(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        $request->validate([
            'photo_key' => 'required|in:photo2,photo3,photo4'
        ]);

        $member = Register::findOrFail($memberId);

        $photoKey = $request->photo_key;

        if (blank($member->$photoKey)) {
            return response()->json([
                'status' => true,
                'message' => __('messages.msg_photo_removed_successfully')
            ]);
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

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_photo_removed_successfully')
        ]);
    }

    public function removeIdProof(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        $request->validate([
            'proof_key' => 'required|in:id_proof_front,id_proof_back',
        ]);

        $member = Register::findOrFail($memberId);

        $key = $request->proof_key;

        if (blank($member->$key)) {
            // Because Before Saving Functionality same flow
            return response()->json([
                'status' => true,
                'message' => __('messages.msg_id_proof_removed_successfully'),
            ]);
        }

        $path = _getConstant('upload_path.MEMBER_IDPROOF_URL');

        // Delete selected ID proof
        UploadHelper::deleteFile($path, $member->$key);

        // Remove selected proof
        $member->$key = null;

        // Check the other ID proof
        $otherKey = $key === 'id_proof_front'
            ? 'id_proof_back'
            : 'id_proof_front';

        // Always mark as unapproved after removing a proof
        $member->id_proof_status = 'UNAPPROVED';

        // Only remove uploaded date when both proofs are deleted
        if (blank($member->$otherKey)) {
            $member->id_proof_uploaded_on = null;
        }

        $member->save();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_id_proof_removed_successfully'),
        ]);
    }

    public function removeHoroscope(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        $member = Register::findOrFail($memberId);

        if (blank($member->horoscope_file)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_file_not_found'),
            ]);
        }

        $path = _getConstant('upload_path.MEMBER_HOROSCOPE_URL');

        UploadHelper::deleteFile($path, $member->horoscope_file);

        $member->update([
            'horoscope_file' => null,
            'horoscope_status' => 'UNAPPROVED',
            'horoscope_uploaded_on' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_horoscope_removed_successfully'),
        ]);
    }

    public function generateAiAboutMe(Request $request, AiGenerateService $aiService): JsonResponse
    {
        try {
            $member = auth()->guard('web')->user();

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
    
    public function downloadBiodataPdf($memberId)
    {
        $memberData = Register::where('id', $memberId)->first();
        $memberData->birthdate = $memberData->birthdate ? Carbon::parse($memberData->birthdate)->format('d-m-Y') : null;

        $dataArr = [
            'resultArr' => $memberData,
            'dataStep1' => [
                'Basic Details' => [
                    'Full Name' => $memberData->fullname,
                    'Gender' => $memberData->gender,
                    'Date of birth' => $memberData->birthdate ? Carbon::parse($memberData->birthdate)->format('d-m-Y') : null,
                    'Marital Status' => $memberData->maritalStatusData->marital_status_name ?? null,
                    'Mother Tongue' => $memberData->motherTongueData->mtongue_name ?? null,
                ],
            ],
            'dataStep2' => [
                'Religious Information' => [
                    'Religion' => $memberData->religionData->religion_name ?? null,
                    'Caste' => $memberData->casteData->caste_name ?? null,
                    'Sub Caste' => $memberData->subcaste ?? null,
                    'Manglik' => $memberData->manglikData->manglik_name ?? null,
                    'Gothra' => $memberData->gothra ?? null,
                    'Moonsign' => $memberData->moonsignData->moonsign_name ?? null,
                    'Star' => $memberData->starData->star_name ?? null
                ],
                'Education & Other Details' => [
                    'Education' => implode(', ', $memberData->education_level_names) ?? null,
                    'Education Details' => $memberData->education_details ?? null,
                    'Occupation' => $memberData->occupationData->occupation_name ?? null,
                    'Annual Income' => $memberData->incomeData->annual_income_name ?? null,
                ],
                'Location Details' => [
                    'Country' => $memberData->countryData->country_name ?? null,
                    'State' => $memberData->stateData->state_name ?? null,
                    'City' => $memberData->cityData->city_name ?? null,
                ],
                'Physical Information' => [
                    'Height' => _displayHeight($memberData->height) ?? null,
                    'Weight' => $memberData->weight ? $memberData->weight . ' Kg' : null,
                    'Eating Habits' => $memberData->dietData->eating_habit_name ?? null,
                    'Smoking Habit' => $memberData->smokeData->smoking_habit_name ?? null,
                    'Drinking Habit' => $memberData->drinkingData->drinking_habit_name ?? null,
                ],
            ]
        ];

        $viewPath = _getConstant('dir_path.ADMIN_DIR_PATH') . '.member/memberPdf';
        $pdf = Pdf::loadView($viewPath, compact('dataArr'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => storage_path(),
                'defaultFont' => 'Poppins',
                'isHtml5ParserEnabled' => true,
            ]);

        $fileName = $memberData->matri_id . '-biodata';
        return $pdf->download($fileName . '.pdf');
    }
}

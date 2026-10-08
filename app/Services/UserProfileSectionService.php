<?php

namespace App\Services;

use App\Models\AnnualIncomeMaster;
use App\Models\CasteMaster;
use App\Models\CountryMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\EducationMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MotherTongueMaster;
use App\Models\OccupationMaster;
use App\Models\ReligionMaster;
use App\Models\SmokingHabitMaster;
use App\Models\StateMaster;
use stdClass;

class UserProfileSectionService
{
    public static function getSections($member): array
    {
        return [
            self::basicDetails($member),
            self::religiousInformation($member),
            self::locationDetails($member),
            self::educationCareer($member),
            self::physicalInformation($member),
            self::familyDetails($member),
        ];
    }

    // Private Section Methods
    private static function basicDetails($member): array
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            $member->mobile = _getConstant('DISABLE_IN_DEMO_LABEL');
            $member->email = _getConstant('DISABLE_IN_DEMO_LABEL');
        }

        $fields = [
            [
                'key'   => 'matri_id',
                'label' => __('messages.field_lbl_matri_id'),
                'selected_ids'  => $member->matri_id ?? null,
                'value' => $member->matri_id,
            ],
            [
                'key'   => 'profileby',
                'label' => __('messages.field_lbl_profile_by'),
                'selected_ids'  => $member->profileby ?? null,
                'value' => $member->profileByData?->translated_name ?? null,
            ],
            [
                'key'   => 'mother_tongue',
                'label' => __('messages.field_lbl_mother_tongue'),
                'selected_ids'  => $member->mother_tongue ?? null,
                'value' => $member->motherTongueData?->translated_name ?? null,
            ],
            [
                'key'   => 'marital_status',
                'label' => __('messages.field_lbl_marital_status'),
                'selected_ids'  => $member->marital_status ?? null,
                'value' => $member->maritalStatusData?->translated_name ?? null,
            ],
            [
                'key'   => 'birthplace',
                'label' => __('messages.field_lbl_birth_place'),
                'selected_ids'  => $member->birthplace ?? null,
                'value' => $member->birthplace,
            ],
            [
                'key'   => 'birthtime',
                'label' => __('messages.field_lbl_birth_time'),
                'selected_ids'  => $member->birthtime ?? null,
                'value' => _birthtimeDisplay($member->birthtime),
            ],
        ];

        // Conditional fields
        if (($member->maritalStatusData->marital_status_name ?? null) !== 'Unmarried') {
            $fields[] = [
                'key'   => 'total_children',
                'label' => __('messages.field_lbl_total_children'),
                'selected_ids'  => $member->total_children ?? null,
                'value' => $member->totalChildrenData?->translated_name ?? null,
            ];

            $fields[] = [
                'key'   => 'status_children',
                'label' => __('messages.field_lbl_status_children'),
                'selected_ids'  => $member->status_children ?? null,
                'value' => $member->statusChildrenData?->translated_name ?? null,
            ];
        }

        foreach ($fields as $key => $value) {
            if ($value['key'] == 'birthplace') {
                if (!_checkFieldEnable('birthplace', 'user_profile_list')) {
                    unset($fields[$key]);
                }
            }
            if ($value['key'] == 'birthtime') {
                if (!_checkFieldEnable('birthtime', 'user_profile_list')) {
                    unset($fields[$key]);
                }
            }
        }

        return [
            'section_key' => 'basic_details',
            'label'       => __('messages.lbl_basic_details'),
            'fields'      => $fields,
        ];
    }

    private static function religiousInformation($member): array
    {
        $fields = [
            [
                'key'   => 'religion',
                'label' => __('messages.field_lbl_religion'),
                'selected_ids'  => $member->religion ?? null,
                'value' => $member->religionData?->translated_name ?? null,
            ],
            [
                'key'   => 'caste',
                'label' => __('messages.field_lbl_caste'),
                'selected_ids'  => $member->caste ?? null,
                'value' => $member->casteData?->translated_name ?? null,
            ],
            [
                'key'   => 'subcaste',
                'label' => __('messages.field_lbl_sub_caste'),
                'selected_ids'  => $member->subcaste ?? null,
                'value' => $member->subcaste ?? null,
            ],
            [
                'key'   => 'moonsign',
                'label' => __('messages.field_lbl_moonsing'),
                'selected_ids'  => $member->moonsign ?? null,
                'value' => $member->moonsignData?->translated_name ?? null,
            ],
            [
                'key'   => 'manglik',
                'label' => __('messages.field_lbl_manglik'),
                'selected_ids'  => $member->manglik ?? null,
                'value' => $member->manglikData?->translated_name ?? null,
            ],
            [
                'key'   => 'star',
                'label' => __('messages.field_lbl_star'),
                'selected_ids'  => $member->star ?? null,
                'value' => $member->starData?->translated_name ?? null,
            ],
            [
                'key'   => 'gothra',
                'label' => __('messages.field_lbl_gothra'),
                'selected_ids'  => $member->gothra ?? null,
                'value' => $member->gothra ?? null,
            ],
            [
                'key'   => 'horoscope',
                'label' => __('messages.field_horoscope'),
                'selected_ids'  => $member->horoscope ?? null,
                'value' => $member->horoscopeData?->translated_name ?? null,
            ],
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'religious_information',
            'label'       => __('messages.lbl_religious_information'),
            'fields'      => $fields,
        ];
    }

    private static function locationDetails($member): array
    {
        $fields = [
            [
                'key'   => 'country_id',
                'label' => __('messages.field_lbl_country'),
                'selected_ids'  => $member->country_id ?? null,
                'value' => $member->countryData?->translated_name ?? null,
            ],
            [
                'key'   => 'state_id',
                'label' => __('messages.field_lbl_state'),
                'selected_ids'  => $member->state_id ?? null,
                'value' => $member->stateData?->translated_name ?? null,
            ],
            [
                'key'   => 'city',
                'label' => __('messages.field_lbl_city'),
                'selected_ids'  => $member->city ?? null,
                'value' => $member->cityData?->translated_name ?? null,
            ],
            [
                'key'   => 'residence_type',
                'label' => __('messages.field_lbl_residence_type'),
                'selected_ids'  => $member->residence_type ?? null,
                'value' => $member->residenceTypeData?->translated_name ?? null,
            ],
            [
                'key'   => 'nri_country',
                'label' => __('messages.field_lbl_nri_originated_country'),
                'selected_ids'  => $member->nri_country ?? null,
                'value' => $member->nri_country ?? null,
            ],
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'location_details',
            'label'       => __('messages.lbl_location_details'),
            'fields'      => $fields,
        ];
    }

    private static function educationCareer($member): array
    {

        $fields = [
            [
                'key'   => 'education_level',
                'label' => __('messages.field_lbl_education'),
                'selected_ids'  => $member->education_level ?? null,
                'value' => implode(', ', $member->education_level_names) ?? null,
            ],
            [
                'key'   => 'education_details',
                'label' => __('messages.field_lbl_education_details'),
                'selected_ids'  => $member->education_details ?? null,
                'value' => $member->education_details ?? null,
            ],
            [
                'key'   => 'occupation',
                'label' => __('messages.field_lbl_occupation'),
                'selected_ids'  => $member->occupation ?? null,
                'value' => $member->occupationData?->translated_name ?? null,
            ],
            [
                'key'   => 'employee_in',
                'label' => __('messages.field_lbl_employee_in'),
                'selected_ids'  => $member->employee_in ?? null,
                'value' => $member->employeeInData?->translated_name ?? null,
            ],
            [
                'key'   => 'income',
                'label' => __('messages.field_lbl_annual_income'),
                'selected_ids'  => $member->income ?? null,
                'value' => $member->incomeData?->translated_name ?? null,
            ],
            [
                'key'   => 'designation_level',
                'label' => __('messages.field_lbl_designation'),
                'selected_ids'  => $member->designation_level ?? null,
                'value' => $member->designationLevelData?->translated_name ?? null,
            ],
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'education_other',
            'label'       => __('messages.lbl_education_other_details'),
            'fields'      => $fields
        ];
    }

    private static function physicalInformation($member): array
    {
        $fields = [
            [
                'key'   => 'height',
                'label' => __('messages.field_lbl_height'),
                'selected_ids'  => $member->height ?? null,
                'value' => _displayHeight($member->height) ?? null,
            ],
            [
                'key'   => 'weight',
                'label' => __('messages.field_lbl_weight'),
                'selected_ids'  => $member->weight ?? null,
                'value' => $member->weight ? $member->weight . ' ' . __('messages.lbl_kg') : null,
            ],
            [
                'key'   => 'diet',
                'label' => __('messages.field_lbl_eating_habits'),
                'selected_ids'  => $member->diet ?? null,
                'value' => $member->dietData?->translated_name ?? null,
            ],
            [
                'key'   => 'smoke',
                'label' => __('messages.field_lbl_smoking'),
                'selected_ids'  => $member->smoke ?? null,
                'value' => $member->smokeData?->translated_name ?? null,
            ],
            [
                'key'   => 'drink',
                'label' => __('messages.field_lbl_drinking'),
                'selected_ids'  => $member->drink ?? null,
                'value' => $member->drinkingData?->translated_name ?? null,
            ],
            [
                'key'   => 'body_type',
                'label' => __('messages.field_lbl_body_type'),
                'selected_ids'  => $member->body_type ?? null,
                'value' => $member->bodyTypeData?->translated_name ?? null,
            ],
            [
                'key'   => 'complexion',
                'label' => __('messages.field_lbl_complexion'),
                'selected_ids'  => $member->complexion ?? null,
                'value' => $member->complexionData?->translated_name ?? null,
            ],
            [
                'key'   => 'blood_group_id',
                'label' => __('messages.field_lbl_blood_group'),
                'selected_ids'  => $member->blood_group_id ?? null,
                'value' => $member->bloodGroupData?->translated_name ?? null,
            ],
            [
                'key'   => 'about_me_description',
                'label' => __('messages.field_lbl_about_me'),
                'selected_ids'  => $member->about_me_description ?? null,
                'value' => $member->about_me_description ?? null,
            ]
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'physical_information',
            'label'       => __('messages.lbl_physical_information'),
            'fields'      => $fields
        ];
    }

    private static function familyDetails($member): array
    {
        $fields = [
            [
                'key'   => 'family_type',
                'label' => __('messages.field_lbl_family_type'),
                'selected_ids'  => $member->family_type ?? null,
                'value' => $member->familyTypeData?->translated_name ?? null,
            ],
            [
                'key'   => 'family_status',
                'label' => __('messages.field_lbl_family_status'),
                'selected_ids'  => $member->family_status ?? null,
                'value' => $member->familyStatusData?->translated_name ?? null,
            ],
            [
                'key'   => 'father_name',
                'label' => __('messages.field_lbl_father_name'),
                'selected_ids'  => $member->father_name ?? null,
                'value' => $member->father_name ?? null,
            ],
            [
                'key'   => 'father_occupation',
                'label' => __('messages.field_lbl_father_occupation'),
                'selected_ids'  => $member->father_occupation ?? null,
                'value' => $member->fatherOccupationData?->translated_name ?? null,
            ],
            [
                'key'   => 'mother_name',
                'label' => __('messages.field_lbl_mother_name'),
                'selected_ids'  => $member->mother_name ?? null,
                'value' => $member->mother_name ?? null,
            ],
            [
                'key'   => 'mother_occupation',
                'label' => __('messages.field_lbl_mother_occupation'),
                'selected_ids'  => $member->mother_occupation ?? null,
                'value' => $member->motherOccupationData?->translated_name ?? null,
            ],
            [
                'key'   => 'no_of_brother',
                'label' => __('messages.field_lbl_no_of_brothers'),
                'selected_ids'  => $member->no_of_brother ?? null,
                'value' => $member->noOfBrotherData?->translated_name ?? null,
            ],
            [
                'key'   => 'no_of_married_brother',
                'label' => __('messages.field_lbl_no_married_of_brothers'),
                'selected_ids'  => $member->no_of_married_brother ?? null,
                'value' => $member->noOfMarriedBrotherData?->translated_name ?? null,
            ],
            [
                'key'   => 'no_of_sister',
                'label' => __('messages.field_lbl_no_of_sisters'),
                'selected_ids'  => $member->no_of_sister ?? null,
                'value' => $member->noOfSisterData?->translated_name ?? null,
            ],
            [
                'key'   => 'no_of_married_sister',
                'label' => __('messages.field_lbl_no_of_married_sisters'),
                'selected_ids'  => $member->no_of_married_sister ?? null,
                'value' => $member->noOfMarriedSisterData?->translated_name ?? null,
            ],
            [
                'key'   => 'family_details',
                'label' => __('messages.field_lbl_family_details'),
                'selected_ids'  => $member->family_details ?? null,
                'value' => $member->family_details ?? null,
            ]
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'family_details',
            'label'       => __('messages.lbl_family_details'),
            'fields'      => $fields,
        ];
    }


    public static function partnerSection($registerPartner, $currentLanguage): array
    {
        if (!$registerPartner) {
            return [
                'section_key' => 'partner_preference',
                'label'       => __('messages.lbl_partner_preferences'),
                'fields'      => [],
            ];
        }

        $ageValue = null;
        if ($registerPartner->part_frm_age && $registerPartner->part_to_age) {
            $ageValue = "{$registerPartner->part_frm_age} " . __('messages.lbl_yrs') . " " .
                __('messages.field_lbl_to') . " {$registerPartner->part_to_age} " . __('messages.lbl_yrs');
        }

        $heightValue = null;
        if ($registerPartner->part_height && $registerPartner->part_height_to) {
            $heightValue = _displayHeight($registerPartner->part_height) . ' ' .
                __('messages.field_lbl_to') . ' ' .
                _displayHeight($registerPartner->part_height_to);
        }

        $displayPartnerValue = new stdClass();
        if ($registerPartner) {
            $partnerFields = [
                'part_religion'       => [ReligionMaster::class, 'religion_name'],
                'part_caste'          => [CasteMaster::class, 'caste_name'],
                'part_country'        => [CountryMaster::class, 'country_name'],
                'part_state'          => [StateMaster::class, 'state_name'],
                'part_marital_status' => [MaritalStatusMaster::class, 'marital_status_name'],
                'part_income'         => [AnnualIncomeMaster::class, 'annual_income_name'],
                'part_education'      => [EducationMaster::class, 'education_name'],
                'part_occupation'     => [OccupationMaster::class, 'occupation_name'],
                'part_mothertongue'   => [MotherTongueMaster::class, 'mtongue_name'],
                'part_manglik'        => [ManglikMaster::class, 'manglik_name'],
                'part_diet'        => [EatingHabitMaster::class, 'eating_habit_name'],
                'part_smoke'        => [SmokingHabitMaster::class, 'smoking_habit_name'],
                'part_drink'        => [DrinkingHabitMaster::class, 'drinking_habit_name'],
            ];
            foreach ($partnerFields as $field => [$model, $column]) {
                $value = $registerPartner->{$field};
                if (!empty($value) && $value !== 'Does Not Matter') {
                    $displayPartnerValue->{$field} = _getLangNamesFromIds($model, $value, $column, $currentLanguage);
                } else {
                    $displayPartnerValue->{$field} = $value;
                }
            }
        }

        $fields = [
            [
                'key'           => 'part_age',
                'label'         => __('messages.field_lbl_partner_age'),
                'selected_ids' => ($registerPartner->part_frm_age ?? '') . ',' . ($registerPartner->part_to_age ?? ''),
                'value'         => $ageValue,
            ],
            [
                'key'           => 'part_height',
                'label'         => __('messages.field_lbl_partner_height'),
                'selected_ids' => ($registerPartner->part_height ?? '') . ',' . ($registerPartner->part_height_to ?? ''),
                'value'         => $heightValue,
            ],
            [
                'key'           => 'part_religion',
                'label'         => __('messages.field_lbl_partner_religion'),
                'selected_ids'  => $registerPartner->part_religion ?? null,
                'value'         => $displayPartnerValue->part_religion ?? null,
            ],
            [
                'key'           => 'part_caste',
                'label'         => __('messages.field_lbl_partner_caste'),
                'selected_ids'  => $registerPartner->part_caste ?? null,
                'value'         => $displayPartnerValue->part_caste ?? null,
            ],
            [
                'key'           => 'part_country',
                'label'         => __('messages.field_lbl_partner_country'),
                'selected_ids'  => $registerPartner->part_country ?? null,
                'value'         => $displayPartnerValue->part_country ?? null,
            ],
            [
                'key'           => 'part_state',
                'label'         => __('messages.field_lbl_partner_state'),
                'selected_ids'  => $registerPartner->part_state ?? null,
                'value'         => $displayPartnerValue->part_state ?? null,
            ],
            [
                'key'           => 'part_marital_status',
                'label'         => __('messages.field_lbl_partner_marital_status'),
                'selected_ids'  => $registerPartner->part_marital_status ?? null,
                'value'         => $displayPartnerValue->part_marital_status ?? null,
            ],
            [
                'key'           => 'part_income',
                'label'         => __('messages.field_lbl_partner_annual_income'),
                'selected_ids'  => $registerPartner->part_income ?? null,
                'value'         => $displayPartnerValue->part_income ?? null,
            ],
            [
                'key'           => 'part_education',
                'label'         => __('messages.field_lbl_partner_education'),
                'selected_ids'  => $registerPartner->part_education ?? null,
                'value'         => $displayPartnerValue->part_education ?? null,
            ],
            [
                'key'           => 'part_occupation',
                'label'         => __('messages.field_lbl_partner_occupation'),
                'selected_ids'  => $registerPartner->part_occupation ?? null,
                'value'         => $displayPartnerValue->part_occupation ?? null,
            ],
            [
                'key'           => 'part_mothertongue',
                'label'         => __('messages.field_lbl_partner_mother_tongue'),
                'selected_ids'  => $registerPartner->part_mothertongue ?? null,
                'value'         => $displayPartnerValue->part_mothertongue ?? null,
            ],
            [
                'key'           => 'part_manglik',
                'label'         => __('messages.field_lbl_partner_manglik'),
                'selected_ids'  => $registerPartner->part_manglik ?? null,
                'value'         => $displayPartnerValue->part_manglik ?? null,
            ],
            [
                'key'           => 'part_diet',
                'label'         => __('messages.field_lbl_partner_eating_habits'),
                'selected_ids'  => $registerPartner->part_diet ?? null,
                'value'         => $displayPartnerValue->part_diet ?? null,
            ],
            [
                'key'           => 'part_smoke',
                'label'         => __('messages.field_lbl_partner_smoking'),
                'selected_ids'  => $registerPartner->part_smoke ?? null,
                'value'         => $displayPartnerValue->part_smoke ?? null,
            ],
            [
                'key'           => 'part_drink',
                'label'         => __('messages.field_lbl_partner_drinking'),
                'selected_ids'  => $registerPartner->part_drink ?? null,
                'value'         => $displayPartnerValue->part_drink ?? null,
            ],
        ];

        $fields = array_values(array_filter($fields, function ($item) {
            return _checkFieldEnable($item['key'], 'user_profile_list');
        }));

        return [
            'section_key' => 'partner_preference',
            'label' => __('messages.lbl_partner_preferences'),
            'fields' => $fields,
        ];
    }
}

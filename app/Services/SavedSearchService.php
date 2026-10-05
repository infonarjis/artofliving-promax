<?php

namespace App\Services;

use App\Models\AnnualIncomeMaster;
use App\Models\BloodGroupMaster;
use App\Models\BodyTypeMaster;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\ComplexionMaster;
use App\Models\CountryMaster;
use App\Models\DesignationMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\EducationMaster;
use App\Models\EmployeeMaster;
use App\Models\HoroscopeMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MoonsignMaster;
use App\Models\MotherTongueMaster;
use App\Models\OccupationMaster;
use App\Models\ReligionMaster;
use App\Models\SmokingHabitMaster;
use App\Models\StarMaster;
use App\Models\StateMaster;

class SavedSearchService
{
    public static function getDisplayValue($searchData)
    {
        $ageValue = null;
        if ($searchData->part_frm_age && $searchData->part_to_age) {
            $ageValue = "{$searchData->part_frm_age} " . __('messages.lbl_yrs') . " " .
                __('messages.field_lbl_to') . " {$searchData->part_to_age} " . __('messages.lbl_yrs');
        }

        $heightValue = null;
        if ($searchData->part_height && $searchData->part_height_to) {
            $heightValue = _displayHeight($searchData->part_height) . ' ' .
                __('messages.field_lbl_to') . ' ' .
                _displayHeight($searchData->part_height_to);
        }
        if ($searchData->search_page_name == 'Quick Search' || $searchData->search_page_name == 'Advance Search') {
            $fieldsArray = [
                [
                    'key'           => 'part_age',
                    'label'         => __('messages.field_lbl_partner_age'),
                    'selected_ids' => ($searchData->part_frm_age ?? '') . ',' . ($searchData->part_to_age ?? ''),
                    'value'         => $ageValue,
                ],
                [
                    'key'           => 'part_height',
                    'label'         => __('messages.field_lbl_partner_height'),
                    'selected_ids' => ($searchData->part_height ?? '') . ',' . ($searchData->part_height_to ?? ''),
                    'value'         => $heightValue,
                ],
                [
                    'key'          => 'marital_status',
                    'label'        => __('messages.field_lbl_marital_status'),
                    'selected_ids' => $searchData->marital_status,
                    'value' => _getDataNames(MaritalStatusMaster::class, $searchData->marital_status, 'marital_status_name'),
                ],
                [
                    'key'          => 'mother_tongue',
                    'label'        => __('messages.field_lbl_mother_tongue'),
                    'selected_ids' => $searchData->mother_tongue,
                    'value' => _getDataNames(MotherTongueMaster::class, $searchData->mother_tongue, 'mtongue_name'),
                ],
                [
                    'key'          => 'religion',
                    'label'        => __('messages.field_lbl_religion'),
                    'selected_ids' => $searchData->religion,
                    'value' => _getDataNames(ReligionMaster::class, $searchData->religion, 'religion_name'),
                ],
                [
                    'key'          => 'caste',
                    'label'        => __('messages.field_lbl_caste'),
                    'selected_ids' => $searchData->caste,
                    'value' => _getDataNames(CasteMaster::class, $searchData->caste, 'caste_name'),
                ],
                [
                    'key'          => 'manglik',
                    'label'        => __('messages.field_lbl_manglik'),
                    'selected_ids' => $searchData->manglik,
                    'value' => _getDataNames(ManglikMaster::class, $searchData->manglik, 'manglik_name'),
                ],
                [
                    'key'          => 'moonsign',
                    'label'        => __('messages.field_lbl_moonsing'),
                    'selected_ids' => $searchData->moonsign,
                    'value' => _getDataNames(MoonsignMaster::class, $searchData->manglik, 'moonsign_name'),
                ],
                [
                    'key'          => 'star',
                    'label'        => __('messages.field_lbl_star'),
                    'selected_ids' => $searchData->star,
                    'value' => _getDataNames(StarMaster::class, $searchData->star, 'star_name')
                ],
                [
                    'key'          => 'horoscope',
                    'label'        => __('messages.field_horoscope'),
                    'selected_ids' => $searchData->horoscope,
                    'value' => _getDataNames(HoroscopeMaster::class, $searchData->horoscope, 'horoscope_name'),
                ],
                [
                    'key'          => 'country',
                    'label'        => __('messages.field_lbl_country'),
                    'selected_ids' => $searchData->country,
                    'value' => _getDataNames(CountryMaster::class, $searchData->country, 'country_name')
                ],
                [
                    'key'          => 'state',
                    'label'        => __('messages.field_lbl_state'),
                    'selected_ids' => $searchData->state,
                    'value' => _getDataNames(StateMaster::class, $searchData->state, 'state_name')
                ],
                [
                    'key'          => 'city',
                    'label'        => __('messages.field_lbl_city'),
                    'selected_ids' => $searchData->city,
                    'value' => _getDataNames(CityMaster::class, $searchData->city, 'city_name')
                ],
                [
                    'key'          => 'education_level',
                    'label'        => __('messages.field_lbl_education'),
                    'selected_ids' => $searchData->education_level,
                    'value' => _getDataNames(EducationMaster::class, $searchData->education_level, 'education_name')
                ],
                [
                    'key'          => 'occupation',
                    'label'        => __('messages.field_lbl_occupation'),
                    'selected_ids' => $searchData->occupation,
                    'value' => _getDataNames(OccupationMaster::class, $searchData->occupation, 'occupation_name')
                ],
                [
                    'key'          => 'employee_in',
                    'label'        => __('messages.field_lbl_employee_in'),
                    'selected_ids' => $searchData->employee_in,
                    'value' => _getDataNames(EmployeeMaster::class, $searchData->employee_in, 'employee_name')
                ],
                [
                    'key'          => 'designation_level',
                    'label'        => __('messages.field_lbl_designation'),
                    'selected_ids' => $searchData->designation_level,
                    'value' => _getDataNames(DesignationMaster::class, $searchData->designation_level, 'designation_name')
                ],
                [
                    'key'          => 'income',
                    'label'        => __('messages.field_lbl_annual_income'),
                    'selected_ids' => $searchData->income,
                    'value' => _getDataNames(AnnualIncomeMaster::class, $searchData->income, 'annual_income_name')
                ],
                [
                    'key'          => 'diet',
                    'label'        => __('messages.field_lbl_eating_habits'),
                    'selected_ids' => $searchData->diet,
                    'value' => _getDataNames(EatingHabitMaster::class, $searchData->diet, 'eating_habit_name')
                ],
                [
                    'key'          => 'smoke',
                    'label'        => __('messages.field_lbl_smoking'),
                    'selected_ids' => $searchData->smoke,
                    'value' => _getDataNames(SmokingHabitMaster::class, $searchData->smoke, 'smoking_habit_name')
                ],
                [
                    'key'          => 'drink',
                    'label'        => __('messages.field_lbl_drinking'),
                    'selected_ids' => $searchData->drink,
                    'value' => _getDataNames(DrinkingHabitMaster::class, $searchData->drink, 'drinking_habit_name')
                ],
                [
                    'key'          => 'body_type',
                    'label'        => __('messages.field_lbl_body_type'),
                    'selected_ids' => $searchData->body_type,
                    'value' => _getDataNames(BodyTypeMaster::class, $searchData->body_type, 'body_type_name')
                ],
                [
                    'key'          => 'body_type',
                    'label'        => __('messages.field_lbl_body_type'),
                    'selected_ids' => $searchData->body_type,
                    'value' => _getDataNames(BodyTypeMaster::class, $searchData->body_type, 'body_type_name')
                ],
                [
                    'key'          => 'complexion',
                    'label'        => __('messages.field_lbl_complexion'),
                    'selected_ids' => $searchData->complexion,
                    'value' => _getDataNames(ComplexionMaster::class, $searchData->complexion, 'complexion_name')
                ],
                [
                    'key'          => 'blood_group_id',
                    'label'        => __('messages.field_lbl_blood_group'),
                    'selected_ids' => $searchData->blood_group_id,
                    'value' => _getDataNames(BloodGroupMaster::class, $searchData->blood_group_id, 'blood_group_name')
                ],
                [
                    'key'          => 'with_photo',
                    'label'        => __('messages.field_lbl_with_photo'),
                    'selected_ids' => $searchData->with_photo,
                    'value'        => $searchData->with_photo ?? 'No',
                ],
            ];
            if($searchData->search_page_name == 'Quick Search'){
                $fields = array_values(array_filter($fieldsArray, function ($item) {
                    return _checkFieldEnable($item['key'], 'quick_search');
                }));
            }else{
                $fields = array_values(array_filter($fieldsArray, function ($item) {
                    return _checkFieldEnable($item['key'], 'advance_search');
                }));
            }

            return $fields;
        } elseif ($searchData->search_page_name == 'Keyword Search') {
            return [
                [
                    'key'          => 'keyword',
                    'label'        => __('messages.lbl_id_search'),
                    'selected_ids' => $searchData->keyword,
                    'value'        => $searchData->keyword ?? 'No',
                ],
                [
                    'key'          => 'with_photo',
                    'label'        => __('messages.field_lbl_with_photo'),
                    'selected_ids' => $searchData->with_photo,
                    'value'        => $searchData->with_photo ?? 'No',
                ],
            ];
        } elseif ($searchData->search_page_name == 'Id Search') {
            return [
                [
                    'key'          => 'id_search',
                    'label'        => __('messages.lbl_id_search'),
                    'selected_ids' => $searchData->id_search,
                    'value'        => $searchData->id_search ?? 'No',
                ],
                [
                    'key'          => 'with_photo',
                    'label'        => __('messages.field_lbl_with_photo'),
                    'selected_ids' => $searchData->with_photo,
                    'value'        => $searchData->with_photo ?? 'No',
                ]
            ];
        }
    }
}

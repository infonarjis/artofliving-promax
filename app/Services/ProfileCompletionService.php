<?php

namespace App\Services;

use App\Models\Register;
use App\Models\RegisterPartner;

class ProfileCompletionService
{
    /**
     * Define completion rules here.
     * Add/remove fields ONLY in this array.
     */
    private static array $rules = [

        // Section => required fields
        'basic_details' => [
            'profileby',
            'fullname',
            'mother_tongue',
            'marital_status',
            'birthdate'
        ],

        'religious_information' => [
            'religion',
            'caste',
        ],

        'education_details' => [
            'education_level',
            'occupation',
            'designation_level',
        ],

        'location_details' => [
            'country_id',
            'state_id',
            'city',
            'alternate_number',
        ],

        'physical_information' => [
            'height',
            'weight',
            'about_me_description',
        ],

        'family_details' => [
            'father_name',
            'father_occupation',
            'mother_name',
            'mother_occupation',
        ],

        'photos' => [
            'selfie_photo',
            'photo1',
        ],

        'id_proof' => [
            'id_proof_front',
            'id_proof_back',
        ],

        // 'horoscope' => [
        //     'horoscope_file',
        // ],
    ];

    public static function calculate(Register $member): int
    {
        $completedSections = 0;

        foreach (self::$rules as $fields) {
            if (self::areFieldsFilled($member, $fields)) {
                $completedSections++;
            }
        }

        // Partner preference (separate table)
        if (RegisterPartner::where('member_id', $member->id)->exists()) {
            $completedSections++;
        }

        $totalSections = count(self::$rules) + 1; // +1 for partner preference

        return (int) round(($completedSections / $totalSections) * 100);
    }

    private static function areFieldsFilled(Register $member, array $fields): bool
    {
        foreach ($fields as $field) {
            if (blank($member->$field)) {
                return false;
            }
        }
        return true;
    }
}

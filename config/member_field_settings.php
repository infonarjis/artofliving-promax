<?php

/*
|--------------------------------------------------------------------------
| Member Field Settings
|--------------------------------------------------------------------------
| label                  Human-readable name shown in the admin UI.
| field_disable          'Yes' = required field. Pages are forced on for
|                        every context, and search (if applicable) is
|                        forced on too — see MemberFieldCheck::forcedTokens().
| field_show_in_search   'Yes' = this field can be searched at all, so the
|                        Search Settings columns render as toggles.
|                        'No'  = search doesn't apply — UI shows
|                        "Not searchable" and search tokens are never
|                        written for it, no matter what's posted.
|
| Everything else is driven purely by what the admin checks and saves —
| there are no seed defaults; a newly synced field starts fully off.
*/

return [

    'Basic Information' => [
        'profileby'       => ['label' => 'Profile By', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'mother_tongue'   => ['label' => 'Mother Tongue', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'marital_status'  => ['label' => 'Marital Status', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'total_children'  => ['label' => 'Total Children', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'status_children' => ['label' => 'Status Children', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'birthplace'      => ['label' => 'Birth Place', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'birthtime'       => ['label' => 'Birth Time', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

    'Religion Information' => [
        'religion'  => ['label' => 'Religion', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'caste'     => ['label' => 'Caste', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'subcaste'  => ['label' => 'Sub Caste', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'manglik'   => ['label' => 'Manglik', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'gothra'    => ['label' => 'Gothra', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'moonsign'  => ['label' => 'Moonsign', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'star'      => ['label' => 'Star', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'horoscope' => ['label' => 'Horoscope', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
    ],

    'Education & Other Details' => [
        'education_level'   => ['label' => 'Education', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'education_details' => ['label' => 'Education Details', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'occupation'        => ['label' => 'Occupation', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'employee_in'       => ['label' => 'Employee In', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'income'            => ['label' => 'Annual Income', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'designation_level' => ['label' => 'Designation', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
    ],

    'Location Information' => [
        'country_id'       => ['label' => 'Country', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'state_id'         => ['label' => 'State', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'city'             => ['label' => 'City', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'alternate_number' => ['label' => 'Alternate Number', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'residence_type'   => ['label' => 'Residence Type', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'nri_country'      => ['label' => 'If NRI Originated Country', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'address'          => ['label' => 'Address', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

    'Physical Information' => [
        'height'               => ['label' => 'Height', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'weight'               => ['label' => 'Weight', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'diet'                 => ['label' => 'Eating Habits', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'smoke'                => ['label' => 'Smoking Habit', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'drink'                => ['label' => 'Drinking Habit', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'body_type'            => ['label' => 'Body type', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'complexion'           => ['label' => 'Complexion', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'blood_group_id'       => ['label' => 'Blood Group', 'field_disable' => 'No', 'field_show_in_search' => 'Yes'],
        'about_me_description' => ['label' => 'About Me', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

    'Family Details' => [
        'family_type'           => ['label' => 'Family Type', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'family_status'         => ['label' => 'Family Status', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'father_name'           => ['label' => 'Father Name', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'father_occupation'     => ['label' => 'Father Occupation', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'mother_name'           => ['label' => 'Mother Name', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'mother_occupation'     => ['label' => 'Mother Occupation', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'no_of_brother'         => ['label' => 'No Of Brothers', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'no_of_married_brother' => ['label' => 'No Of Married Brothers', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'no_of_sister'          => ['label' => 'No Of Sisters', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'no_of_married_sister'  => ['label' => 'No Of Married Sisters', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'family_details'        => ['label' => 'Family Details', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

    'Partner Preference' => [
        'part_age'            => ['label' => 'Partner Age', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'part_height'         => ['label' => 'Partner Height', 'field_disable' => 'Yes', 'field_show_in_search' => 'Yes'],
        'part_religion'       => ['label' => 'Partner Religion', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'part_caste'          => ['label' => 'Partner Caste', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'part_country'        => ['label' => 'Partner Country', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'part_state'          => ['label' => 'Partner State', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'part_marital_status' => ['label' => 'Partner Marital Status', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'part_income'         => ['label' => 'Partner Annual Income', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_education'      => ['label' => 'Partner Education', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_occupation'     => ['label' => 'Partner Occupation', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_mothertongue'   => ['label' => 'Partner Mother Tongue', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_manglik'        => ['label' => 'Partner Manglik', 'field_disable' => 'No', 'field_show_in_search' => 'No'],

        'part_diet'        => ['label' => 'Partner Eating Habits', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_smoke'        => ['label' => 'Partner Smoking Habits', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'part_drink'        => ['label' => 'Partner Drinking Habits', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

    'Member Photos' => [
        'photo1' => ['label' => 'Photo 1', 'field_disable' => 'Yes', 'field_show_in_search' => 'No'],
        'photo2' => ['label' => 'Photo 2', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'photo3' => ['label' => 'Photo 3', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
        'photo4' => ['label' => 'Photo 4', 'field_disable' => 'No', 'field_show_in_search' => 'No'],
    ],

];
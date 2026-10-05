<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class RegisterSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_IN');

        $records = [];

        for ($i = 1; $i <= 100; $i++) {

            // ✅ Alternate gender every record
            $gender = $i % 2 == 1 ? 'Male' : 'Female';
            ## Profile Images:
            if ($gender == 'Male') {
                $imageUrl = "https://randomuser.me/api/portraits/men/" . rand(1, 99) . ".jpg"; // Random male image
            } else {
                $imageUrl = "https://randomuser.me/api/portraits/women/" . rand(1, 99) . ".jpg"; // Random female image
            }
            $image = imagecreatefromjpeg($imageUrl);
            $imageName = 'user_' . time() . '_' . $i . '.webp';
            $imagePath = Storage::path('assets/memberPhotos/' . $imageName);
            imagewebp($image, $imagePath);
            imagedestroy($image);
            $photo = $imageName;

            $firstName = $faker->firstName($gender == 'Male' ? 'male' : 'female');
            $lastName  = $faker->lastName;

            $records[] = [
                'user_type' => 0,
                'matri_id' => 'MATRI' . (3000 + $i),
                'prefix' => 'PROMATRI',
                'terms' => 'Yes',

                'email' => $faker->unique()->userName . rand(100, 999) . '@gmail.com',
                'email_verify_status' => 'Verify',

                'mobile' => '+91-' . $faker->unique()->numberBetween(6000000000, 9999999999),
                'mobile_verify_status' => 'Yes',

                'password' => Hash::make('123456'),

                'fullname'   => $firstName . ' ' . $lastName,

                'birthdate' => $faker->dateTimeBetween('-35 years', '-21 years')->format('Y-m-d'),
                'birthplace' => 'Ahemdabad',
                'birthtime' => '11:57',
                'gender' => $gender,

                'country_id' => 95,
                'state_id' => 1307,
                'city' => '987577',

                'profileby' => 'Self',
                'height' => rand(50, 72),
                'weight' => rand(45, 85),
                'marital_status' => '1',

                'religion' => 5,
                'caste' => 6,

                'mother_tongue' => 19,
                'diet' => 'Vegetarian',
                'smoke' => 'No',
                'drink' => 'No',

                'education_level' => rand(1, 30),
                'education_details' => 'BTECH IT',
                'employee_in' => rand(2, 4),
                'occupation' => rand(1, 10),
                'income' => rand(1, 10),
                'designation_level' => rand(1, 10),

                'family_type' => 'Joint Family',
                'father_name' => 'Test Father Name',
                'father_occupation' => 'Test Father Occupation',
                'mother_name' => 'Test Mother Name',
                'mother_occupation' => 'Test Mother Occupations',

                // Photo
                'photo1' => $photo,
                'photo1_status' => 'APPROVED',
                'photo1_uploaded_on' => now(),

                'registered_from' => 'Website',
                'user_agent' => 'Seeder',

                'agent_approve' => 'APPROVED',
                'fstatus' => 'Unfeatured',
                'logged_in' => '0',

                'adminrole_view_status' => 'No',
                'contact_visibility' => 0,
                'photo_visibility' => '1',
                'video_call_setting' => 1,
                'voice_call_setting' => 1,

                'profile_setting' => '0',
                'status' => 'APPROVED',

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('registers')->insert($records);


        $getRegisterData = DB::table('registers')->select('id', 'matri_id')->orderBy('id', 'ASC')->get();
        foreach ($getRegisterData as $key => $value) {
            ## Update Matri id:
            DB::table('registers')
            ->where('id', $value->id)
            ->update([
                'matri_id' => 'PROMATRI' . $value->id
            ]);

            ## Insert Register Partners :
            $insertRecords = [
                'member_id' => $value->id,
                'part_frm_age' => '18',
                'part_to_age' => '60',
                'part_height' => '48',
                'part_height_to' => '84',
                'part_marital_status' => 'Does Not Matter',
                'part_religion' => 'Does Not Matter',
                'part_caste' => 'Does Not Matter',
                'part_country' => 'Does Not Matter',
                'part_state' => 'Does Not Matter',
                'part_income' => 'Does Not Matter',
                'part_education' => 'Does Not Matter',
                'part_occupation' => 'Does Not Matter',
                'part_mothertongue' => 'Does Not Matter',
                'part_manglik' => 'Does Not Matter',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('register_partners')->updateOrInsert($insertRecords);
        }
    }
}

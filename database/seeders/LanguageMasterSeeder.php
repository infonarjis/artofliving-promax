<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertArr = [
            'id' => 1,
            'lang_name' => 'English',
            'lang_code' => 'en',
            'status' => 'APPROVED',
            'is_default' => 'Yes',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        DB::table('language_master')->insert($insertArr);
    }

}

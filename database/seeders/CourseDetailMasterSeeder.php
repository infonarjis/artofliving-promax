<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseDetailMasterSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // [id, course_name, is_deleted]
        $rows = [
            [5,  'HAPPINESS PROGRAM', false],
            [6,  'PART II  ( ART OF SILENCE) PROGRAM/ADVANCE MEDITATION PROGRAM', false],
            [7,  'SANYAM', false],
            [8,  'SAHAJ SAMADHI MEDITATION', false],
            [9,  'DSN', false],
            [10, 'VTP or PRE-TTP', false],
            [11, 'TTP', false],
            [12, 'BLESSINGS PROGRAM', false],
            [13, 'YLTP', false],
            [14, 'UPANAYANAM', false],
            [15, 'YES', false],
            [16, 'YES +', false],
            [17, 'SRI SRI YOGA LEVEL 1', false],
            [18, 'SRI SRI YOGA LEVEL 2', false],
            [19, 'AYURVEDIC COOKING', false],
            [20, 'SHAKTI KRIYA', false],
            [21, 'Art Excel', false],
            [22, 'Youth Empowerment Seminar', false],
            [24, 'Sri Sri Natya', false],
            [25, 'Vigyan Bhairav', false],
            [26, 'Prajna Yoga', false],
            [27, 'Bridge Program Sri Sri school of yoga', false],
            [28, 'QCI certification Yoga Instructors certification', false],
            [29, 'RYT 200', false],
            [30, '350H RYT', false],
            [31, 'test123', true],
            [32, 'Cooking in Gujarati Family', true],
            [33, 'test1223', true],
            [34, 'test +', true],
        ];

        $data = array_map(fn($r) => [
            'id'          => $r[0],
            'course_name' => trim($r[1]),
            'lang_code'   => 'en',
            'lang_id'     => null,   // base (default language) records
            'status'      => 'APPROVED',
            'created_at'  => $now,
            'updated_at'  => $now,
            'deleted_at'  => $r[2] ? $now : null,
        ], $rows);

        DB::table('course_detail_masters')->insert($data);
    }
}

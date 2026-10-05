<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->enum('Yesart_of_living_teacher', ['Yes', 'No'])
                ->nullable()
                ->default(null)
                ->after('income');

            $table->string('teacher_code')
                ->nullable()
                ->default(null)
                ->after('Yesart_of_living_teacher');

            $table->string('teaching_courses', 255)
                ->nullable()
                ->default(null)
                ->after('teacher_code')
                ->comment('Comma separated course IDs');

            $table->enum('have_art_of_living_program', ['Yes', 'No'])
                ->nullable()
                ->default(null)
                ->after('teaching_courses');

            $table->string('teacher_name', 255)
                ->nullable()
                ->default(null)
                ->after('have_art_of_living_program');

            $table->string('teacher_mobile_no', 20)
                ->nullable()
                ->default(null)
                ->after('teacher_name');

            $table->text('art_of_living_program')
                ->nullable()
                ->default(null)
                ->after('teacher_mobile_no');

            $table->string('no_of_years_in_artofliving', 20)
                ->nullable()
                ->default(null)
                ->after('art_of_living_program');
        });
    }

    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->dropColumn([
                'Yesart_of_living_teacher',
                'teacher_code',
                'teaching_courses',
                'have_art_of_living_program',
                'teacher_name',
                'teacher_mobile_no',
                'art_of_living_program',
                'no_of_years_in_artofliving',
            ]);
        });
    }
};
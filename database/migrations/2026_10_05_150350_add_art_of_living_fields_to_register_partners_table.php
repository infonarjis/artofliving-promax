<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('register_partners', function (Blueprint $table) {
            $table->string('part_art_of_living_teacher', 255)
                ->nullable()
                ->default(null)
                ->after('part_manglik');

            $table->string('part_have_art_of_living_program', 255)
                ->nullable()
                ->default(null)
                ->after('part_art_of_living_teacher');
        });
    }

    public function down(): void
    {
        Schema::table('register_partners', function (Blueprint $table) {
            $table->dropColumn([
                'part_art_of_living_teacher',
                'part_have_art_of_living_program',
            ]);
        });
    }
};
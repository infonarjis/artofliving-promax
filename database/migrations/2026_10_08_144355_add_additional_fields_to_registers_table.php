<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->text('field_of_study')->nullable()->after('no_of_years_in_artofliving');
            $table->string('job_title', 255)->nullable()->after('field_of_study');
            $table->string('company_name', 255)->nullable()->after('job_title');
            $table->enum('disbalities', ['Not Available', 'Yes', 'No'])->nullable()->after('company_name');
            $table->string('disabilites_details', 255)->nullable()->after('disbalities');
            $table->string('details_of_children', 255)->nullable()->after('disabilites_details');
            $table->text('interest')->nullable()->after('details_of_children');
            $table->text('father_occupation_other')->nullable()->after('interest');
            $table->text('mother_occupation_other')->nullable()->after('father_occupation_other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->dropColumn([
                'field_of_study',
                'job_title',
                'company_name',
                'disbalities',
                'disabilites_details',
                'details_of_children',
                'interest',
                'father_occupation_other',
                'mother_occupation_other',
            ]);
        });
    }
};

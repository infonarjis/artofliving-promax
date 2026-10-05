<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'staff_prefix')) {
                $table->string('staff_prefix', 100)->nullable()->after('id');
            }
            if (!Schema::hasColumn('staff', 'gender')) {
                $table->enum('gender', ['Male', 'Female'])->default('Male')->after('type');
            }
            if (!Schema::hasColumn('staff', 'profile_image')) {
                $table->string('profile_image', 100)->nullable()->after('basic_salary');
            }
            if (!Schema::hasColumn('staff', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('profile_image');
            }
            if (!Schema::hasColumn('staff', 'marital_status')) {
                $table->string('marital_status')->nullable()->after('birthdate');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

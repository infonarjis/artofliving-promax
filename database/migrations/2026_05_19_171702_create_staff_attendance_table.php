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
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')
                ->index();
            $table->dateTime('punch_in')
                ->nullable();
            $table->text('punch_in_remarks')
                ->nullable();
            $table->dateTime('punch_out')
                ->nullable();
            $table->text('punch_out_remarks')
                ->nullable();
            $table->enum('attendance_status', [
                'Present',
                'Absent',
                'Late',
                'Early'
            ])->default('Present');
            $table->string('ip_address')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};

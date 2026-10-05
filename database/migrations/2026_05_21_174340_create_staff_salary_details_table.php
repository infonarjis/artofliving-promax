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
        Schema::create('staff_salary_details', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_salary_id')->index();
            $table->unsignedBigInteger('staff_id')->index();
            $table->string('month_year', 20);
            $table->double('basic_salary')->default(0);
            $table->double('total_earning')->default(0);
            $table->double('total_deduction')->default(0);
            $table->double('total_net_payable_salary')->default(0);
            $table->double('total_days')->default(0);
            $table->double('working_days')->default(0);
            $table->double('total_holi_day')->default(0);
            $table->double('total_present_days')->default(0);
            $table->double('attendance_leaves')->default(0);
            $table->double('total_working_hours')->default(0);
            $table->double('total_attendance_hour')->default(0);
            $table->double('overtime_shortfall_hrs')->default(0);
            $table->double('total_paid_leave')->default(0);
            $table->double('total_sick_leave')->default(0);
            $table->double('total_annual_leave')->default(0);
            $table->double('applicable_paid_leave')->default(0);
            $table->double('applicable_sick_leave')->default(0);
            $table->double('total_applicable_this_month_leaves')->default(0);
            $table->double('applied_paid_leave')->default(0);
            $table->double('applied_sick_leave')->default(0);
            $table->double('total_leaves')->default(0);
            $table->double('remaining_paid_leave')->default(0);
            $table->double('remaining_sick_leave')->default(0);
            $table->double('total_remain_leaves')->default(0);
            $table->double('deductable_paid_leaves')->default(0);
            $table->double('deductable_sick_leaves')->default(0);
            $table->double('total_deductable_leaves')->default(0);
            $table->double('perday_salary')->default(0);
            $table->double('payable_days')->default(0);
            $table->double('net_payable_salary')->default(0);
            $table->double('total_unpaid_leaves')->default(0);
            $table->double('total_unpaid_leaves_amount')->default(0);
            $table->enum('status', [
                'APPROVED',
                'UNAPPROVED'
            ])->default('APPROVED');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_details');
    }
};

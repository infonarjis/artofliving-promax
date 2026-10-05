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
        Schema::create('staff_salary_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('total_days')
                ->default(0);
            $table->unsignedBigInteger('working_days')
                ->default(0);
            $table->unsignedBigInteger('payable_days')
                ->default(0);
            $table->decimal('basic_salary', 10, 2)
                ->default(0.00);
            $table->decimal('total_earning', 10, 2)
                ->default(0.00);
            $table->decimal('total_deduction', 10, 2)
                ->default(0.00);
            $table->decimal('total_net_payable_salary', 10, 2)
                ->default(0.00);
            $table->string('month_year', 7)
                ->nullable();
            $table->date('salary_pay_date')
                ->nullable();
            $table->enum('status', [
                'APPROVED',
                'UNAPPROVED'
            ])->default('APPROVED');
            $table->timestamps();
            $table->softDeletes();
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_master');
    }
};

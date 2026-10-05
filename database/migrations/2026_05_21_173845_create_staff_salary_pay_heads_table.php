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
        Schema::create('staff_salary_pay_heads', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_salary_id')
                ->index();
            $table->unsignedBigInteger('staff_id')
                ->index();
            $table->unsignedBigInteger('pay_head_id')
                ->index();
            $table->string('pay_head_title')
                ->nullable();
            $table->enum('pay_head_type', [
                'Earning',
                'Deduction'
            ]);
            $table->double('pay_head_amount')
                ->default(0);
            $table->string('month_year', 20);
            $table->enum('status', [
                'APPROVED',
                'UNAPPROVED'
            ]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_pay_heads');
    }
};

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
        Schema::create('staff_pay_head_masters', function (Blueprint $table) {
            $table->id();

            $table->string('title')
                ->comment('Title of the pay head');

            $table->text('description')
                ->nullable()
                ->comment('Description of the pay head');

            $table->enum('pay_head_type', ['Earning', 'Deduction'])
                ->comment('Type of pay head: Earning or Deduction');


            $table->enum('status', ['APPROVED', 'UNAPPROVED'])
                ->default('APPROVED');
            $table->string('lang_code', 10)->nullable();
            $table->unsignedBigInteger('lang_id')->nullable();

            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_pay_head_masters');
    }
};

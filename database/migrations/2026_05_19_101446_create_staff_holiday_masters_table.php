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
        Schema::create('staff_holiday_masters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('holiday_date');
            $table->enum('status', ['APPROVED', 'UNAPPROVED'])
                ->default('APPROVED');
            $table->string('lang_code', 10)
                ->nullable();
            $table->unsignedBigInteger('lang_id')
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
        Schema::dropIfExists('staff_holiday_masters');
    }
};

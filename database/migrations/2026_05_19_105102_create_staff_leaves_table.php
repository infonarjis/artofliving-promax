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
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('leave_type')
                ->nullable();
            $table->string('subject')
                ->nullable();
            $table->text('message')
                ->nullable();
            $table->string('total_leave')
                ->nullable();
            $table->date('leave_start_date')
                ->nullable();
            $table->date('leave_end_date')
                ->nullable();
            $table->text('leave_reply_msg')
                ->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])
                ->default('PENDING');
            $table->string('lang_code', 10)
                ->nullable();
            $table->unsignedBigInteger('lang_id')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leaves');
    }
};

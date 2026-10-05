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
        Schema::create('staff_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('title')
                ->nullable();
            $table->text('description')
                ->nullable();
            $table->string('receipt')
                ->nullable();
            $table->enum('status', ['UNAPPROVED', 'APPROVED'])
                ->default('UNAPPROVED');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_reimbursements');
    }
};

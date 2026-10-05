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
        Schema::create('member_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->unique();
            $table->integer('risk_score')->default(0);
            $table->integer('warning_count')->default(0);
            $table->boolean('is_restricted')->default(false);
            $table->boolean('is_suspended')->default(false);
            $table->timestamp('last_warning_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_risk_scores');
    }
};

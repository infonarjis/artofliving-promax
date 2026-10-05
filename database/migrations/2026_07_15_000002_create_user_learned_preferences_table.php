<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per member holding their auto-learned preference profile:
 * - preference_data: normalized affinity per categorical field/value (religion, caste, etc.)
 * - numeric_data: weighted mean/std for age and height, learned from positive-signal profiles
 *
 * Recomputed by BehaviorLearningService::learnPreferences(), either
 * automatically (every N new activity logs) or via the nightly command.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_learned_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->unique();
            $table->json('preference_data')->nullable();
            $table->json('numeric_data')->nullable();
            $table->unsignedInteger('sample_size')->default(0);
            $table->timestamp('last_learned_at')->nullable();
            $table->timestamps();

            // $table->foreign('member_id')->references('id')->on('registers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_learned_preferences');
    }
};

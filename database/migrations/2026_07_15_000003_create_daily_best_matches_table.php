<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Precomputed "Best Matches Today" cache, refreshed by the nightly
 * matchmaking:generate-best-matches command. Keeps the "Best Matches
 * Today" page instant instead of scoring the whole candidate pool
 * on every page load.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_best_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('matched_member_id');
            $table->unsignedTinyInteger('match_percent');
            $table->json('score_breakdown')->nullable();
            $table->unsignedInteger('rank')->default(0);
            $table->date('computed_date');
            $table->timestamps();

            $table->unique(['member_id', 'matched_member_id', 'computed_date'], 'daily_match_unique');
            $table->index(['member_id', 'computed_date', 'rank']);

            // $table->foreign('member_id')->references('id')->on('registers')->onDelete('cascade');
            // $table->foreign('matched_member_id')->references('id')->on('registers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_best_matches');
    }
};

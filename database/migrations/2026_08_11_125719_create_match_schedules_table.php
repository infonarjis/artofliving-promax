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
        Schema::create('match_schedules', function (Blueprint $table) {
            $table->id();

            // One row per scheduled run-day
            $table->date('schedule_date')->index();

            // Snapshot of settings used for this run (from SiteSetting form)
            $table->unsignedInteger('send_total_match')->default(1);
            $table->json('match_criteria')->nullable();

            // Progress counters
            $table->unsignedInteger('total_members')->default(0);      // total eligible members found for this run
            $table->unsignedInteger('processed_members')->default(0);  // members looped through so far
            $table->unsignedInteger('matches_sent')->default(0);       // total match rows created (SendMatchesSms)
            $table->unsignedInteger('emails_sent')->default(0);        // members who actually got the email
            $table->unsignedInteger('sms_sent')->default(0)->after('emails_sent');

            // Resume cursor (mirrors auto_match_sms_id but scoped per schedule)
            $table->unsignedBigInteger('last_processed_id')->default(0);

            // pending: created, not started yet
            // in_progress: currently processing batches (cron may run several times)
            // completed: all eligible members processed + emails sent
            // failed: an exception stopped the run
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])
                  ->default('pending');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_schedules');
    }
};

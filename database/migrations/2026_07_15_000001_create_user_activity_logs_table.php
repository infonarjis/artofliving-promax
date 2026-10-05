<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores every behavioral signal a member generates against another
 * profile: view, shortlist, interest, ignore, block, photo_request.
 * This is the raw feed the BehaviorLearningService learns from.
 *
 * NOTE: adjust the referenced table name ('registers') below if your
 * Register model uses a different table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('target_member_id');
            $table->string('action_type', 30); // view | shortlist | interest | ignore | block | photo_request
            $table->float('weight')->default(0); // signed signal strength, see BehaviorLearningService::ACTION_WEIGHTS
            $table->timestamps();

            $table->index(['member_id', 'action_type']);
            $table->index(['member_id', 'target_member_id']);
            $table->index('created_at');

            // $table->foreign('member_id')->references('id')->on('registers')->onDelete('cascade');
            // $table->foreign('target_member_id')->references('id')->on('registers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};

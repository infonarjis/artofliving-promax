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
        Schema::create('plan_upgrade_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('previous_payment_id')->nullable();
            $table->unsignedBigInteger('new_payment_id');
            $table->unsignedBigInteger('previous_plan_id')->nullable();

            $table->string('previous_plan_name')->nullable();

            $table->unsignedBigInteger('new_plan_id');

            $table->string('new_plan_name');

            $table->integer('carried_forward_days')->default(0);
            $table->integer('carried_forward_view_profile')->default(0);
            $table->integer('carried_forward_interest')->default(0);
            $table->integer('carried_forward_contact_views')->default(0);
            $table->integer('carried_forward_video_minutes')->default(0);
            $table->integer('carried_forward_audio_minutes')->default(0);

            $table->timestamp('upgrade_date');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['member_id', 'upgrade_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_upgrade_histories');
    }
};

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
        Schema::table('payments', function (Blueprint $table) {
            // Which payment (if any) this one renewed/upgraded from.
            $table->unsignedBigInteger('previous_payment_id')
                ->nullable()
                ->after('plan_id');

            // 'Yes' when this payment was created via upgrade/renew (i.e. a
            // previous active plan existed and was merged into this one).
            $table->enum('is_renewal', ['Yes', 'No'])
                ->default('No')
                ->after('current_plan');

            // Remaining validity days carried forward from the previous plan.
            $table->integer('carried_forward_days')->default(0)->after('addon_validity_days');

            // Remaining feature balances carried forward from the previous plan.
            $table->integer('carried_forward_view_profile')->default(0)->after('addon_view_profile');
            $table->integer('carried_forward_interest')->default(0)->after('addon_interest');
            $table->integer('carried_forward_contact_views')->default(0)->after('addon_contact_views');
            $table->integer('carried_forward_video_minutes')->default(0)->after('addon_video_minutes');
            $table->integer('carried_forward_audio_minutes')->default(0)->after('addon_audio_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'previous_payment_id',
                'is_renewal',
                'carried_forward_days',
                'carried_forward_view_profile',
                'carried_forward_interest',
                'carried_forward_contact_views',
                'carried_forward_video_minutes',
                'carried_forward_audio_minutes',
            ]);
        });
    }
};

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
        Schema::table('custom_chat_conversation', function (Blueprint $table) {
            // pending  -> request sent, waiting for the other member to respond
            // accepted -> both members can chat freely
            // rejected -> request was rejected OR an accepted chat was later rejected/ended
            $table->enum('request_status', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->after('member2_matri_id');

            // member_id of whoever sent the original chat request
            $table->unsignedBigInteger('requested_by')->nullable()->after('request_status');

            // when the receiver accepted / rejected the request
            $table->timestamp('responded_at')->nullable()->after('requested_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_chat_conversation', function (Blueprint $table) {
            $table->dropColumn(['request_status', 'requested_by', 'responded_at']);
        });
    }
};

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
        Schema::table('send_matches_sms', function (Blueprint $table) {
            $table->enum('email_sent_status', ['No', 'Yes'])
                ->default('No')
                ->after('other_id');

            $table->enum('sms_sent_status', ['No', 'Yes'])
                ->default('No')
                ->after('email_sent_status');

            $table->dropColumn('sent_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('send_matches_sms', function (Blueprint $table) {
            $table->dropColumn([
                'email_sent_status',
                'sms_sent_status',
            ]);
            $table->enum('sent_status', ['No', 'Yes'])
                ->default('No');
        });
    }
};

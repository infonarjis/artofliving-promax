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
        Schema::table('site_config', function (Blueprint $table) {
            $table->string('sms_api_url')->nullable()->after('service_tax');
            $table->string('sms_api_sender_id')->nullable()->after('sms_api_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_config', function (Blueprint $table) {
            $table->dropColumn(['sms_api_url', 'sms_api_sender_id',]);
        });
    }
};

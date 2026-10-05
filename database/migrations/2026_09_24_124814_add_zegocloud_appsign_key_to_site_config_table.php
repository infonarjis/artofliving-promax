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
        if (!Schema::hasColumn('site_config', 'zegocloud_appsign_key')) {
            Schema::table('site_config', function (Blueprint $table) {
                $table->string('zegocloud_appsign_key', 255)
                    ->nullable()
                    ->after('zego_voice_call_setting');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('site_config', 'zegocloud_appsign_key')) {
            Schema::table('site_config', function (Blueprint $table) {
                $table->dropColumn('zegocloud_appsign_key');
            });
        }
    }
};

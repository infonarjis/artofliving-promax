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
            $table->enum('gemini_api_status', ['APPROVED', 'UNAPPROVED'])
                ->default('UNAPPROVED')
                ->after('gemini_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_config', function (Blueprint $table) {
            $table->dropColumn('gemini_api_status');
        });
    }
};

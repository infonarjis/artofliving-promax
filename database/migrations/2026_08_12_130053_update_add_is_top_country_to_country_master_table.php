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
        Schema::table('country_master', function (Blueprint $table) {
            $table->boolean('is_top_country')
                ->default(false)
                ->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_master', function (Blueprint $table) {
            $table->dropColumn('is_top_country');
        });
    }
};

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
        Schema::table('lead_generations', function (Blueprint $table) {
            $table->time('followup_time')
                  ->nullable()
                  ->default(null)
                  ->after('followup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_generations', function (Blueprint $table) {
            $table->dropColumn('followup_time');
        });
    }
};

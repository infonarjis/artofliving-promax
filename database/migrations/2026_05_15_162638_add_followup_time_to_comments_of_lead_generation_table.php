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
        Schema::table('comments_of_lead_generation', function (Blueprint $table) {
            $table->time('next_followup_time')
                  ->nullable()
                  ->default(null)
                  ->after('next_followup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments_of_lead_generation', function (Blueprint $table) {
            $table->dropColumn('next_followup_time');
        });
    }
};

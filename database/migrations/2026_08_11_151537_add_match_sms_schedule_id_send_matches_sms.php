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
            $table->unsignedBigInteger('match_schedule_id')
                ->nullable()
                ->after('id');

            $table->index('match_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('send_matches_sms', function (Blueprint $table) {
            $table->dropIndex(['match_schedule_id']);
            $table->dropColumn('match_schedule_id');
        });
    }
};

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
        Schema::table('match_schedules', function (Blueprint $table) {
            $table->enum('match_sending_mode', ['email', 'sms', 'both'])
                ->default('email')
                ->after('match_criteria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_schedules', function (Blueprint $table) {
            //
        });
    }
};

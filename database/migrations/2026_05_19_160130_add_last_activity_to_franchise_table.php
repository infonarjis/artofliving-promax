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
        Schema::table('franchise', function (Blueprint $table) {
            if (!Schema::hasColumn('franchise', 'last_activity')) {
                $table->dateTime('last_activity')
                    ->nullable()
                    ->after('last_login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franchise', function (Blueprint $table) {
            $table->dropColumn('last_activity');
        });
    }
};

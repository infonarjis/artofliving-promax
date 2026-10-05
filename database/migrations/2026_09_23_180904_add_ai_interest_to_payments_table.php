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
        Schema::table('membership_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('membership_plans', 'ai_interest')) {
                $table->boolean('ai_interest')
                    ->default(false)
                    ->after('can_chat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // if (Schema::hasColumn('membership_plans', 'ai_interest')) {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('ai_interest');
        });
        // }
    }
};

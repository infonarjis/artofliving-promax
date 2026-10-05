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
        Schema::table('member_risk_scores', function (Blueprint $table) {
            $table->text('suspend_reason')->nullable()->after('member_id');
            $table->timestamp('suspended_at')->nullable()->after('last_warning_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_risk_scores', function (Blueprint $table) {
            $table->dropColumn('suspend_reason');
            $table->dropColumn('suspended_at');
        });
    }
};

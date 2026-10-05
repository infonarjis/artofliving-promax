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
        Schema::table('staff_role', function (Blueprint $table) {
            $table->enum('lead_generation_convert_member', ['Yes', 'No'])->default('No')->after('personalized_chat');
            $table->enum('active_to_paid_member', ['Yes', 'No'])->default('No')->after('lead_generation_convert_member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_role', function (Blueprint $table) {
            $table->dropColumn(['lead_generation_convert_member', 'active_to_paid_member',]);
        });
    }
};

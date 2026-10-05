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
        if (! Schema::hasColumn('staff_role', 'personalized_chat')) {
            Schema::table('staff_role', function (Blueprint $table) {
                $table->enum('personalized_chat', ['All Members', 'Own Members', 'No'])
                    ->default('No')
                    ->after('personalized_member');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('staff_role', 'personalized_chat')) {
            Schema::table('staff_role', function (Blueprint $table) {
                $table->dropColumn('personalized_chat');
            });
        }
    }
};

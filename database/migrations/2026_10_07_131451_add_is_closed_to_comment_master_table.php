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
        Schema::table('comment_master', function (Blueprint $table) {
            $table->enum('is_closed', ['Yes', 'No'])
                ->default('No')
                ->after('follow_up_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_master', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });
    }
};

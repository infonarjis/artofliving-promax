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
        Schema::table('notification_templates', function (Blueprint $table) {
            // Remove old module column
            $table->dropColumn('module');

            // Add sender and receiver types
            $table->enum('sender_type', ['User', 'Admin', 'System'])
                ->default('System')
                ->after('description');

            $table->enum('receiver_type', ['User', 'Admin'])
                ->default('User')
                ->after('sender_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['sender_type', 'receiver_type']);

            // Restore old module column
            $table->enum('module', ['User', 'Admin'])
                ->default('User')
                ->after('description');
        });
    }
};

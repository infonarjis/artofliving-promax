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
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')
                ->nullable()
                ->after('id');

            $table->enum('admin_type', ['admin', 'staff', 'franchise'])
                ->nullable()
                ->after('admin_id');

            $table->index(['admin_type', 'admin_id'], 'admin_notifications_admin_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropIndex('admin_notifications_admin_index');
            $table->dropColumn(['admin_id', 'admin_type']);
        });
    }
};

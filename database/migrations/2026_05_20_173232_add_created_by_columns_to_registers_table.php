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
        Schema::table('registers', function (Blueprint $table) {
            $table->enum('created_by', [
                'Admin',
                'Staff',
                'Franchise'
            ])->default('Admin')->after('franchise_assign_date');

            $table->integer('created_by_id')
                ->nullable()
                ->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->dropColumn([
                'created_by',
                'created_by_id',
            ]);
        });
    }
};

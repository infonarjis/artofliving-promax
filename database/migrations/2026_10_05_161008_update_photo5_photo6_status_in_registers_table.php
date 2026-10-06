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
            $table->enum('photo5_status', ['APPROVED', 'UNAPPROVED'])->nullable()->default('UNAPPROVED')->change();
            $table->enum('photo6_status', ['APPROVED', 'UNAPPROVED'])->nullable()->default('UNAPPROVED')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            // Restore the previous column definitions here if needed.
        });
    }
};

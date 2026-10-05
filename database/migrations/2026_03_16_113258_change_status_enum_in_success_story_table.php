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
        Schema::table('success_story', function (Blueprint $table) {
            $table->enum('status', ['UNAPPROVED','APPROVED','PENDING'])
                  ->default('UNAPPROVED')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('success_story', function (Blueprint $table) {
            $table->enum('status', ['UNAPPROVED','APPROVED'])
                  ->default('UNAPPROVED')
                  ->change();
        });
    }
};

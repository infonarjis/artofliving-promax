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
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['dark', 'light']);
            $table->string('variable', 60);   // e.g. primary-color, black-color-1
            $table->string('value', 60);      // e.g. #0d56de, #ffffffb3, rgba(...)
            $table->timestamps();
 
            $table->unique(['mode', 'variable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};

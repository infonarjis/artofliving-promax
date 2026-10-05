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
        Schema::create('app_design_options', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // dashboard_design | profile_card_style | my_profile_design | other_profile_design
            $table->string('design_key')->unique(); // e.g. dashboard_1, card_2 - used by the app to select layout
            $table->string('image');
            $table->enum('status', ['approved', 'pending'])->default('pending');
            $table->timestamps();

            $table->index(['category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_design_options');
    }
};

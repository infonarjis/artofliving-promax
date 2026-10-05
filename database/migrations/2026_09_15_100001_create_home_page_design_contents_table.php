<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Holds the actual field VALUES for every dynamic design, one row per
     * language. All field values live in a single json `data` column, so
     * adding/renaming/removing a field on a design never requires a
     * migration — only a change to home_page_designs.schema.
     *
     * Pattern mirrors the existing HomePageSection lang_id/lang_code setup:
     *  - the default-language row has lang_id = NULL
     *  - a translated row has lang_id = <id of the default-language row>
     */
    public function up(): void
    {
        Schema::create('home_page_design_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained('home_page_designs')->cascadeOnDelete();
            $table->unsignedBigInteger('lang_id')->nullable();
            $table->string('lang_code');
            $table->json('data')->nullable();
            $table->string('status')->default('APPROVED');
            $table->timestamps();

            $table->unique(['design_id', 'lang_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_design_contents');
    }
};

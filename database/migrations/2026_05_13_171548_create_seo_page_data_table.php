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
        Schema::create('seo_page_data', function (Blueprint $table) {
            $table->id();

            // IDENTIFICATION
            $table->string('page_slug')->index(); // home, login, blog, blog-detail
            $table->enum('status', ['APPROVED', 'UNAPPROVED'])->default('APPROVED');

            // BASIC
            $table->string('page_title')->nullable(); // Admin reference
            $table->string('h1_title')->nullable();

            // META
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            // TECHNICAL SEO
            $table->string('canonical_url')->nullable();
            $table->string('meta_robots')->default('index,follow');

            // OPEN GRAPH
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');

            // TWITTER
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            // EXTRA
            $table->string('breadcrumb_title')->nullable();
            $table->json('schema_json')->nullable();

            // SITEMAP
            $table->float('sitemap_priority')->default(0.8);
            $table->string('sitemap_changefreq')->default('weekly');

            // REDIRECT
            $table->string('redirect_url')->nullable();

            // LANGUAGE
            $table->string('lang_code', 10)->default('en');
            $table->unsignedBigInteger('lang_id')->nullable();
            $table->string('hreflang_group')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['page_slug', 'lang_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_page_data');
    }
};

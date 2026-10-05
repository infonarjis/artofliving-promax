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
        Schema::table('affiliate_home_page', function (Blueprint $table) {
            $table->string('affiliate_testimonial_title')->nullable()->after('affiliate_feature_sec6_subtitle');
            $table->text('affiliate_testimonial_subtitle')->nullable()->after('affiliate_testimonial_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_home_page', function (Blueprint $table) {
            $table->dropColumn([
                'affiliate_testimonial_title',
                'affiliate_testimonial_subtitle',
            ]);
        });
    }
};

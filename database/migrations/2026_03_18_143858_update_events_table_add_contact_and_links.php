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
         Schema::table('events', function (Blueprint $table) {

            // Add contact fields
            $table->string('contact_number')->nullable()->after('description');
            $table->string('contact_email')->nullable()->after('contact_number');

            // Rename column
            $table->renameColumn('event_google_link', 'event_youtube_link');

            // Add social links
            $table->string('event_instagram_link')->nullable()->after('event_youtube_link');
            $table->string('event_pinterest_link')->nullable()->after('event_instagram_link');

            $table->dropColumn('map_tooltip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // Drop added columns
            $table->dropColumn([
                'contact_number',
                'contact_email',
                'event_instagram_link',
                'event_pinterest_link'
            ]);

            // Rename back
            $table->renameColumn('event_youtube_link', 'event_google_link');

            $table->text('map_tooltip')->nullable();
        });
    }
};

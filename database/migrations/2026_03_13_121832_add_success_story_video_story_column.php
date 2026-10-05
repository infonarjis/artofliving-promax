<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('success_story', function (Blueprint $table) {
            // Add new columns
            $table->enum('story_type', ['Video Story', 'Photo Story'])->nullable()->after('id');
            $table->enum('video_type', ['video', 'youtube'])->nullable()->after('story_type');

            // Rename weddingphoto only if it exists
            if (Schema::hasColumn('success_story', 'weddingphoto')) {
                $table->renameColumn('weddingphoto', 'wedding_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('success_story', function (Blueprint $table) {
            $table->dropColumn(['story_type', 'video_type']);

            if (Schema::hasColumn('success_story', 'wedding_file')) {
                $table->renameColumn('wedding_file', 'weddingphoto');
            }
        });
    }
};
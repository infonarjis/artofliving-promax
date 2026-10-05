<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `admin_alerts`
            MODIFY `member_register`
            ENUM('Read', 'Unread')
            NOT NULL DEFAULT 'Read'
        ");

        DB::statement("
            ALTER TABLE `admin_alerts`
            MODIFY `photo_upload`
            ENUM('Read', 'Unread')
            NOT NULL DEFAULT 'Read'
        ");

        DB::statement("
            ALTER TABLE `admin_alerts`
            MODIFY `id_proof_upload`
            ENUM('Read', 'Unread')
            NOT NULL DEFAULT 'Read',
            MODIFY `horoscope_upload`
            ENUM('Read', 'Unread')
            NULL DEFAULT 'Read',
            MODIFY `delete_profile_request`
            ENUM('Read', 'Unread')
            NOT NULL DEFAULT 'Read',
            MODIFY `affiliate_member`
            ENUM('Read', 'Unread')
            NOT NULL DEFAULT 'Read'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_alerts', function (Blueprint $table) {
            //
        });
    }
};

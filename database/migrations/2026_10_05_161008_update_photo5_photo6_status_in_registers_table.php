<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First convert existing TINYINT values to temporary valid text values
        DB::statement("
            ALTER TABLE registers
            MODIFY photo5_status VARCHAR(20) NULL
        ");

        DB::statement("
            UPDATE registers
            SET photo5_status = CASE
                WHEN photo5_status = '1' THEN 'APPROVED'
                ELSE 'UNAPPROVED'
            END
        ");

        // Now convert VARCHAR to ENUM
        DB::statement("
            ALTER TABLE registers
            MODIFY photo5_status
            ENUM('APPROVED', 'UNAPPROVED')
            NULL
            DEFAULT 'UNAPPROVED'
        ");
    }

    public function down(): void
    {
        // Convert ENUM back to TINYINT
        DB::statement("
            ALTER TABLE registers
            MODIFY photo5_status TINYINT(1) NULL DEFAULT 0
        ");
    }
};
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
        // MySQL doesn't support altering enum values directly via Schema::table(),
        // so we modify the column with raw SQL.
        DB::statement("ALTER TABLE `express_interest` MODIFY `send_type` ENUM('AI', 'Manual', 'Reminder') NOT NULL DEFAULT 'Manual'");
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // NOTE: if any rows have send_type = 'Reminder' at rollback time,
        // this will fail unless you first update those rows to 'Manual' (or another valid value).
        DB::statement("UPDATE `express_interest` SET `send_type` = 'Manual' WHERE `send_type` = 'Reminder'");
        DB::statement("ALTER TABLE `express_interest` MODIFY `send_type` ENUM('AI', 'Manual') NOT NULL DEFAULT 'Manual'");
    }
};

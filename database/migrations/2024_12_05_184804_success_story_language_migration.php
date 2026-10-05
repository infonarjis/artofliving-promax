<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuccessStoryLanguageMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('success_story', function (Blueprint $table) {
            if (!Schema::hasColumn('success_story', 'lang_code')) {
                $table->string('lang_code', 10)->nullable();
            }
            if (!Schema::hasColumn('success_story', 'lang_id')) {
                $table->unsignedBigInteger('lang_id')->nullable();
            }
            DB::statement("ALTER TABLE `success_story` MODIFY `successmessage` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            DB::statement("ALTER TABLE `success_story` MODIFY `bridename` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            DB::statement("ALTER TABLE `success_story` MODIFY `groomname` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

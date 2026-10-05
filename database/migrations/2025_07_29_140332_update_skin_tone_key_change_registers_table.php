<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSkinToneKeyChangeRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('registers', 'skin_tone') && !Schema::hasColumn('registers', 'complexion')) {
            DB::statement("ALTER TABLE `registers` CHANGE `skin_tone` `complexion` VARCHAR(255)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('registers', 'complexion') && !Schema::hasColumn('registers', 'skin_tone')) {
            DB::statement("ALTER TABLE `registers` CHANGE `complexion` `skin_tone` VARCHAR(255)");
        }
    }
}

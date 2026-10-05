<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegisterNoOfMarriedBrotherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            if (!Schema::hasColumn('registers', 'no_of_married_brother')) {
                $table->text('no_of_married_brother')->nullable()->after('no_of_brother');
            }
        });
        Schema::table('registers', function (Blueprint $table) {
            if (!Schema::hasColumn('registers', 'no_of_married_sister')) {
                $table->text('no_of_married_sister')->nullable()->after('no_of_sister');
            }
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

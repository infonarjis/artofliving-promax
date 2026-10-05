<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHomepageSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_page_section', function (Blueprint $table) {
            if (!Schema::hasColumn('home_page_section', 'homepage1_banner_title3')) {
                $table->text('homepage1_banner_title3')->nullable()->after('id');
            }
        });

        Schema::table('home_page_section', function (Blueprint $table) {
            if (!Schema::hasColumn('home_page_section', 'homepage1_banner_subtitle3')) {
                $table->text('homepage1_banner_subtitle3')->nullable()->after('homepage1_banner_title3');
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
        Schema::table('home_page_section', function (Blueprint $table) {
            if (Schema::hasColumn('home_page_section', 'homepage1_banner_title3')) {
                $table->dropColumn('homepage1_banner_title3');
            }
            if (Schema::hasColumn('home_page_section', 'homepage1_banner_subtitle3')) {
                $table->dropColumn('homepage1_banner_subtitle3');
            }
        });
    }
}

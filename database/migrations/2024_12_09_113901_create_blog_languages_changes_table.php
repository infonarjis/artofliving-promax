<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogLanguagesChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_master', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_master', 'lang_code')) {
                $table->string('lang_code', 10)->nullable()->after('blog_image');
            }
            if (!Schema::hasColumn('blog_master', 'lang_id')) {
                $table->unsignedBigInteger('lang_id')->nullable()->after('lang_code');
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
        Schema::dropIfExists('blog_languages_changes');
    }
}

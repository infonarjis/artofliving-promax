<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateAllMastersAddLanguageColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    protected array $tables = [
        'religion'             => 'religion_name',
        'caste'                => 'caste_name',
        'country_master'       => 'country_name',
        'state_master'         => 'state_name',
        'city_master'          => 'city_name',
        'occupation'           => 'occupation_name',
        'education_master'     => 'education_name',
        'designation_master'   => 'designation_name',
        'employee_master'      => 'employee_name',
        'mothertongue'         => 'mtongue_name',
        'star'                 => 'star_name',
        'moonsign'             => 'moonsign_name',
        'annual_income_master' => 'annual_income_name',
    ];

    public function up()
    {
        foreach ($this->tables as $tableName => $columnName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'lang_code')) {
                        $table->string('lang_code', 10)->default('en')->nullable()->before('is_deleted');
                    }
                    if (!Schema::hasColumn($tableName, 'lang_id')) {
                        $table->unsignedBigInteger('lang_id')->default(1)->nullable()->after('lang_code');
                    }
                });

                // Convert the main column to utf8mb4 safely
                $column = $this->tables[$tableName];
                DB::statement("ALTER TABLE `{$tableName}` MODIFY `{$column}` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName => $columnName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'lang_code')) {
                        $table->dropColumn('lang_code');
                    }
                    if (Schema::hasColumn($tableName, 'lang_id')) {
                        $table->dropColumn('lang_id');
                    }
                });

                // Revert to utf8 if needed
                $column = $this->tables[$tableName];
                DB::statement("ALTER TABLE `{$tableName}` MODIFY `{$column}` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci");
            }
        }

    }
}

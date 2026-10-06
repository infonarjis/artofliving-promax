<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaticMasterMigrationController extends Controller
{
    /**
     * Seeds every static master table from the arrays defined in staticMasters().
     * Safe to run again: each table is cleared and re-filled.
     */
    public function databaseMigration(): JsonResponse
    {
        $result = [];

        try {
            $masters = $this->staticMasters();

            /**
             * STEP 1: CHECK ALL TABLES / COLUMNS EXIST BEFORE TOUCHING DATA
             */
            $missing = [];
            foreach ($masters as $key => $master) {
                if (!Schema::hasTable($master['table'])) {
                    $missing[] = "{$master['table']} (table missing)";
                } elseif (!Schema::hasColumn($master['table'], $master['column'])) {
                    $missing[] = "{$master['table']}.{$master['column']} (column missing)";
                }
            }

            if (!empty($missing)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Create / rename these tables or columns first, or fix the names in staticMasters().',
                    'missing' => $missing,
                ], 422);
            }

            /**
             * STEP 2: CLEAR TABLES
             * (TRUNCATE causes an implicit commit, so it runs before the transaction)
             */
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($masters as $master) {
                DB::table($master['table'])->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            /**
             * STEP 3: INSERT ALL INSIDE ONE TRANSACTION
             */
            DB::transaction(function () use ($masters, &$result) {
                foreach ($masters as $key => $master) {
                    $table      = $master['table'];
                    $hasStatus  = Schema::hasColumn($table, 'status');
                    $hasLang    = Schema::hasColumn($table, 'lang_code') && Schema::hasColumn($table, 'lang_id');
                    $rows       = [];

                    foreach (array_values($master['values']) as $index => $name) {
                        $row = [
                            'id'                => $index + 1,
                            $master['column']   => $name,
                            'created_at'        => now(),
                        ];

                        if ($hasLang) {
                            $row['lang_code'] = 'en';
                            $row['lang_id']   = 1;
                        }

                        if ($hasStatus) {
                            $row['status'] = 'APPROVED';
                        }

                        $rows[] = $row;
                    }

                    DB::table($table)->insert($rows);
                    $result[$key] = count($rows);
                }
            });

            return response()->json([
                'status'   => true,
                'message'  => 'Static masters migrated successfully.',
                'migrated' => $result,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * ONE PLACE TO EDIT: key => table, name column, values.
     * Table / column names follow your existing naming pattern;
     * change them here if your actual names differ.
     */
    private function staticMasters(): array
    {
        return [
            'marital_status' => [
                'table'  => 'marital_status_masters',
                'column' => 'marital_status_name',
                'values' => [
                    'Never Married' => 'Never Married',
                    'Annulled' => 'Annulled',
                    'Divorced' => 'Divorced',
                    'Divorced with Kids' => 'Divorced with Kids',
                    'Widowed' => 'Widowed',
                    'Widowed with Kids' => 'Widowed with Kids',
                    'Separated' => 'Separated',
                    'Separated with Kids' => 'Separated with Kids'
                ],
            ],
            'status_children' => [
                'table'  => 'status_child_masters',
                'column' => 'status_child_name',
                'values' => [
                    'Living with me',
                    'Not living with me',
                    'I have no children',
                ],
            ],
            'manglik' => [
                'table'  => 'manglik_masters',
                'column' => 'manglik_name',
                'values' => array('No' => 'No', 'Yes' => 'Yes', 'Maybe' => 'Maybe', 'Anshik' => 'Anshik'),
            ],
            'diet' => [
                'table'  => 'eating_habit_masters',
                'column' => 'eating_habit_name',
                'values' => [
                    'Vegetarian' => 'Vegetarian',
                    'Non Vegetarian' => 'Non Vegetarian',
                    'Eggetarian' => 'Eggetarian',
                    'Vegan' => 'Vegan'
                ],
            ],
            'smoke' => [
                'table'  => 'smoking_habit_masters',
                'column' => 'smoking_habit_name',
                'values' => ['Yes' => 'Yes', 'No' => 'No', 'Planning to quit' => 'Planning to quit'],
            ],
            'drink' => [
                'table'  => 'drinking_habit_masters',
                'column' => 'drinking_habit_name',
                'values' => [
                    'Non-drinker' => 'Non-drinker',
                    'Occasionally' => 'Occasionally',
                    'Socially' => 'Socially',
                    'Regularly' => 'Regularly',
                    'Planning to quit' => 'Planning to quit'
                ],
            ],
            'profileby' => [
                'table'  => 'profileby_masters',
                'column' => 'profileby_name',
                'values' => array(
                    'Self' => 'Self',
                    'Parents' => 'Parents',
                    'Guardian' => 'Guardian',
                    'Friends' => 'Friends',
                    'Sibling' => 'Sibling',
                    'Relatives' => 'Relatives'
                ),
            ],
            'family_type' => [
                'table'  => 'family_type_masters',
                'column' => 'family_type_name',
                'values' => array('Nuclear Family' => 'Nuclear Family', 'Joint Family' => 'Joint Family'),
            ],
            'family_status' => [
                'table'  => 'family_status_masters',
                'column' => 'family_status_name',
                'values' => array(
                    'Rich' => 'Rich',
                    'Upper Middle Class' => 'Upper Middle Class',
                    'Middle Class' => 'Middle Class',
                    'Lower Middle Class' => 'Lower Middle Class',
                    'Poor Family' => 'Poor Family'
                ),
            ],
            'no_of_brothers' => [
                'table'  => 'no_of_bro_sis_masters',
                'column' => 'no_of_bro_sis_name',
                'values' => array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '4 +' => '4 +'),
            ],
            'no_of_married_brother' => [
                'table'  => 'married_bro_masters',
                'column' => 'married_bro_name',
                'values' => array(
                    'No married brother' => 'No married brother',
                    'One married brother' => 'One married brother',
                    'Two married brothers' => 'Two married brothers',
                    'Three married brothers' => 'Three married brothers',
                    'Four married brothers' => 'Four married brothers',
                    'Above four married brothers' => 'Above four married brothers'
                ),
            ],
            'no_of_married_sister' => [
                'table'  => 'married_sis_masters',
                'column' => 'married_sis_name',
                'values' => array(
                    'No married sister' => 'No married sister',
                    'One married sister' => 'One married sister',
                    'Two married sisters' => 'Two married sisters',
                    'Three married sisters' => 'Three married sisters',
                    'Four married sisters' => 'Four married sisters',
                    'Above four married sisters' => 'Above four married sisters'
                ),
            ],
        ];
    }
}

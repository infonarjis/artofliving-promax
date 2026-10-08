<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationMasterMigrationController extends Controller
{
    /**
     * Old `highest_qualification_master` (mysql_old, latin1)  ->  new `education_master` (utf8mb4)
     *
     * Old ids are kept, so members' qualification ids keep pointing to the same names.
     *
     *   /migrate/education-master             empties education_master first, then copies the old rows
     *   /migrate/education-master?truncate=0  keeps the rows already in education_master; rows with the same id
     *                                         are overwritten, the others stay
     */
    public function educationMaster(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Education master migrated successfully.',
                'migrated' => $this->migrate($request->query('truncate', '1') !== '0'),
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

    private function migrate(bool $truncate): array
    {
        if ($truncate) {
            // TRUNCATE causes an implicit commit, so it runs before the transaction
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('education_master')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $now = now();

        // the name is latin1 in the old table: same latin1 -> utf8mb4 repair as the other tables
        $olds = DB::connection('mysql_old')
            ->table('highest_qualification_master')
            ->select('highest_qualification_master.*')
            ->selectRaw('CONVERT(CAST(CONVERT(highest_qualification_name USING latin1) AS BINARY) USING utf8mb4) AS name_fixed')
            ->orderBy('id')
            ->get();

        $usedIds   = [];
        $withId    = [];
        $withoutId = [];
        $noName    = 0;

        foreach ($olds as $o) {
            $name = $this->nz($o->name_fixed);
            if ($name === null) {
                $noName++;
            }

            $row = [
                'status'         => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                'education_name' => $name === null ? null : mb_substr($name, 0, 255),
                'lang_code'      => 'en',
                'lang_id'        => 1,
                // old is_deleted = 'Yes' -> soft delete
                'deleted_at'     => $o->is_deleted === 'Yes' ? $now : null,
            ];

            $id = (int) $o->id;
            if ($id > 0 && !isset($usedIds[$id])) {
                $usedIds[$id] = true;
                $withId[]     = ['id' => $id] + $row;
            } else {
                $withoutId[] = $row;
            }
        }

        DB::transaction(function () use ($withId, $withoutId) {
            foreach ($withId as $row) {
                DB::table('education_master')->updateOrInsert(['id' => $row['id']], $row);
            }
            // inserted last so auto-increment ids continue after the highest kept id
            foreach ($withoutId as $row) {
                DB::table('education_master')->insert($row);
            }
        });

        $migrated = count($withId) + count($withoutId);

        $result = [
            'old_total'      => $olds->count(),
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
            'truncated'      => $truncate,
        ];

        if ($noName) {
            $result['rows_without_name'] = $noName;
        }

        if ($migrated !== $olds->count()) {
            $result['warning'] = 'migrated does not match the old table count.';
        }

        return $result;
    }

    /** '' / whitespace / null -> null */
    private function nz($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
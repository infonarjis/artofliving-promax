<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeadGenerationMigrationController extends Controller
{
    private const CHUNK = 500;

    /**
     * Old `leads_generation` (mysql_old, latin1)  ->  new `lead_generations` (utf8mb4)
     *
     * Run it BEFORE the lead comments migration: comments point to lead_generations.id,
     * and this migration keeps the old ids.
     *
     * The new table has no `address` and no `phone_no_4` column. If you add them
     * (string `address` 250, string `phone_no_4` 50) they are filled; otherwise they are
     * skipped and the response says how many rows had data in them.
     */
    public function leadGenerations(): JsonResponse
    {
        set_time_limit(0);

        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Lead generations migrated successfully.',
                'migrated' => $this->migrate(),
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

    private function migrate(): array
    {
        $hasAddress = Schema::hasColumn('lead_generations', 'address');
        $hasPhone4  = Schema::hasColumn('lead_generations', 'phone_no_4');

        // TRUNCATE causes an implicit commit, so it runs before the insert transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('lead_generations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('leads_generation')->count();
        $now      = now();

        $usedIds           = [];
        $batch             = [];  // rows with their old id, inserted as the table is read
        $withoutId         = [];  // old id 0 / duplicate -> inserted last, the new table assigns an id
        $migrated          = 0;
        $followupText      = 0;   // followup_date was not a date -> kept as the original text
        $addressNotKept    = 0;
        $phone4NotKept     = 0;

        /**
         * One pass over EVERY old row (no id based paging).
         * username / address are latin1 in the old table, so they get the same
         * latin1 -> utf8mb4 repair used for the other tables.
         */
        $olds = DB::connection('mysql_old')
            ->table('leads_generation')
            ->select('leads_generation.*')
            ->selectRaw($this->fixLatin1('username') . ' AS username_fixed')
            ->selectRaw($this->fixLatin1('address') . ' AS address_fixed')
            ->orderBy('id')
            ->cursor();

        DB::beginTransaction();

        try {
            foreach ($olds as $o) {
                [$followupDate, $followupTime, $followupReadable] = $this->followup($o->followup_date);
                if (!$followupReadable) {
                    $followupText++;
                }

                $regDate = $this->clean($o->reg_date);

                $row = [
                    'username'              => $this->cut($o->username_fixed, 255),
                    'email'                 => $this->cut($o->email, 100),
                    'gender'                => $this->cut($o->gender, 6),
                    'marital_status'        => null,
                    'phone_no_1'            => $this->cut($o->phone_no_1, 20),
                    'phone_no_2'            => $this->cut($o->phone_no_2, 50),
                    'phone_no_3'            => $this->cut($o->phone_no_3, 50),
                    // old int country id (0 = none) -> new varchar column, the id is kept as text
                    'country'               => ((int) $o->country) > 0 ? (string) (int) $o->country : null,
                    'interest'              => $this->cut($o->interest, 50) ?? 'New Register',
                    'adminrole_id'          => $this->nzInt($o->adminrole_id),
                    'franchised_by'         => $this->nzInt($o->franchised_by),
                    'staff_assign_id'       => $this->cut($o->staff_assign_id, 200),
                    'staff_assign_date'     => $this->clean($o->staff_assign_date),
                    'franchise_assign_id'   => $this->cut($o->franchise_assign_id, 100),
                    'franchise_assign_date' => $this->clean($o->franchise_assign_date),
                    'validate_number'       => in_array($o->validate_number, ['Yes', 'No'], true) ? $o->validate_number : null,
                    // old column name is misspelled: is_registerd
                    'is_registered'         => $o->is_registerd === 'Yes' ? 'Yes' : 'No',
                    'member_matri_id'       => $this->cut($o->member_matri_id, 255),
                    'commented'             => $o->commented === '1' ? '1' : '0',
                    // old is_closed Yes/No -> new lead_status close/open
                    'lead_status'           => $o->is_closed === 'Yes' ? 'close' : 'open',
                    'followup_date'         => $followupDate,
                    'followup_time'         => $followupTime,
                    'created_at'            => $regDate,
                    'updated_at'            => $regDate ?? $now->format('Y-m-d H:i:s'),
                    // old is_deleted = 'Yes' -> soft delete
                    'deleted_at'            => $o->is_deleted === 'Yes' ? $now : null,
                ];

                $address = $this->cut($o->address_fixed, 250);
                if ($hasAddress) {
                    $row['address'] = $address;
                } elseif ($address !== null) {
                    $addressNotKept++;
                }

                $phone4 = $this->cut($o->phone_no_4, 50);
                if ($hasPhone4) {
                    $row['phone_no_4'] = $phone4;
                } elseif ($phone4 !== null) {
                    $phone4NotKept++;
                }

                $id = (int) $o->id;
                if ($id > 0 && !isset($usedIds[$id])) {
                    $usedIds[$id] = true;
                    $batch[]      = ['id' => $id] + $row;

                    if (count($batch) >= self::CHUNK) {
                        DB::table('lead_generations')->insert($batch);
                        $migrated += count($batch);
                        $batch = [];
                    }
                } else {
                    $withoutId[] = $row;
                }
            }

            if ($batch) {
                DB::table('lead_generations')->insert($batch);
                $migrated += count($batch);
            }

            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('lead_generations')->insert($chunk);
                $migrated += count($chunk);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $result = [
            'old_total'      => $oldTotal,
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
        ];

        if ($followupText) {
            $result['followup_date_kept_as_text'] = $followupText;
        }

        if ($addressNotKept) {
            $result['address_not_migrated'] = $addressNotKept . ' rows (add an `address` column to lead_generations and run again)';
        }

        if ($phone4NotKept) {
            $result['phone_no_4_not_migrated'] = $phone4NotKept . ' rows (add a `phone_no_4` column to lead_generations and run again)';
        }

        if ($migrated !== $oldTotal) {
            $result['warning'] = 'migrated does not match the old table count.';
        }

        return $result;
    }

    /**
     * Old followup_date is free text (varchar, often NULL). The new table has followup_date (varchar) + followup_time (time).
     *   NULL / '' / '0000-00-00'  -> [null, null, true]
     *   '2026-10-08'              -> ['2026-10-08', null, true]
     *   '2026-10-08 03:30 PM'     -> ['2026-10-08', '15:30:00', true]
     *   '08-10-2026' (day first)  -> ['2026-10-08', null, true]
     *   anything else             -> [original text, null, false]  (kept so nothing is lost, and counted)
     *
     * @return array{0:?string,1:?string,2:bool}  [date, time, readable]
     */
    private function followup($value): array
    {
        $value = $this->clean($value);
        if ($value === null) {
            return [null, null, true];
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $m)) {
            $date = [(int) $m[1], (int) $m[2], (int) $m[3]];
        } elseif (preg_match('/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/', $value, $m)) {
            $date = [(int) $m[3], (int) $m[2], (int) $m[1]];
        } else {
            return [mb_substr($value, 0, 100), null, false];
        }

        if (!checkdate($date[1], $date[2], $date[0])) {
            return [mb_substr($value, 0, 100), null, false];
        }

        $time = null;
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap]m)?/i', substr($value, strlen($m[0])), $t)) {
            $hour = (int) $t[1];
            if (!empty($t[4])) {
                $hour = ($hour % 12) + (strtolower($t[4]) === 'pm' ? 12 : 0);
            }
            if ($hour < 24 && (int) $t[2] < 60) {
                $time = sprintf('%02d:%02d:%02d', $hour, (int) $t[2], (int) ($t[3] ?? 0));
            }
        }

        return [sprintf('%04d-%02d-%02d', $date[0], $date[1], $date[2]), $time, true];
    }

    /** SQL that repairs text stored as UTF-8 bytes inside a latin1 column */
    private function fixLatin1(string $column): string
    {
        return "CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)";
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

    private function nzInt($value): ?int
    {
        return (is_numeric($value) && (int) $value > 0) ? (int) $value : null;
    }

    /** MySQL zero dates ('0000-00-00') and empty values -> null */
    private function clean($value): ?string
    {
        $value = $this->nz($value);

        return ($value === null || str_starts_with($value, '0000')) ? null : $value;
    }

    private function cut($value, int $length): ?string
    {
        $value = $this->nz($value);

        return $value === null ? null : mb_substr($value, 0, $length);
    }
}

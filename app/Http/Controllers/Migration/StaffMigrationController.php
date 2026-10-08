<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffMigrationController extends Controller
{
    /**
     * Old `staff` (mysql_old, latin1)  ->  new `staff` (utf8mb4)
     *
     * - old `role` is copied to `role_id`, so the roles table must use the same role ids
     * - old `password` holds a bcrypt hash for some staff and the plain text password for the others;
     *   plain text values are hashed so every staff password in the new table is a hash
     * - every new-only column (salary, leave, bank, commission ...) keeps the default of the new table
     * - the new `staff` table is emptied first
     */
    public function staff(): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Staff migrated successfully.',
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
        // TRUNCATE causes an implicit commit, so it runs before the transaction
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('staff')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();

        // username is latin1 in the old table: same latin1 -> utf8mb4 repair as the other tables
        $olds = DB::connection('mysql_old')
            ->table('staff')
            ->select('staff.*')
            ->selectRaw('CONVERT(CAST(CONVERT(username USING latin1) AS BINARY) USING utf8mb4) AS username_fixed')
            ->orderBy('id')
            ->get();

        $usedIds    = [];
        $withId     = [];
        $withoutId  = [];
        $rehashed   = 0;   // plain text password -> hashed
        $noPassword = 0;   // empty password
        $roles      = [];  // role id => staff count (to check against the new roles table)

        foreach ($olds as $o) {
            $createdAt = $this->clean($o->created_on) ?? $now->format('Y-m-d H:i:s');

            [$password, $wasPlain] = $this->password($o->password);
            if ($wasPlain) {
                $rehashed++;
            }
            if ($password === null) {
                $noPassword++;
            }

            $roleId = ((int) $o->role) > 0 ? (int) $o->role : null;
            if ($roleId !== null) {
                $roles[$roleId] = ($roles[$roleId] ?? 0) + 1;
            }

            $row = [
                'role_id'            => $roleId,
                'type'               => 'Staff',
                'username'           => $this->cut($o->username_fixed, 250),
                'email'              => $this->cut($o->email, 250),
                'password'           => $password,
                'password_decrypted' => $this->cut($o->password_decrypted, 255),
                'c_password'         => $this->cut($o->c_password, 255),
                'mobile'             => $this->cut($o->mobile, 50),
                'ip_address'         => $this->cut($o->ip_address, 50),
                'last_login'         => $this->clean($o->last_login),
                'status'             => $o->status === 'APPROVED' ? 'APPROVED' : 'UNAPPROVED',
                'created_at'         => $createdAt,
                'updated_at'         => $createdAt,
                // old is_deleted = 'Yes' -> soft delete
                'deleted_at'         => $o->is_deleted === 'Yes' ? $now : null,
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
            foreach (array_chunk($withId, 100) as $chunk) {
                DB::table('staff')->insert($chunk);
            }
            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, 100) as $chunk) {
                DB::table('staff')->insert($chunk);
            }

            // staff_prefix = 'STF-' + staff id (done after the inserts so rows that got a new id are covered too)
            DB::table('staff')->update(['staff_prefix' => DB::raw("CONCAT('STF-', id)")]);
        });

        $migrated = count($withId) + count($withoutId);

        ksort($roles);

        $result = [
            'old_total'          => $olds->count(),
            'migrated'           => $migrated,
            'ids_reassigned'     => count($withoutId),
            'passwords_hashed'   => $rehashed,
            'staff_by_role_id'   => $roles,
        ];

        if ($noPassword) {
            $result['staff_without_password'] = $noPassword;
        }

        if ($migrated !== $olds->count()) {
            $result['warning'] = 'migrated does not match the old table count.';
        }

        return $result;
    }

    /**
     * bcrypt hash ('$2y$...')  -> kept as it is
     * plain text               -> Hash::make(plain)
     * empty                    -> null
     *
     * @return array{0:?string,1:bool}  [password, wasPlainText]
     */
    private function password($value): array
    {
        $value = $this->nz($value);
        if ($value === null) {
            return [null, false];
        }

        if (preg_match('/^\$2[abxy]\$\d{2}\$/', $value) && strlen($value) === 60) {
            return [$value, false];
        }

        return [Hash::make($value), true];
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

    /** '' / null / MySQL zero date -> null */
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
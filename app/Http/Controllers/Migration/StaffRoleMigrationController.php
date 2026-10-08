<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StaffRoleMigrationController extends Controller
{
    /**
     * Permission columns that exist in the OLD and the NEW table with the same name and the same
     * values (All Members / Own Members / No).
     */
    private const SAME_PERMISSIONS = [
        'view_member', 'edit_member', 'view_profile', 'delete_member', 'approve_member',
        'unapprove_member', 'suspend_member', 'add_comment', 'view_comment',
        'photo_approval', 'photo_delete', 'horoscope_approval', 'horoscope_delete',
        'id_proof_approval', 'id_proof_delete', 'view_lead_generation',
        'lead_generation_add_comment', 'lead_generation_view_comment', 'match_making',
    ];

    /**
     * Old `staff_role` (mysql_old, latin1)  ->  new `staff_role` (utf8mb4)
     *
     * Role ids are kept: staff.role_id points to them. Run this BEFORE the staff migration.
     */
    public function staffRoles(): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Staff roles migrated successfully.',
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
        DB::table('staff_role')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();

        // role_name is latin1 in the old table: same latin1 -> utf8mb4 repair as the other tables
        $olds = DB::connection('mysql_old')
            ->table('staff_role')
            ->select('staff_role.*')
            ->selectRaw('CONVERT(CAST(CONVERT(role_name USING latin1) AS BINARY) USING utf8mb4) AS role_name_fixed')
            ->orderBy('id')
            ->get();

        $rows = [];

        foreach ($olds as $o) {
            $createdAt = $this->clean($o->created_on) ?? $now->format('Y-m-d H:i:s');

            $row = [
                'id'        => (int) $o->id,
                'role_name' => $this->nz($o->role_name_fixed),
                'add_member'          => $this->yesNo($o->add_member, 'Yes'),
                'add_lead_generation' => $this->yesNo($o->add_lead_generation, 'Yes'),
            ];

            // same name, same values
            foreach (self::SAME_PERMISSIONS as $column) {
                $row[$column] = $this->permission($o->{$column});
            }

            // renamed: approve_to_paid_member -> active_to_paid_member
            $row['active_to_paid_member'] = $this->permission($o->approve_to_paid_member);

            // old Yes/No flag -> new All Members / No (one old flag feeds both new columns)
            $bulk = $this->flag($o->send_bulk_email_and_sms);
            $row['send_bulk_email']        = $bulk;
            $row['send_bulk_notification'] = $bulk;

            $personalized = $this->flag($o->personalized_member);
            $row['personalized_member'] = $personalized;

            // new permissions with no old column: taken from the closest old permission
            $row['selfie_photo_approval'] = $row['photo_approval'];   // a selfie is a photo
            $row['selfie_photo_delete']   = $row['photo_delete'];
            $row['personalized_chat']     = $personalized;

            // new permissions with no equivalent at all: least privilege (the column default would grant 'Own Members')
            $row['edit_lead_generation']           = 'No';
            $row['delete_lead_generation']         = 'No';
            $row['lead_generation_convert_member'] = 'No';
            $row['lead_import']                    = 'No';

            $rows[] = $row + [
                'status'     => $o->status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                // old is_deleted = 'Yes' -> soft delete
                'deleted_at' => $o->is_deleted === 'Yes' ? $now : null,
            ];
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('staff_role')->insert($chunk);
            }
        });

        $result = [
            'old_total' => $olds->count(),
            'migrated'  => count($rows),
            'role_ids'  => array_column($rows, 'id'),
        ];

        if (count($rows) !== $olds->count()) {
            $result['warning'] = 'migrated does not match the old table count.';
        }

        return $result;
    }

    /** All Members / Own Members / No  (anything else, e.g. an empty value, becomes 'No') */
    private function permission($value): string
    {
        return in_array($value, ['All Members', 'Own Members'], true) ? $value : 'No';
    }

    /** Yes / No column; $default is used when the old value is not one of them */
    private function yesNo($value, string $default): string
    {
        return in_array($value, ['Yes', 'No'], true) ? $value : $default;
    }

    /** old Yes/No flag -> new All Members / No */
    private function flag($value): string
    {
        return $value === 'Yes' ? 'All Members' : 'No';
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
}
<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MembershipPlanMigrationController extends Controller
{
    /**
     * Old table location. Change these if the old table lives in another
     * database connection (defined in config/database.php) or has another name.
     * null = default connection.
     */
    private ?string $oldConnection = 'mysql_old';
    private string $oldTable = 'membership_plan';
    private string $newTable = 'membership_plans';

    /**
     * Copies every row of the old `membership_plan` table into `membership_plans`.
     * Safe to run again: the new table is cleared and re-filled.
     */
    public function databaseMigration(): JsonResponse
    {
        try {
            $old = DB::connection($this->oldConnection);

            /**
             * STEP 1: CHECK TABLES / COLUMNS EXIST BEFORE TOUCHING DATA
             */
            $missing = [];

            if (!Schema::connection($this->oldConnection)->hasTable($this->oldTable)) {
                $missing[] = "{$this->oldTable} (old table missing)";
            }
            if (!Schema::hasTable($this->newTable)) {
                $missing[] = "{$this->newTable} (new table missing - run php artisan migrate)";
            } else {
                foreach (array_keys($this->emptyRow()) as $column) {
                    if (!Schema::hasColumn($this->newTable, $column)) {
                        $missing[] = "{$this->newTable}.{$column} (column missing)";
                    }
                }
            }

            if (!empty($missing)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Create these tables or columns first, or fix the names at the top of the controller.',
                    'missing' => $missing,
                ], 422);
            }

            /**
             * STEP 2: READ OLD DATA AND BUILD NEW ROWS (before clearing anything)
             */
            $oldRows = $old->table($this->oldTable)->orderBy('id')->get();

            if ($oldRows->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => "Old table {$this->oldTable} has no rows. Nothing was changed.",
                ], 422);
            }

            $rows = $oldRows->map(fn($r) => $this->mapRow($r))->all();

            /**
             * STEP 3: CLEAR NEW TABLE
             * (TRUNCATE causes an implicit commit, so it runs before the transaction)
             */
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table($this->newTable)->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            /**
             * STEP 4: INSERT ALL INSIDE ONE TRANSACTION
             */
            DB::transaction(function () use ($rows) {
                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table($this->newTable)->insert($chunk);
                }
            });

            return response()->json([
                'status'   => true,
                'message'  => 'Membership plans migrated successfully.',
                'migrated' => [
                    'total'   => count($rows),
                    'deleted' => collect($rows)->whereNotNull('deleted_at')->count(),
                ],
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
     * ONE PLACE TO EDIT: old column => new column mapping lives here.
     */
    private function mapRow(object $r): array
    {
        $createdAt = $r->created_on ?: now();
        $isYes     = fn($v) => strtolower((string) $v) === 'yes';
        $text      = fn($v) => (isset($v) && trim($v) !== '') ? trim($v) : null;

        return [
            'id'        => $r->id,
            'plan_name' => $r->plan_name ?? '',
            'plan_type' => in_array($r->plan_type, ['PAID', 'FREE'], true) ? $r->plan_type : 'PAID',
            'plan_amount' => $r->plan_amount ?? 0,

            // Android in-app purchase (old table had a single set, no iOS)
            'in_app_purchase_android_id'     => $text($r->in_app_product_id),
            'in_app_purchase_android_amount' => $r->in_app_price,
            'in_app_purchase_ios_id'         => null,
            'in_app_purchase_ios_amount'     => null,

            'plan_discount'    => 0,
            'plan_description' => $text($r->plan_offers),
            'currency_code'    => strtoupper(substr($r->plan_amount_type ?: 'INR', 0, 3)),
            'validity_days'    => $r->plan_validity ?? 0,

            'interests_limit'     => $r->plan_connect ?? 0,
            'contact_views_limit' => $r->plan_view_contact ?? 0,
            'video_minutes_limit' => 0,
            'audio_minutes_limit' => 0,
            'view_profile_limit'  => 0,

            'can_chat'        => $isYes($r->chat) ? 1 : 0,
            'ai_interest'     => 0,
            'is_personalized' => ($isYes($r->is_assisted) || $isYes($r->receive_suggested_matches)) ? 1 : 0,

            'status'     => $r->status === 'APPROVED' ? 'APPROVED' : 'UNAPPROVED',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'deleted_at' => $isYes($r->is_deleted) ? now() : null,
        ];
    }

    /**
     * Column list of the new table, used only for the existence check in step 1.
     */
    private function emptyRow(): array
    {
        return array_fill_keys([
            'id',
            'plan_name',
            'plan_type',
            'plan_amount',
            'in_app_purchase_android_id',
            'in_app_purchase_android_amount',
            'in_app_purchase_ios_id',
            'in_app_purchase_ios_amount',
            'plan_discount',
            'plan_description',
            'currency_code',
            'validity_days',
            'interests_limit',
            'contact_views_limit',
            'video_minutes_limit',
            'audio_minutes_limit',
            'view_profile_limit',
            'can_chat',
            'ai_interest',
            'is_personalized',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ], null);
    }
}

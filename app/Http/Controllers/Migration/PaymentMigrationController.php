<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentMigrationController extends Controller
{
    /** Keep (rows x columns) under the 65,535 placeholder limit of one INSERT. */
    private const CHUNK = 200;

    /** New plan master used to find plan_id from the old plan NAME: [table, name column] */
    private const PLAN_TABLE = ['plans', 'plan_name'];

    /**
     * Old plan name (lower case) -> plan_id in the new plan master.
     * Add every name listed under "unmatched_plans" in the response here, e.g. 'platinum' => 4
     */
    private const PLAN_ALIASES = [
        // 'platinum' => 4,
    ];

    /**
     * Old `payments` (mysql_old)  ->  new `payments`
     *
     * Run the registers migration first: member_id is found through registers.matri_id.
     */
    public function payments(): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Payments migrated successfully.',
                'migrated' => $this->migratePayments(),
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

    private function migratePayments(): array
    {
        /* ---- lookups ---- */
        $members = $this->loadMembers();
        $plans   = $this->loadPlans();

        /* ---- clear the new table (TRUNCATE causes an implicit commit, so it runs first) ---- */
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('payments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('payments')->count();
        $now      = now();

        $withId          = [];  // old id > 0 and not used before -> keep the id
        $withoutId       = [];  // id 0 / duplicate               -> new table assigns one
        $usedIds         = [];
        $skippedNoMember = [];  // matri_id not found in the new registers
        $skippedNoDates  = 0;   // neither a usable activation nor expiry date
        $unmatchedPlans  = [];  // old plan name => rows

        /**
         * One pass over EVERY old row (no id based paging, so rows with id 0 / duplicate ids are not lost).
         * The three free-text columns are latin1 in the old table, so they get the same
         * latin1 -> utf8mb4 repair that was used for the other masters.
         */
        $olds = DB::connection('mysql_old')
            ->table('payments')
            ->select('payments.*')
            ->selectRaw($this->fixLatin1('payment_note') . ' AS payment_note_fixed')
            ->selectRaw($this->fixLatin1('bank_detail') . ' AS bank_detail_fixed')
            ->selectRaw($this->fixLatin1('discount_detail') . ' AS discount_detail_fixed')
            ->orderBy('id')
            ->cursor();

        foreach ($olds as $o) {
            /* ---- member ---- */
            $member = $members[mb_strtolower(trim((string) $o->matri_id))] ?? null;
            if ($member === null) {
                $skippedNoMember[] = $this->nz($o->matri_id) ?? '(empty)';
                continue;
            }

            /* ---- dates / validity ---- */
            $duration  = (is_numeric($o->plan_duration) && (int) $o->plan_duration > 0) ? (int) $o->plan_duration : null;
            $activated = $this->clean($o->plan_activated);
            $expired   = $this->clean($o->plan_expired);

            if ($activated === null && $expired !== null && $duration !== null) {
                $activated = date('Y-m-d', strtotime("{$expired} -{$duration} days"));
            }
            if ($expired === null && $activated !== null && $duration !== null) {
                $expired = date('Y-m-d', strtotime("{$activated} +{$duration} days"));
            }
            if ($activated === null || $expired === null) {
                $skippedNoDates++;
                continue;
            }
            $duration ??= max(0, (int) round((strtotime($expired) - strtotime($activated)) / 86400));

            /* ---- plan ---- */
            $planId = $this->resolvePlanId($o->plan_name, $member, $plans);
            if ($planId === 0) {
                $label = $this->nz($o->plan_name) ?? '(empty plan name)';
                $unmatchedPlans[$label] = ($unmatchedPlans[$label] ?? 0) + 1;
            }

            /* ---- amounts ---- */
            $amount   = round((float) $o->plan_amount, 2);
            $tax      = round((float) $o->tax_amount, 2);
            $discount = round((float) $o->discount_amount, 2);
            $grand    = round((float) $o->grand_total, 2);
            // old rows often have grand_total = 0 although plan_amount is set
            if ($grand <= 0 && $amount > 0) {
                $grand = max(0, round($amount + $tax - $discount, 2));
            }

            /* ---- note: old payment_note + bank_detail (+ in-app price) in one text ---- */
            $notes = [];
            if (($v = $this->nz($o->payment_note_fixed)) !== null) {
                $notes[] = $v;
            }
            if (($v = $this->nz($o->bank_detail_fixed)) !== null) {
                $notes[] = "Bank detail: {$v}";
            }
            if ((float) $o->in_app_price > 0) {
                $notes[] = 'In-app price: ' . $this->num($o->in_app_price);
            }

            $interests    = $this->int($o->plan_connect);
            $contactViews = $this->int($o->plan_view_contacts);

            $row = [
                'member_id'                => $member['id'],
                'purchase_token'           => $this->bin($o->org_transaction_id),
                'plan_id'                  => $planId,
                'previous_payment_id'      => null,
                'plan_type'                => null,
                'plan_name'                => $this->cut($o->plan_name, 255),
                'plan_discount'            => null,
                'plan_description'         => $this->nz($o->plan_offers),
                'plan_activate_date'       => $activated,
                'plan_expiry_date'         => $expired,
                'auto_renewing'            => 0,
                'plan_validity_days'       => $duration,
                'addon_validity_days'      => 0,
                'carried_forward_days'     => 0,
                'total_validity_days'      => $duration,

                // old plan_connect / connect_used -> interests
                'plan_interest'            => $interests,
                'addon_interest'           => 0,
                'carried_forward_interest' => 0,
                'interests_total'          => $interests,
                'interests_used'           => $this->int($o->connect_used),

                // old plan_view_contacts / view_contacts_used -> contact views
                'plan_contact_views'       => $contactViews,
                'addon_contact_views'      => 0,
                'carried_forward_contact_views' => 0,
                'contact_views_total'      => $contactViews,
                'contact_views_used'       => $this->int($o->view_contacts_used),

                'can_chat'                 => $o->chat === 'Yes' ? 1 : 0,
                'ai_interest'              => 0,
                'is_personalized'          => 0,

                'payment_mode'             => $this->cut($o->payment_mode, 50),
                'payment_received_from'    => $this->cut($o->name, 100),
                'transaction_id'           => $this->cut($this->bin($o->transaction_id), 255),
                'plan_amount'              => $amount,
                'currency_code'            => $this->currency($o->currency),
                'tax_name'                 => $this->cut($o->tax_name, 255),
                'tax_percentage'           => $o->tax_percentage === null ? null : $this->num($o->tax_percentage),
                'tax_amount'               => $tax,
                'coupon_id'                => null,
                'discount_detail'          => $this->cut($o->discount_detail_fixed, 255),
                'discount_amount'          => $discount,
                'grand_total'              => $grand,

                'franchise_id'             => $this->nzInt($o->franchise_id),
                'franchise_comm_per'       => round((float) $o->franchise_comm_per, 2),
                'franchise_comm_amt'       => round((float) $o->franchise_comm_amt, 2),
                'staff_id'                 => null,
                'staff_comm_per'           => 0,
                'staff_comm_amt'           => 0,

                'current_plan'             => $o->current_plan === 'Yes' ? 'Yes' : 'No',
                'is_renewal'               => 'No',
                'payment_note'             => $notes ? implode("\n", $notes) : null,
                // old UNAPPROVED / APPROVED -> new PENDING / SUCCESS
                'status'                   => $o->status === 'APPROVED' ? 'SUCCESS' : 'PENDING',
                'assign_by'                => 'Admin',

                'created_at'               => $activated . ' 00:00:00',
                'updated_at'               => $activated . ' 00:00:00',
                'deleted_at'               => $o->is_deleted === 'Yes' ? $now : null,
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
            foreach (array_chunk($withId, self::CHUNK) as $chunk) {
                DB::table('payments')->insert($chunk);
            }
            // inserted last so auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, self::CHUNK) as $chunk) {
                DB::table('payments')->insert($chunk);
            }
        });

        $migrated = count($withId) + count($withoutId);
        $skipped  = count($skippedNoMember) + $skippedNoDates;

        $result = [
            'old_total'      => $oldTotal,
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
            'skipped'        => $skipped,
        ];

        if ($skippedNoMember) {
            $result['skipped_no_member'] = [
                'count'            => count($skippedNoMember),
                'matri_id_sample'  => array_slice(array_values(array_unique($skippedNoMember)), 0, 50),
            ];
        }

        if ($skippedNoDates) {
            $result['skipped_no_dates'] = $skippedNoDates;
        }

        if ($unmatchedPlans) {
            arsort($unmatchedPlans);
            $result['unmatched_plans'] = $unmatchedPlans;
        }

        if ($migrated + $skipped !== $oldTotal) {
            $result['warning'] = 'migrated + skipped does not match the old table count.';
        }

        return $result;
    }

    /* ----------------------------------------------------------------
     |  LOOKUPS
     * ---------------------------------------------------------------- */

    /** matri_id (lower case) => ['id' => registers.id, 'plan_id' => ..., 'plan_name' => ...] */
    private function loadMembers(): array
    {
        $members = [];

        DB::table('registers')
            ->select('id', 'matri_id', 'plan_id', 'plan_name')
            ->whereNotNull('matri_id')
            ->orderBy('id')
            ->each(function ($r) use (&$members) {
                $members[mb_strtolower(trim((string) $r->matri_id))] = [
                    'id'        => (int) $r->id,
                    'plan_id'   => (int) $r->plan_id,
                    'plan_name' => mb_strtolower(trim((string) $r->plan_name)),
                ];
            }, 1000);

        if (!$members) {
            throw new Exception('The new `registers` table is empty. Run the registers migration first.');
        }

        return $members;
    }

    /** plan name (lower case) => plan_id, from the new plan master (skipped if that table does not exist) */
    private function loadPlans(): array
    {
        [$table, $column] = self::PLAN_TABLE;

        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return [];
        }

        return DB::table($table)
            ->pluck('id', $column)
            ->mapWithKeys(fn($id, $name) => [mb_strtolower(trim((string) $name)) => (int) $id])
            ->all();
    }

    /**
     * 1. PLAN_ALIASES   2. plan master by name   3. the member's own plan when the name is the same
     * Returns 0 when nothing matches (plan_id is NOT NULL in the new table).
     */
    private function resolvePlanId($planName, array $member, array $plans): int
    {
        $key = mb_strtolower(trim((string) $planName));
        if ($key === '') {
            return 0;
        }

        if (isset(self::PLAN_ALIASES[$key])) {
            return (int) self::PLAN_ALIASES[$key];
        }

        if (isset($plans[$key])) {
            return $plans[$key];
        }

        if ($member['plan_id'] > 0 && $member['plan_name'] === $key) {
            return $member['plan_id'];
        }

        return 0;
    }

    /* ----------------------------------------------------------------
     |  HELPERS
     * ---------------------------------------------------------------- */

    /** SQL that repairs text stored as UTF-8 bytes inside a latin1 column */
    private function fixLatin1(string $column): string
    {
        return "CONVERT(CAST(CONVERT({$column} USING latin1) AS BINARY) USING utf8mb4)";
    }

    /** varbinary -> clean UTF-8 string */
    private function bin($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return $this->nz($value);
    }

    /** old free-text currency -> 3 letter code (default INR) */
    private function currency($value): string
    {
        $value = strtoupper(trim((string) $value));

        if (preg_match('/^[A-Z]{3}$/', $value)) {
            return $value;
        }

        return in_array($value, ['$', 'US$', 'DOLLAR', 'DOLLARS'], true) ? 'USD' : 'INR';
    }

    /** 18.0 -> '18', 12.5 -> '12.5' */
    private function num($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    /** numeric -> int, anything else (text such as 'Unlimited', null) -> 0 */
    private function int($value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function nzInt($value): ?int
    {
        return (is_numeric($value) && (int) $value > 0) ? (int) $value : null;
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

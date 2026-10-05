<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PartnerPreferenceService
{
    public function apply(Builder $q, $p): void
    {
        if (!$p) return;

        $dnm = _getConstant('common_label.DOESNT_MATTER');

        // ---------- Marital ----------
        $this->whereInCsv($q, 'marital_status', $p->part_marital_status, $dnm);

        // ---------- DOB (Age) ----------
        if ($p->part_frm_age && $p->part_to_age) {
            $q->whereBetween('birthdate', [
                Carbon::now()->subYears($p->part_to_age),
                Carbon::now()->subYears($p->part_frm_age),
            ]);
        }

        // ---------- Height ----------
        if ($p->part_height && $p->part_height_to) {
            $q->whereBetween('height', [
                (float) $p->part_height,
                (float) $p->part_height_to
            ]);
        }

        // ---------- Direct Mapping ----------
        $map = [
            'part_religion'     => 'religion',
            'part_caste'        => 'caste',
            'part_country'      => 'country_id',
            'part_state'        => 'state_id',
            'part_mothertongue' => 'mother_tongue',
            'part_income'       => 'income',
            'part_diet'         => 'diet',
            'part_occupation'   => 'occupation',
            'part_manglik'      => 'manglik',
            'part_education'    => 'education_level',
        ];

        foreach ($map as $pf => $db) {
            $this->whereInCsv($q, $db, $p->$pf ?? null, $dnm);
        }
    }

    /* ---------- Helpers ---------- */

    private function whereInCsv(Builder $q, string $field, $raw, string $dnm): void
    {
        $values = $this->parseValues($raw);

        if (empty($values) || in_array($dnm, $values, true)) {
            return; // no preference stated, or "Does Not Matter" -> don't constrain this field
        }

        $q->whereIn($field, $values);
    }

    private function parseValues($raw): array
    {
        if (!$raw) {
            return [];
        }

        if (is_array($raw)) {
            return array_map('trim', $raw);
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return array_map('trim', $decoded);
        }

        return array_map('trim', explode(',', $raw));
    }
}

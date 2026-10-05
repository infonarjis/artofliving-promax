<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * AI compatibility scoring engine.
 *
 * Final match % = blend of:
 *   - explicitScore(): how well the profile matches the member's explicitly
 *     stated partner preference (RegisterPartner), weighted per field with
 *     partial credit for near-miss age/height instead of a hard cutoff.
 *   - BehaviorLearningService::getAffinityScore(): how well the profile
 *     matches what the member has *actually* engaged with (views,
 *     shortlists, interests, blocks), learned automatically over time.
 *
 * This fixes two bugs from the original implementation:
 *   1. All rules were weighted equally (+1 each) — age and religion counted
 *      the same as, say, mother tongue. Now each field has a configurable weight.
 *   2. The 'education' preference was compared against a profile field
 *      literally named 'education' — which doesn't exist on Register
 *      (the real column is 'education_level') — so education never matched.
 *
 * mutualPercent() adds a second dimension: percent()/scoreBreakdown() are
 * one-directional ("would the auth user like this candidate"). Ranking by
 * that alone can surface profiles that would never reciprocate. mutualPercent()
 * combines the forward score with a reverse score (would the candidate like
 * the auth user, judged by the candidate's own preference + behavior) via a
 * harmonic mean, so only matches strong on *both* sides rank highly.
 */
class MatchMakingService
{
    protected array $pref = [];

    /** Relative importance of each explicit-preference field. Tune freely per business rules. */
    protected array $fieldWeights = [
        'age'            => 15,
        'height'         => 8,
        'marital_status' => 10,
        'religion'       => 15,
        'caste'          => 10,
        'mother_tongue'  => 8,
        'country_id'     => 5,
        'state_id'       => 5,
        'education_level' => 10,
        'occupation'     => 6,
        'manglik'        => 8,
    ];

    /** How much weight explicit preference vs. learned behavior gets in the final score. */
    protected float $explicitRatio = 0.75;
    protected float $behaviorRatio = 0.25;

    protected BehaviorLearningService $behaviorService;

    public function __construct(BehaviorLearningService $behaviorService)
    {
        $this->behaviorService = $behaviorService;
    }

    public function setPreference($partnerPref): void
    {
        $this->pref = $this->normalize($partnerPref);
    }

    protected function normalize($pref): array
    {
        if (!$pref) {
            return [];
        }

        return [
            'marital_status' => $this->arr($pref->part_marital_status),
            'religion'       => $this->arr($pref->part_religion),
            'caste'          => $this->arr($pref->part_caste),
            'country_id'     => $this->arr($pref->part_country),
            'state_id'       => $this->arr($pref->part_state),
            'education_level' => $this->arr($pref->part_education),
            'occupation'     => $this->arr($pref->part_occupation),
            'mother_tongue'  => $this->arr($pref->part_mothertongue),
            'manglik'        => $this->arr($pref->part_manglik),
            'age_from'       => $pref->part_frm_age,
            'age_to'         => $pref->part_to_age,
            'height_from'    => $pref->part_height,
            'height_to'      => $pref->part_height_to,
        ];
    }

    protected function arr($val): array
    {
        if (!$val) {
            return [];
        }

        if (is_array($val)) {
            return array_map('trim', $val);
        }

        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_map('trim', $decoded);
        }

        return array_map('trim', explode(',', $val));
    }

    /**
     * Convenience wrapper — final blended match percentage (0-100).
     * Pass $memberId to include the learned-behavior component; omit it
     * (e.g. for anonymous/guest browsing) to fall back to explicit-only scoring.
     */
    public function percent($profile, ?int $memberId = null): int
    {
        return $this->scoreBreakdown($profile, $memberId)['percent'];
    }

    /**
     * Full score breakdown — useful for showing "why this match" in the UI,
     * and for the nightly best-matches job to store alongside the percentage.
     * $prefOverride lets callers score against a preference other than the
     * one set via setPreference() — used by mutualPercent() to score the
     * "reverse" direction (candidate's preference against the auth user's
     * own profile) without permanently swapping the service's active state.
     */
    public function scoreBreakdown($profile, ?int $memberId = null, ?array $prefOverride = null): array
    {
        [$explicitScore, $explicitDetail] = $this->explicitScore($profile, $prefOverride);

        $behaviorScore = $memberId ? $this->behaviorService->getAffinityScore($memberId, $profile) : null;

        $final = $behaviorScore === null
            ? $explicitScore
            : ($explicitScore * $this->explicitRatio) + ($behaviorScore * $this->behaviorRatio);

        return [
            'percent'        => (int) round(max(0, min(100, $final))),
            'explicit_score' => (int) round($explicitScore),
            'behavior_score' => $behaviorScore,
            'detail'         => $explicitDetail,
        ];
    }

    /**
     * Mutual match score: combines "would the auth user like this candidate"
     * (forward) with "would this candidate like the auth user" (reverse)
     * using a harmonic mean, so a lopsided match (e.g. 95 / 40) scores low
     * rather than the misleadingly high ~68 a plain average would give.
     * Harmonic mean is used specifically because it only rewards scores
     * that are high on *both* sides — one weak side drags the result down
     * much harder than a simple average would.
     *
     * @param  Model  $candidateProfile    the candidate being scored, with
     *                                     the categorical/age/height fields selected
     * @param  Model  $authProfileSnapshot the authenticated member's own
     *                                     profile row (same fields), used as
     *                                     the "profile" being judged in reverse
     * @param  mixed  $candidatePartnerPref the candidate's RegisterPartner row (or null)
     * @param  int|null $authMemberId      auth user's id, for their behavioral score
     * @param  int    $candidateMemberId   candidate's id, for their behavioral score
     */
    public function mutualPercent(
        $candidateProfile,
        $authProfileSnapshot,
        $candidatePartnerPref,
        ?int $authMemberId,
        int $candidateMemberId
    ): array {
        // Forward: does the auth user like the candidate? Uses whatever
        // preference is currently active via setPreference().
        $forward = $this->percent($candidateProfile, $authMemberId);

        // Reverse: does the candidate like the auth user? Scored against the
        // candidate's own stated preference, without touching $this->pref.
        $candidatePref = $this->normalize($candidatePartnerPref);
        $reverse = $this->scoreBreakdown($authProfileSnapshot, $candidateMemberId, $candidatePref)['percent'];

        $mutual = ($forward + $reverse) === 0
            ? 0
            : (int) round((2 * $forward * $reverse) / ($forward + $reverse));

        return [
            'mutual'  => $mutual,
            'forward' => $forward,
            'reverse' => $reverse,
        ];
    }

    /**
     * Weighted score (0-100) from an explicitly stated partner preference.
     * Age/height get partial credit for near-misses via rangeScore() instead
     * of an all-or-nothing boolean, so a 31-year-old isn't scored identically
     * to a 45-year-old against a "25-30" preference.
     *
     * @param  array|null  $prefOverride  use this preference set instead of
     *                                    $this->pref (see scoreBreakdown() above)
     */
    protected function explicitScore($profile, ?array $prefOverride = null): array
    {
        $pref = $prefOverride ?? $this->pref;

        if (empty($pref)) {
            return [50, []]; // no stated preference at all -> neutral baseline
        }

        $totalWeight = 0;
        $earned = 0;
        $detail = [];

        if (!empty($pref['age_from']) && !empty($pref['age_to']) && !empty($profile->birthdate)) {
            $w = $this->fieldWeights['age'];
            $totalWeight += $w;

            // Calculate current age from birthdate
            $age = Carbon::parse($profile->birthdate)->age;

            // Compare calculated age with preferred age range
            $score = $this->rangeScore(
                (float) $age,
                (float) $pref['age_from'],
                (float) $pref['age_to']
            );

            $earned += $score * $w;
            $detail['age'] = $score;
        }

        if (!empty($pref['height_from']) && !empty($pref['height_to']) && !empty($profile->height)) {
            $w = $this->fieldWeights['height'];
            $totalWeight += $w;
            $score = $this->rangeScore((float) $profile->height, (float) $pref['height_from'], (float) $pref['height_to']);
            $earned += $score * $w;
            $detail['height'] = $score;
        }

        // prefKey => actual Register column name (fixes the 'education' -> 'education_level' mismatch)
        $fieldMap = [
            'marital_status' => 'marital_status',
            'religion'       => 'religion',
            'caste'          => 'caste',
            'country_id'     => 'country_id',
            'state_id'       => 'state_id',
            'education_level' => 'education_level',
            'occupation'     => 'occupation',
            'mother_tongue'  => 'mother_tongue',
            'manglik'        => 'manglik',
        ];

        foreach ($fieldMap as $prefKey => $profileField) {
            $prefValues = $pref[$prefKey] ?? [];

            // Empty pref or "Does Not Matter" -> ignore this field entirely (no weight, no score impact)
            if (empty($prefValues) || in_array('Does Not Matter', $prefValues, true)) {
                continue;
            }

            $w = $this->fieldWeights[$prefKey] ?? 5;
            $totalWeight += $w;

            $match = in_array($profile->{$profileField} ?? null, $prefValues, true);
            $earned += $match ? $w : 0;
            $detail[$prefKey] = $match ? 1 : 0;
        }

        if ($totalWeight === 0) {
            return [50, []];
        }

        return [($earned / $totalWeight) * 100, $detail];
    }

    /**
     * 1.0 when $value falls inside [$min, $max]; decays linearly to 0 over a
     * tolerance band outside the range, instead of an abrupt cutoff.
     */
    protected function rangeScore(float $value, float $min, float $max): float
    {
        if ($value >= $min && $value <= $max) {
            return 1.0;
        }

        $tolerance = max(($max - $min) * 0.5, 2);
        $distance = $value < $min ? ($min - $value) : ($value - $max);

        return max(0, 1 - ($distance / $tolerance));
    }
}

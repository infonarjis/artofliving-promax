<?php

namespace App\Services;

use App\Jobs\RelearnMemberPreferences;
use App\Models\Register;
use App\Models\UserActivityLog;
use App\Models\UserLearnedPreference;
use Carbon\Carbon;

/**
 * Tracks member behavior (profile view / contact view / shortlist / interest
 * / interest outcome / photo request / photo request outcome / block) and
 * turns it into a learned preference profile that MatchMakingService blends
 * with the member's explicitly stated partner preference.
 *
 * Two directions of signal:
 *   - Initiating actions (view, shortlist, interest, photo_request) tell us
 *     what the ACTING member is drawn to.
 *   - Outcome actions (interest/photo accepted or rejected) tell us what the
 *     RESPONDING member is drawn to — an accepted interest is a mutual,
 *     explicit "yes" and is the strongest signal in the whole system, so
 *     make sure both sides of every accept/reject flow call the matching
 *     track*Accepted()/track*Rejected() method on the RESPONDER's id, not
 *     the sender's.
 *
 * How auto-learning works:
 *   1. Every tracked action is stored as a signed "weight" (positive =
 *      attraction, negative = repulsion) in user_activity_logs.
 *   2. After every N new actions (AUTO_LEARN_EVERY_N_ACTIONS), a
 *      RelearnMemberPreferences job is queued to re-aggregate the member's
 *      recent activity into a normalized preference distribution per field
 *      (religion, caste, education, etc.) and a weighted mean/std for age &
 *      height. This is cached in user_learned_preferences so scoring never
 *      has to re-crunch raw logs. It's queued rather than run inline because
 *      track() is called from methods that hold DB-level row locks
 *      (Payment::lockForUpdate() in the interest/photo-request flows).
 *   3. getAffinityScore() turns that learned profile into a 0-100 "how much
 *      would this member likely like this profile" score for any candidate.
 */
class BehaviorLearningService
{
    /**
     * Signal weights per action type. Positive = attraction, negative = repulsion.
     *
     * INTEREST_ACCEPTED is the highest weight in the table on purpose — it's
     * the only signal that's mutually, explicitly confirmed by both members,
     * versus everything else which reflects only one side's intent.
     * CONTACT_VIEW sits above a plain profile view because unlocking contact
     * details costs a limited paid credit, so it reflects real commitment,
     * not idle browsing.
     */
    public const ACTION_WEIGHTS = [
        UserActivityLog::USER_PROFILE_VIEW => 1,
        UserActivityLog::USER_CONTACT_VIEW => 3,
        UserActivityLog::SHORTLIST         => 4,
        UserActivityLog::INTEREST          => 6,
        UserActivityLog::INTEREST_ACCEPTED => 9,
        UserActivityLog::INTEREST_REJECTED => -3,
        UserActivityLog::PHOTO_REQUEST     => 5,
        UserActivityLog::PHOTO_ACCEPTED    => 6,
        UserActivityLog::PHOTO_REJECTED    => -2,
        UserActivityLog::BLOCK             => -8,
    ];

    /** Re-learn a member's preference profile after this many new signals since the last learn. */
    public const AUTO_LEARN_EVERY_N_ACTIONS = 5;

    /** How many recent activity rows to analyze per learning pass. */
    protected int $lookback = 300;

    /** Profile attributes analyzed for categorical (non-numeric) affinity. */
    protected array $categoricalFields = [
        'religion',
        'caste',
        'marital_status',
        'mother_tongue',
        'country_id',
        'state_id',
        'education_level',
        'occupation',
        'manglik',
    ];

    /** In-request cache so scoring a whole result list only hits the DB once per member. */
    protected array $learnedCache = [];

    // ---------------------------------------------------------------
    // TRACKING — call these from wherever the corresponding action happens.
    // ---------------------------------------------------------------

    /** Call from the profile-view flow, e.g. addProfileViewCount(). */
    public function trackProfileView(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::USER_PROFILE_VIEW);
    }

    /**
     * Call from viewContact(), immediately after ViewContactDetail::create()
     * succeeds (i.e. only on a genuinely NEW reveal, not the "already
     * viewed" early-return branch — that already gates duplicates for you).
     */
    public function trackContactView(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::USER_CONTACT_VIEW);
    }

    /**
     * Call ONLY on a genuine state change — first-time create or restore
     * from trashed — not on a no-op call against an already-active shortlist.
     */
    public function trackShortlist(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::SHORTLIST);
    }

    public function trackInterest(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::INTEREST);
    }

    /**
     * Call from ExpressInterest::acceptReject() when the RECEIVER accepts.
     * $memberId = the receiver (whose preference we're learning),
     * $targetMemberId = the original sender.
     */
    public function trackInterestAccepted(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::INTEREST_ACCEPTED);
    }

    /** Same direction as trackInterestAccepted(), for the reject branch. */
    public function trackInterestRejected(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::INTEREST_REJECTED);
    }

    public function trackPhotoRequest(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::PHOTO_REQUEST);
    }

    /** Call from PhotoRequest::acceptReject() when the RECEIVER accepts. */
    public function trackPhotoAccepted(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::PHOTO_ACCEPTED);
    }

    /** Same direction as trackPhotoAccepted(), for the reject branch. */
    public function trackPhotoRejected(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::PHOTO_REJECTED);
    }

    /**
     * Call ONLY on a genuine state change — first-time block or restore
     * from trashed — not on a no-op call against an already-active block.
     */
    public function trackBlock(int $memberId, int $targetMemberId): void
    {
        $this->track($memberId, $targetMemberId, UserActivityLog::BLOCK);
    }

    protected function track(int $memberId, int $targetMemberId, string $actionType): void
    {
        if ($memberId === $targetMemberId) {
            return;
        }

        // De-duplicate profile views: only log once per profile per day, so
        // a user re-opening the same profile 10 times doesn't drown out
        // real signal. (Contact views don't need this — ViewContactDetail
        // already permanently gates them to once-ever before trackContactView
        // is even called.)
        if ($actionType === UserActivityLog::USER_PROFILE_VIEW) {
            $alreadyLoggedToday = UserActivityLog::where('member_id', $memberId)
                ->where('target_member_id', $targetMemberId)
                ->where('action_type', UserActivityLog::USER_PROFILE_VIEW)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($alreadyLoggedToday) {
                return;
            }
        }

        UserActivityLog::create([
            'member_id'        => $memberId,
            'target_member_id' => $targetMemberId,
            'action_type'      => $actionType,
            'weight'           => self::ACTION_WEIGHTS[$actionType] ?? 0,
        ]);

        $this->maybeAutoLearn($memberId);
    }

    /**
     * Auto Learning: queues a re-learn of the member's preference profile
     * once enough new signals have accumulated since the last learning
     * pass, so the system keeps adapting without any manual/cron trigger
     * required. Queued (not run inline) since track() is frequently called
     * from within locked transactions — see class docblock.
     */
    protected function maybeAutoLearn(int $memberId): void
    {
        $learned = UserLearnedPreference::where('member_id', $memberId)->first();
        $since = $learned?->last_learned_at ?? Carbon::createFromTimestamp(0);

        $newActionCount = UserActivityLog::where('member_id', $memberId)
            ->where('created_at', '>', $since)
            ->count();

        if ($newActionCount >= self::AUTO_LEARN_EVERY_N_ACTIONS) {
            RelearnMemberPreferences::dispatch($memberId);
        }
    }

    // ---------------------------------------------------------------
    // LEARNING
    // ---------------------------------------------------------------

    /**
     * Analyze a member's recent activity logs and rebuild their learned
     * preference profile (categorical affinity distributions + numeric
     * age/height ranges, weighted by how strongly they engaged).
     */
    public function learnPreferences(int $memberId): UserLearnedPreference
    {
        $logs = UserActivityLog::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->limit($this->lookback)
            ->get();

        if ($logs->isEmpty()) {
            return UserLearnedPreference::updateOrCreate(
                ['member_id' => $memberId],
                ['preference_data' => [], 'numeric_data' => [], 'sample_size' => 0, 'last_learned_at' => now()]
            );
        }

        $targetIds = $logs->pluck('target_member_id')->unique()->values();

        $targets = Register::whereIn('id', $targetIds)
            ->select(array_merge(['id', 'birthdate', 'height'], $this->categoricalFields))
            ->get()
            ->keyBy('id');

        $categoricalTally = []; // [field => [value => weightedCount]]
        $ageSamples = [];       // [[age, weight], ...]
        $heightSamples = [];

        foreach ($logs as $log) {
            $profile = $targets->get($log->target_member_id);
            if (!$profile) {
                continue;
            }

            $weight = (float) $log->weight;
            if ($weight === 0.0) {
                continue;
            }

            foreach ($this->categoricalFields as $field) {
                $value = $profile->{$field} ?? null;
                if ($value === null || $value === '') {
                    continue;
                }
                $categoricalTally[$field][$value] = ($categoricalTally[$field][$value] ?? 0) + $weight;
            }

            if (!empty($profile->birthdate)) {
                $ageSamples[] = [Carbon::parse($profile->birthdate)->age, $weight];
            }
            if (!empty($profile->height)) {
                $heightSamples[] = [(float) $profile->height, $weight];
            }
        }

        // Normalize each field's tally into a -1..1 affinity score per value.
        $preferenceData = [];
        foreach ($categoricalTally as $field => $valueWeights) {
            $max = max(array_map('abs', $valueWeights)) ?: 1;
            $normalized = [];
            foreach ($valueWeights as $value => $w) {
                $normalized[$value] = round($w / $max, 4);
            }
            arsort($normalized);
            $preferenceData[$field] = $normalized;
        }

        $numericData = [
            'age'    => $this->weightedMeanStd($ageSamples),
            'height' => $this->weightedMeanStd($heightSamples),
        ];

        $record = UserLearnedPreference::updateOrCreate(
            ['member_id' => $memberId],
            [
                'preference_data' => $preferenceData,
                'numeric_data'    => $numericData,
                'sample_size'     => $logs->count(),
                'last_learned_at' => now(),
            ]
        );

        $this->learnedCache[$memberId] = $record;

        return $record;
    }

    /**
     * Weighted mean/std, using only positive-signal samples — negative
     * (rejected/blocked) samples tell us what to avoid, not a "preferred
     * range", so they're excluded from the numeric range estimate on purpose.
     */
    protected function weightedMeanStd(array $samples): ?array
    {
        $samples = array_filter($samples, fn($s) => $s[1] > 0);

        if (empty($samples)) {
            return null;
        }

        $totalWeight = array_sum(array_map(fn($s) => $s[1], $samples));
        if ($totalWeight <= 0) {
            return null;
        }

        $mean = array_sum(array_map(fn($s) => $s[0] * $s[1], $samples)) / $totalWeight;
        $variance = array_sum(array_map(fn($s) => $s[1] * (($s[0] - $mean) ** 2), $samples)) / $totalWeight;
        $std = sqrt($variance) ?: 1;

        return ['mean' => round($mean, 2), 'std' => round(max($std, 1), 2)];
    }

    // ---------------------------------------------------------------
    // SCORING
    // ---------------------------------------------------------------

    /** Loads (and request-caches) a member's learned preference profile. */
    public function getLearnedPreference(int $memberId): ?UserLearnedPreference
    {
        if (array_key_exists($memberId, $this->learnedCache)) {
            return $this->learnedCache[$memberId];
        }

        return $this->learnedCache[$memberId] = UserLearnedPreference::where('member_id', $memberId)->first();
    }

    /**
     * Bulk-warms the in-request cache for a batch of member IDs in a single
     * query. Mutual-match scoring needs a behavioral affinity lookup for
     * every candidate (not just the auth user), so without this, scoring a
     * large chunk fires one query per candidate — call this once per chunk
     * before scoring it. IDs already cached are skipped; IDs with no
     * learned profile yet are cached as null so they aren't re-queried.
     */
    public function preloadLearnedPreferences(array $memberIds): void
    {
        $memberIds = array_values(array_unique(array_filter($memberIds)));
        $toFetch = array_diff($memberIds, array_keys($this->learnedCache));

        if (empty($toFetch)) {
            return;
        }

        $found = UserLearnedPreference::whereIn('member_id', $toFetch)->get()->keyBy('member_id');

        foreach ($toFetch as $id) {
            $this->learnedCache[$id] = $found->get($id);
        }
    }

    /**
     * Behavioral affinity score (0-100): how well $profile fits what
     * $memberId has historically engaged with positively. Returns a
     * neutral 50 when there isn't enough learned data yet (cold start),
     * so new members aren't penalized before they've built a history.
     */
    public function getAffinityScore(int $memberId, $profile): int
    {
        $learned = $this->getLearnedPreference($memberId);

        if (!$learned || (int) $learned->sample_size < 3) {
            return 50;
        }

        $prefData = $learned->preference_data ?? [];
        $numericData = $learned->numeric_data ?? [];
        $scores = [];

        foreach ($this->categoricalFields as $field) {
            if (empty($prefData[$field])) {
                continue;
            }
            $value = $profile->{$field} ?? null;
            if ($value === null) {
                continue;
            }
            $affinity = $prefData[$field][$value] ?? 0; // -1..1
            $scores[] = ($affinity + 1) / 2 * 100;
        }

        if (!empty($numericData['age']['mean']) && !empty($profile->birthdate)) {
            $age = Carbon::parse($profile->birthdate)->age;
            $scores[] = $this->gaussianScore($age, $numericData['age']['mean'], $numericData['age']['std']);
        }

        if (!empty($numericData['height']['mean']) && !empty($profile->height)) {
            $scores[] = $this->gaussianScore((float) $profile->height, $numericData['height']['mean'], $numericData['height']['std']);
        }

        if (empty($scores)) {
            return 50;
        }

        return (int) round(array_sum($scores) / count($scores));
    }

    /**
     * Gaussian-style falloff so a value close to the learned mean still
     * scores well, instead of a hard in/out-of-range cutoff.
     */
    protected function gaussianScore(float $value, float $mean, float $std): float
    {
        $std = $std ?: 1;
        $z = ($value - $mean) / $std;
        return max(0, 100 * exp(-0.5 * $z * $z));
    }
}

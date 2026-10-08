<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserMigrationController extends Controller
{
    /** Keep (rows x columns) under the 65,535 placeholder limit of one INSERT. */
    private const CHUNK = 200;

    /** Course names / IDs that could not be matched to course_detail_masters (label => number of rows) */
    private array $unmatchedCourses = [];

    /** Short course names resolved by unique prefix, so they can be verified (label => number of rows) */
    private array $prefixMatchedCourses = [];

    /**
     * New master tables used to turn old TEXT values (e.g. 'Nuclear Family')
     * into IDs.  key => [table, name column]
     * These come from StaticMasterMigrationController, so run that first.
     */
    private const LOOKUPS = [
        'profileby'       => ['profileby_masters', 'profileby_name'],
        'marital_status'  => ['marital_status_masters', 'marital_status_name'],
        'family_type'     => ['family_type_masters', 'family_type_name'],
        'family_status'   => ['family_status_masters', 'family_status_name'],
        'married_brother' => ['no_of_bro_sis_masters', 'no_of_bro_sis_name'],
        'married_sister'  => ['no_of_bro_sis_masters', 'no_of_bro_sis_name'],

        'manglik'         => ['manglik_masters', 'manglik_name'],
        'moonsign'        => ['moonsign_master', 'moonsign_name'],
        'diet'            => ['eating_habit_masters', 'eating_habit_name'],
        'smoke'           => ['smoking_habit_masters', 'smoking_habit_name'],
        'drink'           => ['drinking_habit_masters', 'drinking_habit_name'],
    ];

    /**
     * Old course names that do not match any course_detail_masters name
     * -> the course_detail_masters ID to use instead.
     * Key = course name in lower case (spacing does not matter), value = ID.
     * Add every value from "unmatched_courses" in the response here.
     */
    private const COURSE_ALIASES = [
        'PRE-TTP' => 10,
        'HAPPINESS' => 5,
        'PART' => 6,
        'SAHAJ' => 8,
        'BLESSINGS' => 12,
        'SHAKTI' => 20,
        'Art' => 21,
        // 'SRI' => 7,
        'Vigyan' => 25,
        'Youth' => 22,
        'QCI' => 28,
    ];

    /* ----------------------------------------------------------------
     |  PUBLIC ENDPOINTS
     * ---------------------------------------------------------------- */

    /** Old `registers` -> new `registers` */
    public function registers(): JsonResponse
    {
        return $this->run(fn() => ['registers' => $this->migrateRegisters()]);
    }

    /** Old `register_partners` -> new `register_partners` */
    public function registerPartners(): JsonResponse
    {
        return $this->run(fn() => ['register_partners' => $this->migratePartners()]);
    }

    /** Both, in the right order */
    public function all(): JsonResponse
    {
        return $this->run(fn() => [
            'registers'         => $this->migrateRegisters(),
            'register_partners' => $this->migratePartners(),
        ]);
    }

    private function run(callable $job): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Migrated successfully.',
                'migrated' => $job(),
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

    /* ----------------------------------------------------------------
     |  REGISTERS
     * ---------------------------------------------------------------- */

    private function migrateRegisters(): array
    {
        $maps = $this->loadLookups();
        $this->unmatchedCourses     = [];
        $this->prefixMatchedCourses = [];

        // TRUNCATE causes an implicit commit, so it runs before the chunk transactions
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('registers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $migrated = 0;

        DB::connection('mysql_old')
            ->table('registers')
            ->chunkById(self::CHUNK, function ($olds) use ($maps, &$migrated) {
                $rows = [];
                foreach ($olds as $old) {
                    $rows[] = $this->mapRegister($old, $maps);
                }

                DB::transaction(fn() => DB::table('registers')->insert($rows));
                $migrated += count($rows);
            }, 'id');

        $result = [
            'old_total' => DB::connection('mysql_old')->table('registers')->count(),
            'migrated'  => $migrated,
        ];

        if ($this->prefixMatchedCourses) {
            arsort($this->prefixMatchedCourses);
            $result['matched_by_prefix'] = $this->prefixMatchedCourses;
        }

        if ($this->unmatchedCourses) {
            arsort($this->unmatchedCourses);
            $result['unmatched_courses'] = $this->unmatchedCourses;
        }

        return $result;
    }

    private function mapRegister(object $o, array $maps): array
    {
        /* ---- education ---- */
        $educationDetails = $this->nz($o->education_level);
        if ($o->other_education_level === 'Yes' && ($v = $this->nz($o->other_education_level_name)) !== null) {
            $educationDetails = trim(($educationDetails ?? '') . "\n" . $v);
        }

        $row = [
            'id'                     => $o->id,
            'is_converted_from_lead' => 'No',
            'user_type'              => $o->user_type === 'Assisted' ? 1 : 0,
            'matri_id'               => $o->matri_id,
            'prefix'                 => $o->prefix,
            'terms'                  => $o->terms === 'Yes' ? 'Yes' : 'No',
            'email'                  => $o->email,
            'cpassword_expire'       => $this->clean($o->cpassword_expire),
            'cpassword'              => $o->cpassword,
            'email_verify_status'    => $o->cpass_status === 'Verify' ? 'Verify' : 'Not-Verify',
            'mobile'                 => $o->mobile,
            'mobile_verify_status'   => $o->mobile_verify_status === 'Yes' ? 'Yes' : 'No',
            'password'               => $o->password,
            'fullname'               => $this->nz($o->fullname) ?? $this->nz(trim($o->first_name . ' ' . $o->last_name)),
            'birthdate'              => $this->clean($o->birthdate),
            'gender'                 => in_array($o->gender, ['Male', 'Female'], true) ? $o->gender : null,
            'country_id'             => $this->fk($o->country_id),
            'state_id'               => $this->fk($o->state_id),
            'city'                   => $this->fk($o->city),
            'alternate_number'       => $o->alternative_number,
            'birthplace'             => $o->birthplace,
            'birthtime'              => $o->birthtime,
            'profileby'              => $this->lookup($o->profileby, $maps['profileby']),
            'height'                 => $o->height === null ? null : (string) $o->height,
            'marital_status'         => $this->lookup($o->marital_status, $maps['marital_status']),
            'details_of_children'    => $this->nz($o->details_of_children),

            'religion'               => $this->fk($o->religion),
            'caste'                  => $this->fk($o->caste),
            'subcaste'               => $this->cut($o->subcaste, 255),
            'manglik'                => $this->lookup($o->manglik, $maps['manglik']),
            'star'                   => $this->fk($o->star),
            'gothra'                 => $this->cut($o->gothra, 255),
            'moonsign'               => $this->lookup($o->moonsign, $maps['moonsign']),
            'mother_tongue'          => $this->fk($o->mother_tongue),

            'diet'                   => $this->lookup($o->diet, $maps['diet']),
            'smoke'                  => $this->lookup($o->smoke, $maps['smoke']),
            'drink'                  => $this->lookup($o->drink, $maps['drink']),

            'education_level'        => $this->cut($o->highest_qualification, 255),
            'education_details'      => $this->nz($o->education_level),
            'occupation'             => $this->fk($o->occupation),
            'income'                 => $this->fk($o->income),

            'family_type'            => $this->lookup($o->family_type, $maps['family_type']),
            'father_name'            => $this->cut($o->father_name, 255),
            'father_occupation_other' => $this->nz($o->father_occupation),
            // 'father_occupation'      => '',
            'mother_name'             => $this->cut($o->mother_name, 255),
            'mother_occupation_other' => $this->nz($o->mother_occupation),
            // 'mother_occupation'      => '',
            'family_status'          => $this->lookup($o->family_status, $maps['family_status']),
            'no_of_married_brother'  => $this->siblingCount($o->no_of_married_brother, $maps['married_brother']),
            'no_of_married_sister'   => $this->siblingCount($o->no_of_married_sister, $maps['married_sister']),
            'family_details'         => $this->nz($o->family_details),

            // old profile_text -> new about_me_description
            'about_me_description'   => $this->nz($o->profile_text),

            /* ---- Art of Living ---- */
            'have_art_of_living_program' => $this->nz($o->have_art_of_living_program),
            'Yesart_of_living_teacher'   => $this->nz($o->Yesart_of_living_teacher),
            'teacher_code'               => $this->nz($o->teacher_code),
            'art_of_living_program'      => $this->courseIds($o->art_of_living_program, $maps['course']),
            'teaching_courses'           => $this->courseIds($o->teaching_courses, $maps['course']),
            'no_of_years_in_artofliving' => $this->nz($o->no_of_years_in_artofliving),
            'teacher_name'               => $this->nz($o->teacher_name),
            'teacher_mobile_no'          => $this->nz($o->teacher_mobile_no),

            // Extra Field Added To Field Not Exist In New Project :
            'field_of_study'            => $this->nz($o->field_of_study),
            'job_title'                 => $this->nz($o->job_title),
            'company_name'              => $this->nz($o->company_name),
            'disbalities'               => $this->nz($o->disbalities),
            'disabilites_details'       => $this->nz($o->disabilites_details),
            'interest'                  => $this->nz($o->interest),
        ];

        /* ---- photos : Yes/No -> APPROVED/UNAPPROVED ---- */
        for ($i = 1; $i <= 6; $i++) {
            $row["photo{$i}"]        = $this->nz($o->{"photo{$i}"});
            $row["photo{$i}_status"] = ($o->{"photo{$i}_approve"} ?? 'No') === 'Yes' ? 'APPROVED' : 'UNAPPROVED';
        }

        return $row + [
            'id_proof_type'         => $o->id_proof_type,
            'id_proof_front'        => $o->id_proof_front,
            'id_proof_back'         => $o->id_proof_back,
            'id_proof_status'       => $o->id_proof_approve === 'APPROVED' ? 'APPROVED' : 'UNAPPROVED',
            'id_proof_uploaded_on'  => $this->clean($o->id_proof_uploaded_on),

            'plan_id'               => (int) $o->plan_id,
            'plan_name'             => $o->plan_name,
            'plan_status'           => in_array($o->plan_status, ['Paid', 'Expired'], true) ? $o->plan_status : 'Not Paid',
            'plan_expired_on'       => $this->clean($o->plan_expired_on),

            'registered_from'       => $this->registeredFrom($o),
            'user_agent'            => $o->user_agent,
            'android_device_id'     => $o->android_device_id,
            'app_status'            => $o->app_status,
            'ios_device_id'         => $o->ios_device_id,
            'ios_app_status'        => $o->ios_app_status,
            'install_date_android'  => $this->clean($o->install_date_android),
            'install_date_ios'      => $this->clean($o->install_date_ios),
            'uninstall_date_android' => $this->clean($o->uninstall_date_android),
            'uninstall_date_ios'    => $this->clean($o->uninstall_date_ios),
            'web_device_id'         => $o->web_device_id,
            'ip'                    => $o->ip,
            'agent'                 => $o->agent,
            'agent_approve'         => $o->agent_approve === 'APPROVED' ? 'APPROVED' : 'UNAPPROVED',
            'last_login'            => $this->clean($o->last_login),

            'suspended_by'          => $o->suspended_by,
            'suspended_by_name'     => $o->suspended_by_name,
            'suspended_on'          => $this->clean($o->suspended_on),
            'fstatus'               => $o->fstatus === 'Featured' ? 'Featured' : 'Unfeatured',
            'logged_in'             => $o->logged_in === '1' ? '1' : '0',

            'adminrole_id'          => (int) $o->adminrole_id,
            'staff_assign_id'       => (int) $o->staff_assign_id,
            'staff_assign_date'     => $this->clean($o->staff_assign_date),
            'franchised_by'         => (int) $o->franchised_by,
            'franchise_assign_id'   => (int) $o->franchise_assign_id,
            'franchise_assign_date' => $this->clean($o->franchise_assign_date),
            'commented'             => $o->commented === '1' ? '1' : '0',
            'adminrole_view_status' => $o->adminrole_view_status === 'Yes' ? 'Yes' : 'No',

            'is_verify'             => $o->is_verify === 'Yes' ? 'Yes' : 'No',
            // old enum('0','1') / enum('0','1','2')  ->  new tinyint
            'contact_visibility'    => (int) $o->contact_visibility,
            'photo_visibility'      => (int) $o->photo_visibility,

            'status'                => in_array($o->status, ['APPROVED', 'Suspended'], true) ? $o->status : 'UNAPPROVED',

            'created_at'            => $this->clean($o->created_at) ?? $this->clean($o->registered_on),
            'updated_at'            => $this->clean($o->updated_at),
            // old is_deleted = 'Yes' -> soft delete
            'deleted_at'            => $o->is_deleted === 'Yes' ? ($this->clean($o->updated_at) ?? now()) : null,
        ];
    }

    /** old: Mobile App / Front End / Back End / Other  ->  new: Android / IOS / Website / Admin / Other */
    private function registeredFrom(object $o): string
    {
        return match ($o->registered_from) {
            'Front End'  => 'Website',
            'Back End'   => 'Admin',
            'Mobile App' => (!empty($o->ios_device_id) && empty($o->android_device_id)) ? 'IOS' : 'Android',
            default      => 'Other',
        };
    }

    /* ----------------------------------------------------------------
     |  REGISTER PARTNERS
     * ---------------------------------------------------------------- */

    private function migratePartners(): array
    {
        $maps = $this->loadLookups();
        $now  = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('register_partners')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $oldTotal = DB::connection('mysql_old')->table('register_partners')->count();

        /**
         * Read EVERY old row in a single pass. (No id based paging: rows whose id is 0 or
         * duplicated are silently skipped by chunkById, which is why only part of the table came across.)
         */
        $withId    = [];   // old id is > 0 and not used before  -> keep the id
        $withoutId = [];   // id is 0 / duplicate                  -> let the new table assign one
        $usedIds   = [];

        $olds = DB::connection('mysql_old')
            ->table('register_partners')
            ->orderBy('id')
            ->orderBy('member_id')
            ->cursor();

        foreach ($olds as $o) {
            $row = $this->mapPartner($o, $maps, $now);
            $id  = (int) $o->id;

            if ($id > 0 && !isset($usedIds[$id])) {
                $usedIds[$id] = true;
                $withId[]     = ['id' => $id] + $row;
            } else {
                $withoutId[] = $row;
            }
        }

        DB::transaction(function () use ($withId, $withoutId) {
            foreach (array_chunk($withId, 500) as $chunk) {
                DB::table('register_partners')->insert($chunk);
            }
            // inserted last so the auto-increment ids continue after the highest kept id
            foreach (array_chunk($withoutId, 500) as $chunk) {
                DB::table('register_partners')->insert($chunk);
            }
        });

        $migrated = count($withId) + count($withoutId);

        $result = [
            'old_total'      => $oldTotal,
            'migrated'       => $migrated,
            'ids_reassigned' => count($withoutId),
        ];

        if ($migrated !== $oldTotal) {
            $result['warning'] = 'Migrated count does not match the old table count.';
        }

        return $result;
    }

    private function mapPartner(object $o, array $maps, $now): array
    {
        return [
            'member_id'           => $o->member_id,
            'part_frm_age'        => $o->part_frm_age === null ? null : (string) $o->part_frm_age,
            'part_to_age'         => $o->part_to_age === null ? null : (string) $o->part_to_age,
            'part_height'         => $o->part_height === null ? null : (string) $o->part_height,
            'part_height_to'      => $o->part_height_to === null ? null : (string) $o->part_height_to,
            // old looking_for ('Never Married', ...) -> comma separated marital status IDs
            'part_marital_status' => $this->csvIds($o->looking_for, $maps['marital_status']),
            'part_religion'       => $this->nz($o->part_religion),
            'part_caste'          => $this->nz($o->part_caste),
            'part_country'        => $this->nz($o->part_country),
            'part_state'          => null,
            'part_income'         => $this->nz($o->part_income),
            'part_education'      => null,
            'part_occupation'     => null,
            'part_mothertongue'   => $this->nz($o->part_mothertongue),
            'part_manglik'        => null,

            'part_diet'           => $this->csvIds($o->part_diet, $maps['diet']),
            'part_smoke'          => $this->csvIds($o->part_smoke, $maps['smoke']),
            'part_drink'          => $this->csvIds($o->part_drink, $maps['drink']),

            'part_art_of_living_teacher'      => $this->nz($o->part_art_of_living_teacher),
            'part_have_art_of_living_program' => $this->nz($o->part_have_art_of_living_program),

            'created_at'          => $now,
            'updated_at'          => $now,
            'deleted_at'          => $o->is_deleted === 'Yes' ? $now : null,
        ];
    }

    /* ----------------------------------------------------------------
     |  HELPERS
     * ---------------------------------------------------------------- */

    /** Load name -> id maps from the new master tables (fails early if they are missing). */
    private function loadLookups(): array
    {
        $maps    = [];
        $missing = [];

        foreach (self::LOOKUPS as $key => [$table, $column]) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
                continue;
            }

            $maps[$key] = DB::table($table)
                ->pluck('id', $column)
                ->mapWithKeys(fn($id, $name) => [mb_strtolower(trim((string) $name)) => (int) $id])
                ->all();
        }

        // Art of Living courses (name => id), compared with courseKey() so spacing / case does not matter
        if (Schema::hasTable('course_detail_masters')) {
            $maps['course'] = DB::table('course_detail_masters')
                ->pluck('id', 'course_name')
                ->mapWithKeys(fn($id, $name) => [$this->courseKey((string) $name) => (int) $id])
                ->all();
        } else {
            $missing[] = 'course_detail_masters';
        }

        if ($missing) {
            throw new Exception(
                'Create and fill these master tables first. Missing tables: ' . implode(', ', $missing)
            );
        }

        return $maps;
    }

    /** Comparison key for course names: case, repeated spaces and spaces around ( ) / + are ignored */
    private function courseKey(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', mb_strtolower(trim($name)));

        return str_replace([' / ', ' /', '/ ', '( ', ' )', ' +'], ['/', '/', '/', '(', ')', '+'], $name);
    }

    /**
     * Course names ('BLESSINGS PROGRAM,DSN,...') or IDs ('5,13,22')  ->  '12,3,40'
     * (comma separated course_detail_masters IDs, duplicates removed).
     *
     * A course name is resolved in this order:
     *   1. COURSE_ALIASES   (explicit name -> ID, e.g. 'PRE-TTP' => 10)
     *   2. exact name match in course_detail_masters
     *   3. unique prefix match ('HAPPINESS' -> 'HAPPINESS PROGRAM'), reported in matched_by_prefix
     * Names that match nothing are left out and counted in $unmatchedCourses;
     * IDs are kept, and counted there too if the master has no such ID.
     */
    private function courseIds($value, array $map): ?string
    {
        $value = $this->nz($value);
        if ($value === null) {
            return null;
        }

        $ids = [];

        foreach (explode(',', $value) as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }

            if (is_numeric($token)) {
                $id = (int) $token;
                if (!in_array($id, $map, true)) {
                    $label = "ID {$id} (not in course_detail_masters)";
                    $this->unmatchedCourses[$label] = ($this->unmatchedCourses[$label] ?? 0) + 1;
                }
                $ids[] = $id;
                continue;
            }

            $key = $this->courseKey($token);
            $id  = self::COURSE_ALIASES[$key] ?? $map[$key] ?? $this->coursePrefixMatch($key, $map);

            if ($id === null) {
                $label = preg_replace('/\s+/', ' ', $token);
                $this->unmatchedCourses[$label] = ($this->unmatchedCourses[$label] ?? 0) + 1;
                continue;
            }

            $ids[] = $id;
        }

        $ids = array_values(array_unique($ids));

        return $ids ? implode(',', $ids) : null;
    }

    /**
     * 'happiness' -> ID of 'happiness program', but only when exactly ONE course name
     * starts with it (as whole words). Several matches = ambiguous = null.
     */
    private function coursePrefixMatch(string $key, array $map): ?int
    {
        $length = mb_strlen($key);
        if ($length < 3) {
            return null;
        }

        $found = [];
        foreach ($map as $name => $id) {
            if (str_starts_with($name, $key) && !ctype_alnum(mb_substr($name, $length, 1))) {
                $found[$name] = $id;
            }
        }

        if (count($found) !== 1) {
            return null;
        }

        $name  = array_key_first($found);
        $label = "{$key} => {$name} (ID {$found[$name]})";
        $this->prefixMatchedCourses[$label] = ($this->prefixMatchedCourses[$label] ?? 0) + 1;

        return $found[$name];
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

    /** numeric id > 0 -> int, anything else (0, '', text) -> null */
    private function fk($value): ?int
    {
        return (is_numeric($value) && (int) $value > 0) ? (int) $value : null;
    }

    /** numeric -> treated as an ID already, text -> looked up by name in the new master */
    private function lookup($value, array $map): ?int
    {
        $value = $this->nz($value);
        if ($value === null) {
            return null;
        }

        return is_numeric($value) ? (int) $value : ($map[mb_strtolower($value)] ?? null);
    }

    /** '1,2,Never Married' -> '1,2,<id>'; unknown text such as 'Does Not Matter' is kept as is */
    private function csvIds($value, array $map): ?string
    {
        $value = $this->nz($value);
        if ($value === null) {
            return null;
        }

        $out = [];
        foreach (explode(',', $value) as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }
            $out[] = is_numeric($token) ? $token : ($map[mb_strtolower($token)] ?? $token);
        }

        return implode(',', $out);
    }

    /** 'No married brother' -> id of 'None'; '2' / '2 brothers' -> id of '2'; 6 or more -> '6+' */
    private function siblingCount($value, array $map): ?int
    {
        $value = $this->nz($value);
        if ($value === null) {
            return null;
        }

        if (preg_match('/^no\b/i', $value)) {
            return $map['none'] ?? null;
        }

        if (preg_match('/\d+/', $value, $m)) {
            $n = (int) $m[0];
            if ($n === 0) {
                return $map['none'] ?? null;
            }

            return $map[$n >= 6 ? '6+' : (string) $n] ?? null;
        }

        return null;
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

    public function partnerNotExistData()
    {
        $migrated = 0;

        try {
            DB::table('registers as r')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('register_partners as rp')
                        ->whereColumn('rp.member_id', 'r.id');
                })
                ->select('r.id')
                ->chunkById(
                    self::CHUNK,
                    function ($registers) use (&$migrated) {

                        foreach ($registers as $register) {
                            DB::table('register_partners')->insert([
                                'member_id' => $register->id,
                            ]);

                            $migrated++;
                        }
                    },
                    'r.id',
                    'id'
                );

            return response()->json([
                'status'   => true,
                'message'  => 'Migrated successfully.',
                'migrated' => $migrated,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

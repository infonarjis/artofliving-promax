<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;

class Register extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, Notifiable, CanResetPassword, SoftDeletes;

    protected $table = 'registers';

    protected $fillable = [
        'is_verify',
        'user_type',
        'matri_id',
        'prefix',
        'terms',
        'email',
        'email_verification_token',
        'email_verification_token_expires_at',
        'email_verify_status',
        'mobile',
        'mobile_verify_status',
        'mobile_otp',
        'mobile_otp_expires_at',
        'password',
        'fullname',
        'birthdate',
        'gender',
        'country_id',
        'state_id',
        'city',
        'alternate_number',
        'residence_type',
        'nri_country',
        'address',
        'birthplace',
        'birthtime',
        'profileby',
        'height',
        'weight',
        'marital_status',
        'total_children',
        'status_children',
        'religion',
        'caste',
        'subcaste',
        'manglik',
        'star',
        'gothra',
        'moonsign',
        'horoscope',
        'mother_tongue',
        'diet',
        'smoke',
        'drink',
        'body_type',
        'complexion',
        'blood_group_id',
        'education_level',
        'designation_level',
        'education_details',
        'employee_in',
        'occupation',
        'income',
        'Yesart_of_living_teacher',
        'teacher_code',
        'teaching_courses',
        'have_art_of_living_program',
        'teacher_name',
        'teacher_mobile_no',
        'art_of_living_program',
        'no_of_years_in_artofliving',
        'family_type',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'family_status',
        'no_of_brother',
        'no_of_sister',
        'no_of_married_brother',
        'no_of_married_sister',
        'family_details',
        'about_me_description',
        'photo1',
        'photo1_status',
        'photo1_uploaded_on',
        'photo2',
        'photo2_status',
        'photo2_uploaded_on',
        'photo3',
        'photo3_status',
        'photo3_uploaded_on',
        'photo4',
        'photo4_status',
        'photo4_uploaded_on',
        'photo5',
        'photo5_status',
        'photo5_uploaded_on',
        'photo6',
        'photo6_status',
        'photo6_uploaded_on',
        'selfie_photo',
        'selfie_photo_status',
        'selfie_photo_uploaded_on',
        'id_proof_type',
        'id_proof_front',
        'id_proof_back',
        'id_proof_status',
        'id_proof_uploaded_on',
        'horoscope_file',
        'horoscope_status',
        'horoscope_uploaded_on',
        'plan_id',
        'plan_name',
        'plan_status',
        'plan_expired_on',
        'registered_from',
        'user_agent',
        'android_device_id',
        'app_status',
        'ios_device_id',
        'ios_app_status',
        'install_date_android',
        'install_date_ios',
        'uninstall_date_android',
        'uninstall_date_ios',
        'web_device_id',
        'ip',
        'agent',
        'agent_approve',
        'last_login',
        'suspended_by',
        'suspended_by_name',
        'suspended_on',
        'fstatus',
        'logged_in',
        'adminrole_id',
        'staff_assign_id',
        'staff_assign_date',
        'franchised_by',
        'franchise_assign_id',
        'franchise_assign_date',
        'commented',
        'adminrole_view_status',
        'contact_visibility',
        'photo_visibility',
        'video_call_setting',
        'voice_call_setting',
        'auto_interest_enabled',
        'daily_interest_limit',
        'min_match_percentage',
        'affiliate_member_id',
        'verification_affiliate_member_id',
        'latitude',
        'longitude',
        'register_step',
        'status',
        'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birthdate'            => 'date:Y-m-d',
        'plan_expired_on'      => 'date:Y-m-d',
        'photo1_uploaded_on'   => 'datetime',
        'photo2_uploaded_on'   => 'datetime',
        'photo3_uploaded_on'   => 'datetime',
        'photo4_uploaded_on'   => 'datetime',
        'id_proof_uploaded_on' => 'datetime',
        'horoscope_uploaded_on' => 'datetime',
        'install_date_android' => 'datetime',
        'install_date_ios'     => 'datetime',
        'uninstall_date_android' => 'datetime',
        'uninstall_date_ios'   => 'datetime',
        'staff_assign_date'    => 'datetime',
        'suspended_on'         => 'datetime',
        'last_login'           => 'datetime',
        'logged_in'            => 'boolean',
        'user_type'            => 'integer',
        'contact_visibility'   => 'integer',
    ];

    protected $multiSelectFields = [
        'education_level',
        'teaching_courses',
        'art_of_living_program'
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->multiSelectFields) && is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::setAttribute($key, $value);
    }

    ## Scopes :
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeMale($query)
    {
        return $query->where('gender', 'Male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'Female');
    }

    public function scopePaid($query)
    {
        return $query->where('plan_status', 'Paid');
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function partnerPreference(): HasOne
    {
        return $this->hasOne(RegisterPartner::class, 'member_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'member_id');
    }

    public function currentPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'member_id')
            ->where('current_plan', 'Yes')
            ->latestOfMany();
    }

    public function sentInterests(): HasMany
    {
        return $this->hasMany(ExpressInterest::class, 'sender_member_id');
    }

    public function receivedInterests(): HasMany
    {
        return $this->hasMany(ExpressInterest::class, 'receiver_member_id');
    }

    public function sentPhotoRequests(): HasMany
    {
        return $this->hasMany(PhotoRequest::class, 'sender_member_id');
    }

    public function receivedPhotoRequests(): HasMany
    {
        return $this->hasMany(PhotoRequest::class, 'receiver_member_id');
    }

    public function viewedContactDetails(): HasMany
    {
        return $this->hasMany(ViewContactDetail::class, 'sender_member_id');
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(UserLoginHistory::class, 'member_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(MemberNotification::class, 'receiver_member_id');
    }

    public function sentMatches(): HasMany
    {
        return $this->hasMany(MatchList::class, 'receiver_member_id', 'id');
    }

    public function receivedMatches(): HasMany
    {
        return $this->hasMany(MatchList::class, 'receiver_member_id');
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SaveSearch::class, 'member_id');
    }

    public function videoCallHistory(): HasMany
    {
        return $this->hasMany(VideoCallHistory::class, 'sender_member_id');
    }

    public function viewedProfiles(): HasMany
    {
        return $this->hasMany(ViewedProfile::class, 'sender_member_id');
    }

    public function profileViewedBy(): HasMany
    {
        return $this->hasMany(ViewedProfile::class, 'receiver_member_id');
    }

    public function matchmakerList(): HasMany
    {
        return $this->hasMany(MemberMatchmakerList::class, 'self_member_id');
    }

    public function deleteRequest(): HasOne
    {
        return $this->hasOne(MemberDeleteProfile::class, 'sender');
    }

    public function latestDeleteRequest()
    {
        return $this->hasOne(MemberDeleteProfile::class, 'sender')->latestOfMany('sent_on');
    }

    public function adminChatList(): HasOne
    {
        return $this->hasOne(PersonalizeAdminChatList::class, 'member_id');
    }

    public function adminChats(): HasMany
    {
        return $this->hasMany(PersonalizeAdminChat::class, 'member_id');
    }

    public function comments()
    {
        return $this->hasMany(CommentMaster::class, 'member_id', 'id');
    }
    public function latestComment()
    {
        return $this->hasOne(CommentMaster::class, 'member_id', 'id')
            ->latestOfMany(); // Laravel 9+ magic (VERY IMPORTANT)
    }

    public function staffData(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_assign_id', 'id');
    }

    public function franchiseData(): BelongsTo
    {
        return $this->belongsTo(Franchise::class, 'franchise_assign_id', 'id');
    }

    public function affiliateData(): BelongsTo
    {
        return $this->belongsTo(AffiliateMember::class, 'affiliate_member_id', 'id');
    }

    public function profileByData()
    {
        return $this->belongsTo(ProfileByMaster::class, 'profileby');
    }

    public function maritalStatusData()
    {
        return $this->belongsTo(MaritalStatusMaster::class, 'marital_status');
    }

    public function totalChildrenData()
    {
        return $this->belongsTo(TotalChildMaster::class, 'total_children');
    }

    public function statusChildrenData()
    {
        return $this->belongsTo(StatusChildMaster::class, 'status_children');
    }

    public function motherTongueData()
    {
        return $this->belongsTo(MotherTongueMaster::class, 'mother_tongue');
    }

    public function religionData()
    {
        return $this->belongsTo(ReligionMaster::class, 'religion');
    }

    public function casteData()
    {
        return $this->belongsTo(CasteMaster::class, 'caste');
    }

    public function manglikData()
    {
        return $this->belongsTo(ManglikMaster::class, 'manglik');
    }

    public function moonsignData()
    {
        return $this->belongsTo(MoonsignMaster::class, 'moonsign');
    }

    public function starData()
    {
        return $this->belongsTo(StarMaster::class, 'star');
    }

    public function horoscopeData()
    {
        return $this->belongsTo(HoroscopeMaster::class, 'horoscope');
    }

    public function countryData()
    {
        return $this->belongsTo(CountryMaster::class, 'country_id');
    }

    public function stateData()
    {
        return $this->belongsTo(StateMaster::class, 'state_id');
    }
    public function cityData()
    {
        return $this->belongsTo(CityMaster::class, 'city');
    }

    public function fatherOccupationData()
    {
        return $this->belongsTo(OccupationMaster::class, 'father_occupation');
    }

    public function motherOccupationData()
    {
        return $this->belongsTo(OccupationMaster::class, 'mother_occupation');
    }

    public function residenceTypeData()
    {
        return $this->belongsTo(ResidenceMaster::class, 'residence_type');
    }

    public function getEducationLevelNamesAttribute()
    {
        if (blank($this->education_level)) {
            return [];
        }

        $ids = array_filter(array_map('trim', explode(',', $this->education_level)));

        return EducationMaster::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($education) {
                return $education->translated_name;
            })
            ->toArray();
    }

    public function getTeachingCoursesNamesAttribute()
    {
        if (blank($this->teaching_courses)) {
            return [];
        }

        $ids = array_filter(array_map('trim', explode(',', $this->teaching_courses)));

        return CourseDetailMaster::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($course) {
                return $course->translated_name;
            })
            ->toArray();
    }

    public function getArtOfLivingProgramNamesAttribute()
    {
        if (blank($this->art_of_living_program)) {
            return [];
        }

        $ids = array_filter(array_map('trim', explode(',', $this->art_of_living_program)));

        return CourseDetailMaster::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($course) {
                return $course->translated_name;
            })
            ->toArray();
    }

    public function occupationData()
    {
        return $this->belongsTo(OccupationMaster::class, 'occupation');
    }

    public function employeeInData()
    {
        return $this->belongsTo(EmployeeMaster::class, 'employee_in');
    }

    public function incomeData()
    {
        return $this->belongsTo(AnnualIncomeMaster::class, 'income');
    }

    public function designationLevelData()
    {
        return $this->belongsTo(DesignationMaster::class, 'designation_level');
    }

    public function dietData()
    {
        return $this->belongsTo(EatingHabitMaster::class, 'diet');
    }

    public function smokeData()
    {
        return $this->belongsTo(SmokingHabitMaster::class, 'smoke');
    }

    public function drinkingData()
    {
        return $this->belongsTo(DrinkingHabitMaster::class, 'drink');
    }

    public function bodyTypeData()
    {
        return $this->belongsTo(BodyTypeMaster::class, 'body_type');
    }

    public function complexionData()
    {
        return $this->belongsTo(ComplexionMaster::class, 'complexion');
    }

    public function bloodGroupData()
    {
        return $this->belongsTo(BloodGroupMaster::class, 'blood_group_id');
    }

    public function familyTypeData()
    {
        return $this->belongsTo(FamilyTypeMaster::class, 'family_type');
    }

    public function familyStatusData()
    {
        return $this->belongsTo(FamilyStatusMaster::class, 'family_status');
    }

    public function noOfBrotherData()
    {
        return $this->belongsTo(NoOfBroSisMaster::class, 'no_of_brother');
    }

    public function noOfSisterData()
    {
        return $this->belongsTo(NoOfBroSisMaster::class, 'no_of_sister');
    }

    public function noOfMarriedBrotherData()
    {
        return $this->belongsTo(MarriedBroMaster::class, 'no_of_married_brother');
    }

    public function noOfMarriedSisterData()
    {
        return $this->belongsTo(MarriedSisMaster::class, 'no_of_married_sister');
    }

    public function riskScore()
    {
        return $this->hasOne(MemberRiskScore::class, 'member_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'Suspended';
    }

    public function hasPaidPlan(): bool
    {
        return $this->plan_status === 'Paid';
    }

    public function scopePhotoVisible($query)
    {
        // Works with both web and API/app authentication
        $currentMember = auth()->guard('web')->user()
            ?? auth()->guard('api')->user();

        return $query

            // At least one approved photo must exist
            ->where(function ($q) {

                $q->where(function ($q) {
                    $q->whereNotNull('photo1')
                        ->where('photo1', '!=', '')
                        ->where('photo1_status', 'APPROVED');
                })

                    ->orWhere(function ($q) {
                        $q->whereNotNull('photo2')
                            ->where('photo2', '!=', '')
                            ->where('photo2_status', 'APPROVED');
                    })

                    ->orWhere(function ($q) {
                        $q->whereNotNull('photo3')
                            ->where('photo3', '!=', '')
                            ->where('photo3_status', 'APPROVED');
                    })

                    ->orWhere(function ($q) {
                        $q->whereNotNull('photo4')
                            ->where('photo4', '!=', '')
                            ->where('photo4_status', 'APPROVED');
                    });
            })

            // Photo visibility
            ->where(function ($q) use ($currentMember) {

                // Public photos
                $q->where('photo_visibility', 1);

                // Paid members can view paid photos
                if ($currentMember?->plan_status === 'Paid') {
                    $q->orWhere('photo_visibility', 2);
                }

                // Private photos - visible after accepted photo request
                if ($currentMember !== null) {

                    $currentMemberId = $currentMember->id;

                    $q->orWhere(function ($sub) use ($currentMemberId) {

                        $sub->where('photo_visibility', 0)

                            ->whereExists(function ($exists) use ($currentMemberId) {

                                $exists
                                    ->selectRaw('1')
                                    ->from((new \App\Models\PhotoRequest)->getTable())

                                    ->where('receiver_response', 'Accepted')
                                    ->where('status', 'APPROVED')

                                    ->where(function ($cond) use ($currentMemberId) {

                                        // Current member sent request
                                        $cond->where(function ($c) use ($currentMemberId) {

                                            $c->where(
                                                'sender_member_id',
                                                $currentMemberId
                                            )
                                                ->whereColumn(
                                                    'receiver_member_id',
                                                    'registers.id'
                                                );
                                        })

                                            // Profile owner sent request
                                            ->orWhere(function ($c) use ($currentMemberId) {

                                                $c->where(
                                                    'receiver_member_id',
                                                    $currentMemberId
                                                )
                                                    ->whereColumn(
                                                        'sender_member_id',
                                                        'registers.id'
                                                    );
                                            });
                                    });
                            });
                    });
                }
            });
    }

    public function isBlockedByAuth()
    {
        $authId = auth()->guard('web')->id();

        return BlockProfile::where('sender_member_id', $authId)
            ->where('receiver_member_id', $this->id)
            ->exists();
    }

    public function scopeCommon(Builder $q, $member = null): Builder
    {
        $member = $member ?: auth()->user();

        if ($member && isset($member->user_type) && $member->user_type == 0) {
            $q->where($this->getTable() . '.user_type', 0);
        }

        return $q;
    }

    static function updateMatriId($id)
    {
        $siteConfigArr = _getSiteSetting();
        $matriIdPrefix = $siteConfigArr['matri_prefix'] ?? 'MATRI';
        $matriId = $matriIdPrefix . $id;
        self::where('id', $id)->update(['matri_id' => $matriId]);
        return $matriId;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaveSearch extends Model
{
    use SoftDeletes;
    protected $table = 'save_search';

    protected $fillable = [
        'member_id',
        'search_page_name',
        'search_name',
        'from_age',
        'to_age',
        'from_height',
        'to_height',
        'marital_status',
        'religion',
        'caste',
        'manglik',
        'star',
        'mother_tongue',
        'country',
        'state',
        'city',
        'education_level',
        'employee_in',
        'designation_level',
        'occupation',
        'income',
        'diet',
        'smoke',
        'drink',
        'with_photo',
        'complexion',
        'body_type',
        'keyword',
        'id_search'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Scopes ────────────────────────────────────────────────────────────────

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatusMaster::class, 'marital_status');
    }

    public function religionData()
    {
        return $this->belongsTo(ReligionMaster::class, 'religion');
    }

    public function casteData()
    {
        return $this->belongsTo(CasteMaster::class, 'caste');
    }

    public function motherTongueData()
    {
        return $this->belongsTo(MotherTongueMaster::class, 'mother_tongue');
    }

    public function manglikData()
    {
        return $this->belongsTo(ManglikMaster::class, 'manglik');
    }

    public function starData()
    {
        return $this->belongsTo(StarMaster::class, 'star');
    }

    public function countryData()
    {
        return $this->belongsTo(CountryMaster::class, 'country');
    }

    public function stateData()
    {
        return $this->belongsTo(StateMaster::class, 'state');
    }

    public function cityData()
    {
        return $this->belongsTo(CityMaster::class, 'city');
    }

    public function educationData()
    {
        return $this->belongsTo(EducationMaster::class, 'education_level');
    }

    public function occupationData()
    {
        return $this->belongsTo(OccupationMaster::class, 'occupation');
    }

    public function employeeData()
    {
        return $this->belongsTo(EmployeeMaster::class, 'employee_in');
    }

    public function designationData()
    {
        return $this->belongsTo(DesignationMaster::class, 'designation_level');
    }

    public function incomeData()
    {
        return $this->belongsTo(AnnualIncomeMaster::class, 'income');
    }

    public function dietData()
    {
        return $this->belongsTo(EatingHabitMaster::class, 'diet');
    }

    public function smokeData()
    {
        return $this->belongsTo(SmokingHabitMaster::class, 'smoke');
    }

    public function drinkData()
    {
        return $this->belongsTo(DrinkingHabitMaster::class, 'drink');
    }

    public function complexionData()
    {
        return $this->belongsTo(ComplexionMaster::class, 'complexion');
    }

    public function bodyTypeData()
    {
        return $this->belongsTo(BodyTypeMaster::class, 'body_type');
    }
}

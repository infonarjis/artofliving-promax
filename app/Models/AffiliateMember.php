<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateMember extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'affiliates';

    protected $table = 'affiliate_member';

    protected $primaryKey = 'id';

    public $timestamps = false; // because you are using datetime, not Laravel timestamps

    protected $fillable = [
        'fullname',
        'mobile',
        'email',
        'gender',
        'image',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'referral_code',
        'qr_image',
        'bank_account_holder_name',
        'bank_account_type',
        'bank_name',
        'bank_account_number',
        'bank_ifsc_code',
        'upi_id',
        'verify_profile',
        'paid_profile',
        'on_field_verify_profile',
        'verify_profile_commission',
        'paid_profile_commission',
        'on_field_verify_profile_commission',
        'last_activity',
        'web_device_id',
        'ip_address',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'verify_profile_commission' => 'double',
        'paid_profile_commission' => 'double',
        'on_field_verify_profile_commission' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Override default password column for Auth
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function country()
    {
        return $this->belongsTo(CountryMaster::class, 'country_id');
    }
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(CityMaster::class, 'city_id');
    }
}

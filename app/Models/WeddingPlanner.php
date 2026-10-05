<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeddingPlanner extends Model
{
    use SoftDeletes;
    protected $table = 'wedding_planner';

    protected $primaryKey = 'id';

    public $timestamps = true; // You already have created_at & updated_at

    protected $fillable = [
        'category_id',
        'status',
        'title',
        'planner_name',
        'image',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'capacity',
        'description',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'mobile',
        'email',
        'start_rate_range',
        'end_rate_range',
        'currency',
        'website',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'google_link',
        'map_location'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'country_id'  => 'integer',
        'state_id'    => 'integer',
        'city_id'     => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'category_id');
    }
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }


    public function reviews()
    {
        return $this->hasMany(VendorReview::class, 'vendor_id')->approved();
    }
}

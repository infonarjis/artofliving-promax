<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorReview extends Model
{
    use SoftDeletes;
    protected $table = 'vendor_reviews';

    protected $primaryKey = 'id';

    public $timestamps = false; 
    // Because table does not have updated_at column

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'description',
        'star',
        'created_at',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'star'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function vendor()
    {
        return $this->belongsTo(WeddingPlanner::class, 'vendor_id');
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

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional - Clean Rating Output)
    |--------------------------------------------------------------------------
    */

    public function getStarRatingAttribute()
    {
        return (int) $this->star;
    }
}
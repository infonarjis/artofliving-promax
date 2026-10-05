<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorInquiry extends Model
{
    use SoftDeletes;
    protected $table = 'vendor_inquiry';

    protected $primaryKey = 'id';

    public $timestamps = false; 
    // Because your table uses datetime manually 
    // and does NOT have updated_at column

    protected $fillable = [
        'vendor_id',
        'name',
        'mobile',
        'wedding_date',
        'total_guest',
        'sent_info_by',
        'description',
        'created_at'
    ];

    protected $casts = [
        'wedding_date' => 'datetime',
        'created_at'   => 'datetime',
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
    | Scope (Optional but Recommended)
    |--------------------------------------------------------------------------
    */
}
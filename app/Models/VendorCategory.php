<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorCategory extends Model
{
    use SoftDeletes;

    protected $table = 'vendor_category';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'category_name',
        'image',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function vendors()
    {
        return $this->weddingPlanners();
    }

    public function weddingPlanners()
    {
        return $this->hasMany(WeddingPlanner::class, 'category_id');
    }
}

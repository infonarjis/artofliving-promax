<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffActivity extends Model
{
    use SoftDeletes;
    protected $table = 'staff_activities';

    protected $primaryKey = 'id';

    public $timestamps = false; // table has only created_at

    protected $fillable = [
        'member_id',
        'staff_id',
        'activity_type',
        'description',
        'status',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        // Auto set created_at
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function member()
    {
        return $this->belongsTo(Register::class,'member_id','id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class,'staff_id','id');
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

    public function scopeUnapproved($query)
    {
        return $query->where('status', 'UNAPPROVED');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class FranchiseRole extends Model
{
    use SoftDeletes;

    protected $table = 'franchise_role';

    protected $primaryKey = 'id';

    public $timestamps = false; // table uses created_on, not created_at

    protected $dates = ['deleted_at', 'created_on'];

    protected $fillable = [
        'role_name',
        'view_member',
        'add_member',
        'edit_member',
        'delete_member',
        'view_profile',
        'approve_member',
        'match_making',
        'unapprove_member',
        'send_mail',
        'horoscope_approval',
        'photo_approval',
        'active_to_paid_member',
        'add_comment',
        'view_comment',
        'photo_delete',
        'horoscope_delete',
        'id_proof_approval',
        'id_proof_delete',
        'suspend_member',
        'status',
        'created_on',
    ];

    protected $casts = [
        'created_on' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            self::clearRoleCache($model->id);
        });

        static::deleted(function ($model) {
            self::clearRoleCache($model->id);
        });
    }

    public static function clearRoleCache($roleId)
    {
        Cache::forget("role_perm_Franchise_{$roleId}");
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
    | Helpers (Permission Check)
    |--------------------------------------------------------------------------
    */

    public function hasPermission(string $column, string $expected = 'Yes'): bool
    {
        if (!isset($this->$column)) {
            return false;
        }

        return $this->$column === $expected;
    }

    public function hasMemberAccess(string $column, string $type = 'Own Members'): bool
    {
        if (!isset($this->$column)) {
            return false;
        }

        return in_array($this->$column, ['All Members', $type]);
    }
}

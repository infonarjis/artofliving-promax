<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminAlert extends Model
{
    use SoftDeletes;

    protected $table = 'admin_alerts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'admin_id',
        'admin_type',
        'member_register',
        'photo_upload',
        'id_proof_upload',
        'horoscope_upload',
        'delete_profile_request',
        'affiliate_member',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants (for clean usage)
    |--------------------------------------------------------------------------
    */
    public const TYPE_ADMIN = 'Admin';
    public const TYPE_STAFF = 'Staff';

    public const STATUS_READ = 'Read';
    public const STATUS_UNREAD = 'Unread';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        // Change model if your admin model name is different
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeUnread($query)
    {
        return $query->where(function ($q) {
            $q->where('member_register', self::STATUS_UNREAD)
              ->orWhere('photo_upload', self::STATUS_UNREAD)
              ->orWhere('id_proof_upload', self::STATUS_UNREAD)
              ->orWhere('horoscope_upload', self::STATUS_UNREAD)
              ->orWhere('delete_profile_request', self::STATUS_UNREAD)
              ->orWhere('affiliate_member', self::STATUS_UNREAD);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead(): bool
    {
        return $this->update([
            'member_register'       => self::STATUS_READ,
            'photo_upload'          => self::STATUS_READ,
            'id_proof_upload'       => self::STATUS_READ,
            'horoscope_upload'      => self::STATUS_READ,
            'delete_profile_request'=> self::STATUS_READ,
            'affiliate_member'      => self::STATUS_READ,
        ]);
    }

    public function hasAnyUnread(): bool
    {
        return in_array(self::STATUS_UNREAD, [
            $this->member_register,
            $this->photo_upload,
            $this->id_proof_upload,
            $this->horoscope_upload,
            $this->delete_profile_request,
            $this->affiliate_member,
        ], true);
    }
}
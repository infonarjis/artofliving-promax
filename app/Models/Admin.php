<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'last_activity',
    ];

    public function staffRole()
    {
        return $this->belongsTo(StaffRole::class, 'staff_role_id')->withDefault();
    }
}

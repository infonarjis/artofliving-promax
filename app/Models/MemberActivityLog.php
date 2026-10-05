<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberActivityLog extends Model
{
    protected $fillable = [
        'member_id',
        'target_member_id',
        'activity_type',
        'meta'
    ];
}
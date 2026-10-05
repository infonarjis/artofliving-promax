<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = [
        'member_id',
        'target_member_id',
        'action_type',
        'weight',
    ];

    public const USER_PROFILE_VIEW  = 'user_profile_view';
    public const USER_CONTACT_VIEW  = 'user_contact_view';
    public const SHORTLIST          = 'shortlist';
    public const INTEREST           = 'interest';
    public const INTEREST_ACCEPTED  = 'interest_accepted';
    public const INTEREST_REJECTED  = 'interest_rejected';
    public const PHOTO_REQUEST      = 'photo_request';
    public const PHOTO_ACCEPTED     = 'photo_accepted';
    public const PHOTO_REJECTED     = 'photo_rejected';
    public const BLOCK              = 'block';

    public function member()
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    public function targetMember()
    {
        return $this->belongsTo(Register::class, 'target_member_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateReferralClick extends Model
{
    use SoftDeletes;
    protected $table = 'affiliate_referral_clicks';
    public $timestamps = false;

    protected $fillable = [
        'affiliate_member_id',
        'referral_code',
        'ip_address',
        'browser',
        'device',
        'referral_url',
        'clicked_at'
    ];
}

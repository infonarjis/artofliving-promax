<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class FranchiseLoginHistory extends Model
{
    use SoftDeletes;

    protected $table = 'franchise_login_history';

    protected $fillable = [
        'franchise_id',
        'login_at',
        'logout_at',
        'ip_address',
        'browser',
        'os',
        'device',
        'is_mobile',
        'is_tablet',
        'is_bot',
        'is_bot_name',
    ];

    protected $casts = [
        'login_at'   => 'datetime',
        'logout_at'  => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (very useful for admin reports)
    |--------------------------------------------------------------------------
    */

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('login_at', today());
    }

    public function scopeActiveSession(Builder $query)
    {
        return $query->whereNull('logout_at');
    }

    public function scopeMobile(Builder $query)
    {
        return $query->where('is_mobile', 'yes');
    }

    public function scopeTablet(Builder $query)
    {
        return $query->where('is_tablet', 'yes');
    }

    public function scopeBots(Builder $query)
    {
        return $query->where('is_bot', 'yes');
    }

    public function scopeBetweenDates(Builder $query, $from, $to)
    {
        return $query->whereBetween('login_at', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSessionDurationAttribute()
    {
        if (!$this->logout_at) {
            return null;
        }

        return $this->logout_at->diffForHumans($this->login_at, true);
    }
}
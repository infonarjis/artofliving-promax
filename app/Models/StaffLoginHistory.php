<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffLoginHistory extends Model
{
    use SoftDeletes;

    protected $table = 'staff_login_history';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $dates = [
        'login_at',
        'logout_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'staff_id',
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
        'is_mobile'  => 'boolean',
        'is_tablet'  => 'boolean',
        'is_bot'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public static function logLogin($staffId, $request, $agent)
    {
        return self::create([
            'staff_id'   => $staffId,
            'login_at'   => now(),
            'ip_address' => $request->ip(),
            'browser'    => $agent->browser(),
            'os'         => $agent->platform(),
            'device'     => $agent->device(),
            'is_mobile'  => $agent->isMobile(),
            'is_tablet'  => $agent->isTablet(),
            'is_bot'     => $agent->isRobot(),
            'is_bot_name'=> $agent->robot(),
        ]);
    }

    public function markLogout()
    {
        $this->update([
            'logout_at' => now(),
        ]);
    }
}
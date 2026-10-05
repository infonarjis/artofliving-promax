<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDesignOption extends Model
{
    protected $guarded = ['id'];

    const CATEGORIES = [
        'dashboard_design'     => 'Dashboard Design',
        'profile_card_style'   => 'Profile Card Style',
        'my_profile_design'    => 'My Profile Design',
        'other_profile_design' => 'Other Profile Design',
        'privacy_settings_design'  => 'Privacy Settings Design',
        'bottom_bar_design'  => 'Bottom Bar Design',
    ];

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppThemeSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'setting_group'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = ['mode', 'variable', 'value'];

    public function scopeMode($query, string $mode)
    {
        return $query->where('mode', $mode);
    }
}
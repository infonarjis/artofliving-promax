<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalizedEnquiry extends Model
{
    protected $fillable = [
        'full_name',
        'mobile_no',
        'email',
        'ip_address',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateTestimonial extends Model
{
    use SoftDeletes;
    protected $table = 'affiliate_testimonials';

    protected $fillable = [
        'image',
        'description',
        'name',
        'designation',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }
}
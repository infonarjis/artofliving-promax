<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddOnPackage extends Model
{
    use SoftDeletes;
    
    protected $table = 'add_on_package';

    protected $fillable = [
        'package_title',
        'package_category',
        'package_count',
        'package_amount',
        'package_currency_code',
        'description',
        'in_app_purchase_android_id',
        'in_app_purchase_android_amount',
        'in_app_purchase_ios_id',
        'in_app_purchase_ios_amount',
        'status'
    ];

    protected $casts = [
        'package_count'  => 'integer',
        'package_amount' => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes (VERY IMPORTANT for this table)
    |--------------------------------------------------------------------------
    */
    // Only approved and not deleted
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    // By category
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('package_category', $category);
    }
}
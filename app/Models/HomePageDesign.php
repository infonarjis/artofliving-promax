<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageDesign extends Model
{
    protected $table = 'home_page_designs';

    protected $fillable = [
        'design_key',
        'design_name',
        'thumbnail',
        'view_folder',
        'asset_path',
        'controller_type',
        'schema',
        'is_active',
        'is_default',
        'is_deletable',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'schema'       => 'array',
        'is_active'    => 'boolean',
        'is_default'   => 'boolean',
        'is_deletable' => 'boolean',
    ];

    public function contents()
    {
        return $this->hasMany(HomePageDesignContent::class, 'design_id');
    }

    /** Default-language content row (lang_id is NULL on that row). */
    public function defaultContent()
    {
        return $this->hasOne(HomePageDesignContent::class, 'design_id')->whereNull('lang_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Flatten schema tabs into a single [field_key => field_config] array —
     * used by the controller to build the update() whitelist and to run
     * file-upload handling generically, the same way the legacy
     * HomePageSectionController does with its hardcoded $updateArr.
     */
    public function flatFields(): array
    {
        $fields = [];
        foreach (($this->schema['tabs'] ?? []) as $tab) {
            foreach (($tab['fields'] ?? []) as $key => $config) {
                $fields[$key] = $config;
            }
        }
        return $fields;
    }
}

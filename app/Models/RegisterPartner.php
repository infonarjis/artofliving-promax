<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisterPartner extends Model
{
    use SoftDeletes;
    protected $table = 'register_partners';

    protected $fillable = [
        'member_id',
        'part_marital_status',
        'part_religion',
        'part_caste',
        'part_frm_age',
        'part_to_age',
        'part_height',
        'part_height_to',
        'part_country',
        'part_state',
        'part_income',
        'part_education',
        'part_occupation',
        'part_mothertongue',
        'part_manglik'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $multiSelectFields = [
        'part_marital_status',
        'part_religion',
        'part_caste',
        'part_country',
        'part_state',
        'part_income',
        'part_education',
        'part_occupation',
        'part_mothertongue',
        'part_manglik',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->multiSelectFields) && is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::setAttribute($key, $value);
    }

    // ─── Relationships ─────────────────────────────────────────────────────────
    public function member(): BelongsTo
    {
        return $this->belongsTo(Register::class, 'member_id');
    }

    // Generic Helpers :
    public function getMultiSelectValue($field)
    {
        return !empty($this->$field)
            ? explode(',', $this->$field)
            : [];
    }

    /**
     * Return comma separated field as string.
     */
    public function getMultiSelectString($field): string
    {
        return implode(', ', $this->getMultiSelect($field));
    }
}

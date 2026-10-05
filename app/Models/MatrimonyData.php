<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MatrimonyData extends Model
{
    use SoftDeletes;
    protected $table = 'matrimony_data';

    protected $fillable = [
        'status',
        'pagename',
        'title',
        'slug',
        'matrimony_description',
        'banner_img',
        'search_type',
        'matrimony_name',
        'match_type',
        'matri_id_groom',
        'matri_id_bride',
        'meta_keyword',
        'meta_title',
        'meta_description',
        'lang_code',
        'lang_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'match_type' => 'integer',
        'lang_id'    => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    // protected static function booted()
    // {
    //     static::saved(function ($model) {

    //         // clear page cache
    //         Cache::forget('matrimony_pages_homepage');
    //         Cache::forget('matrimony_page_'.$model->slug);

    //         // clear list caches
    //         $types = ['Religion','Caste','Mother-Tongue','Country','State','City'];

    //         foreach ($types as $type) {
    //             Cache::forget("matrimony_list_{$type}_4");
    //             Cache::forget("matrimony_list_{$type}_all");
    //         }

    //     });

    //     static::deleted(function ($model) {
    //         Cache::forget('matrimony_pages_homepage');
    //         Cache::forget('matrimony_page_'.$model->slug);

    //         $types = ['Religion','Caste','Mother-Tongue','Country','State','City'];

    //         foreach ($types as $type) {
    //             Cache::forget("matrimony_list_{$type}_4");
    //             Cache::forget("matrimony_list_{$type}_all");
    //         }

    //     });
    // }

    public function scopeActive($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeByLang($query, string $lang = 'en')
    {
        return $query->where('lang_code', $lang);
    }

    public function scopeAutoMatch($query)
    {
        return $query->where('match_type', 0);
    }

    public function scopeManualMatch($query)
    {
        return $query->where('match_type', 1);
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isAutoMatch(): bool
    {
        return $this->match_type === 0;
    }

    public function isManualMatch(): bool
    {
        return $this->match_type === 1;
    }

    public function getMatriIdGroomArrayAttribute(): array
    {
        return $this->matri_id_groom ? json_decode($this->matri_id_groom, true) ?? [] : [];
    }

    public function getMatriIdBrideArrayAttribute(): array
    {
        return $this->matri_id_bride ? json_decode($this->matri_id_bride, true) ?? [] : [];
    }


    public function matchTypeLabel(): string
    {
        return $this->match_type === 0 ? 'Auto' : 'Manually';
    }

    public function getMatrimonyNameLabelAttribute(): string
    {
        $ids = array_filter(
            array_map('trim', explode(',', $this->matrimony_name ?? ''))
        );

        if (empty($ids)) {
            return '';
        }

        $modelMap = [
            'Religion'      => [ReligionMaster::class, 'religion_name'],
            'Caste'         => [CasteMaster::class, 'caste_name'],
            'Mother-Tongue' => [MotherTongueMaster::class, 'mtongue_name'],
            'Country'       => [CountryMaster::class, 'country_name'],
            'State'         => [StateMaster::class, 'state_name'],
            'City'          => [CityMaster::class, 'city_name'],
        ];

        if (!isset($modelMap[$this->search_type])) {
            return '';
        }

        [$model, $nameColumn] = $modelMap[$this->search_type];

        return $model::whereIn('id', $ids)
            ->pluck($nameColumn)
            ->implode(', ');
    }

    public function scopeLanguageFallback($query, string $defaultLang, string $lang)
    {
        // No translation needed, just serve default language rows
        if ($lang === $defaultLang) {
            return $query->where('lang_code', $defaultLang);
        }

        return $query->where(function ($q) use ($defaultLang, $lang) {
            // rows already translated into the requested language
            $q->where('lang_code', $lang)
                // OR default-language rows that have no translation yet
                ->orWhere(function ($q2) use ($defaultLang, $lang) {
                    $q2->where('lang_code', $defaultLang)
                        ->whereNotExists(function ($sub) use ($lang) {
                            $sub->select(DB::raw(1))
                                ->from('matrimony_data as t')
                                ->whereColumn('t.lang_id', 'matrimony_data.id')
                                ->where('t.lang_code', $lang)
                                ->where('t.status', 'APPROVED')
                                ->whereNull('t.deleted_at');
                        });
                });
        });
    }

    public function religionData()
    {
        return $this->belongsTo(ReligionMaster::class, 'matrimony_name');
    }

    public function casteData()
    {
        return $this->belongsTo(CasteMaster::class, 'matrimony_name');
    }

    public function motherTongueData()
    {
        return $this->belongsTo(MotherTongueMaster::class, 'matrimony_name');
    }

    public function countryData()
    {
        return $this->belongsTo(CountryMaster::class, 'matrimony_name');
    }

    public function stateData()
    {
        return $this->belongsTo(StateMaster::class, 'matrimony_name');
    }
    
    public function cityData()
    {
        return $this->belongsTo(CityMaster::class, 'matrimony_name');
    }
}

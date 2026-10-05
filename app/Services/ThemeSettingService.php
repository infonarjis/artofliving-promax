<?php

namespace App\Services;

use App\Models\AppThemeSetting;
use App\Support\AppThemeSettingDefinitions;
use Illuminate\Support\Facades\Cache;

class ThemeSettingService
{
    const CACHE_KEY = 'app_theme_settings_kv';

    ## Returns [key => value] for every registered setting, DB values merged over defaults :
    public static function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $defaults = AppThemeSettingDefinitions::defaults();
            $stored = AppThemeSetting::pluck('setting_value', 'setting_key')->toArray();
            return array_merge($defaults, array_filter($stored, fn($v) => !is_null($v)));
        });
    }

    public static function get(string $key, $default = null)
    {
        return self::all()[$key] ?? $default;
    }

    ## Normalizes a Yes/No-style setting into a real boolean, for the API :
    public static function bool(string $key, bool $fallback = true): bool
    {
        $val = self::get($key, $fallback ? 'Yes' : 'No');
        return in_array($val, ['Yes', 'yes', '1', 1, true], true);
    }

    ## Mass upsert — only writes keys that exist in the registry, ignores everything else :
    public static function set(array $data): void
    {
        $definitions = AppThemeSettingDefinitions::all();

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $definitions)) {
                continue;
            }

            AppThemeSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $value,
                    'setting_group' => $definitions[$key]['group'],
                ]
            );
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
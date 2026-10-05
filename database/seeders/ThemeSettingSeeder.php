<?php
// database/seeders/ThemeSettingSeeder.php

namespace Database\Seeders;

use App\Models\ThemeSetting;
use App\Services\ThemeService;
use Illuminate\Database\Seeder;

class ThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ThemeService::defaults() as $mode => $variables) {
            foreach ($variables as $variable => $value) {
                ThemeSetting::updateOrCreate(
                    ['mode' => $mode, 'variable' => $variable],
                    ['value' => $value]
                );
            }
        }
    }
}

// Run once with: php artisan db:seed --class=ThemeSettingSeeder
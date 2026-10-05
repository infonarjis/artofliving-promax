<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Services\ThemeService;
use App\Support\WebThemeColorPresets;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class WebThemeSettingController extends Controller
{
    public function index(): View
    {
        $settings = ThemeService::all();
        $pageName = 'Theme Settings';
        $activePreset = $this->detectActivePreset($settings);

        return view('admin.themeSettings.index', compact('settings', 'pageName', 'activePreset'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'dark'    => ['array'],
            'dark.*'  => ['nullable', 'string', 'max:60', 'regex:/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\))$/'],
            'light'   => ['array'],
            'light.*' => ['nullable', 'string', 'max:60', 'regex:/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\))$/'],
        ]);

        $defaults = ThemeService::defaults();

        foreach (['dark', 'light'] as $mode) {
            foreach ($data[$mode] ?? [] as $variable => $value) {
                // only allow editing variables we actually expose
                if (!array_key_exists($variable, $defaults[$mode]) || $value === null || $value === '') {
                    continue;
                }

                ThemeSetting::updateOrCreate(
                    ['mode' => $mode, 'variable' => $variable],
                    ['value' => $value]
                );
            }
        }

        ThemeService::clearCache();

        return back()->with('success', 'Theme colors updated successfully.');
    }

    ## One-click apply — writes the preset's dark + light colors straight to settings :
    public function applyColorPreset(Request $request): RedirectResponse
    {
        $key = $request->input('preset_key');
        $preset = WebThemeColorPresets::find($key);

        if (!$preset) {
            return back()->with('error', 'Invalid theme preset selected.');
        }

        $defaults = ThemeService::defaults();

        foreach (['dark', 'light'] as $mode) {
            foreach ($preset[$mode] as $variable => $value) {
                // skip variables ThemeService doesn't track (e.g. 'bg-main' isn't in
                // defaults() yet) — writing them would create inert DB rows that
                // generateCss() never reads, and would also break detectActivePreset()
                if (!array_key_exists($variable, $defaults[$mode])) {
                    continue;
                }

                ThemeSetting::updateOrCreate(
                    ['mode' => $mode, 'variable' => $variable],
                    ['value' => $value]
                );
            }
        }

        ThemeService::clearCache();

        return back()->with('success', $preset['name'] . ' theme applied successfully.');
    }

    ## Detects whether current saved dark+light colors exactly match a known preset (for the checkmark) :
    private function detectActivePreset(array $settings): ?string
    {
        $defaults = ThemeService::defaults();

        foreach (WebThemeColorPresets::all() as $key => $preset) {
            foreach (['dark', 'light'] as $mode) {
                foreach ($preset[$mode] as $variable => $value) {
                    // only compare variables ThemeService actually tracks — a preset
                    // key like 'bg-main' that isn't in defaults() has nothing to
                    // compare against and shouldn't disqualify the match
                    if (!array_key_exists($variable, $defaults[$mode])) {
                        continue;
                    }

                    if (($settings[$mode][$variable] ?? null) !== $value) {
                        continue 3;
                    }
                }
            }
            return $key;
        }
        return null;
    }

    public function reset(): RedirectResponse
    {
        ThemeSetting::query()->delete();
        ThemeService::clearCache();

        return back()->with('success', 'Theme colors reset to default.');
    }
}
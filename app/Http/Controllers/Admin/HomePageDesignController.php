<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use App\Models\ThemeSetting;
use App\Services\AdminFormBuilderService;
use App\Services\ThemeService;
use App\Support\HomePageDesignColorPresets;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HomePageDesignController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
    }

    /** Design picker: thumbnail grid, activate / edit / delete. */
    public function index()
    {
        $designs = HomePageDesign::orderBy('sort_order')->orderBy('id')->get();

        $themePresetSwatches = [];
        foreach ($designs as $design) {
            $swatch = HomePageDesignColorPresets::swatchFor($design->design_key);
            if ($swatch) {
                $themePresetSwatches[$design->design_key] = $swatch;
            }
        }

        $dataArr = [
            'pageName'             => 'Homepage Designs',
            'designs'              => $designs,
            'themePresetSwatches'  => $themePresetSwatches,
            'extraJsArr'           => ['/custom/js/homePageDesign/addEdit.js'],
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/homePageDesign/index', $dataArr);
    }

    /** Form to register a brand-new design (schema builder UI). */
    public function create()
    {
        $dataArr = [
            'pageName'   => 'Add New Homepage Design',
            'extraJsArr' => ['/custom/js/homePageDesign/schemaBuilder.js'],
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/homePageDesign/create', $dataArr);
    }

    public function store(Request $request)
    {
        $request->validate([
            'design_key'  => 'required|alpha_dash|unique:home_page_designs,design_key',
            'design_name' => 'required|string|max:150',
            'view_folder' => 'required|alpha_dash',
            'thumbnail'   => 'required|image',
            'schema'      => 'required|json',
        ]);

        $schema = json_decode($request->schema, true) ?: ['tabs' => []];

        $thumbPath = UploadHelper::uploadFile(
            $request->file('thumbnail'),
            _getConstant('upload_path.HOMEPAGE_DESIGN_THUMB_URL'),
            ''
        );

        try {
            $design = HomePageDesign::create([
                'design_key'      => $request->design_key,
                'design_name'     => $request->design_name,
                'thumbnail'       => $thumbPath,
                'view_folder'     => $request->view_folder,
                'asset_path'      => 'assets/home-designs/' . $request->view_folder,
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_active'       => false,
                'is_default'      => false,
                'is_deletable'    => true,
                'sort_order'      => (HomePageDesign::max('sort_order') ?? 0) + 1,
            ]);

            // Seed an empty default-language content row so edit() has
            // something to bind the form to straight away.
            HomePageDesignContent::create([
                'design_id' => $design->id,
                'lang_id'   => null,
                'lang_code' => _getDefaultLanguage(),
                'data'      => array_fill_keys(array_keys($design->flatFields()), ''),
                'status'    => 'APPROVED',
            ]);

            // Best-effort scaffolding — never block the admin flow if the
            // filesystem isn't writable in this environment.
            $this->scaffoldDesignFiles($design);

            return redirect()
                ->route('admin.homePageDesign.edit', $design->id)
                ->with('success', 'Design registered. Now add its content, then drop the real markup into resources/views/home/designs/' . $design->view_folder . '/index.blade.php');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Generic dynamic edit form — one tab per schema['tabs'] entry. */
    public function edit($id)
    {
        $design = HomePageDesign::findOrFail($id);

        if ($design->controller_type === 'legacy') {
            // design1 keeps its own dedicated, already-built admin screen.
            return redirect()->route('admin.homePageSection.index');
        }

        $content = HomePageDesignContent::forDesignAndLanguage(
            $design->id,
            _getDefaultLanguage(),
            _getDefaultLanguage()
        );
        $rowData = (object) ($content->data ?? []);

        $formsByTab = [];
        foreach (($design->schema['tabs'] ?? []) as $tabKey => $tab) {
            $otherData = [
                'mode'    => 'edit',
                'id'      => $design->id,
                'rowData' => $rowData,
            ];
            $formsByTab[$tabKey] = [
                'label' => $tab['label'] ?? ucfirst($tabKey),
                'html'  => $this->adminFormBuilderService->generateFormElement($tab['fields'] ?? [], $otherData),
            ];
        }

        $dataArr = [
            'pageName'        => 'Edit: ' . $design->design_name,
            'mode'            => 'edit',
            'design'          => $design,
            'languageDataArr' => _getActiveLanguage(),
            'formsByTab'      => $formsByTab,
            'extraJsArr'      => ['/custom/js/homePageDesign/addEdit.js'],
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/homePageDesign/edit', $dataArr);
    }

    /** Generic update — works for every dynamic design, driven by its schema. */
    public function update(Request $request, $id)
    {
        $design = HomePageDesign::findOrFail($id);
        $postData = $request->all();
        $fields = $design->flatFields();
        $allowedKeys = array_keys($fields);

        $updateData = array_intersect_key($postData, array_flip($allowedKeys));

        // Generic file-upload handling — identical pattern to
        // HomePageSectionController::update(), driven by schema config
        // instead of a hardcoded array.
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (!in_array($key, $allowedKeys, true) || empty($value['name'])) {
                    continue;
                }
                $path = $fields[$key]['path_value'] ?? 'upload_path.HOMEPAGE_BANNER_IMAGE_URL';
                $oldValue = $postData[$key . '_val'] ?? '';
                $updateData[$key] = UploadHelper::uploadFile(
                    $request->file($key),
                    _getConstant($path),
                    $oldValue
                );
            }
        }

        if (empty(array_filter($updateData))) {
            return redirect()
                ->route('admin.homePageDesign.edit', $design->id)
                ->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'))
                ->with('active_tab', $request->active_tab);
        }

        $defaultLanguage = _getDefaultLanguage();
        $langCode = $request->lang_code ?: $defaultLanguage;

        try {
            $defaultRow = HomePageDesignContent::where('design_id', $design->id)->whereNull('lang_id')->first();

            if ($langCode === $defaultLanguage || !$defaultRow) {
                if (!$defaultRow) {
                    $defaultRow = HomePageDesignContent::create([
                        'design_id' => $design->id,
                        'lang_id'   => null,
                        'lang_code' => $defaultLanguage,
                        'data'      => [],
                        'status'    => 'APPROVED',
                    ]);
                }
                $defaultRow->update(['data' => array_merge($defaultRow->data ?? [], $updateData)]);
            } else {
                $row = HomePageDesignContent::where('design_id', $design->id)
                    ->where('lang_code', $langCode)
                    ->first();

                if ($row) {
                    $row->update(['data' => array_merge($row->data ?? [], $updateData)]);
                } else {
                    HomePageDesignContent::create([
                        'design_id' => $design->id,
                        'lang_id'   => $defaultRow->id,
                        'lang_code' => $langCode,
                        'data'      => $updateData,
                        'status'    => 'APPROVED',
                    ]);
                }
            }

            return redirect()
                ->route('admin.homePageDesign.edit', $design->id)
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'))
                ->with('active_tab', $request->active_tab);
        } catch (Exception $e) {
            return redirect()
                ->route('admin.homePageDesign.edit', $design->id)
                ->with('error', $e->getMessage())
                ->with('active_tab', $request->active_tab);
        }
    }

    /**
     * Schema manager — lets the admin add/rename/remove tabs and fields on
     * a design that already exists, without touching code. Reuses the same
     * JS builder as create(), just pre-filled with the current schema.
     */
    public function manageFields($id)
    {
        $design = HomePageDesign::findOrFail($id);

        if ($design->controller_type === 'legacy') {
            return redirect()->route('admin.homePageSection.index');
        }

        $dataArr = [
            'pageName'   => 'Manage Fields: ' . $design->design_name,
            'design'     => $design,
            'extraJsArr' => ['/custom/js/homePageDesign/schemaBuilder.js'],
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/homePageDesign/manageFields', $dataArr);
    }

    /**
     * Saves the edited schema. Existing field values are preserved (merge,
     * not replace) — new fields are added with an empty value to every
     * language row that already exists; fields removed from the schema
     * keep their stored value (harmless) in case they're re-added later.
     */
    public function updateSchema(Request $request, $id)
    {
        $request->validate(['schema' => 'required|json']);

        $design = HomePageDesign::findOrFail($id);
        $newSchema = json_decode($request->schema, true) ?: ['tabs' => []];

        try {
            $design->update(['schema' => $newSchema]);

            $newFieldKeys = array_keys($design->fresh()->flatFields());
            foreach ($design->contents as $content) {
                $data = $content->data ?? [];
                foreach ($newFieldKeys as $key) {
                    if (!array_key_exists($key, $data)) {
                        $data[$key] = '';
                    }
                }
                $content->update(['data' => $data]);
            }

            return redirect()
                ->route('admin.homePageDesign.edit', $design->id)
                ->with('success', 'Fields updated. New fields are ready to fill in below.');
        } catch (Exception $e) {
            return redirect()
                ->route('admin.homePageDesign.manageFields', $design->id)
                ->with('error', $e->getMessage());
        }
    }

    /** Only one homepage design may be live at a time.
     *  If this design has a matching sitewide color preset, the admin
     *  explicitly chose (via the activation-confirm modal) whether to
     *  also apply it to Site Theme Colors — never applied silently. */
    public function activate(Request $request, $id)
    {
        $design = HomePageDesign::findOrFail($id);

        HomePageDesign::query()->update(['is_active' => false]);
        $design->update(['is_active' => true]);

        $message = $design->design_name . ' is now the live homepage.';

        if ($request->boolean('apply_theme_colors')) {
            $applied = $this->applySiteThemePreset($design->design_key);
            if ($applied) {
                $message .= ' Site-wide theme colors were updated to match.';
            }
        }

        return redirect()
            ->route('admin.homePageDesign.index')
            ->with('success', $message);
    }

    /** Writes the design's preset into theme_settings so ThemeService::all()
     *  picks it up everywhere (Site Theme Colors screen + generated CSS).
     *  Returns false (no-op) if this design has no preset defined. */
    private function applySiteThemePreset(string $designKey): bool
    {
        $preset = HomePageDesignColorPresets::for($designKey);

        if (!$preset) {
            return false; // no preset defined for this design — leave current site colors untouched
        }

        foreach (['dark', 'light'] as $mode) {
            foreach ($preset[$mode] as $variable => $value) {
                ThemeSetting::updateOrCreate(
                    ['mode' => $mode, 'variable' => $variable],
                    ['value' => $value]
                );
            }
        }

        ThemeService::clearCache();

        return true;
    }

    /**
     * Deleting a design removes its DB row + content (and best-effort its
     * blade view / asset folder). Blocked for the default design and
     * blocked entirely in demo mode.
     */
    public function destroy($id)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return redirect()
                ->route('admin.homePageDesign.index')
                ->with('error', 'Deleting homepage designs is disabled in demo mode.');
        }

        $design = HomePageDesign::findOrFail($id);

        if ($design->is_default || !$design->is_deletable) {
            return redirect()
                ->route('admin.homePageDesign.index')
                ->with('error', 'The default homepage design cannot be deleted.');
        }

        try {
            $design->contents()->delete();

            if ($design->view_folder) {
                $viewDir = resource_path('views/home/designs/' . $design->view_folder);
                if (File::isDirectory($viewDir)) {
                    File::deleteDirectory($viewDir);
                }
            }
            if ($design->asset_path) {
                $assetDir = public_path($design->asset_path);
                if (File::isDirectory($assetDir)) {
                    File::deleteDirectory($assetDir);
                }
            }

            $design->delete();

            return redirect()
                ->route('admin.homePageDesign.index')
                ->with('success', 'Homepage design deleted.');
        } catch (Exception $e) {
            return redirect()
                ->route('admin.homePageDesign.index')
                ->with('error', $e->getMessage());
        }
    }

    public function getLangData(Request $request)
    {
        $design = HomePageDesign::findOrFail($request->id);
        $langCode = $request->langCode;
        $defaultLanguage = _getDefaultLanguage();

        $row = HomePageDesignContent::forDesignAndLanguage($design->id, $langCode, $defaultLanguage);
        $data = $row->data ?? array_fill_keys(array_keys($design->flatFields()), '');

        if ($langCode !== $defaultLanguage) {
            // Don't prefill translation fields with the default language's
            // text/images when no translation exists yet — same behaviour
            // as HomePageSectionController::getLangData().
            $exists = HomePageDesignContent::where('design_id', $design->id)
                ->where('lang_code', $langCode)->exists();
            if (!$exists) {
                $data = array_fill_keys(array_keys($data), '');
            }
        }

        $data['lang_code'] = $langCode;

        return response()->json($data);
    }

    /**
     * Best-effort scaffolding so registering a design in the admin UI
     * immediately gives the developer somewhere to paste the real markup.
     * Never throws — filesystem permissions vary per environment.
     */
    private function scaffoldDesignFiles(HomePageDesign $design): void
    {
        try {
            $viewDir = resource_path('views/home/designs/' . $design->view_folder);
            if (!File::isDirectory($viewDir)) {
                File::makeDirectory($viewDir, 0755, true);
                File::put($viewDir . '/index.blade.php', $this->stubBlade($design));
            }

            foreach (['css', 'js', 'images'] as $sub) {
                $dir = public_path($design->asset_path . '/' . $sub);
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
            }
        } catch (Exception $e) {
            // Swallow — scaffolding is a convenience, not a hard requirement.
        }
    }

    private function stubBlade(HomePageDesign $design): string
    {
        $fieldEchoes = implode("\n", array_map(
            fn($key) => "        {{-- {$key}: {{ \$data['{$key}'] ?? '' }} --}}",
            array_keys($design->flatFields())
        ));

        return <<<BLADE
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ \$data['banner_title'] ?? '{$design->design_name}' }}</title>
    <link rel="stylesheet" href="{{ asset('{$design->asset_path}/css/main.css') }}">
</head>
<body>
    {{-- TODO: paste the real {$design->design_key} homepage markup here. --}}
    {{-- Available in \$data (from HomePageDesignContent), keyed by schema field: --}}
{$fieldEchoes}

    {{-- Shared, non-editable data available on every design: --}}
    {{-- \$latestProfile, \$successStoryArr, \$matrimonyPagesData, \$religionList --}}

    <script src="{{ asset('{$design->asset_path}/js/main.js') }}"></script>
</body>
</html>
BLADE;
    }
}

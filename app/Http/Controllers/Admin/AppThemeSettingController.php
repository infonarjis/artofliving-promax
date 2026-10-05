<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AppDesignOption;
use App\Services\AdminFormBuilderService;
use App\Services\ThemeSettingService;
use App\Support\AppThemeColorPresets;
use App\Support\AppThemeSettingDefinitions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppThemeSettingController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;

    ## tab-key => [label, icon] — the single source of truth for the sidebar + panels :
    private array $tabs = [
        'color-typography' => ['label' => 'Colors & Typography', 'icon' => 'bx-palette',      'demo_locked' => false],
        'design-gallery'   => ['label' => 'Design Gallery',      'icon' => 'bx bx-images',    'demo_locked' => false],
        'general-app'      => ['label' => 'General App',         'icon' => 'bx-mobile',       'demo_locked' => false],
        'feature-toggle'   => ['label' => 'Feature Controls',    'icon' => 'bx-toggle-left',  'demo_locked' => false],
        'maintenance'      => ['label' => 'Maintenance',         'icon' => 'bx-wrench',       'demo_locked' => true],
        'app-update'       => ['label' => 'App Force Update',    'icon' => 'bx-refresh',      'demo_locked' => true],
    ];

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->middleware(fn($request, $next) => _checkAdminAccessOnly() ?: $next($request));
        $this->adminFormBuilderService = $adminFormBuilderService;
    }

    public function index()
    {
        $tabFormHtml = [];
        foreach (array_keys($this->tabs) as $tabKey) {
            $tabFormHtml[$tabKey] = $this->buildTabForm($tabKey);
        }

        $dataArr = [
            'pageName' => 'App Theme Settings',
            'id' => '1',
            'tabs' => $this->tabs,
            'tabFormHtml' => $tabFormHtml,
            'designGalleryHtml' => $this->designGalleryAddEditForm(),
            'isDemoDisabled' => _getConstant('DISABLE_DEMO') == 'Enabled',
        ];

    return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/appThemeSetting/addEdit', $dataArr);
    }

    ## Whether this specific tab is blocked right now (demo mode + tab is marked locked) :
    private function isTabDemoBlocked(string $tab): bool
    {
        $isDemo = _getConstant('DISABLE_DEMO') == 'Enabled';
        $isLocked = $this->tabs[$tab]['demo_locked'] ?? true; // default to locked if undefined, safest default

        return $isDemo && $isLocked;
    }

    ## Generic form renderer — drives every settings tab from the registry :
    private function buildTabForm(string $tab): string
    {
        $definitions = AppThemeSettingDefinitions::group($tab);
        $currentValues = ThemeSettingService::all();

        $elementArr = [];
        foreach ($definitions as $key => $def) {
            $field = [
                'label'  => $def['label'],
                'column' => $def['column'],
            ];

            switch ($def['type']) {
                case 'color':
                    $field['input_type'] = 'color';
                    break;
                case 'number':
                    $field['input_type'] = 'number';
                    if (!empty($def['other'])) {
                        $field['other'] = $def['other'];
                    }
                    break;
                case 'url':
                    $field['input_type'] = 'url';
                    break;
                case 'textarea':
                    $field['type'] = 'textarea';
                    break;
                case 'dropdown':
                    $field['type'] = 'dropdown';
                    $field['value_arr'] = $def['options'];
                    break;
                case 'radio_bool':
                    $field['type'] = 'radio';
                    $field['value_arr'] = ['Yes' => 'Yes', 'No' => 'No'];
                    break;
            }

            $elementArr[$key] = $field;
        }

        $otherData = [
            'mode' => 'edit',
            'id' => 1,
            'rowData' => (object) ($currentValues ?? []),
        ];

        $formHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        ## Inject the color preset picker above the color fields on this specific tab :
        if ($tab === 'color-typography') {
            $formHtml = $this->buildColorPresetPicker() . $formHtml;
        }

        return $formHtml;
    }

    public function saveTab(Request $request, string $tab)
    {
        if (!array_key_exists($tab, $this->tabs)) {
            abort(404);
        }

        if ($this->isTabDemoBlocked($tab)) {
            return back()->with('active_tab', $tab)->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $definitions = AppThemeSettingDefinitions::group($tab);
        $postData = $request->all();

        $updateData = [];
        foreach ($definitions as $key => $def) {
            if ($def['type'] === 'radio_bool') {
                $updateData[$key] = $postData[$key] ?? $def['default'];
            } elseif (array_key_exists($key, $postData)) {
                $updateData[$key] = $postData[$key];
            }
        }

        if (!empty($updateData)) {
            ThemeSettingService::set($updateData);
            return back()->with('active_tab', $tab)->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }

        return back()->with('active_tab', $tab)->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    public function resetTab(Request $request, string $tab)
    {
        if (!array_key_exists($tab, $this->tabs)) {
            abort(404);
        }

        if ($this->isTabDemoBlocked($tab)) {
            return back()->with('active_tab', $tab)->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $definitions = AppThemeSettingDefinitions::group($tab);

        $defaultData = [];
        foreach ($definitions as $key => $def) {
            $defaultData[$key] = $def['default'];
        }

        if (!empty($defaultData)) {
            ThemeSettingService::set($defaultData);
            return back()->with('active_tab', $tab)->with('success', 'Settings reset to default values.');
        }

        return back()->with('active_tab', $tab)->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    public function designGalleryAddEditForm()
    {
        $categories = AppDesignOption::CATEGORIES;

        $activeCategory = session('active_design_category', array_key_first($categories));
        if (!array_key_exists($activeCategory, $categories)) {
            $activeCategory = array_key_first($categories);
        }

        $navHtml = '<ul class="nav nav-pills design-gallery-subtabs mb-4" id="designGalleryTabs" role="tablist">';
        $paneHtml = '<div class="tab-content" id="designGalleryTabContent">';

        foreach ($categories as $category => $label) {
            $tabId = 'designcat-' . $category;
            $isActive = $category === $activeCategory;

            $navHtml .= '
            <li class="nav-item" role="presentation">
                <button class="nav-link ' . ($isActive ? 'active' : '') . '" id="' . $tabId . '-tab"
                    data-bs-toggle="pill" data-bs-target="#' . $tabId . '" type="button" role="tab">
                    ' . e($label) . '
                </button>
            </li>';

            $paneHtml .= '<div class="tab-pane fade ' . ($isActive ? 'show active' : '') . '" id="' . $tabId . '" role="tabpanel">';
            $paneHtml .= $this->renderDesignCategoryPane($category, $label);
            $paneHtml .= '</div>';
        }

        $navHtml .= '</ul>';
        $paneHtml .= '</div>';

        return $navHtml . $paneHtml;
    }

    private function renderDesignCategoryPane(string $category, string $label): string
    {
        $options = AppDesignOption::category($category)->orderBy('id', 'ASC')->get();
        $addModalId = 'addDesignModal_' . $category;

        $html = '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<p class="text-muted small mb-0">' . $options->count() . ' design(s) uploaded</p>';
        // $html .= '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#' . $addModalId . '">
        //         <i class="bx bx-plus"></i> Add Design
        //       </button>';
        $html .= '</div>';
        $html .= '<div class="design-card-grid">';

        if ($options->isEmpty()) {
            $html .= '<p class="text-muted small mb-0">No designs uploaded yet for this category.</p>';
        }

        foreach ($options as $option) {
            $imageUrl = _assetUrl('upload_path.DESIGN_IMAGE_URL') . $option->image;
            $isApproved = $option->status === 'approved';
            $displayKey = ucwords(str_replace('_', ' ', $option->design_key));
            $editModalId = 'editDesignModal_' . $option->id;

            $html .= '<div class="design-card' . ($isApproved ? ' is-approved' : '') . '">';
            $html .= '<div class="design-card-img-wrap">';
            $html .= '<span class="design-status-badge ' . ($isApproved ? 'approved' : 'pending') . '">'
                . ($isApproved ? '<i class="bx bx-check-circle"></i> Approved' : '<i class="bx bx-time-five"></i> Pending')
                . '</span>';
            $html .= '<button type="button" class="design-edit-btn" data-bs-toggle="modal" data-bs-target="#' . $editModalId . '" title="Replace image">
                <i class="bx bx-pencil"></i>
              </button>';
            $html .= '<a href="' . e($imageUrl) . '" target="_blank" rel="noopener" class="design-img-link" title="Open full image in new tab">
                <img src="' . e($imageUrl) . '" alt="' . e($displayKey) . '" loading="lazy">
                <span class="design-zoom-hint"><i class="bx bx-link-external"></i></span>
              </a>';
            $html .= '</div>';

            $html .= '<div class="design-card-body">';
            $html .= '<p class="design-card-title">' . e($displayKey) . '</p>';
            $html .= '<div class="design-card-actions">';

            if ($isApproved) {
                $html .= '<span class="btn btn-sm btn-success w-100 disabled"><i class="bx bx-check"></i> Active</span>';
            } else {
                $html .= '
            <form method="POST" action="' . route('admin.designOptionApprove') . '" class="flex-fill">
                ' . csrf_field() . '
                <input type="hidden" name="id" value="' . $option->id . '">
                <button type="submit" class="btn btn-sm btn-outline-success w-100">Approve</button>
            </form>';
            }

            // $html .= '
            // <form method="POST" action="' . route('admin.designOptionDelete') . '" class="flex-fill" onsubmit="return confirm(\'Delete this design?\');">
            //     ' . csrf_field() . '
            //     <input type="hidden" name="id" value="' . $option->id . '">
            //     <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
            // </form>';

            $html .= '</div></div></div>';

            ## Edit (replace image) modal for this specific design :
            $html .= '
                <div class="modal fade" id="' . $editModalId . '" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="' . route('admin.designOptionEdit') . '" enctype="multipart/form-data">
                                ' . csrf_field() . '
                                <input type="hidden" name="id" value="' . $option->id . '">
                                <div class="modal-header">
                                    <h6 class="modal-title">Replace Image — ' . e($displayKey) . '</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3 text-center">
                                        <img src="' . e($imageUrl) . '" alt="' . e($displayKey) . '" style="max-height:180px;max-width:100%;border:1px solid #e5e7eb;border-radius:6px;">
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label">New Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp" required>
                                        <small class="text-muted">PNG, JPG, or WEBP. Max 3MB. Replaces the current image; status and design key stay the same.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>';
        }

        $html .= '</div>'; // .design-card-grid

        ## Add Design Modal :
        $html .= '
        <div class="modal fade" id="' . $addModalId . '" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="' . route('admin.designOptionUpload') . '" enctype="multipart/form-data">
                        ' . csrf_field() . '
                        <input type="hidden" name="category" value="' . e($category) . '">
                        <div class="modal-header">
                            <h6 class="modal-title">Add ' . e($label) . '</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp" required>
                                <small class="text-muted">PNG, JPG, or WEBP. Max 3MB. Full-screen mockups work best.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';

        return $html;
    }

    public function designOptionUpload(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'design-gallery')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|in:' . implode(',', array_keys(AppDesignOption::CATEGORIES)),
            'image'    => 'required|image|mimes:png,jpg,jpeg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            return back()->with('active_tab', 'design-gallery')->with('active_design_category', $request->input('category'))->with('error', ucwords($validator->errors()->first()));
        }

        $category = $request->input('category');
        $path = _getConstant('upload_path.DESIGN_IMAGE_URL');
        $uploadedFile = UploadHelper::uploadFile($request->file('image'), $path);

        $hasExistingApproved = AppDesignOption::category($category)->approved()->exists();

        $data = AppDesignOption::create([
            'category' => $category,
            'image'    => $uploadedFile,
            'status'   => $hasExistingApproved ? 'pending' : 'approved',
        ]);

        $lastDesignKey = AppDesignOption::where('category', $category)
            ->where('id', '!=', $data->id)
            ->whereNotNull('design_key')
            ->orderByDesc('id')
            ->value('design_key');

        $lastNumber = 0;
        if ($lastDesignKey && preg_match('/(\d+)$/', $lastDesignKey, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $data->update(['design_key' => 'design_' . ($lastNumber + 1)]);

        Cache::forget('design_options_approved');

        return back()
            ->with('active_tab', 'design-gallery')
            ->with('active_design_category', $category) // stay on this category's tab
            ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    ## Replace the image on an existing design — status and design_key are preserved :
    public function designOptionEdit(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()
                ->with('active_tab', 'design-gallery')
                ->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer|exists:app_design_options,id',
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:3072',
        ]);

        $option = AppDesignOption::find($request->input('id'));

        if ($validator->fails() || !$option) {
            return back()
                ->with('active_tab', 'design-gallery')
                ->with('active_design_category', $option->category ?? null)
                ->with('error', $validator->fails() ? ucwords($validator->errors()->first()) : _getConstant('responce_message.DATA_NOT_UPDATED'));
        }

        $path = _getConstant('upload_path.DESIGN_IMAGE_URL');

        ## Remove old file before saving the new one :
        $oldFilePath = $path . $option->image;
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        $uploadedFile = UploadHelper::uploadFile($request->file('image'), $path);
        $option->update(['image' => $uploadedFile]);

        Cache::forget('design_options_approved');

        return back()
            ->with('active_tab', 'design-gallery')
            ->with('active_design_category', $option->category)
            ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    public function designOptionApprove(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'design-gallery')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $option = AppDesignOption::find($request->input('id'));

        if (!$option) {
            return back()->with('active_tab', 'design-gallery')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }

        $category = $option->category;

        DB::transaction(function () use ($option) {
            AppDesignOption::category($option->category)->update(['status' => 'pending']);
            $option->update(['status' => 'approved']);
        });

        Cache::forget('design_options_approved');

        return back()
            ->with('active_tab', 'design-gallery')
            ->with('active_design_category', $category)
            ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    public function designOptionDelete(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('active_tab', 'design-gallery')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $option = AppDesignOption::find($request->input('id'));
        $category = $option->category ?? null;

        if ($option) {
            $path = _getConstant('upload_path.DESIGN_IMAGE_URL') . $option->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $option->delete();
            Cache::forget('design_options_approved');
        }

        return back()
            ->with('active_tab', 'design-gallery')
            ->with('active_design_category', $category)
            ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }

    ## Renders the preset swatch picker, injected above the Colors & Typography form :
    private function buildColorPresetPicker(): string
    {
        $currentColors = ThemeSettingService::all();
        $activePreset = $this->detectActivePreset($currentColors);

        $html = '<div class="col-12 mb-4">';
        $html .= '<label class="form-label d-block mb-2">Quick Theme Presets</label>';
        $html .= '<div class="color-preset-grid">';

        foreach (AppThemeColorPresets::all() as $key => $preset) {
            $isActive = $activePreset === $key;
            $colors = $preset['colors'];
            $html .= '<div class="color-preset-card' . ($isActive ? ' is-active' : '') . '" data-preset="' . e($key) . '"'
                . ' data-colors=\'' . json_encode($colors) . '\'>';
            $html .= '<div class="title-btn">';
            $html .= '<p class="color-preset-name">' . ($isActive ? ' <i class="bx bx-check-circle text-success"></i>' : '') . e(Str::limit($preset['name'], 20))
                . '</p>';
            $html .= '<button type="button" class="btn btn-sm btn-primary color-preset-apply-btn" style="font-size:10px;" data-preset-key="' . e($key) . '">Apply Now</button>';
            $html .= "</div>";
            $html .= '<div class="color-preset-swatches">';
            foreach (['primary_color', 'secondary_color', 'gradient_start', 'gradient_end', 'scaffold_bg_color'] as $swatchKey) {
                $html .= '<span class="swatch" style="background:' . e($colors[$swatchKey]) . '"></span>';
            }
            $html .= '</div>';
            $html .= '<div class="color-preset-actions">';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    ## Detects whether current saved colors exactly match a known preset (for the checkmark) :
    private function detectActivePreset(array $currentColors): ?string
    {
        foreach (AppThemeColorPresets::all() as $key => $preset) {
            $matches = true;
            foreach ($preset['colors'] as $field => $value) {
                if (($currentColors[$field] ?? null) !== $value) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                return $key;
            }
        }
        return null;
    }

    ## One-click apply — writes the preset's colors straight to settings, no form step needed :
    public function applyColorPreset(Request $request)
    {
        if ($this->isTabDemoBlocked('color-typography')) {
            return back()->with('active_tab', 'color-typography')->with('error', _getConstant('responce_message.DISABLE_IN_DEMO_LABEL'));
        }

        $key = $request->input('preset_key');
        $preset = AppThemeColorPresets::find($key);

        if (!$preset) {
            return back()->with('active_tab', 'color-typography')->with('error', 'Invalid theme preset selected.');
        }

        ThemeSettingService::set($preset['colors']);

        return back()->with('active_tab', 'color-typography')->with('success', $preset['name'] . ' theme applied successfully.');
    }
}

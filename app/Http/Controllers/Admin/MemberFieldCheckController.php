<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberFieldCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class MemberFieldCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
    }

    public function index()
    {
        MemberFieldCheck::syncFromConfig();

        $config = config('member_field_settings', []);

        $stored = MemberFieldCheck::active()
            ->get()
            ->keyBy(fn($row) => $row->section_name . '|' . $row->field_name);

        $sections = [];
        foreach ($config as $sectionName => $fields) {
            foreach ($fields as $fieldName => $fieldConfig) {
                $locked     = strtolower($fieldConfig['field_disable'] ?? 'No') === 'yes';
                $searchable = strtolower($fieldConfig['field_show_in_search'] ?? 'Yes') === 'yes';

                if ($locked) {
                    $selectedAll = MemberFieldCheck::forcedTokens($searchable);
                } else {
                    $row = $stored->get($sectionName . '|' . $fieldName);
                    $selectedAll = $row && $row->field_value ? explode(',', $row->field_value) : [];
                }

                $sections[$sectionName][] = [
                    'name'            => $fieldName,
                    'label'           => $fieldConfig['label'] ?? MemberFieldCheck::label($fieldName),
                    'locked'          => $locked,
                    'searchable'      => $searchable,
                    'selected_pages'  => array_values(array_intersect($selectedAll, array_keys(MemberFieldCheck::PAGE_OPTIONS))),
                    'selected_search' => array_values(array_intersect($selectedAll, array_keys(MemberFieldCheck::SEARCH_OPTIONS))),
                ];
            }
        }

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/memberFieldCheck/addEdit', [
            'pageName'      => 'Member Field Enable Disable Settings',
            'formUrl'       => 'admin.memberFieldAddEdit',
            'sections'      => $sections,
            'pageOptions'   => MemberFieldCheck::PAGE_OPTIONS,
            'searchOptions' => MemberFieldCheck::SEARCH_OPTIONS,
        ]);
    }

    public function memberFieldAddEdit(Request $request)
    {
        $section = $request->input('section_name');
        $config  = config("member_field_settings.$section", []);

        foreach ($config as $fieldName => $fieldConfig) {
            $locked     = strtolower($fieldConfig['field_disable'] ?? 'No') === 'yes';
            $searchable = strtolower($fieldConfig['field_show_in_search'] ?? 'Yes') === 'yes';

            if ($locked) {
                $combined = MemberFieldCheck::forcedTokens($searchable);
            } else {
                $pages = (array) $request->input("fields.$fieldName.pages", []);

                if ($searchable) {
                    $search = (array) $request->input("fields.$fieldName.search", []);
                    $search = array_diff($search, ['search_result']);
                    if (array_intersect($search, ['quick_search', 'advance_search'])) {
                        $search[] = 'search_result';
                    }
                } else {
                    $search = [];
                }

                $combined = array_unique(array_merge($pages, $search));
            }

            MemberFieldCheck::updateOrInsert(
                ['section_name' => $section, 'field_name' => $fieldName],
                ['field_value' => implode(',', $combined), 'status' => 'APPROVED', 'updated_at' => now()]
            );
        }

        // member_field_check_map
        Cache::forget('member_field_check_map');
        // Artisan::call('optimize:clear');

        return redirect()->route('admin.memberFieldAddEditForm')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
    }
}
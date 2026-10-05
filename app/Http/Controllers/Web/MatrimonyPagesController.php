<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\CountryMaster;
use App\Models\MatrimonyData;
use App\Models\MotherTongueMaster;
use App\Models\Register;
use App\Models\ReligionMaster;
use App\Models\StateMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MatrimonyPagesController extends Controller
{
    protected $cacheTtl = 3600; // Cache time in seconds (1 hour)

    // search_type (DB value) => translation key, single source of truth for both methods below
    protected $typeLabelKeys = [
        'Religion'      => 'field_lbl_religion',
        'Caste'         => 'field_lbl_caste',
        'Mother-Tongue' => 'field_lbl_mother_tongue',
        'Country'       => 'field_lbl_country',
        'State'         => 'field_lbl_state',
        'City'          => 'field_lbl_city',
    ];

    /**
     * Show the matrimony page by slug.
     */
    public function index($slug)
    {
        $currentLanguage = App::getLocale();
        $defaultLanguage = _getDefaultLanguage();

        $matrimony = MatrimonyData::active()
            ->languageFallback($defaultLanguage, $currentLanguage)
            ->where('slug', $slug)
            ->first();

        if ($matrimony === null) {
            abort(404);
        }

        $matrimony->matrimony_name = $this->resolveLabel($matrimony);

        $listType = [];
        foreach ($this->typeLabelKeys as $type => $labelKey) {
            $listType[$type] = $this->getMatrimonyList($type, 4); // plain collection again
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.matrimonyPages.index', [
            'matrimony'    => $matrimony,
            'listType'     => $listType,
            'religionList' => ReligionMaster::getDropdown($currentLanguage),
        ]);
    }

    public function moreDetails($type)
    {
        $typeMapping = [
            'religion'      => 'Religion',
            'caste'         => 'Caste',
            'mother-tongue' => 'Mother-Tongue',
            'country'       => 'Country',
            'state'         => 'State',
            'city'          => 'City',
        ];

        if (!isset($typeMapping[$type])) {
            return redirect()->route('web.home.index');
        }

        $searchType = $typeMapping[$type];

        $result = $this->getMatrimonyList($searchType); // No limit for more details page

        $listType = [];
        foreach ($this->typeLabelKeys as $value => $labelKey) {
            $listType[$value] = $this->getMatrimonyList($value, 4); // plain collection again
        }

        $currentLanguage = App::getLocale();
        $religionList = ReligionMaster::getDropdown($currentLanguage);
        $pageLabel = __('messages.' . $this->typeLabelKeys[$searchType]);

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.matrimonyPages.more_details',
            compact('listType', 'result', 'type', 'religionList', 'pageLabel')
        );
    }

    /**
     * Get matrimony list by search type with optional limit, translated label included.
     */
    protected function getMatrimonyList(string $searchType, ?int $limit = null)
    {
        $currentLanguage = App::getLocale();
        $defaultLanguage = _getDefaultLanguage();

        $relationMap = [
            'Religion'      => 'religionData',
            'Caste'         => 'casteData',
            'Mother-Tongue' => 'motherTongueData',
            'Country'       => 'countryData',
            'State'         => 'stateData',
            'City'          => 'cityData',
        ];

        $query = MatrimonyData::active()
            ->languageFallback($defaultLanguage, $currentLanguage)
            ->where('search_type', $searchType)
            ->select(['id', 'pagename', 'matrimony_name', 'slug', 'search_type']);
        if (isset($relationMap[$searchType])) {
            $query->with($relationMap[$searchType]);
        }
        if ($limit) {
            $query->limit($limit);
        }
        $items = $query->get();

        foreach ($items as $item) {
            $item->matrimony_name = $this->resolveLabel($item, $relationMap);
        }

        return $items;
    }

    /**
     * Resolve the translated display label for a MatrimonyData row based on its search_type.
     */
    protected function resolveLabel(MatrimonyData $item, array $relationMap = [])
    {
        $relationMap = $relationMap ?: [
            'Religion'      => 'religionData',
            'Caste'         => 'casteData',
            'Mother-Tongue' => 'motherTongueData',
            'Country'       => 'countryData',
            'State'         => 'stateData',
            'City'          => 'cityData',
        ];

        $relation = $relationMap[$item->search_type] ?? null;

        return $relation ? optional($item->{$relation})->translated_name : null;
    }

    public function members(Request $request, $slug)
    {
        $gender = $request->input('gender', 'Male');

        $matrimony = MatrimonyData::active()->where('slug', $slug)->firstOrFail();

        $query = Register::active()->where('gender', $gender);
        // Match type based filtering
        if ($matrimony->match_type == 0) {
            switch ($matrimony->search_type) {
                case 'Religion':
                    $search = ReligionMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('religion', $search->id);
                    }
                    break;

                case 'Caste':
                    $search = CasteMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('caste', $search->id);
                    }
                    break;

                case 'Mother-Tongue':
                    $search = MotherTongueMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('mother_tongue', $search->id);
                    }
                    break;

                case 'Country':
                    $search = CountryMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('country_id', $search->id);
                    }
                    break;

                case 'State':
                    $search = StateMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('state_id', $search->id);
                    }
                    break;

                case 'City':
                    $search = CityMaster::active()->where('id', $matrimony->matrimony_name)->first();
                    if ($search) {
                        $query->where('city', $search->id);
                    }
                    break;
            }
        } else {
            if ($gender === 'Female' && !blank($matrimony->matri_id_bride)) {
                $ids = is_array($matrimony->matri_id_bride) ? $matrimony->matri_id_bride : json_decode($matrimony->matri_id_bride, true);
                $query->whereIn('matri_id', $ids ?? []);
            }

            if ($gender === 'Male' && !blank($matrimony->matri_id_groom)) {
                $ids = is_array($matrimony->matri_id_groom) ? $matrimony->matri_id_groom : json_decode($matrimony->matri_id_groom, true);
                $query->whereIn('matri_id', $ids ?? []);
            }
        }
        $members = $query
            ->select([
                'id',
                'matri_id',
                'fullname',
                'gender',
                'birthdate',
                'height',
                'religion',
                'photo1',
                'photo1_status',
                'plan_name',
                'plan_status',
                'city',
                'state_id',
                'country_id',
                'photo_visibility'
            ])
            ->latest()->paginate(20);
        foreach ($members as $p) {
            $p->hasPhotoRequestAccess = '';
        }

        return response()->json([
            'html' => view('web.matrimonyPages.member_ajax_result', compact('members'))->render()
        ]);
    }
}

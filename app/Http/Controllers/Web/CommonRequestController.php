<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\StateMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CommonRequestController extends Controller
{
    public function searchCities(Request $request)
    {
        $search = $request->q;

        $cities = CityMaster::active()->where('city_name', 'like', '%' . $search . '%')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'text' => $city->city_name
                ];
            })
        ]);
    }

    public function getDependencyData(Request $request)
    {
        $ids = is_array($request->id)
            ? $request->id
            : explode(',', (string) $request->id);

        $currentLanguage = App::getLocale();
        $type = $request->type;

        $isPartnerType = str_starts_with($type, 'part_');

        if (in_array('Does Not Matter', $ids, true)) {
            return response()->json(['Does Not Matter' => _getLang('lbl_does_not_matter')]);
        }

        if (in_array($type, ['caste', 'part_caste'])) {
            $data = CasteMaster::getDropdown($ids, $currentLanguage);
        } elseif (in_array($type, ['state', 'state_id', 'part_state'])) {
            $data = StateMaster::getDropdown($ids, $currentLanguage);
        } elseif (in_array($type, ['city', 'part_city'])) {
            $data = CityMaster::getDropdown($ids, $currentLanguage);
        } else {
            $data = [];
        }

        // Add "Does Not Matter" as the first option for partner fields
        if ($isPartnerType) {
            $data = [
                'Does Not Matter' => _getLang('lbl_does_not_matter'),
                ...$data,
            ];
        }

        return response()->json($data);
    }
}

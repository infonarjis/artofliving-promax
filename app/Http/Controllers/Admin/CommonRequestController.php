<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\CountryMaster;
use App\Models\MotherTongueMaster;
use App\Models\ReligionMaster;
use App\Models\StateMaster;
use Illuminate\Http\Request;

## Services :
use Illuminate\Support\Facades\App;

class CommonRequestController extends Controller
{
    ## Get List Common :
    public function getList(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html'   => '',
            'data'   => [],
        ];

        $postData = $request->all();
        if (blank($postData)) {
            return response()->json($responseArr, 200);
        }

        //Fix typo + prepare selected array
        $ids = is_array($request->current_val) ? $request->current_val : explode(',', (string) $request->current_val);
        $currentLanguage = App::getLocale();
        $data = [];
        $label = '';

        ## Data Load :
        ## Get List Dropdown :
        if ($request->get_list == 'religion_lists') {
            $label = _getLang('field_lbl_select_religion');
            $data  = ReligionMaster::getDropdown($currentLanguage);
        }
        if ($request->get_list == 'caste_dropdown') {
            $label = _getLang('field_lbl_select_caste');
            $data  = CasteMaster::active()->where('lang_code', $currentLanguage)->pluck('caste_name', 'id')->toArray();
        }
        if ($request->get_list == 'mothertongue_lists') {
            $label = _getLang('field_lbl_select_mother_tongue');
            $data  = MotherTongueMaster::getDropdown($currentLanguage);
        }
        if ($request->get_list == 'country_lists') {
            $label = _getLang('field_lbl_select_country');
            $data  = CountryMaster::getDropdown($currentLanguage);
        }
        if ($request->get_list == 'state_listsm') {
            $label = _getLang('field_lbl_select_state');
            $data  = StateMaster::active()->where('lang_code', $currentLanguage)->pluck('state_name', 'id')->toArray();
        }
        if ($request->get_list == 'city_listsm') {
            $label = _getLang('field_lbl_select_city');
            $data  = CityMaster::active()->where('lang_code', $currentLanguage)->pluck('city_name', 'id')->toArray();
        }

        ## Depedency Dropdown :
        if ($request->get_list == 'caste_list') {
            $label = _getLang('field_lbl_select_caste');
            $data  = CasteMaster::getDropdown($ids, $currentLanguage);
        }
        if ($request->get_list == "state_list") {
            $label = _getLang('field_lbl_select_state');
            $data = StateMaster::getDropdown($ids, $currentLanguage);
        }
        if ($request->get_list == "city_list") {
            $label = _getLang('field_lbl_select_city');
            $data = CityMaster::getDropdown($ids, $currentLanguage);
        }

        ##  Default options :
        $htmlCode = '';
        if ($request->disp_on == 'part_caste') {
            $htmlCode .= '<option value="">' . _getLang('field_lbl_select_partner_caste') . '</option>';
            $htmlCode .= '<option value="Does Not Matter">' . _getLang('lbl_does_not_matter') . '</option>';
        } elseif ($request->disp_on == 'part_state') {
            $htmlCode .= '<option value="">' . _getLang('field_lbl_select_partner_state') . '</option>';
            $htmlCode .= '<option value="Does Not Matter">' . _getLang('lbl_does_not_matter') . '</option>';
        } elseif ($request->disp_on == 'caste') {
            $htmlCode .= '<option value="">' . _getLang('field_lbl_select_caste') . '</option>';
        } elseif ($request->disp_on == 'state_id') {
            $htmlCode .= '<option value="">' . _getLang('field_lbl_select_state') . '</option>';
        } elseif ($request->disp_on == 'city') {
            $htmlCode .= '<option value="">' . _getLang('field_lbl_select_city') . '</option>';
        } else {
            $htmlCode .= '<option value="">Select ' . $label . '</option>';
        }

        ## Append options :
        foreach ($data as $key => $val) {
            $htmlCode .= '<option value="' . $key . '">' . $val . '</option>';
        }

        $responseArr = [
            'status' => 'success',
            'msg'    => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'html'   => $htmlCode,
            'data'   => $postData,
        ];
        return response()->json($responseArr, 200);
    }

    public function searchCities(Request $request)
    {
        // If selected city ID is provided, return that city
        if ($request->filled('id')) {
            $city = CityMaster::active()
                ->where('id', $request->id)
                ->first();

            return response()->json([
                'results' => $city ? [[
                    'id' => $city->id,
                    'text' => $city->city_name,
                ]] : []
            ]);
        }

        $search = trim($request->q ?? '');

        $cities = CityMaster::active()
            ->when($search, function ($query) use ($search) {
                $query->where('city_name', 'like', '%' . $search . '%');
            })
            ->orderBy('city_name', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'text' => $city->city_name,
                ];
            })
        ]);
    }
}

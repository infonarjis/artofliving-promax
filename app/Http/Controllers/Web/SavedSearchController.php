<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SaveSearch;
use Illuminate\Http\Request;
use App\Services\SavedSearchService;

class SavedSearchController extends Controller
{
    public function index()
    {
        $memberId = auth()->guard('web')->id();

        $data = SaveSearch::with([
                'member',
                'maritalStatus',
                'religionData',
                'casteData',
                'motherTongueData',
                'manglikData',
                'starData',
                'countryData',
                'stateData',
                'cityData',
                'educationData',
                'occupationData',
                'employeeData',
                'designationData',
                'incomeData',
                'dietData',
                'smokeData',
                'drinkData',
                'complexionData',
                'bodyTypeData'
            ])
            ->where('member_id', $memberId)
            ->latest()
            ->paginate(10);
        
        $data->transform(function ($item) {
            $item->display_values = SavedSearchService::getDisplayValue($item);
            return $item;
        });

        // AJAX response
        if (request()->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.savedSearch.ajax_result', compact('data'))->render();
        }
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.savedSearch.index', compact('data'));
    }

    ## Delete Saved Search :
    public function destroy($id)
    {
        $memberId = auth()->guard('web')->id();

        $search = SaveSearch::where('id', $id)
            ->where('member_id', $memberId)
            ->first();

        if (!$search) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_record_not_found')
            ]);
        }

        $search->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_saved_search_deleted_successfully')
        ]);
    }

    public function apply($id)
    {
        $memberId = auth()->guard('web')->id();

        $search = SaveSearch::where('id', $id)
            ->where('member_id', $memberId)
            ->firstOrFail();

        $filters = [
            'part_frm_age'      => $search->from_age,
            'part_to_age'       => $search->to_age,

            'part_height'       => $search->from_height,
            'part_height_to'    => $search->to_height,

            'marital_status'    => $search->marital_status ? explode(',', $search->marital_status) : [],
            'religion'          => $search->religion ? explode(',', $search->religion) : [],
            'caste'             => $search->caste ? explode(',', $search->caste) : [],
            'mother_tongue'     => $search->mother_tongue ? explode(',', $search->mother_tongue) : [],
            'country_id'        => $search->country ? explode(',', $search->country) : [],
            'state_id'          => $search->state ? explode(',', $search->state) : [],
            'city'              => $search->city ? explode(',', $search->city) : [],
            'education_level'   => $search->education_level ? explode(',', $search->education_level) : [],
            'occupation'        => $search->occupation ? explode(',', $search->occupation) : [],
            'employee_in'       => $search->employee_in ? explode(',', $search->employee_in) : [],
            'income'            => $search->income ? explode(',', $search->income) : [],
            'designation_level' => $search->designation_level ? explode(',', $search->designation_level) : [],
            'manglik'           => $search->manglik ? explode(',', $search->manglik) : [],
            'star'              => $search->star ? explode(',', $search->star) : [],
            'diet'              => $search->diet ? explode(',', $search->diet) : [],
            'smoke'             => $search->smoke ? explode(',', $search->smoke) : [],
            'drink'             => $search->drink ? explode(',', $search->drink) : [],
            'complexion'     => $search->complexion ? explode(',', $search->complexion) : [],
            'body_type'      => $search->body_type ? explode(',', $search->body_type) : [],
            'with_photo'        => $search->with_photo ?? '',
            'keyword'           => $search->keyword ?? '',
            'id_search'         => $search->id_search ?? '',
        ];

        // Remove only null and empty string values
        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        return redirect()->route('web.search.searchResult', $filters);
    }

    public function savedSearch(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        $payload = json_decode($request->search_payload, true);

        $data = [
            'member_id'         => $memberId,
            'search_page_name'  => $request->search_page_name,
            'search_name'       => $request->search_name,

            'from_age'          => $payload['part_frm_age'] ?? null,
            'to_age'            => $payload['part_to_age'] ?? null,
            'from_height'       => $payload['part_height'] ?? null,
            'to_height'         => $payload['part_height_to'] ?? null,

            'marital_status'    => !empty($payload['marital_status']) ? implode(',', (array) $payload['marital_status']) : null,
            'mother_tongue'     => !empty($payload['mother_tongue']) ? implode(',', (array) $payload['mother_tongue']) : null,

            'religion'          => !empty($payload['religion']) ? implode(',', (array) $payload['religion']) : null,
            'caste'             => !empty($payload['caste']) ? implode(',', (array) $payload['caste']) : null,
            'manglik'           => !empty($payload['manglik']) ? implode(',', (array) $payload['manglik']) : null,
            'moonsign'          => !empty($payload['moonsign']) ? implode(',', (array) $payload['moonsign']) : null,
            'star'              => !empty($payload['star']) ? implode(',', (array) $payload['star']) : null,
            'horoscope'         => !empty($payload['horoscope']) ? implode(',', (array) $payload['horoscope']) : null,

            'country'           => !empty($payload['country_id']) ? implode(',', (array) $payload['country_id']) : null,
            'state'             => !empty($payload['state_id']) ? implode(',', (array) $payload['state_id']) : null,
            'city'              => !empty($payload['city']) ? implode(',', (array) $payload['city']) : null,
            
            'education_level'   => !empty($payload['education_level']) ? implode(',', (array) $payload['education_level']) : null,
            'occupation'        => !empty($payload['occupation']) ? implode(',', (array) $payload['occupation']) : null,
            'employee_in'       => !empty($payload['employee_in']) ? implode(',', (array) $payload['employee_in']) : null,
            'designation_level' => !empty($payload['designation_level']) ? implode(',', (array) $payload['designation_level']) : null,
            'income'            => !empty($payload['income']) ? implode(',', (array) $payload['income']) : null,

            'diet'              => !empty($payload['diet']) ? implode(',', (array) $payload['diet']) : null,
            'smoke'             => !empty($payload['smoke']) ? implode(',', (array) $payload['smoke']) : null,
            'drink'             => !empty($payload['drink']) ? implode(',', (array) $payload['drink']) : null,
            'body_type'         => !empty($payload['body_type']) ? implode(',', (array) $payload['body_type']) : null,
            'complexion'        => !empty($payload['complexion']) ? implode(',', (array) $payload['complexion']) : null,
            'blood_group_id'    => !empty($payload['blood_group_id']) ? implode(',', (array) $payload['blood_group_id']) : null,

            'with_photo'        => $payload['photo_search'] ?? 'No',
            'keyword'           => $payload['keyword_search'] ?? null,
            'id_search'         => $payload['id_search'] ?? null,
        ];
        SaveSearch::create($data);

        // Immediately apply same filters :
        return redirect()->route('web.search.searchResult', $payload);
    }
}

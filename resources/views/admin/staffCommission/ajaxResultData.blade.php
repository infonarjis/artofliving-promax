@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Staff Name</th>
                <th scope="col">Matri Id</th>
                <th scope="col">Plan Name</th>
                <th scope="col">Plan Amount</th>
                <th scope="col">Plan Offer Amount</th>
                <th scope="col">Currency</th>
                <th scope="col">Commission Percentage</th>
                <th scope="col">Commission Amount</th>
                <th scope="col">Created On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <td>{{ _displayNotAvailable($value->staff->username ?? null) }} ({{ _displayNotAvailable($value->staff->staff_prefix ?? null) }})</td>
                <td>{{ _displayNotAvailable($value->matri_id) }}</td>
                <td>{{ _displayNotAvailable($value->plan_name) }}</td>
                <td>{{ _displayNotAvailable($value->plan_amount) }}</td>
                <td>{{ _displayNotAvailable($value->plan_offer_amount) }}</td>
                <td>{{ _displayNotAvailable($value->currency) }}</td>
                <td>{{ _displayNotAvailable($value->commission_percentage) }}</td>
                <td>{{ _displayNotAvailable($value->commssion_amount) }}</td>
                <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')

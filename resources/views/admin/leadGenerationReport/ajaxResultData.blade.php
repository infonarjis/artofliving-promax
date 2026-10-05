@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Lead Name</th>
                <th scope="col">Lead Interest</th>
                <th scope="col">Lead Email</th>
                <th scope="col">Staff Name</th>
                <th scope="col">Staff Email</th>
                <th scope="col">Franchise Name</th>
                <th scope="col">Franchise Email</th>
                <th scope="col">Followup Date</th>
            </tr>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <td>{{ _displayNotAvailable($value->leadGeneration->username ?? null) }}</td>
                    <td>{{ _displayNotAvailable($value->leadGeneration->interest ?? null) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->leadGeneration->email ?? null) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable(optional($value->leadGeneration->staff)->username) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable(optional($value->leadGeneration->staff)->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable(optional($value->leadGeneration->franchise)->username) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable(optional($value->leadGeneration->franchise)->email) }}</td>
                    @endif
                    <td>{{ _displayDate($value->next_followup_date, 'j F, Y h:i A') }}</td>
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

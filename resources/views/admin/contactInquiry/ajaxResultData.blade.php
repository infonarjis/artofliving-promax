@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Mobile No</th>
            <th scope="col">Subject</th>
            <th scope="col">Message</th>
            <th scope="col">Created On</th>
        </tr>
    </thead>
    @foreach ($resultArr as $key =>$value)
        <tbody>
            <tr class="table_data_val">
                <td>{{ _displayNotAvailable($value->name) }}</td>
                @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                    <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                @else
                    <td>{{ _displayNotAvailable($value->email) }}</td>
                @endif
                @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                    <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                @else
                    <td>{{ _displayNotAvailable($value->mobile_number) }}</td>
                @endif
                <td>
                    @php
                        $subject = _displayNotAvailable($value->subject);
                    @endphp
                    @if(strlen(strip_tags($subject)) > 30)
                        {{ \Illuminate\Support\Str::limit(strip_tags($subject), 30) }}
                        <a href="javascript:void(0)"
                        class="text-primary fw-semibold readMoreLink"
                        data-model-label="Feedback"
                        data-description="{{ e($subject) }}">
                            Read more
                        </a>
                    @else
                        {{ $subject }}
                    @endif
                </td>
                <td>
                    @php
                        $message = _displayNotAvailable($value->message);
                    @endphp
                    @if(strlen(strip_tags($message)) > 30)
                        {{ \Illuminate\Support\Str::limit(strip_tags($message), 30) }}
                        <a href="javascript:void(0)"
                        class="text-primary fw-semibold readMoreLink"
                        data-model-label="Feedback"
                        data-description="{{ e($message) }}">
                            Read more
                        </a>
                    @else
                        {{ $message }}
                    @endif
                </td>
                <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
            </tr>
        </tbody>
    @endforeach
</table>
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')

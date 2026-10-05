@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">Member 1 Matri Id</th>
                <th scope="col">Member 2 Matri Id</th>
                <th scope="col">Created On</th>
                <th scope="col">Total Meetings</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="checkbox" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                    value="<?php echo $value->id; ?>"></th>
                <td>{{ $value->member1_matri_id }}</td>
                <td>{{ $value->member2_matri_id }}</td>
                <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
                @php
                    $totalMeetingCount = $value->totalMeetingCount;
                    $label = ($totalMeetingCount > 1) ? "Meetings" : "Meeting";
                @endphp
                <td><a href="{{ route('admin.personalizeMeeting.memberIndex',$value->id) }}">
                    ({{$totalMeetingCount}}) {{ $label }}</a>
                </td>
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

@if (isset($resultArr) && count($resultArr) > 0)
<table class="table">
    <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
    <thead>
        <tr class="text-nowrap table_header">
            <th scope="col">Default Language</th>
            <th scope="col">Language</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($resultArr as $key => $value)
            <tr class="table_data_val">
                <td>{{ $value['defaultLang'] }}</td>
                <td>{{ $value['currentLang'] }}</td>
                <td>
                    <a class="languageChange" href="javascript:void(0)" 
                    data-key="{{ $key }}"
                    data-lang_code="{{ $resultArr->langCode }}"
                    data-action="{{ route('admin.languageTemplates.getLangData') }}"
                    target-modal="#languageChangeModal"><i class="bx bxs-edit"></i> Edit</a>
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

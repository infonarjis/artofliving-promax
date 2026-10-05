@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">
                    {{-- <input class="all_check pointer" type="checkbox"> --}}
                </th>
                <th scope="col">Action</th>
                <th scope="col">Payment Logo</th>
                <th scope="col">Payment Name</th>
                <th scope="col">Payment Mode</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultArr as $key => $value)
            <tr class="table_data_val">
                <th scope="row" class="text-center"><input type="radio" class="checkboxId" id="ps<?php echo $value->id; ?>" name="id[]"
                        value="<?php echo $value->id; ?>"></th>
                <td>
                    <a href="{{route($dataArr->actionButtonUrl['edit'],$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="Edit"><i class="bx bxs-edit"></i></a>
                    <a href="{{route($dataArr->actionButtonUrl['view'],$value->id) }}" class="action-button" data-bs-toggle="tooltip"  data-bs-placement="top" title="View"><i class="bx bx-show"></i></a>
                </td>
                <td class="text-center">
                    @php
                      ## Image Arr :
                      $imgArr = '';
                      if (!blank($value->logo) && _checkStorageFileExists('upload_path.PAYMENT_LOGO_URL',$value->logo)) {
                        $imgArr = _assetUrl('upload_path.PAYMENT_LOGO_URL').$value->logo;
                      }
                    @endphp
                    <a target="_blank" href="{{ $imgArr }}">
                        <img src="{{ $imgArr }}" alt class="w-px-100" />
                    </a>
                </td>
                <td>{{ _displayNotAvailable($value->name) }}</td>
                <td>{{ _displayNotAvailable(_getStaticArr('paymentModeArr',$value->payment_mode)) }}</td>
                @if ($value->status == 'APPROVED')
                <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                @endif
                @if ($value->status == 'UNAPPROVED')
                <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
                @endif
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

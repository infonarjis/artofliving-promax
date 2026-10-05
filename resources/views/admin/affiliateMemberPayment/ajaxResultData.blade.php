@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col">Status</th>
                <th scope="col">Affiliate Member</th>
                <th scope="col">Affiliate Mobile</th>
                <th scope="col">Affiliate Email</th>
                <th scope="col">Amount</th>
                <th scope="col">Admin Remarks</th>
                <th scope="col">Created at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    @if ($value->is_transfered == '1')
                        <td><span class="badge bg-label-success me-1">Transfered</span></td>
                    @endif
                    @if ($value->is_transfered == '0')
                        <td><span class="badge bg-label-danger me-1">Pending </span>
                            (<a href="#" data-bs-placement="top" data-bs-toggle="modal"
                                data-bs-target="#addAdminRemarksModal" data-bs-custom-class="custom-tooltip"
                                data-bs-title="Add Remarks" class="ms-1"
                                onclick="getProfileReportId(<?php echo $value->id; ?>)" title="Add Remarks">
                                Update
                            </a>)
                        </td>
                    @endif
                    <td>{{ _displayNotAvailable($value->affiliateMember->fullname) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->affiliateMember->email) }}</td>
                    @endif
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->affiliateMember->mobile) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable($value->amount) }}</td>
                    <td>
                        @php
                            $adminRemarks = _displayNotAvailable($value->admin_remark);
                        @endphp
                        @if (strlen(strip_tags($adminRemarks)) > 30)
                            {{ \Illuminate\Support\Str::limit(strip_tags($adminRemarks), 30) }}
                            <a href="javascript:void(0)" class="text-primary fw-semibold readMoreLink"
                                data-model-label="Admin Remarks"
                                data-description="{{ e($adminRemarks) }}">
                                Read more
                            </a>
                        @else
                            {{ $adminRemarks }}
                        @endif
                    </td>
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

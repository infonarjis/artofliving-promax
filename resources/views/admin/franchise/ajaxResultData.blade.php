@if (isset($resultArr) && count($resultArr) > 0)
    <table class="table">
        <caption class="d-none">&nbsp;&nbsp; Result Data</caption>
        <thead>
            <tr class="text-nowrap table_header">
                <th scope="col" class="text-center"><input class="all_check pointer" type="checkbox"></th>
                <th scope="col">View Member</th>
                <th scope="col">Action</th>
                <th scope="col">Status</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
                <th scope="col">Created On</th>
                <th scope="col">Referral Link</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultArr as $key => $value)
                <tr class="table_data_val">
                    <th scope="row" class="text-center"><input type="checkbox" class="checkboxId"
                            id="ps<?php echo $value->id; ?>" name="id[]" value="<?php echo $value->id; ?>"></th>
                    <td><a target="_blank"
                            href="{{ route('admin.franchiseMember.memberIndex', base64_encode($value->id)) }}">
                            <button type="button" class="btn btn-primary px-1 py-1 fs-normal fs-12">View
                                Member</button>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route($dataArr->actionButtonUrl['edit'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i
                                class="bx bxs-edit"></i></a>
                        <a href="{{ route($dataArr->actionButtonUrl['view'], $value->id) }}" class="action-button"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i
                                class="bx bx-show"></i></a>
                    </td>
                    @if ($value->status == 'APPROVED')
                        <td><span class="badge bg-label-success me-1">{{ $value->status }}</span></td>
                    @endif
                    @if ($value->status == 'UNAPPROVED')
                        <td><span class="badge bg-label-danger me-1">{{ $value->status }}</span></td>
                    @endif
                    <td>{{ _displayNotAvailable($value->username) }}</td>
                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                        <td>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</td>
                    @else
                        <td>{{ _displayNotAvailable($value->email) }}</td>
                    @endif
                    <td>{{ _displayNotAvailable($value->password_decrypted) }}</td>
                    <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
                    <td>
                        @php
                            $refferalCode = $value->referral_code;
                            $refferalLink = route('web.register.referral', [
                                'type' => 'franchise',
                                'code' => $refferalCode,
                            ]);
                        @endphp

                        @if ($refferalLink)
                            <div class="d-flex align-items-center gap-2">
                                <span id="referralLink-{{ $value->id }}">
                                    {{ $refferalLink }}
                                </span>

                                <button type="button" class="btn btn-sm btn-primary copy-referral-btn"
                                    data-link="{{ $refferalLink }}" title="Copy Referral Link">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            </div>
                        @else
                            N/A
                        @endif
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

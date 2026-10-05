@php
    $authUser = Auth::user();
    $userType = _adminUserType($authUser->type);
@endphp
@if (isset($resultArr) && count($resultArr) > 0)
    @foreach ($resultArr as $key => $value)
        <div class="inner_adProfileMian mt-2">
            <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
                <div class="left_member_profilesDiv">
                </div>
                <div class="right_content_MDivd w-100">
                    <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                        <div class="user_name_aprv d-flex align-items-center gap-3">
                            <h4>{{ $value->member->fullname ?? '' }} ({{ $value->member->matri_id ?? '' }})</h4>
                        </div>
                    </div>
                    <div class="btm_users_details">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-3">
                                <div class="details_userviewas">
                                    <h6><span>Plan Name</span>: {{ $value->plan_name }}</h6>
                                    <h6><span>Plan Amount</span>: {{ $value->plan_amount }}</h6>
                                    <h6><span>Grand Total</span>: {{ $value->grand_total }}</h6>
                                    <h6><span>Plan Activated</span>:
                                        {{ _displayDate($value->plan_activate_date, 'j F, Y') }}</h6>
                                    <h6><span>Current Plan</span>: {{ $value->current_plan }}</h6>
                                    @if ($userType == 'Franchise')
                                        <h6><span>Commission Percentage</span>: {{ $value->grand_total }}</h6>
                                    @endif
                                    <h6><span>Transaction Id</span>: {{ _displayNotAvailable($value->transaction_id) }}
                                    </h6>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl sec mt-0 mt-lg-3 mt-md-3 mt-sm-3">
                                <div class="details_userviewas">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        <h6><span>Email</span>: {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</h6>
                                    @else
                                        <h6><span>Email</span>: {{ $value->member->email ?? '' }}</h6>
                                    @endif
                                    <h6><span>Plan Validity</span>: {{ $value->plan_validity_days }} Days</h6>
                                    <h6><span>Payment Mode</span>: {{ $value->payment_mode }}</h6>
                                    <h6><span>Plan Expired</span>:
                                        {{ _displayDate($value->plan_expiry_date, 'j F, Y') }}</h6>
                                    <h6><span>Staff Name</span>:
                                        {{ _displayNotAvailable($value->member->staffData->username ?? null) }}</h6>
                                    @if ($userType == 'Franchise')
                                        <h6><span>Commission Amount</span>: {{ $value->franchise_comm_amt }}</h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route($dataArr->actionButtonUrl['viewInvoice'], $value->id) }}">
                        <button class="view-invoice-btn">
                            <i class='bx bx-bookmark-alt'></i> View Invoice
                        </button>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="No_dataFound text-center p-4">
        <img src="{{ _assetUrl('upload_path.ADMIN_NO_DATA_FOUND') }}" alt="No Data Found" class="Nodata-img">
    </div>
@endif
@include('admin.commonPagination')

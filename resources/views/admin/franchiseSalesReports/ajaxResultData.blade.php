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
                                    <h6><span>Plan Name</span>: {{ _displayNotAvailable($value->plan_name) }}</h6>
                                    <h6><span>Plan Amount</span>: {{ _displayNotAvailable($value->plan_amount) }}</h6>
                                    <h6><span>Grand Total</span>: {{ _displayNotAvailable($value->grand_total) }}</h6>
                                    <h6><span>Current Plan</span>: {{ _displayNotAvailable($value->current_plan) }}</h6>
                                    <h6><span>Transaction Id</span>: {{ _displayNotAvailable($value->transaction_id) }}
                                    </h6>
                                    <h6><span>Franchise Name </span>:
                                        {{ _displayNotAvailable($value->member->franchiseData->username) }}</h6>

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl sec mt-0 mt-lg-3 mt-md-3 mt-sm-3">
                                <div class="details_userviewas">
                                    <h6><span>Plan Activated</span>:
                                        {{ _displayDate($value->plan_activate_date, 'j F, Y') }}</h6>
                                    <h6><span>Plan Validity</span>:
                                        {{ _displayNotAvailable($value->plan_validity_days) }} Days</h6>
                                    <h6><span>Plan Expired</span>:
                                        {{ _displayDate($value->plan_expiry_date, 'j F, Y') }}</h6>
                                    <h6><span>Payment Mode</span>: {{ _displayNotAvailable($value->payment_mode) }}
                                    </h6>
                                    <h6><span>Payment Received From</span>:
                                        {{ _displayNotAvailable($value->payment_received_from) }}</h6>
                                    <h6><span>Franchise Commission Per(%)</span>:
                                        {{ _displayNotAvailable($value->franchise_comm_per) }}</h6>
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

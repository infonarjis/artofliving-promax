@if (isset($resultArr) && count($resultArr) > 0)
    @foreach($resultArr as $key => $value)
        <div class="inner_adProfileMian {{ ($key > 0) ? 'mt-3' : '' }}">
        <div class="inner_flexDiv-membr d-block d-lg-flex gap-3">
            <div class="left_member_profilesDiv">
            </div>
            <div class="right_content_MDivd w-100">
                <div class="top_usenr_btnsgroups d-block d-lg-flex align-items-center justify-content-between">
                    <div class="user_name_aprv d-flex align-items-center gap-3">
                        <h4><span class="fw-semibold w-25">Event Name</span>: {{ $value->event->title }}</h4>
                    </div>
                </div>
                <div class="btm_users_details">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 border-light-dtl mt-3 pb-0 pb-lg-3 pb-md-4 pb-sm-4">
                            <div class="details_userviewas">
                                <h6><span class="fw-semibold w-25">Name</span>: {{ $value->event->title }}</h6>
                                <h6><span class="fw-semibold w-25">Mobile No</span>: 
                                    @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($value->mobile) }}
                                    @endif
                                </h6>
                                <h6><span class="fw-semibold w-25">Email</span>: 
                                    @if(_getConstant('DISABLE_DEMO') ==  'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($value->email) }}
                                    @endif
                                </h6>
                                <h6><span class="fw-semibold w-25">Hear Abour Us</span>: {{ _displayNotAvailable($value->hear_about_us) }}</h6>
                                <h6><span class="fw-semibold w-25">Payment Mode</span>: {{ $value->payment_mode }}</h6>
                            </div>
                        </div>
                        <div
                            class="col-lg-6 col-md-6 col-sm-6 border-light-dtl
                            sec mt-0 mt-lg-3 mt-md-3 mt-sm-3 pb-0 pb-lg-3 pb-md-4 pb-sm-4">
                            <div class="details_userviewas">
                                <h6><span class="fw-semibold w-30">Per Ticket Price</span>: {{ $value->ticket_price }}</h6>
                                <h6><span class="fw-semibold w-30">Ticket Quantity</span>: {{ $value->tickets_qty }}</h6>
                                @if($value->tax_applicable == 'Yes')
                                    <h6><span class="fw-semibold w-30">Tax Amount ({{ $value->tax_name }} {{ $value->tax_percentage }}%)</span>: {{ $value->tax_amount }}</h6>
                                @endif
                                <h6><span class="fw-semibold w-30">Total Amount</span>: {{ $value->grand_total }}</h6>
                                <h6><span class="fw-semibold w-30">Registered On</span>: {{ _displayDate($value->created_at, 'j F, Y h:i A') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
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

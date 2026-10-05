@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')

                        <!-- invoise panel section start  -->
                        <section class="common-section-bg pb-4 pb-lg-5 pt-2">
                            <div class="common-section-page">
                                <div class="container">
                                    <div class="common-bgwhite-main p-3 p-lg-4 px-lg-5 mt-3">
                                        <div class="right-myprofile-dashed">
                                            <div class="row">
                                                <div class="col-lg-8 col-md-7 col-6 pe-0">
                                                    <div class="current-plantitle pt-1">
                                                        <div class="fts-24 fw-7 white-color-n">
                                                            {{ __('messages.lbl_invoice') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-5 col-6 ps-sm-0">
                                                    <div class="d-flex justify-content-sm-end gap-2 mt-2 mt-sm-0">
                                                        <a href="{{ route('web.currentPlan.downloadInvoice', $invoicePlan->id) }}"
                                                            class="btn-plan-download fts-15 fw-5">
                                                            <iconify-icon icon="hugeicons:download-03"
                                                                class="fts-24"></iconify-icon>
                                                            {{ __('messages.lbl_download') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="common-bglight-main p-3 p-lg-4 mt-3 mt-lg-4">
                                                <div class="invoice_headerbar pb-3">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-4 col-12">
                                                            <div class="invoice_logo">
                                                                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                                                    alt="{{ $configArr['web_name'] }}" class="invoice-logo">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-md-4 col-6">
                                                            <div
                                                                class="email-number-mng text-start text-lg-end text-md-end mt-2 mt-lg-0 mt-md-0">
                                                                <div class="fts-18 fw-4 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $configArr['web_name'] }}
                                                                </div>
                                                                <div class="fts-15 fw-4 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $configArr['contact_no'] }}
                                                                </div>
                                                                <div class="fts-15 fw-4 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $configArr['contact_email'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="invoice_centerbar py-3 py-lg-4 py-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_invoice_to') }}</div>
                                                                <div class="mt-1 fw-6 white-color-n fts-15">
                                                                    {{ $authUser->fullname }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails text-end">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_invoice') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $configArr['invoice_prefix'] . $invoicePlan->id }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_mobile') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n mt-1 d-inline-block text-break">
                                                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                                    @else
                                                                        {{ $authUser->mobile }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails text-end">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_customer_id') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $authUser->matri_id }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_email') }}</div>
                                                                <a href="#"
                                                                    class="fts-15 fw-5 primary-color-n mt-1 d-inline-block text-break">
                                                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                                    @else
                                                                        {{ $authUser->email }}
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="titledetails text-end">
                                                                <div class="fts-15 fw-4 white-color70-n">
                                                                    {{ __('messages.lbl_payment_mode') }}</div>
                                                                <div class="fts-15 fw-5 white-color-n mt-1 d-inline-block text-break">
                                                                    {{ $invoicePlan->payment_mode }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="invoicetables pb-4 mb-lg-5 mb-md-5 mt-3">
                                                    <div class="innertableinvoice mb-3">
                                                        <div class="maxSet_invoicetable">
                                                            <table aria-label="table" class="w-100">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">{{ __('messages.lbl_qty') }}
                                                                        </th>
                                                                        <th scope="col">
                                                                            {{ __('messages.lbl_plan_name') }}</th>
                                                                        <th scope="col">
                                                                            {{ __('messages.lbl_plan_activated_on') }}</th>
                                                                        <th scope="col" class="darkclr">
                                                                            {{ __('messages.lbl_plan_expired_on') }}</th>
                                                                        <th scope="col">
                                                                            {{ __('messages.lbl_plan_amount') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td class="text-start">
                                                                            {{ $invoicePlan->plan_name }}</td>
                                                                        <td>{{ _displayDate($invoicePlan->plan_activate_date, 'j F, Y') }}
                                                                        </td>
                                                                        <td>{{ _displayDate($invoicePlan->plan_expiry_date, 'j F, Y') }}
                                                                        </td>
                                                                        <td class="txtbig">
                                                                            {{ $invoicePlan->currency_code }}
                                                                            {{ $invoicePlan->plan_amount }}</td>
                                                                    </tr>
                                                                    @if ($addOnPlan->isNotEmpty())
                                                                        @foreach ($addOnPlan as $key => $plan)
                                                                            <tr>
                                                                                <td>{{ $key + 2 }}</td>
                                                                                <td class="text-start">
                                                                                    {{ $plan->package_title }}</td>
                                                                                <td>{{ _displayDate($invoicePlan->plan_activate_date, 'j F, Y') }}
                                                                                </td>
                                                                                <td>{{ _displayDate($invoicePlan->plan_expiry_date, 'j F, Y') }}
                                                                                </td>
                                                                                <td class="txtbig">
                                                                                    {{ $invoicePlan->currency_code }}
                                                                                    {{ $plan->package_amount }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="table_totalsmain">
                                                        @if ($invoicePlan->discount_amount > 0)
                                                            <div class="mt-3 text-end mb-2">
                                                                <div class="white-color-n fts-16 fw-5">
                                                                    {{ __('messages.lbl_plan_discount') }}
                                                                    <span class="fts-20 fw-6 green-color-n ps-3">
                                                                        {{ $invoicePlan->plan_currency . ' ' . $invoicePlan->discount_amount }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($invoicePlan->tax_amount > 0)
                                                            <div class="gstsetintable">
                                                                <div class="white-color-n fts-16 fw-5">
                                                                    {{ $invoicePlan->tax_name }}
                                                                    ({{ $invoicePlan->tax_percentage }}%)
                                                                    <span class="white-color-n fts-18 fw-6 ps-3">
                                                                        {{ $invoicePlan->currency_code }}
                                                                        {{ $invoicePlan->tax_amount }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="mt-3 text-end">
                                                            <div class="white-color-n fts-16 fw-5">
                                                                {{ __('messages.lbl_grand_total') }}
                                                                <span class="fts-20 fw-6 green-color-n ps-3">
                                                                    {{ $invoicePlan->currency_code }}
                                                                    {{ $invoicePlan->grand_total }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="invoice_footers text-center pt-3">
                                                    <div class="fw-6 white-color-n fts-20">
                                                        {{ __('messages.lbl_thank_you') }}</div>
                                                    <div class="fts-14 fw-4 white-color70-n mt-1">
                                                        {{ __('messages.lbl_invoice_messages') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection

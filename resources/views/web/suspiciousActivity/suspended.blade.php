@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- Suspended Section Start -->
    <section class="common-section-bg py-5 my-lg-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-10 px-3">
                    <div class="common-bgwhite-main p-4 p-lg-5 text-center suspended-card wow fadeInUp"
                        data-wow-duration="0.8s">
                        <div class="suspended-icon-wrap mb-4">
                            <iconify-icon icon="solar:user-block-bold-duotone" class="suspended-icon"></iconify-icon>
                        </div>
                        <h1 class="fts-32 fw-7 white-color-n mb-3">{{ __('messages.lbl_your_account_has_been_suspended') }}
                        </h1>
                        <p class="fts-16 fw-4 white-color70-n mb-4">
                            {{ __('messages.lbl_your_account_has_been_suspended_subtitle') }}
                        </p>
                        <div class="suspended-notice p-3 mb-4">
                            <p class="fts-14 fw-5 white-color-n m-0 d-flex align-items-center">
                                <iconify-icon icon="solar:info-circle-bold"
                                    class="me-2 fts-20 primary-color-n"></iconify-icon>
                                <span>Please contact the administrator or raise a ticket to appeal this decision.</span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                            <a href="#" class="comman-bg-btn px-5 py-3 fts-16 fw-6">Contact Admin</a>
                            <a href="#" class="btn-outline-custom px-5 py-3 fts-16 fw-6">Raise Ticket</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

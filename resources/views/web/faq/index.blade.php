@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        .faq-content-page {
            color: var(--white-color-70);
        }

        .faq-content-page ul {
            list-style-type: disc !important;
            padding-left: 20px;
        }

        .faq-content-page ol {
            list-style-type: decimal !important;
            padding-left: 20px;
        }

        .faq-content-page li {
            display: list-item !important;
        }
    </style>
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- faq section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 col-lg-8 mx-auto">
                        <div class="common-bgwhite-main p-3 p-md-4">
                            <div class="faq-heading">
                                <h1 class="fts-24 fw-6 white-color-n">{{ __('messages.lbl_faqs') }}</h1>
                                <div class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_faqs_subtitle') }}</div>
                            </div>
                        </div>
                        <div class="main-ctmacordion accordion pt-1 pt-lg-2" id="faqAccording">
                            @foreach ($resultListArr as $key => $faq)
                                @php
                                    $collapseId = 'faqCollapse' . $key;
                                @endphp
                                <div class="accordion-item my-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                            aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                            aria-controls="{{ $collapseId }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="{{ $collapseId }}"
                                        class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                        data-bs-parent="#faqAccording">

                                        <div class="accordion-body mt-2">
                                            <div class="fts-14 fw-4 white-color70-n faq-content-page">
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

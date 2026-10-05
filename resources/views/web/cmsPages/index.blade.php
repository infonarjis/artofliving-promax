@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        .cms-pages-innercontents {
            color: var(--white-color-70);
        }

        .cms-pages-innercontents ul {
            list-style-type: disc !important;
            padding-left: 20px;
        }

        .cms-pages-innercontents ol {
            list-style-type: decimal !important;
            padding-left: 20px;
        }

        .cms-pages-innercontents li {
            display: list-item !important;
        }

        .cms-pages-innercontents a {
            color: rgba(var(--bs-link-color-rgb), var(--bs-link-opacity, 1));
        }
    </style>
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="common-bgwhite-main p-md-4 p-3 mx-lg-5">
                    <h1 class="fw-6 fts-24 white-color-n">{{ $resultListArr->page_title }}</h1>
                    <div class="cms-pages-innercontents pt-1">
                        <p class="fts-15 fw-4 white-color70-n mt-2">
                            {!! $resultListArr->page_content !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

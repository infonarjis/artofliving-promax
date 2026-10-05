@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <section class="common-section-bg pb-4 pb-lg-5 mt-3">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-3 col-lg-4 pe-lg-0">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.matrimonyPages.matrimonyPageSidebar')
                    </div>
                    <div class="col-xxl-9 col-lg-8 ps-lg-4 mt-3 mt-lg-0">
                        <div class="right-browse-matrimony">
                            <div class="breadcrumb-browse">
                                <a href="" class="white-color-n"><i class='bx bx-home-alt fts-18'></i></a>
                                @php
                                    $typeLabels = [
                                        'Religion' => __('messages.lbl_religion_matrimonials'),
                                        'Caste' => __('messages.lbl_caste_matrimonials'),
                                        'Mother-Tongue' => __('messages.lbl_mother_tongue_matrimonials'),
                                        'Country' => __('messages.lbl_country_matrimonials'),
                                        'State' => __('messages.lbl_state_matrimonials'),
                                        'City' => __('messages.lbl_city_matrimonials'),
                                    ];
                                @endphp
                                <p class="fts-15 fw-5">
                                    {{ $typeLabels[ucwords($type)] ?? $type }}
                                </p>
                            </div>
                            <div class="browse-matri-bottommain mt-3">
                                <ul class="browse-city-list d-flex gap-2 flex-wrap">
                                    @foreach ($result as $item)
                                        @php
                                            $slugList = str_replace(' ', '-', strtolower($item->slug));
                                        @endphp
                                        <li>
                                            <a href="{{ route('web.matrimony.index', $slugList) }}" class="city-items">
                                                {{ $item->matrimony_name ?? 'N/A' }}
                                            </a>
                                            <a href="{{ route('web.matrimony.index', $slugList) }}"
                                                class="city-arrow"><iconify-icon
                                                    icon="iconamoon:arrow-right-2-light"></iconify-icon></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

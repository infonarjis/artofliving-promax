<div class="pro-common-leftbar py-1">
    <div class="common-tabs-design p-2">
        <ul class="nav flex-nowrap d-flex nav-pills mx-1" id="pills-tab" role="tablist">
            <li class="nav-item w-100">
                <button class="nav-link active fts-13" id="search-tab" data-bs-toggle="pill" data-bs-target="#search"
                    type="button" role="tab" aria-controls="search" aria-selected="false"><iconify-icon
                        icon="hugeicons:search-add" class="fts-20"></iconify-icon>{{ __('messages.lbl_search') }}</button>
            </li>
        </ul>
        <div class="common-tablist-design mt-3 px-1">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="search" role="tabpanel" aria-labelledby="search-tab">
                    <form action="{{ route('web.search.searchResult') }}" method="GET">
                        <div class="search-browse-form">
                            <div class="custom-select2-div mb-3">
                                <div class="edit_inputMain-sltr w-100">
                                    <label for="gender-search">{{ __('messages.field_lbl_gender') }}</label>
                                    <select name="gender" id="gender-search" class="Single_searchDv">
                                        <option value="Male" selected>{{ __('messages.field_lbl_male') }}</option>
                                        <option value="Female">{{ __('messages.field_lbl_female') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="custom-select2-div mb-3">
                                <div class="edit_inputMain-sltr">
                                    <label for="">{{ __('messages.field_lbl_select_age') }}</label>
                                    <div class="d-flex d-flex gap-2 align-items-center">
                                        <div class="edit_inputMain-sltr w-50">
                                            @php
                                                $ageRange = _ageRang();    
                                            @endphp
                                            <select name="part_frm_age" id="part_frm_age" class="Single_searchDv">
                                                @foreach ($ageRange as $id => $age)
                                                    <option {{ $id == 18 ? 'selected' : '' }} value="{{ $id }}">{{ $age }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <p class="fts-14 fw-5 white-color-n">{{ __('messages.field_lbl_to') }}</p>
                                        <div class="edit_inputMain-sltr w-50">
                                            <select name="part_to_age" id="part_to_age" class="Single_searchDv">
                                                @foreach ($ageRange as $id => $age)
                                                    <option {{ $id == 60 ? 'selected' : '' }} value="{{ $id }}">{{ $age }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="custom-select2-div mb-3">
                                <div class="edit_inputMain-sltr">
                                    <label for="">{{ __('messages.field_lbl_select_height') }}</label>
                                    <div class="d-flex d-flex gap-2 align-items-center">
                                        @php
                                            $heightList = _heightList();
                                        @endphp
                                        <div class="edit_inputMain-sltr w-50">
                                            <select name="" id="height-from" class="Single_searchDv">
                                                @foreach ($heightList as $id => $height)
                                                    <option {{ $id == 50 ? 'selected' : '' }} value="{{ $id }}">{{ $height }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <p class="fts-14 fw-5 white-color-n">{{ __('messages.field_lbl_to') }}</p>
                                        <div class="edit_inputMain-sltr w-50">
                                            <select name="" id="height-to" class="Single_searchDv">
                                                @foreach ($heightList as $id => $height)
                                                    <option {{ $id == 86 ? 'selected' : '' }} value="{{ $id }}">{{ $height }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="custom-select2-div mb-3">
                                <div class="edit_inputMain-sltr w-100">
                                    <label for="religion">{{ __('messages.field_lbl_religion') }}</label>
                                    <select name="religion[]" id="religion" class="js-example-basic-multiple" multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_religion') }}" onchange="dependentDropdown('#religion', '#caste', 'caste', '{{ __('messages.field_lbl_select_caste') }}')">
                                        @foreach ($religionList as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row px-2">
                                <div class="col-6 px-1">
                                    <div class="with-photos-search">
                                        <input type="checkbox" name="photo_search" id="photo_search" class="d-none" value="Yes">
                                        <label for="photo_search" class="fts-14">
                                            <span class="d-inline-block ms-3">{{ __('messages.field_lbl_with_photo') }}</span></label>
                                    </div>
                                </div>
                                <div class="col-6 px-1">
                                    <button class="comman-bg-btn align-items-center fts-14 w-100" type="submit">
                                        <iconify-icon icon="gg:search" class="fts-18 me-1"></iconify-icon>
                                        {{ __('messages.lbl_search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $sections = [
        'Religion' => __('messages.lbl_religion_matrimonials'),
        'Caste' => __('messages.lbl_caste_matrimonials'),
        'Mother-Tongue' => __('messages.lbl_mother_tongue_matrimonials'),
        'Country' => __('messages.lbl_country_matrimonials'),
        'State' => __('messages.lbl_state_matrimonials'),
        'City' => __('messages.lbl_city_matrimonials'),
    ];

    $routeTypeMapping = [
        'Religion' => 'religion',
        'Caste' => 'caste',
        'Mother-Tongue' => 'mother-tongue',
        'Country' => 'country',
        'State' => 'state',
        'City' => 'city',
    ];
@endphp

@foreach ($sections as $key => $title)
    @if (!empty($listType[$key]) && $listType[$key]->isNotEmpty())
        <div class="pro-common-leftbar p-3 mt-3">
            <h4 class="fts-16 fw-5 primary-color-n">{{ $title }}</h4>
            <ul class="browse-religion-list mt-2">
                @foreach ($listType[$key] as $item)
                    <li class="religion-item py-2">
                        <a href="{{ route('web.matrimony.index', $item->slug) }}" class="fts-14">
                            {{ $item->pagename }}
                            <span><iconify-icon icon="iconamoon:arrow-right-2-light"></iconify-icon></span>
                        </a>
                    </li>
                @endforeach
                @if (count($listType[$key]) > 3)
                    <li class="more-religion py-2">
                        <a href="{{ route('web.matrimony.moreDetails', $routeTypeMapping[$key]) }}"
                            class="more-items fts-14">
                            {{ __('messages.lbl_more_details') }}
                            <span><iconify-icon icon="iconamoon:arrow-down-2-light"></iconify-icon></span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    @endif
@endforeach

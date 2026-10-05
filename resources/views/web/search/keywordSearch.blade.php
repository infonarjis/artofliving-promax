<form class="searchForm" id="keywordSearchForm" action="{{ route('web.search.searchResult') }}" method="get">
    <div class="row px-1 mt-2">
        <div class="col-md-12 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="keyword_search">{{ __('messages.lbl_keyword_search') }} </label>
                <input type="text" name="keyword_search" id="keyword_search"
                    placeholder="{{ __('messages.lbl_keyword_seach_like') }}" class="input_comman_field">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3 d-flex align-items-center">
            <div class="checkbox-input-search pt-md-1">
                <input type="checkbox" name="photo_search" id="photo_search" class="d-none" value="Yes">
                <label for="photo_search" class="fts-14">{{ __('messages.field_lbl_with_photo') }}</label>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center justify-content-lg-start gap-2 gap-lg-3 mt-3">
        <button type="submit" class="form-bg-btn fw-4 fts-15">{{ __('messages.lbl_search_btn') }}</button>
        @if(Auth::check())
            <button type="button" class="form-bg-btn fw-4 fts-15" data-bs-toggle="modal"
            data-bs-target="#savedSearch">{{ __('messages.lbl_saved_search') }}</button>
        @endif
    </div>
</form>

<form action="{{ route('web.search.searchResult') }}" method="get">
    <div class="row px-1 mt-2">
        <div class="col-md-12 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="id_search">{{ __('messages.lbl_id_search') }} </label>
                <input type="text" name="id_search" id="id_search"
                    placeholder="{{ __('messages.lbl_search_with_matri_id') }}" class="input_comman_field">
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

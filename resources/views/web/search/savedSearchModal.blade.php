<div class="customsmallmodel_light save-search modal fade" id="savedSearch" tabindex="-1" aria-labelledby="savedSearchLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n" id="savedSearchLabel">{{ __('messages.lbl_save_search') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <form action="{{ route('web.savedSearch.save') }}" method="POST" id="savedSearchForm">
                @csrf
                <input type="hidden" name="search_payload" id="search_payload">
                <input type="hidden" name="search_page_name" id="search_page_name">
                <div class="modal_liteBody px-3 px-lg-4 py-3">
                    <div class="mt-3">
                        <div class="comman_inputfield_main">
                            <label
                                class="fts-14 fw-4 white-color70-n mb-2 d-block">{{ __('messages.lbl_enter_save_search_name') }}</label>
                            <input type="text" name="search_name" id="search_name"
                                placeholder="{{ __('messages.lbl_enter_your_search_name') }}" class="input_comman_field">
                        </div>
                    </div>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="submit"
                            class="click-changeButton">{{ __('messages.lbl_save_and_search') }}</button>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(document).on('click', '[data-bs-target="#savedSearch"]', function() {

            // Find active tab form automatically
            let activeForm = $('.tab-pane.active').find('form.searchForm');

            if (!activeForm.length) return;

            let formData = {};

            activeForm.serializeArray().forEach(function(item) {
                if (formData[item.name]) {
                    if (!Array.isArray(formData[item.name])) {
                        formData[item.name] = [formData[item.name]];
                    }
                    formData[item.name].push(item.value);
                } else {
                    formData[item.name] = item.value;
                }
            });

            // Select2 values
            activeForm.find('.js-example-basic-multiple').each(function() {
                let name = $(this).attr('name');
                let val = $(this).val();
                if (val) {
                    formData[name.replace('[]', '')] = val;
                }
            });

            // Checkbox
            activeForm.find('input[type="checkbox"]').each(function() {
                formData[$(this).attr('name')] = $(this).is(':checked') ? 'Yes' : 'No';
            });

            // Range values (works for all forms)
            activeForm.find('input[type="hidden"]').each(function() {
                formData[$(this).attr('name')] = $(this).val();
            });

            // Gender
            let gender = activeForm.find('input[name="gender"]:checked').val();
            if (gender) formData['gender'] = gender;

            // Detect which tab
            let pageName = $('.nav-link.active').text().trim();

            $('#search_page_name').val(pageName);
            $('#search_payload').val(JSON.stringify(formData));
        });
    </script>
@endpush

<div class="modal-dialog modal-dialog-centered cstm-withset">
    <div class="modal-content modelcont-ctms p-4 position-relative">
        <div class="heading-modal">
            <h1 class="modal-title" id="exampleModalLabel">Filter Data </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                    class='bx bx-x'></i></button>
        </div>
        <div class="bottom_saveTimebsg mt-4">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <?php echo $fromHtml; ?>
                        <div class="col-lg-8 col-md-8 col-sm-8 gap-4">
                            <input type="hidden" value="0" name="isFilterApply" id="isFilterApply">
                            <button type="submit"
                                class="submit-btn-mmbre mt-4 btn btn-primary {{ $formSubmitBtnClass }}"
                                id="{{ $formSubmitBtnId }}">Submit</button>
                            <!-- Clear Filter -->                            
                            <button type="button" class="clear-filter-btn mt-4 btn btn-dark clearFilter">Clear Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- filter modal popup section end -->

<div class="modal-dialog modal-dialog-centered cstm-withset">
  <div class="modal-content modelcont-ctms p-4 position-relative">
    <div class="heading-modal">
      <h1 class="modal-title" id="exampleModalLabel">Change Language</h1>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
          class='bx bx-x'></i></button>
    </div>
    <div class="socila-viewwd-cdysa mt-3">
      <div class="row">
        <div class="col-lg-12 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
          <div class="box_pse-boxsdaf">
            <h4>Default Language : <span class="mt-2">{{ $resultArr['default_lang_value'] }}</span></h4>
          </div>
        </div>
      </div>
    </div>
    <form id="changeLanguageForm" method="POST" action="{{ route('admin.languageTemplates.addEdit') }}">
      <div class="ckediterbox my-3">
        <label class="form-label" for="">Translation Language</label>
        <textarea required name="new_change_language" id="new_change_language" class="form-control form-control-1"
        rows="4" placeholder="Translation Language">{{ $resultArr['current_lang_value'] }}</textarea>
      </div>
      <div class="bottom_saveTimebsg mt-4">
        <div class="row">
          <div class="col-lg-8 col-md-8 col-sm-8"></div>
          <div class="col-lg-4 col-md-4 col-sm-4">
            @csrf
            <input type="hidden" name="lang_key" id="lang_key" value="{{ $resultArr['langKey'] }}">
            <input type="hidden" name="lang_code" id="lang_code" value="{{ $resultArr['langCode'] }}">
            <button type="button" id="changeLanguageSubmit" class="submit-btn-mmbre ">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

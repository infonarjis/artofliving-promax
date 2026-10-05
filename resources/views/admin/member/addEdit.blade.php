@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')

<div class="content-wrapper">
  <!-- Toast with Placements -->
  <div class="bs-toast toast toast-placement-ex m-2" role="alert" aria-live="assertive" aria-atomic="true"
    data-delay="2000">
    <div class="toast-header">
      <i class="bx bx-bell me-2"></i>
      <div class="me-auto fw-semibold toast-title">Bootstrap</div>
      <small>Now</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">Fruitcake chocolate bar tootsie roll gummies gummies jelly beans cake.</div>
  </div>
  <!-- Toast with Placements -->
  <!-- Content -->
  <div class="container-fluid flex-grow-1 container-p-y">
    <!-- Edit Member section start  -->
    <div class="edit_memberSections_mainsdsd">
      <div class="iiner_bg_echange px-4">
        <ul class="nav nav-pills gap-2" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
              type="button" role="tab" aria-controls="pills-home" aria-selected="true">Basic Detail</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
              type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Location Details</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-other-profile-tab" data-bs-toggle="pill"
              data-bs-target="#pills-other-profile" type="button" role="tab" aria-controls="pills-other-profile"
              aria-selected="false">Physical Information</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-family-details-tab" data-bs-toggle="pill"
              data-bs-target="#pills-family-details" type="button" role="tab" aria-controls="pills-family-details"
              aria-selected="false">Family Details</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-partner-preference-tab" data-bs-toggle="pill"
              data-bs-target="#pills-partner-preference" type="button" role="tab"
              aria-controls="pills-partner-preference" aria-selected="false">Partner Preferences</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-upload-photo-tab" data-bs-toggle="pill"
              data-bs-target="#pills-upload-photo" type="button" role="tab" aria-controls="pills-upload-photo"
              aria-selected="false">Upload Photos</button>
          </li>
        </ul>
      </div>
      <div class="form_detailsEditsView">
        <div class="tab-content p-0" id="pills-tabContent">
          <!--Step 1 -->
          <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"
            tabindex="0">
            <div class="form_content_data">
              <form id="{{ $formSubmitBtnId1 }}" name="{{ $formName1 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                  <div class="row">
                    <?php echo $fromHtmlStep1; ?>
                  </div>

                  <h2 class="accordion-header mb-3">
                    <button type="button" class="accordion-button member-register p-sm-2 gap-2 collapsed" data-bs-toggle="collapse" data-bs-target="#religionInformation" aria-expanded="false" aria-controls="religionInformation">
                      <span><i class='bx bx-star'></i></span> Religious Information
                    </button>
                  </h2>
                  <div id="religionInformation" class="accordion-collapse collapse show" data-bs-parent="#accordionExample" style="">
                    <div class="row">
                      <?php echo $fromHtmlReligionStep1; ?>
                    </div>
                  </div>

                  <h2 class="accordion-header mb-3">
                    <button type="button" class="accordion-button member-register p-sm-2 gap-2 collapsed" data-bs-toggle="collapse" data-bs-target="#educationDetails" aria-expanded="false" aria-controls="educationDetails">
                      <span><i class='bx bx-book-reader'></i></span> Education & Other Details
                    </button>
                  </h2>
                  <div id="educationDetails" class="accordion-collapse collapse show" data-bs-parent="#accordionExample" style="">
                    <div class="row">
                      <?php echo $fromHtmlEducationStep1; ?>
                    </div>
                  </div>
                <input type="hidden" name="step" id="step" value="1">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId1 }}" id="{{ $formSubmitBtnId1 }}">Submit</button>
              </form>
            </div>
          </div>
          <!--Step 2 -->
          @php
            $addClass = '';
            if($mode == 'add' && blank($id)){
              $addClass = 'd-none';
            }
          @endphp
          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab"
            tabindex="0">
            @if($mode == 'add' && blank($id))
              <div class="alert alert-danger alert-dismissible memberAlertMsg" role="alert">
                  <b>Note :</b> Please Fill Basic Detail First.
              </div>
            @endif
            <div class="form_content_data {{ $addClass }} formAdd">
              <form id="{{ $formSubmitBtnId2 }}" name="{{ $formName2 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                <div class="row">
                  @csrf
                  <?php echo $fromHtmlStep2; ?>
                </div>
                <input type="hidden" name="step" id="step" value="2">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId2 }}" id="{{ $formSubmitBtnId2 }}">Submit</button>
              </form>
            </div>
          </div>
          <!--Step 3 -->
          <div class="tab-pane fade" id="pills-other-profile" role="tabpanel" aria-labelledby="pills-other-profile-tab"
            tabindex="0">
            @if($mode == 'add' && blank($id))
              <div class="alert alert-danger alert-dismissible memberAlertMsg" role="alert">
                  <b>Note :</b> Please Fill Basic Detail First.
              </div>
            @endif
            <div class="form_content_data {{ $addClass }} formAdd">
              <form id="{{ $formSubmitBtnId3 }}" name="{{ $formName3 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                <div class="row">
                  @csrf
                  <?php echo $fromHtmlStep3; ?>
                  <h4 class="mb-4 text-danger">About Us</h4>
                  <?php echo $fromHtmlAboutStep3; ?>
                </div>
                <input type="hidden" name="step" id="step" value="3">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId3 }}" id="{{ $formSubmitBtnId3 }}">Submit</button>
              </form>
            </div>
          </div>
          <!--Step 3 -->
          <div class="tab-pane fade" id="pills-family-details" role="tabpanel"
            aria-labelledby="pills-family-details-tab" tabindex="0">
            @if($mode == 'add' && blank($id))
              <div class="alert alert-danger alert-dismissible memberAlertMsg" role="alert">
                  <b>Note :</b> Please Fill Basic Detail First.
              </div>
            @endif
            <div class="form_content_data {{ $addClass }} formAdd">
              <form id="{{ $formSubmitBtnId4 }}" name="{{ $formName4 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                <div class="row">
                  @csrf
                  <?php echo $fromHtmlStep4; ?>
                </div>
                <input type="hidden" name="step" id="step" value="4">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId4 }}" id="{{ $formSubmitBtnId4 }}">Submit</button>
              </form>
            </div>
          </div>
          <!--Step 4 -->
          <div class="tab-pane fade" id="pills-partner-preference" role="tabpanel"
            aria-labelledby="pills-partner-preference-tab" tabindex="0">
            @if($mode == 'add' && blank($id))
              <div class="alert alert-danger alert-dismissible memberAlertMsg" role="alert">
                  <b>Note :</b> Please Fill Basic Detail First.
              </div>
            @endif
            <div class="form_content_data {{ $addClass }} formAdd">
              <form id="{{ $formSubmitBtnId5 }}" name="{{ $formName5 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                <div class="row">
                  @csrf
                  <?php echo $fromHtmlStep5; ?>
                </div>
                <input type="hidden" name="step" id="step" value="5">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId5 }}" id="{{ $formSubmitBtnId5 }}">Submit</button>
              </form>
            </div>
          </div>
          <!--Step 5 -->
          <div class="tab-pane fade" id="pills-upload-photo" role="tabpanel" aria-labelledby="pills-upload-photo-tab"
            tabindex="0">
            @if($mode == 'add' && blank($id))
              <div class="alert alert-danger alert-dismissible memberAlertMsg" role="alert">
                  <b>Note :</b> Please Fill Basic Detail First.
              </div>
            @endif
            <div class="form_content_data {{ $addClass }} formAdd">
              <form id="{{ $formSubmitBtnId6 }}" name="{{ $formName6 }}" action="{{ route($formUrl) }}" method="POST"
                enctype="multipart/form-data">
                <div class="row">
                  @csrf
                  <?php echo $fromHtmlStep6; ?>
                </div>
                <input type="hidden" name="step" id="step" value="6">
                <button type="button" class="btn btn-primary {{ $formSubmitBtnClass }}"
                  data-formid="{{ $formSubmitBtnId6 }}" id="{{ $formSubmitBtnId6 }}">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @csrf
  <input type="hidden" name="id" value="{{ $id }}" id="id" />
  <input type="hidden" name="mode" value="{{ $mode }}" id="mode" />

  <input type="hidden" name="formUrl" id="formUrl" value="{{ route($formUrl) }}">
  <input type="hidden" name="successUrl" id="successUrl" value="{{ route($successUrl) }}">
  <!-- Content -->
  <div class="content-backdrop fade"></div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('input', 'input[name="fullname"]', function() {
                let value = $(this).val();
                value = value.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
                $(this).val(value);
            });

            updateMarriedDropdown(
                "#no_of_brother",
                "#no_of_married_brother",
                "Brothers"
            );

            updateMarriedDropdown(
                "#no_of_sister",
                "#no_of_married_sister",
                "Sisters"
            );
            // Trigger after all events attached :
            setTimeout(function() {
                $('#no_of_brother').trigger('change');
                $('#no_of_sister').trigger('change');
            }, 1000);

        });
    </script>
@endpush
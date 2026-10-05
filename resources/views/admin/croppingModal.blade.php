<div class="modal fade" id="cropImageModalpop" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg p-4 p-md-0" role="document">
        <div class="modal-content">
            <div class="modal-header p-0 border-0 position-relative">
                <button type="button" class="close ctmcropclose" data-bs-dismiss="modal" aria-label="Close">
                    <i class='bx bx-x fs-30'></i>
                </button>
            </div>
            <div class="alert alert-error-msg alert-dismissible fade show errorMsgPhoto d-none"
              role="alert" id="errorMsgPhoto">
              <span class="msg-set" id="responseErrMsgPhoto"></span>
            </div>
            <div class="alert alert-success-msg alert-dismissible fade show successMsgPhoto d-none"
              role="alert" id="successMsgPhoto">
              <span class="msg-set" id="responseSucMsgPhoto"></span>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8 ps-lg-5">
                          <div class="cropping_imagmain">
                            <img id="croppingImage" src="{{ _getMemberDefaultImage('Male') }}" alt="">
                          </div>
                        </div>
                        <div class="col-md-4 mt-4 mt-lg-0 mt-md-0">
                            <div class="ctms-prevuisfa">
                                <div class="previewImage"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <div class="">
                <button class="btn-cropping cropMoveUp">Move Up <i class='bx bx-up-arrow-alt'></i></button>
                <button class="btn-cropping cropMoveDown">Move Down <i class='bx bx-down-arrow-alt'></i></button>
                <button class="btn-cropping cropMoveLeft">Move Left <i class='bx bx-left-arrow-alt' ></i></button>
                <button class="btn-cropping cropMoveRight">Move Right <i class='bx bx-right-arrow-alt'></i></button>
                <button class="btn-cropping zoomIn">Zoom In <i class='bx bx-zoom-in'></i></button>
                <button class="btn-cropping zoomOut">Zoom Out <i class='bx bx-zoom-out' ></i></button>
                <button class="btn-cropping rotateLeft">Rotate Left <i class='bx bx-rotate-left'></i></button>
                <button class="btn-cropping rotateRight">Rotate Right <i class='bx bx-rotate-right'></i></button>
                <button class="btn-cropping cropMoveUp ctmcropclose ">Back <i class='bx bx-x'></i></button>
              </div>
              <button type="button" class="croping-btn px-5" id="crop"><i class='bx bx-crop'></i> Crop & Upload</button>
            </div>
        </div>
    </div>
  </div>
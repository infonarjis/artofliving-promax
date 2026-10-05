<div class="modal-dialog modal-dialog-centered cstm-withset">
  <div class="modal-content modelcont-ctms p-4 position-relative">
    <div class="heading-modal">
      <h1 class="modal-title" id="exampleModalLabel">Add Comment</h1>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
          class='bx bx-x'></i></button>
    </div>
    <div class="socila-viewwd-cdysa mt-3">
      <div class="row">
        <div class="col-lg-12 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
          <div class="box_pse-boxsdaf">
            <h4><span>Ticket Number</span> : {{ $commentData['ticket_number'] }}</h4>
          </div>
        </div>
      </div>
    </div>
    <form id="addMemberCommentForm" method="POST" action="{{ route('admin.ticketManagement.saveComment') }}">
      <div class="ckediterbox my-3">
        <textarea required name="comment" id="comment" class="form-control form-control-1"
        rows="5" placeholder="Comment"></textarea>
      </div>
      <div class="bottom_saveTimebsg mt-4">
        <div class="row">
          <div class="col-lg-8 col-md-8 col-sm-8"></div>
          <div class="col-lg-4 col-md-4 col-sm-4">
            @csrf
            <input type="hidden" name="commented_user_type" id="commented_user_type" value="{{ $commentData['commented_user_type'] }}">
            <input type="hidden" name="userId" id="userId" value="{{ $commentData['userId'] }}">
            <input type="hidden" name="ticket_id" id="ticket_id" value="{{ $commentData['ticket_id'] }}">
            <input type="hidden" name="ticket_number" id="ticket_number" value="{{ $commentData['ticket_number'] }}">
            <button type="button" id="addMemberCommentSubmit" class="submit-btn-mmbre mt-4 d-flex">Save Comments</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

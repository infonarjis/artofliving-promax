<!-- view comment modal popup section start  -->
<div class="modal-dialog modal-dialog-centered cstm-withset">
  <div class="modal-content modelcont-ctms p-4 position-relative">
    <div class="heading-modal">
      <h1 class="modal-title" id="exampleModalLabel">View Comment</h1>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
          class='bx bx-x'></i></button>
    </div>
    <div class="socila-viewwd-cdysa mt-3">
      <div class="row">
        <div class="col-lg-12 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
          <div class="box_pse-boxsdaf">
            <h4><span>Ticket Number</span> : {{ $ticketDataArr->ticket_number }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="views_multies-commntsa mt-4 pe-2">
      @if (!empty($resultDataArr) && $resultDataArr != '')
      @foreach ($resultDataArr as $dataValue)
      <div class="single-comment-viewsd mb-3">
        <h4>{{ _displayNotAvailable(ucwords($dataValue->user_type)) }}</h4>
        @php $comment = _displayNotAvailable($dataValue->comment); @endphp
        <p>{!! html_entity_decode($comment) !!}</p>
        <div class="border-setdesignsa d-block d-lg-flex d-md-flex d-sm-flex mt-3 gap-3">
          <h5 class="mt-2"><span>Comment On  :</span> {{ _displayDate($dataValue->created_at, 'j F, Y h:i A') }}</h5>
        </div>
      </div>
      @endforeach
      @else
      <div class="single-comment-viewsd mb-3">
        <h4>No Any Comment Found!</h4>
      </div>
      @endif
    </div>
  </div>
</div>
<!-- view comment modal popup section end -->

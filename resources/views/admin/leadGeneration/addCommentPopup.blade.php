<div class="modal-dialog modal-dialog-centered cstm-withset">
    <div class="modal-content modelcont-ctms p-4 position-relative">
        <div class="heading-modal">
            <h1 class="modal-title" id="exampleModalLabel">Add Comment</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                    class='bx bx-x'></i></button>
        </div>
        <div class="socila-viewwd-cdysa mt-3">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
                    <div class="box_pse-boxsdaf">
                        <div class="icon-social-sddhy">
                            <i class="bx bx-user"></i>
                        </div>
                        <h4>{{ $leadData->username }}</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
                    <div class="box_pse-boxsdaf bs-email">
                        <div class="icon-social-sddhy">
                            <i class="bx bxs-envelope"></i>
                        </div>
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                            <h4>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</h4>
                        @else
                            <h4>{{ _displayNotAvailable($leadData->email) }}</h4>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 px-2 mb-3 mb-lg-0">
                    <div class="box_pse-boxsdaf bs-phon">
                        <div class="icon-social-sddhy">
                            <i class="bx bx-phone"></i>
                        </div>
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                            <h4>{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}</h4>
                        @else
                            <h4>{{ _displayNotAvailable($leadData->phone_no_1) }}</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <form id="addMemberCommentForm" method="POST" action="{{ route('admin.leadGeneration.saveComment') }}">
            <div class="ckediterbox my-3">
                <textarea required name="comment" id="comment" class="form-control form-control-1" rows="5"
                    placeholder="Comment"></textarea>
            </div>
            <div class="bottom_saveTimebsg mt-4">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-1">
                        <div class="edit_inputMain-sltr">
                            <label class="form-label" for="">NEXT FOLLOWUP DATE</label>
                            <input type="date" required min="{{ date('Y-m-d') }}" name="next_followup_date"
                                id="next_followup_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-1">
                        <div class="edit_inputMain-sltr">
                            <label class="form-label" for="">NEXT FOLLOWUP TIME</label>
                            <input type="time" name="next_followup_time" id="next_followup_time"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-1">
                        <div class="edit_inputMain-sltr">
                            <label class="form-label" for="">Lead Status</label>
                            <select name="lead_status" id="lead_status" class="form-control">
                                <option value="open" {{ $leadData->lead_status == 'open' ? 'selected' : '' }}>Open
                                </option>
                                <option value="close" {{ $leadData->lead_status == 'close' ? 'selected' : '' }}>Close
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        @csrf
                        <input type="hidden" name="member_id" id="member_id" value="{{ $leadData->id }}">
                        <input type="hidden" name="posted_by" id="posted_by" value="{{ $commentData['posted_by'] }}">
                        <input type="hidden" name="commented_user_type" id="commented_user_type"
                            value="{{ $commentData['commented_user_type'] }}">
                        <input type="hidden" name="comment_by_id" id="comment_by_id"
                            value="{{ $commentData['comment_by_id'] }}">
                        <input type="hidden" name="follow_up_status" id="follow_up_status"
                            value="{{ $commentData['follow_up_status'] }}">
                        <button type="button" id="addMemberCommentSubmit" class="submit-btn-mmbre mt-4 ">Save
                            Comments</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

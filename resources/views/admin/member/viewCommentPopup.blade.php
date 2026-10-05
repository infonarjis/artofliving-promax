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
                <div class="col-lg-4 col-md-6 col-sm-6 px-2 mb-3 mb-lg-0">
                    <div class="box_pse-boxsdaf">
                        <div class="icon-social-sddhy">
                            <i class="bx bx-user"></i>
                        </div>
                        <h4>{{ _displayNotAvailable($memberData->fullname) }}</h4>
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
                            <h4>{{ _displayNotAvailable($memberData->email) }}</h4>
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
                            <h4>{{ _displayNotAvailable($memberData->mobile) }}</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="views_multies-commntsa mt-4 pe-2">
            @if (!empty($resultDataArr) && $resultDataArr != '')
                @foreach ($resultDataArr as $dataValue)
                    <div class="single-comment-viewsd mb-3">
                        @if ($dataValue->posted_user_type == 'admin')
                            <h4>{{ _displayNotAvailable(ucwords($dataValue->posted_user_type)) }}</h4>
                        @else
                            <h4>{{ _displayNotAvailable($dataValue->staff->username) }}
                                ({{ _displayNotAvailable(ucwords($dataValue->posted_user_type)) }})</h4>
                        @endif
                        @php $comment = _displayNotAvailable($dataValue->comment); @endphp
                        <p>{!! html_entity_decode($comment) !!}</p>
                        <div class="border-setdesignsa d-block d-lg-flex d-md-flex d-sm-flex mt-3 gap-3">
                            <h5 class="mt-2">{{ _displayDate($dataValue->created_at, 'j F, Y h:i A') }}</h5>
                            <h5 class="mt-2"><span>Next Followup Date :</span>
                                {{ _displayDate($dataValue->next_followup_date, 'j F, Y') }}</h5>
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

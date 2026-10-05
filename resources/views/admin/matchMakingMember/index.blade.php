@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- add Member Top sections Start -->
        @include('admin.matchMakingMember.actionButtonsMember')

        @include('admin.message')
        <!-- Member Top sections End -->
        <!-- add member bottom tab sections  -->
        <div class="bottom_AddSectionMBm">
            <div class="tab-content" id="resultData">

            </div>
        </div>
    </div>
    <!-- add comment modal popup section start  -->
    <div class="modal fade" id="add_commentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    </div>
    <!-- view comment modal popup section start  -->
    <div class="modal fade" id="view_commentModal" tabindex="-1" aria-labelledby="exampleModalLabel123" aria-hidden="true">
    </div>
    <!-- filter modal popup section start  -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    </div>

    <!-- Manually Match Send Popup  -->
    <div class="modal fade" id="manuallyMatchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered manually-match-send">
            <div class="modal-content modelcont-ctms p-4 position-relative">
                <div class="heading-modal">
                    <h1 class="modal-title" id="exampleModalLabel">Send Manually Match To {{ $memberRegMatriId }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class='bx bx-x'></i></button>
                </div>
                <div class="bottom_saveTimebsg mt-4">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="manuallyMatchSend" name="manuallyMatchSend"
                                action="{{ route('admin.matchMakingMember.sendManualMatch') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="custom-select2-div">
                                    <div class="edit_inputMain-sltr select2Part w-100 floating-group">
                                        <div class="mb-3 ">
                                            <label class="form-label" for="otherUserData">Member List</label>
                                            <select name="otherUserData" id="otherUserData" class="form-select"></select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="memberMatriId" value="{{ $memberMatriId }}">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <button type="submit"
                                        data-action="{{ route('admin.matchMakingMember.sendManualMatch') }}"
                                        class="submit-btn-mmbre mt-4 btn btn-primary manuallyMatchSendBtn"
                                        id="manuallyMatchSendBtn">Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @csrf
    <input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
    <input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
    @if (!empty($memberMatriId))
        <input type="hidden" name="memberMatriId" id="memberMatriId" value="{{ $memberMatriId }}">
    @endif
    <input type="hidden" name="page" id="page" value="1">
    <input type="hidden" name="isFilterApply" id="isFilterApply" value="0">
@endsection
@push('scripts')
    <script>
        $('.sendMatchManuallyModel').on('click', function() {
            $('#manuallyMatchModal').modal('show');
            if (!$('#otherUserData').hasClass('select2-hidden-accessible')) {
                $('#otherUserData').select2({
                    dropdownParent: $('#manuallyMatchModal'),
                    width: '100%',
                    placeholder: 'Select Member List',
                    allowClear: true,
                    minimumInputLength: 0,
                    ajax: {
                        url: "{{ route('admin.matchMakingMember.getMemberData') }}",
                        type: "GET",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term || '',
                                member_id: "{{ $memberRegId }}" // base64 encoded id or numeric id
                            };
                        },
                        processResults: function(response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
            }
        });
    </script>
@endpush

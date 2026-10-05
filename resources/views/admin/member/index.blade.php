@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- add Member Top sections Start -->
    @include('admin.actionButtonsMember')
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
@csrf
<input type="hidden" name="ajaxRequestUrl" id="ajaxRequestUrl" value="{{ route($ajaxPaginationRequestUrl) }}">
<input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="isFilterApply" id="isFilterApply" value="0">
@endsection

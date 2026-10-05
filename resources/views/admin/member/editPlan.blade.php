@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
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
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- member edit plan design  -->
            <div class="plans_editMainsddss">
                <form name="planAssignForm" id="planAssignForm" action="{{ route('admin.member.editPlanUpdate') }}"
                    method="POST">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="left_profileEDTYg text-center">
                                <?php
                                ## Image Arr :
                                $imgArr = _getMemberDefaultImage($memberData->gender);
                                if (!blank($memberData->photo1) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $memberData->photo1)) {
                                    $imgArr = _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $memberData->photo1;
                                }
                                ?>
                                <img src="{{ $imgArr }}" alt="" class="bxl-kus-profile">
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="right_art-designds mt-4 mt-lg-0">
                                <div class="top_social-dfrgd">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user_icvBoxdssdad">
                                                    <i class='bx bx-user-pin'></i>
                                                </div>
                                                <h4 class="text-steldTops">{{ $memberData->matri_id }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user_icvBoxdssdad">
                                                    <i class='bx bx-user'></i>
                                                </div>
                                                <h4 class="text-steldTops">{{ $memberData->fullname }}</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user_icvBoxdssdad E-clr">
                                                    <i class='bx bxs-envelope'></i>
                                                </div>
                                                @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                    <h4 class="text-steldTops">{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                    </h4>
                                                @else
                                                    <h4 class="text-steldTops">{{ _displayNotAvailable($memberData->email) }}
                                                    </h4>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user_icvBoxdssdad p-clr">
                                                    <i class='bx bx-phone'></i>
                                                </div>
                                                @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                    <h4 class="text-steldTops">{{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                    </h4>
                                                @else
                                                    <h4 class="text-steldTops">
                                                        {{ _displayNotAvailable($memberData->mobile) }}</h4>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assignment Type Toggle: New Plan vs Add-On Only -->
                                <div class="assign_typeToggle mt-2">
                                    <div class="btn-group assign-type-group" role="group" aria-label="Assignment Type">
                                        <input type="radio" class="btn-check" name="assignType" id="assignTypeNew"
                                            value="new_plan" checked>
                                        <label class="btn btn-outline-primary assign-type-btn" for="assignTypeNew">
                                            <i class='bx bx-plus-circle'></i> Assign New Plan
                                        </label>

                                        <input type="radio" class="btn-check" name="assignType" id="assignTypeAddon"
                                            value="addon_only" {{ empty($currentPlanData) ? 'disabled' : '' }}>
                                        <label class="btn btn-outline-primary assign-type-btn" for="assignTypeAddon">
                                            <i class='bx bx-package'></i> Assign Add-On Only
                                        </label>
                                    </div>

                                    @if (empty($currentPlanData))
                                        <small class="text-muted d-block mt-2">
                                            <span class="font-semiBold">Note : </span>"Add-On Only" is disabled — this member has no active plan yet.
                                        </small>
                                    @else
                                        <small class="text-muted d-block mt-2">
                                            Member's current active plan: <strong>{{ $currentPlanData->plan_name }}</strong>
                                        </small>
                                    @endif
                                </div>

                                <div class="plan_searchDivsda mt-2">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 mt-3" id="planSelectWrap">
                                            <div class="custom-select2-div">
                                                <div class="edit_inputMain-sltr select2Part w-100 floating-group">
                                                    <select name="plan_id" id="plan_id"
                                                        class="js-states form-control select2 getPlanData"
                                                        data-placeholder="Select Membership Plan" required>
                                                        <option value="">Select Membership Plan</option>
                                                        @foreach ($membershipPlanArr as $planKey => $planData)
                                                            <option value="{{ $planData->id }}">{{ $planData->plan_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mt-3">
                                            <div class="custom-select2-div">
                                                <div class="edit_inputMain-sltr select2Part w-100 floating-group">
                                                    <select name="payment_mode" id="payment_mode"
                                                        class="js-states form-control" required>
                                                        <option value="">Select Payment Mode</option>
                                                        @foreach ($paymentModeArr as $paymentKey => $paymentData)
                                                            <option value="{{ $paymentData }}">{{ $paymentData }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="right_art-designds mt-4 mt-lg-0">
                                <div class="plans_viewDetails-cdf mt-4" id="resultData">
                                    <h4 class="plan_typesShow text-center">No Plan Selected </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="col-lg-12">
                            <!-- Enhanced Add On Packages Section Start-->
                            <div class="right_art-designds mt-4 mt-lg-0">
                                <div class="plan_searchDivsda mt-2">
                                    <div class="plans_viewDetails-cdf mb-2">
                                        <h4 class="plan_typesShow text-center">Add On Packages</h4>
                                    </div>
                                    @if (isset($addOnPackageArr) && !blank($addOnPackageArr))
                                        <div class="row mt-4">
                                            <div class="col-lg-12 col-md-12">
                                                <div class="addon-packages-container">
                                                    <div class="row g-4">
                                                        @foreach ($addOnPackageArr as $value)
                                                            <div class="col-lg-4 col-md-4 mt-0 mb-2">
                                                                <div class="addon-package-card">
                                                                    <div class="package-header">
                                                                        <div class="package-checkbox">
                                                                            <input
                                                                                class="form-check-input addOnPackageCheckBox"
                                                                                type="checkbox" name="add_on_id[]"
                                                                                id="addOn<?php echo $value->id; ?>"
                                                                                value="<?php echo $value->id; ?>">
                                                                            <label class="form-check-label"
                                                                                for="addOn<?php echo $value->id; ?>"></label>
                                                                        </div>
                                                                        <div class="package-title">
                                                                            <h5>{{ $value->package_title }}</h5>
                                                                            <span
                                                                                class="package-category">{{ $value->package_category }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="package-body">
                                                                        <div class="package-details">
                                                                            <div class="detail-item">
                                                                                <i class='bx bx-money'></i>
                                                                                <span>Package Amount:
                                                                                    <strong>{{ $value->package_amount }}</strong></span>
                                                                            </div>
                                                                            <div class="detail-item">
                                                                                <i class='bx bx-pie-chart-alt-2'></i>
                                                                                <span>Package Count:
                                                                                    <strong>{{ $value->package_count }}</strong></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="package-description">
                                                                            <p>{{ $value->description }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Enhanced Add On Packages Section End -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="payment_notinputert mt-3 position-relative">
                                <textarea name="payment_note" id="payment_note" maxlength="250" class="checkLimit note_pays"
                                    placeholder="Payment Note"></textarea>
                                <h4 class="max_charact" id="add-char">250 / 250 Remaining Character</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mt-lg-4 mt-md-4">
                        <div class="col-12">
                            @csrf
                            <input type="hidden" name="memberId" id="memberId" value="{{ $memberData->id }}" />
                            <button type="button" name="planAssignSubmit" id="planAssignSubmit"
                                class="submit-btn-mmbre">
                                Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- end edit plan  -->
        </div>
        <div class="content-backdrop fade"></div>
    </div>
    @csrf
    <input type="hidden" name="changeStatusUrl" id="changeStatusUrl" value="{{ route($changeStatusUrl) }}">
    <input type="hidden" name="redirectUrl" id="redirectUrl" value="{{ route($redirectUrl) }}">
    <input type="hidden" name="getPlanDataUrl" id="getPlanDataUrl" value="{{ route($getPlanDataUrl) }}">
@endsection
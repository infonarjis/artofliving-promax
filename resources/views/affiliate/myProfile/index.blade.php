@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.mainlayout')
@section('affiliate_after_login_content')
    @php
        $referralLink = route('web.register.referral', [
            'type' => 'affiliate',
            'code' => $affiliateUser->referral_code,
        ]);
        $qrCodeUrl = _assetUrl('upload_path.AFFILIATE_QR_CODE_IMG') . $affiliateUser->qr_image;
    @endphp
    <main class="afd-main">
        <div class="row g-4">
            <!-- LEFT: Profile Avatar & Basic Info -->
            <div class="col-lg-4">
                <div class="afd-panel text-center p-4">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div class="afd-ud-hav" style="width: 100px; height: 100px; font-size: 40px; line-height: 100px;">
                            <img src="{{ _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $affiliateUser->image }}"
                                alt="{{ $affiliateUser->fullname }}" class="">
                        </div>
                    </div>
                    <h4 class="mb-1" style="color: var(--afd-text);">{{ $affiliateUser->fullname }}</h4>
                    <p class="mb-3" style="color: var(--afd-muted); font-size: 14px;">Registered Since
                        {{ _displayDate($affiliateUser->created_at, 'j F, Y') }}</p>

                    <div class="afd-ud-sep my-3"></div>

                    <div class="d-flex flex-column gap-2 text-start">
                        <div class="d-flex align-items-center gap-3 p-2 rounded" style="background: var(--afd-surface2);">
                            <div class="afd-mc-icon" style="width: 36px; height: 36px; min-width: 36px;"><iconify-icon
                                    icon="hugeicons:mail-01" width="20"></iconify-icon></div>
                            <div class="overflow-hidden">
                                <div style="font-size: 12px; color: var(--afd-muted);">Email Address</div>
                                <div
                                    style="color: var(--afd-text); font-size: 14px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{ $affiliateUser->email }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-2 rounded" style="background: var(--afd-surface2);">
                            <div class="afd-mc-icon" style="width: 36px; height: 36px; min-width: 36px;"><iconify-icon
                                    icon="hugeicons:smart-phone-01" width="20"></iconify-icon></div>
                            <div>
                                <div style="font-size: 12px; color: var(--afd-muted);">Phone Number</div>
                                <div style="color: var(--afd-text); font-size: 14px;">{{ $affiliateUser->mobile }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Section -->
                <div class="afd-panel mt-4 p-3">
                    <span class="afd-edit-section-title">Your Referral Link</span>
                    <div class="afd-ref-link-field">
                        <input type="text" class="afd-modal-input" value="{{ $referralLink }}" readonly
                            style="font-size:12px; color:var(--afd-blue)">
                        <button class="afd-qr-btn" onclick="copyLink(this)" style="padding: 0 16px;">
                            <iconify-icon icon="hugeicons:copy-01"></iconify-icon> Copy
                        </button>
                    </div>

                    <div class="afd-qr-showcase">
                        <div class="afd-qr-code-box">
                            <img src="{{ _assetUrl('upload_path.AFFILIATE_QR_CODE_IMG') . $affiliateUser->qr_image }}"
                                alt="QR Code">
                        </div>
                        <div class="afd-ref-actions">
                            @php
                                ## WhatsApp share URL :
                                $whatsappMessage = urlencode(
                                    "Join using my referral link: $referralLink \nQR: $qrCodeUrl",
                                );
                                $whatsappUrl = "https://api.whatsapp.com/send?text=$whatsappMessage";
                            @endphp
                            <a href="{{ $whatsappUrl }}" target="_blank" class="afd-btn-whatsapp">
                                <iconify-icon icon="logos:whatsapp-icon" width="18"></iconify-icon>
                                Share on WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Detailed Forms -->
            <div class="col-lg-8">
                <div class="afd-panel p-4">
                    <div class="afd-panel-header mb-4 border-0 p-0">
                        <span class="d-flex align-items-center gap-2">
                            <iconify-icon icon="hugeicons:user-edit-01" width="24" height="24"></iconify-icon>
                            Personal Information
                        </span>
                    </div>
                    <form id="profileForm" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <div class="afd-avatar-edit-row mb-1">
                                {{-- <div class="afd-avatar-large" id="avatarPreview"></div> --}}
                                <div class="afd-avatar-large" id="avatarPreview"
                                    style="
        background-image:url('{{ _assetUrl('upload_path.AFFILIATE_MEMBER_PHOTOS_URL') . $affiliateUser->image }}');
        background-size:cover;background-position:center;">
                                    @if (!$affiliateUser->image)
                                        {{ strtoupper(substr($affiliateUser->fullname, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <input type="file" id="photoInput" name="image" style="display:none"
                                        accept="image/*">
                                    <button class="afd-modal-btn-save" id="changePhotoBtn"
                                        style="padding: 7px 18px; font-size: 13px; box-shadow:none;">Change Photo</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"
                                style="color: var(--afd-muted); font-size: 13px;">
                                Gender
                            </label>

                            <div class="d-flex align-items-center gap-4 mt-2">

                                <!-- Male -->
                                <div class="form-check">
                                    <input type="radio"
                                        name="gender"
                                        id="gender_male"
                                        value="Male"
                                        class="form-check-input"
                                        {{ $affiliateUser->gender == 'Male' ? 'checked' : '' }}>

                                    <label class="form-check-label"
                                        for="gender_male"
                                        style="color: var(--afd-text);">
                                        Male
                                    </label>
                                </div>

                                <!-- Female -->
                                <div class="form-check">
                                    <input type="radio"
                                        name="gender"
                                        id="gender_female"
                                        value="Female"
                                        class="form-check-input"
                                        {{ $affiliateUser->gender == 'Female' ? 'checked' : '' }}>

                                    <label class="form-check-label"
                                        for="gender_female"
                                        style="color: var(--afd-text);">
                                        Female
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control"
                                placeholder="Enter full name"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->fullname }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Email
                                Address</label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="Enter email address"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->email }}">
                        </div>
                        <div class="col-md-6">
                            @php
                                $parts = explode('-', $affiliateUser->mobile ?? '');
                                $countryCode = $parts[0] ?? '+91';
                                $mobileNumber = $parts[1] ?? '';
                            @endphp
                            <div class="afd-form-group">
                                <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Mobile Number</label>
                                <div class="afd-phone-input-group">
                                    <select name="country_code" id="country_code"
                                        class="afd-modal-input afd-country-select" id="countryCodeSelect"
                                        style="padding-top:8px">
                                        @php echo _defaultCountryCode($countryCode) @endphp
                                    </select>
                                    <div class="afd-input-wrap" style="flex:1">
                                        <i class="mt-1"><iconify-icon
                                                icon="hugeicons:smart-phone-01"></iconify-icon></i>
                                        <input type="text" name="mobile" id="mobile"
                                            placeholder="Enter mobile number" class="afd-modal-input afd-modal-input-icon"
                                            value="{{ $mobileNumber }}" style="color: var(--afd-text);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="afd-panel-header mb-1 border-0 p-0">
                                <span class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="hugeicons:user-edit-01" width="24"
                                        height="24"></iconify-icon>
                                    Bank Information
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control"
                                placeholder="Enter bank name"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->bank_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Bank Account
                                Holder Name</label>
                            <input type="text" name="bank_account_holder_name" id="bank_account_holder_name"
                                placeholder="Account holder name" class="form-control"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->bank_account_holder_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Bank Account
                                Type</label>
                            <div class="afd-select-wrap w-100">
                                <select class="afd-custom-select w-100" name="bank_account_type" id="bank_account_type">
                                    <option value="Savings account"
                                        {{ isset($affiliateUser) && $affiliateUser->bank_account_type == 'Savings account' ? 'selected' : '' }}>
                                        Savings account
                                    </option>

                                    <option value="Current account"
                                        {{ isset($affiliateUser) && $affiliateUser->bank_account_type == 'Current account' ? 'selected' : '' }}>
                                        Current account
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Bank Account
                                Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number"
                                class="form-control" placeholder="Enter account number"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->bank_account_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">Bank IFSC
                                Code</label>
                            <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control"
                                placeholder="Enter IFSC code"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->bank_ifsc_code }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="color: var(--afd-muted); font-size: 13px;">UPI Id</label>
                            <input type="text" name="upi_id" id="upi_id" class="form-control"
                                placeholder="example@upi"
                                style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: var(--afd-text);"
                                value="{{ $affiliateUser->upi_id }}">
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="afd-copy-btn px-4 py-2" style="width: auto;">Save
                                Changes</button>
                        </div>
                    </form>
                </div>

                <div class="afd-panel mt-4 p-4">
                    <div class="afd-panel-header mb-4 border-0 p-0">
                        <span class="d-flex align-items-center gap-2">
                            <iconify-icon icon="hugeicons:lock-security" width="24" height="24"></iconify-icon>
                            Security Settings
                        </span>
                    </div>
                    <form id="passwordForm" class="row g-3">
                        @csrf

                        <!-- Current Password -->
                        <div class="col-md-6">
                            <label class="form-label"
                                style="color: var(--afd-muted); font-size: 13px;">
                                Current Password
                            </label>

                            <div class="position-relative">
                                <input type="password"
                                    name="current_password"
                                    class="form-control password-input"
                                    style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: #fff !important; padding-right: 45px;"
                                    placeholder="Enter current password">

                                <button type="button"
                                    class="password-toggle"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--afd-muted); padding: 0; cursor: pointer;">
                                    <iconify-icon icon="mdi:eye-outline" width="20"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="col-md-6">
                            <label class="form-label"
                                style="color: var(--afd-muted); font-size: 13px;">
                                New Password
                            </label>

                            <div class="position-relative">
                                <input type="password"
                                    name="new_password"
                                    class="form-control password-input"
                                    style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: #fff !important; padding-right: 45px;"
                                    placeholder="Enter new password">

                                <button type="button"
                                    class="password-toggle"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--afd-muted); padding: 0; cursor: pointer;">
                                    <iconify-icon icon="mdi:eye-outline" width="20"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label class="form-label"
                                style="color: var(--afd-muted); font-size: 13px;">
                                Confirm Password
                            </label>

                            <div class="position-relative">
                                <input type="password"
                                    name="new_password_confirmation"
                                    class="form-control password-input"
                                    style="background: var(--afd-surface2); border: 1px solid var(--afd-border); color: #fff !important; padding-right: 45px;"
                                    placeholder="Confirm new password">

                                <button type="button"
                                    class="password-toggle"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--afd-muted); padding: 0; cursor: pointer;">
                                    <iconify-icon icon="mdi:eye-outline" width="20"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 mt-4">
                            <button type="submit"
                                class="afd-copy-btn px-4 py-2"
                                style="width: auto; background-color: var(--afd-surface2); border: 1px solid var(--afd-border);">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $('#changePhotoBtn').on('click', function(e) {
            e.preventDefault();
            $('#photoInput').click();
        });

        $('#photoInput').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatarPreview').html('').css({
                        'background-image': 'url(' + e.target.result + ')',
                        'background-size': 'cover',
                        'background-position': 'center'
                    });
                }
                reader.readAsDataURL(file);
            }
        });

        /* ---------- PROFILE ---------- */
        $("#profileForm").validate({
            errorClass: 'text-danger',
            rules: {
                gender: "required",
                fullname: "required",
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true,
                    digits: true,
                    minlength: 8
                }
            },
            submitHandler: function(form) {

                let fd = new FormData(form);

                $.ajax({
                    url: "{{ route('affiliate.myProfile.update') }}",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            showToastMessage('success', res.message)
                            location.reload();
                        } else {
                            showErrors(res.errors);
                        }
                    }
                });

                return false;
            }
        });

        /* ---------- PASSWORD ---------- */
        $("#passwordForm").validate({
            errorClass: 'text-danger',
            rules: {
                current_password: "required",
                new_password: {
                    required: true,
                    minlength: 6
                }
            },
            submitHandler: function(form) {

                $.post("{{ route('affiliate.myProfile.changePassword') }}",
                    $(form).serialize(),
                    function(res) {
                        if (res.status) {
                            showToastMessage('success', res.message)
                            form.reset();
                        } else {
                            showErrors(res.errors);
                        }
                    }
                );

                return false;
            }
        });

        /* ---------- ERROR HELPER ---------- */
        function showErrors(errors) {
            $('.text-danger').remove();
            $.each(errors, function(k, v) {
                $('[name="' + k + '"]').after('<div class="text-danger">' + v[0] + '</div>');
            });
        }

        function copyLink(button) {
            // Get the input inside the same container
            const input = button.parentElement.querySelector('input');

            // Select and copy the text
            input.select();
            input.setSelectionRange(0, 99999); // for mobile devices

            navigator.clipboard.writeText(input.value).then(() => {
                // Optional: show a success message
                button.innerText = 'Copied!';
                setTimeout(() => {
                    button.innerHTML = '<iconify-icon icon="hugeicons:copy-01"></iconify-icon> Copy';
                }, 1500);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        $(document).on('click', '.password-toggle', function () {

            const button = $(this);
            const input = button.siblings('.password-input');
            const icon = button.find('iconify-icon');

            if (input.attr('type') === 'password') {

                input.attr('type', 'text');

                icon.attr('icon', 'mdi:eye-off-outline');

            } else {

                input.attr('type', 'password');

                icon.attr('icon', 'mdi:eye-outline');
            }
        });
    </script>
@endpush

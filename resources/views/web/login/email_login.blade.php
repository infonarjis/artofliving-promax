<form id="loginForm" method="POST">
    @csrf
    {{-- decoy fields: absorb browser autofill --}}
    <input type="text" name="fake_user" autocomplete="username" tabindex="-1"
        style="position:absolute; opacity:0; height:0; width:0; pointer-events:none;">
    <input type="password" name="fake_pass" autocomplete="new-password" tabindex="-1"
        style="position:absolute; opacity:0; height:0; width:0; pointer-events:none;">

    <div class="comman_inputfield_main position-relative w-100">
        <label for="mobile">{{ __('messages.field_lbl_mobile_number') }}
            <span class="required-field">*</span>
        </label>
        <div class="d-flex gap-3">
            <div class="custom-select2-div country-code">
                <div class="edit_inputMain-sltr w-100">
                    <select name="country_code" id="country_code1" class="Single_searchDv">
                        @php echo _defaultCountryCode() @endphp
                    </select>
                </div>
            </div>
            <div class="position-relative w-100">
                <input type="text" name="mobile" id="mobile" maxlength="12" inputmode="numeric"
                    class="input_comman_field" placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}">
            </div>
        </div>
    </div>

    <div class="comman_inputfield_main position-relative mb-3">
        <label for="password">{{ __('messages.field_lbl_enter_password') }}</label>
        <div class="position-relative icon-display">
            <input type="password" name="password" id="password" autocomplete="new-password" readonly
                onfocus="this.removeAttribute('readonly');" placeholder="{{ __('messages.field_lbl_enter_password') }}"
                class="input_comman_field">
            <div class="toggle-password black-color6-n"></div>
        </div>
    </div>

    <div class="comman_inputfield_main position-relative d-flex align-items-center gap-2 pb-2 icon-display">
        <div class='CaptchaWrap'>
            <div id="captcha-display" class="CaptchaTxtField capcode d-flex">{{ $captchaCode ?? '' }}</div>
        </div>
        <div class="captcha-refresh-btn w-100 position-relative">
            <input type="button" id="refresh-captcha" class="ReloadBtn d-none" value="{{ $captchaCode ?? '' }}"
                maxlength="6" autocomplete="off">
            <label for="refresh-captcha" id="refresh-captcha-btn" class="refresh-icon-captcha">
                <iconify-icon icon="nrk:refresh"></iconify-icon>
            </label>
            <input type="text" name="captcha_code" id="UserCaptchaCode" class="input_comman_field"
                placeholder='{{ __('messages.field_lbl_enter_captcha_code') }}' autocomplete="off" required>
        </div>
        <span id="WrongCaptchaError" class="captcha-error"></span>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="commom-checkboxdiv-l">
            <input type="checkbox" name="remember" id="remember" class="d-none">
            <label for="remember"
                class="comman_chack d-flex align-items-center gap-2 white-color70-n fts-14">{{ __('messages.lbl_remember_me') }}</label>
        </div>
        <a href="{{ route('web.forgotPassword.index') }}"
            class="white-color70-n fw-4 fts-14">{{ __('messages.lbl_forgot_password') }}?</a>
    </div>
    <input type="hidden" name="token" id="token" value="">
    <input type="hidden" name="latitude" id="latitude" value="">
    <input type="hidden" name="longitude" id="longitude" value="">
    <button class="comman-bg-btn fts-15 w-100" type="submit">{{ __('messages.lbl_login') }}</button>
</form>
@if (_getConstant('DISABLE_DEMO') == 'Enabled')
    @php
        $demoUsers = [
            [
                'label' => __('messages.lbl_male_user'),
                'icon' => 'mdi:gender-male',
                'username' => _getConstant('DEMO_CREDENTIALS.male_login.email'),
                'password' => _getConstant('DEMO_CREDENTIALS.male_login.password'),
            ],
            [
                'label' => __('messages.lbl_female_user'),
                'icon' => 'mdi:gender-female',
                'username' => _getConstant('DEMO_CREDENTIALS.female_login.email'),
                'password' => _getConstant('DEMO_CREDENTIALS.female_login.password'),
            ],
        ];
    @endphp
    <div class="demo-login-box mt-4">
        <div class="demo-login-title">{{ __('messages.lbl_demo_login') }}</div>

        <div class="demo-login-grid">
            @foreach ($demoUsers as $u)
                <div class="demo-user-card">
                    <div class="demo-user-head">
                        <iconify-icon icon="{{ $u['icon'] }}"></iconify-icon>
                        <span>{{ $u['label'] }}</span>
                    </div>

                    <div class="demo-cred-row">
                        <span class="demo-cred-label">{{ __('messages.lbl_username') }}</span>
                        <code class="demo-cred-value">{{ $u['username'] }}</code>
                        <button type="button" class="demo-copy-btn" data-copy="{{ $u['username'] }}"
                            title="{{ __('messages.lbl_copy') }}">
                            <iconify-icon icon="mdi:content-copy"></iconify-icon>
                        </button>
                    </div>

                    <div class="demo-cred-row">
                        <span class="demo-cred-label">{{ __('messages.lbl_password') }}</span>
                        <code class="demo-cred-value">{{ $u['password'] }}</code>
                        <button type="button" class="demo-copy-btn" data-copy="{{ $u['password'] }}"
                            title="{{ __('messages.lbl_copy') }}">
                            <iconify-icon icon="mdi:content-copy"></iconify-icon>
                        </button>
                    </div>

                    <button type="button" class="demo-fill-btn" data-username="{{ $u['username'] }}"
                        data-password="{{ $u['password'] }}">
                        {{ __('messages.lbl_autofill') }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
    <script>
        $(document).ready(function() {
            const captchaUrl = "{{ route('web.login.captcha') }}";

            function renderCaptchaCanvas(code) {
                const cd = code.split('').join(' ');
                $('#captcha-display').empty().append(
                    '<canvas id="CapCode" class="capcode" width="300" height="80"></canvas>');
                const c = document.getElementById('CapCode');
                if (!c) return;
                const ctx = c.getContext('2d');
                ctx.fillStyle = '#CD7B28';
                ctx.fillRect(0, 0, c.width, c.height);
                ctx.font = '46px Roboto Slab';
                ctx.fillStyle = '#fff';
                ctx.textAlign = 'center';
                ctx.setTransform(1, -0.12, 0, 1, 0, 15);
                ctx.fillText(cd, c.width / 2, 55);
                $('#UserCaptchaCode').val('');
            }
            renderCaptchaCanvas("{{ $captchaCode ?? '' }}");

            $('#refresh-captcha-btn').click(function() {
                $.get(captchaUrl, function(res) {
                    if (res.success) renderCaptchaCanvas(res.captchaCode);
                });
            });

            $('#UserCaptchaCode').on('input', function() {
                if (this.value.length > 6) this.value = this.value.substring(0, 6);
            });

            $("#loginForm").validate({
                rules: {
                    login: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    captcha_code: {
                        required: true
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "password" || element.attr("name") ===
                        "captcha_code") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else {
                        // Default placement
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    let $btn = $(form).find('button[type="submit"]');
                    $btn.prop('disabled', true).text('{{ __('messages.lbl_signing_in') }}');

                    $.ajax({
                        url: "{{ route('web.login.authenticate') }}",
                        type: "POST",
                        data: $(form).serialize(),
                        success: function(res) {
                            if (res.status) {
                                window.location.href = res.redirect;
                                return;
                            }
                            showToastMessage('error', res.message);
                            if (res.refresh_captcha) renderCaptchaCanvas(res.captchaCode);
                            $btn.prop('disabled', false).text(
                                '{{ __('messages.lbl_login') }}');
                        },
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                showToastMessage('error', xhr.responseJSON?.message ||
                                    'Your session has expired. Please refresh the page and try again.'
                                );
                                setTimeout(() => window.location.reload(), 1200);
                                return;
                            }
                            showToastMessage('error',
                                '{{ __('messages.msg_unexpected_error_occured') }}');
                            $btn.prop('disabled', false).text(
                                '{{ __('messages.lbl_login') }}');
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endpush

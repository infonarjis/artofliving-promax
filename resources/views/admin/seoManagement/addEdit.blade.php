@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')

    <div class="container-xxl flex-grow-1 container-p-y">

        @include('admin.message')
        
        @if (_getConstant('AI_MODE') == 'Enabled')
            <div class="row mb-3">
                <div class="col-xl">
                    <div class="card"
                        style="border:1px solid #e0d7ff; background:linear-gradient(135deg,#f8f5ff 0%,#f0f4ff 100%);">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <p class="mb-0 fw-semibold" style="color:#5c3dc4; font-size:14px;">&#10022; AI SEO
                                            Generator</p>
                                        <p class="mb-0 text-muted" style="font-size:12px;">Enter your page topic and let AI fill
                                            all SEO fields instantly</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2" style="flex:1; max-width:520px; min-width:240px;">
                                    <input type="text" id="ai_page_topic" class="form-control"
                                        placeholder="e.g. Advertise wedding business on matrimony site"
                                        style="font-size:13px;" />
                                    <button type="button" id="ai_generate_btn"
                                        class="btn btn-primary d-flex align-items-center gap-1 text-nowrap"
                                        style="background:#5c3dc4; border-color:#5c3dc4; font-size:13px; padding:7px 16px;">
                                        <span id="ai_btn_text">&#10022; Generate SEO</span>
                                    </button>
                                </div>
                            </div>
                            <div id="ai_status_msg" class="mt-2" style="font-size:12px; display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-6">
                                <?php echo $fromHtml; ?>
                            </div>
                            <input type="hidden" name="lang_id" id="lang_id" value="">
                            <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                                    id="{{ $formSubmitBtnId }}">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        var AI_SEO_AJAX_URL = "{{ route('admin.seoManagement.generateSeo') }}";
        var AI_SEO_CSRF = "{{ csrf_token() }}";
    </script>

    @verbatim
        <script>
            (function() {

                var FIELD_MAP = {
                    seo_title: '[name="seo_title"], #seo_title',
                    seo_description: '[name="seo_description"], #seo_description',
                    seo_keywords: '[name="seo_keywords"], #seo_keywords',
                    og_title: '[name="og_title"], #og_title',
                    og_description: '[name="og_description"], #og_description',
                    meta_robots: '[name="meta_robots"], #meta_robots',
                    schema_json: '[name="schema_json"], #schema_json'
                };

                function setField(selector, value) {
                    var el = document.querySelector(selector);
                    if (!el || !value) return;
                    el.value = value;
                    el.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                    el.style.transition = 'background 0.4s';
                    el.style.background = '#f0ebff';
                    // setTimeout(function() {
                    //     el.style.background = '';
                    // }, 1200);
                }

                function showStatus(msg, type) {
                    type = type || 'info';
                    var el = document.getElementById('ai_status_msg');
                    var colors = {
                        info: '#5c3dc4',
                        success: '#1a7f4b',
                        error: '#c0392b'
                    };
                    el.style.color = colors[type] || colors.info;
                    el.textContent = msg;
                    el.style.display = 'block';
                    if (type === 'success') {
                        setTimeout(function() {
                            el.style.display = 'none';
                        }, 3000);
                    }
                }

                function setLoading(loading) {
                    var btn = document.getElementById('ai_generate_btn');
                    var text = document.getElementById('ai_btn_text');
                    btn.disabled = loading;
                    text.textContent = loading ? 'Generating...' : '\u2736 Generate SEO';
                }

                function generateSEO() {
                    var topic = document.getElementById('ai_page_topic').value.trim();
                    if (!topic) {
                        showStatus('Please enter a page topic first.', 'error');
                        document.getElementById('ai_page_topic').focus();
                        return;
                    }

                    setLoading(true);
                    showStatus('Generating SEO data...', 'info');

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', window.AI_SEO_AJAX_URL, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.setRequestHeader('X-CSRF-TOKEN', window.AI_SEO_CSRF);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState !== 4) return;

                        setLoading(false);

                        try {
                            var res = JSON.parse(xhr.responseText);

                            if (xhr.status === 200 && res.success) {
                                var seo = res.data;
                                setField(FIELD_MAP.seo_title, seo.seo_title);
                                setField(FIELD_MAP.seo_description, seo.seo_description);
                                setField(FIELD_MAP.seo_keywords, seo.seo_keywords);
                                setField(FIELD_MAP.og_title, seo.og_title);
                                setField(FIELD_MAP.og_description, seo.og_description);
                                setField(FIELD_MAP.meta_robots, seo.meta_robots);
                                setField(FIELD_MAP.schema_json, seo.schema_json);
                                showStatus('SEO fields filled successfully! Review and adjust before saving.',
                                    'success');
                            } else {
                                // Show the message returned from the controller (covers 422, 500, etc.)
                                showStatus(res.message || 'Failed to generate SEO data.', 'error');
                            }
                        } catch (e) {
                            // JSON parse failed — show raw HTTP status
                            showStatus('Unexpected server error (HTTP ' + xhr.status + '). Please try again.', 'error');
                        }
                    };

                    xhr.send('topic=' + encodeURIComponent(topic));
                }

                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('ai_generate_btn').addEventListener('click', generateSEO);
                    document.getElementById('ai_page_topic').addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            generateSEO();
                        }
                    });
                });

            }());
        </script>
    @endverbatim

@endsection

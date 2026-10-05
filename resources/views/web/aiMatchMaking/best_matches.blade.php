@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="seaech-result-main">

                            <div class="ai-page-header mb-4">
                                <span
                                    class="ai-section-heading primary-color-n text-uppercase">{{ __('messages.lbl_predictive_matches') }}</span>
                                <h1 class="fts-28 fw-7 white-color-n mb-2">{{ __('messages.lbl_best_matches_today') }}</h1>
                                <p class="fts-15 fw-4 white-color70-n">
                                    @if ($generatedAt)
                                        {{ __('messages.lbl_refreshed_overnight_ranked_by_explicit_preference_and_what_you_ve_engaged_with_recently') }}
                                        <span
                                            style="font-family: var(--font-main); font-size: 12px;">{{ __('messages.lbl_last_updated') }}
                                            {{ _displayDate($generatedAt, 'j F, Y h:i A') }}.</span>
                                    @else
                                        {{ __('messages.lbl_we_re_still_learning_your_preferences_this_list_is_a_live_first_pass_and_will_sharpen_after_tonights_refresh') }}
                                    @endif
                                </p>
                            </div>

                            @if ($bestMatches->isEmpty())
                                <div class="row g-3 bg-white px-4">
                                    <div class="col-lg-8 col-xxl-5 mx-auto">
                                        <div class="p-3 p-lg-4 text-center">
                                            <span
                                                class="ai-section-heading fts-24 mb-1">{{ __('messages.lbl_no_suggestions_yet') }}</span>
                                            <p class="section-subtitle mb-0 text-muted">
                                                {{ __('messages.lbl_browse_a_few_profiles_or_use_the_full_search_your_best_matches_will_appear_here_once') }}
                                            </p>
                                            <a href="{{ route('web.aiMatchMaking.index') }}"
                                                class="error-btn-secondary text-decoration-none mt-4">
                                                <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon>
                                                {{ __('messages.lbl_search_ai_matches') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="pro-matches-list">
                                    <div class="row g-3">
                                        @forelse($bestMatches as $data)
                                            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.memberCardLayouts.index',
                                                ['result' => $data]
                                            )
                                        @empty
                                            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound',
                                                [
                                                    'message' => __('messages.lbl_no_matches_found'),
                                                ]
                                            )
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 ps-lg-2 mt-4">
                        @include('web.dashboard.memberRightSideBar')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- Success story start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page">
            <div class="container">
                <div class="row px-1">
                    <div class="col-12">
                        <div class="common-tabs-design">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link fts-14 fw-5 active" id="pills-stories-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-featured-stories" type="button" role="tab"
                                        aria-controls="pills-featured-stories" aria-selected="true">
                                        <iconify-icon icon="hugeicons:heart-check" width="24" class="me-1"
                                            height="24"></iconify-icon>
                                        {{ __('messages.lbl_featured_success_stories') }}
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fts-14 fw-5" id="pills-video-stories-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-video-stories" type="button" role="tab"
                                        aria-controls="pills-video-stories" aria-selected="false"><iconify-icon
                                            icon="hugeicons:video-01" width="24" class="me-1"
                                            height="24"></iconify-icon></iconify-icon>
                                        {{ __('messages.lbl_video_stories') }}
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fts-14 fw-5" id="pills-add-story-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-idsearch" type="button" role="tab"
                                        aria-controls="pills-idsearch" aria-selected="false"><iconify-icon
                                            icon="hugeicons:heart-add" width="24" class="me-1"
                                            height="24"></iconify-icon>
                                        {{ __('messages.lbl_tell_us_your_story') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-content mt-3 mt-lg-4" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-featured-stories" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <div class="section-wrapper-1">
                            <div class="row g-4">
                                <!-- LEFT COLUMN -->
                                @forelse ($featuredStories as $story)
                                    @php
                                        $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                        if (
                                            !blank($story->wedding_photo) &&
                                            _checkStorageFileExists(
                                                'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                $story->wedding_photo,
                                            )
                                        ) {
                                            $weddingImage =
                                                _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                $story->wedding_photo;
                                        }
                                        $bridegroomName = $story->groomname . ' & ' . $story->bridename;
                                        $storyDesc = Str::limit(strip_tags($story->successmessage), 120);
                                    @endphp
                                    <div class="col-12 col-md-6">
                                        <div class="storie-card">
                                            <h2 class="card-title">{{ $bridegroomName }}</h2>
                                            <div class="title-divider"></div>
                                            <div class="meta">
                                                <span>{{ _displayDate($story->created_at, 'j F, Y') }}</span>
                                            </div>
                                            <a href="{{ route('web.successStory.details', $story->id) }}">
                                                <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}" class="card-img">
                                            </a>
                                            <p class="card-desc text-break">{{ $storyDesc }}</p>
                                            <a href="{{ route('web.successStory.details', $story->id) }}"
                                                class="btn-read-more">{{ __('messages.lbl_read_more') }}</a>
                                        </div>
                                    </div>
                                @empty
                                    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound',
                                        ['message' => __('messages.lbl_no_data_found')]
                                    )
                                @endforelse
                            </div>
                        </div>
                        <div class="mt-0 mt-lg-4">
                            <!-- pagination  -->
                            @if ($featuredStories->hasPages())
                                {{ $featuredStories->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-content mt-3 mt-lg-4" id="pills-tabContent">
                    <div class="tab-pane fade" id="pills-video-stories" role="tabpanel" aria-labelledby="pills-home-tab">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.successStory.videoSection')
                    </div>
                </div>
                <div class="tab-content mt-3 mt-lg-4" id="pills-tabContent">
                    <div class="tab-pane fade" id="pills-idsearch" role="tabpanel"
                        aria-labelledby="pills-add-story-tab">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.successStory.addStory')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll(".vp-wrapper").forEach(wrapper => {
            const poster = wrapper.querySelector(".vp-poster");
            const video = wrapper.querySelector(".vp-video");
            const controls = wrapper.querySelector(".vp-controls");
            const btnPlay = wrapper.querySelector(".btnPlayPause");
            const iconPlay = wrapper.querySelector(".iconPlay");
            const iconPause = wrapper.querySelector(".iconPause");
            const progress = wrapper.querySelector(".vp-progress");
            const timeDisplay = wrapper.querySelector(".vp-time");
            const volume = wrapper.querySelector(".vp-volume");
            const btnMute = wrapper.querySelector(".btnMute");
            const iconVol = wrapper.querySelector(".iconVol");
            const iconMute = wrapper.querySelector(".iconMute");
            const btnFs = wrapper.querySelector(".btnFullscreen");
            const playBtn = wrapper.querySelector(".vp-play-btn");
            const youtube = wrapper.querySelector(".vp-youtube");

            /* ---------------- Start Video ---------------- */
            function startVideo() {
                if (playBtn) playBtn.style.display = "none";
                if (poster) poster.style.display = "none";
                if (video) {
                    video.style.display = "block";
                    video.play();
                }
                if (youtube) {
                    youtube.style.display = "block";
                    youtube.src = youtube.dataset.src;
                }
                if (controls) controls.classList.add("active");
            }
            if (video) {
                video.style.display = "block";
            }
            if (playBtn) {
                playBtn.addEventListener("click", startVideo);
            }
            if (poster) {
                poster.addEventListener("click", startVideo);
            }

            /* ---------------- MP4 Controls ---------------- */
            if (video && btnPlay) {
                btnPlay.addEventListener("click", () => {
                    video.paused ? video.play() : video.pause();
                });
                video.addEventListener("play", updatePlayPause);
                video.addEventListener("pause", updatePlayPause);

                function updatePlayPause() {
                    if (!iconPlay || !iconPause) return;
                    const paused = video.paused;
                    iconPlay.style.display = paused ? "block" : "none";
                    iconPause.style.display = paused ? "none" : "block";
                }

                /* -------- Progress -------- */
                video.addEventListener("timeupdate", () => {
                    if (!video.duration) return;
                    const pct = (video.currentTime / video.duration) * 100;
                    if (progress) progress.value = pct;
                    if (timeDisplay) {
                        timeDisplay.textContent =
                            `${fmt(video.currentTime)} / ${fmt(video.duration)}`;
                    }
                });

                if (progress) {
                    progress.addEventListener("input", () => {
                        video.currentTime = (progress.value / 100) * video.duration;
                    });
                }

                /* -------- Volume -------- */
                if (volume) {
                    volume.addEventListener("input", () => {
                        video.volume = volume.value / 100;
                        video.muted = video.volume === 0;
                        updateVolIcon();
                    });
                }

                if (btnMute) {
                    btnMute.addEventListener("click", () => {
                        video.muted = !video.muted;
                        if (volume) volume.value = video.muted ? 0 : video.volume * 100;
                        updateVolIcon();
                    });
                }

                function updateVolIcon() {
                    if (!iconVol || !iconMute) return;
                    const muted = video.muted || video.volume === 0;
                    iconVol.style.display = muted ? "none" : "block";
                    iconMute.style.display = muted ? "block" : "none";
                }

                /* -------- Fullscreen -------- */
                if (btnFs) {
                    btnFs.addEventListener("click", () => {
                        if (!document.fullscreenElement) {
                            wrapper.requestFullscreen();
                        } else {
                            document.exitFullscreen();
                        }
                    });
                }

                /* -------- Time Format -------- */
                function fmt(s) {
                    const m = Math.floor(s / 60);
                    const sec = Math.floor(s % 60).toString().padStart(2, "0");
                    return `${m}:${sec}`;
                }

                /* -------- Video End -------- */
                video.addEventListener("ended", () => {
                    playBtn.style.removeProperty("display");
                    if (controls) controls.classList.remove("active");
                    if (poster) poster.style.display = "block";
                    video.currentTime = 0;
                    if (progress) progress.value = 0;
                    if (timeDisplay) timeDisplay.textContent = "0:00 / 0:00";
                });
            }
        });
    </script>
@endpush

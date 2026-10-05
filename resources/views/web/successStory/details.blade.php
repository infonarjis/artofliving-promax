@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- Success story details start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-9 col-lg-8 mx-auto">
                        <div class="success-stories-details">
                            @if ($successStory->story_type == 'Photo Story')
                                @php
                                    $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                    if (
                                        !blank($successStory->wedding_photo) &&
                                        _checkStorageFileExists(
                                            'upload_path.SUCCESS_STORY_IMAGE_URL',
                                            $successStory->wedding_photo,
                                        )
                                    ) {
                                        $weddingImage =
                                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                            $successStory->wedding_photo;
                                    }
                                    $bridegroomName = $successStory->groomname . ' & ' . $successStory->bridename;
                                @endphp
                                <div class="story-imagecoupl">
                                    <a href="">
                                        <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}" class="big_storyprofils">
                                    </a>
                                </div>
                                <div class="stories-details-box p-3">
                                    <div class="fw-6 white-color-n fts-28">{{ $bridegroomName }}</div>
                                    <h5 class="fts-13 fw-4 white-color70-n mt-2 d-flex align-items-center flex-wrap">
                                        {{ _displayDate($successStory->marriagedate, 'j F, Y') }} </h5>
                                    <hr class="border-hr-line my-3">
                                    <div class="fts-14 white-color-n fw-4 mt-2 break-word">{!! $successStory->successmessage !!}</div>
                                </div>
                            @else
                                @php
                                    $bridegroomName = $successStory->groomname . ' & ' . $successStory->bridename;
                                    $videoStoryDesc = Str::limit(strip_tags($successStory->successmessage), 120);

                                    $videoFileUrl = '';
                                    $youtubeId = '';
                                    $posterUrl = '';
                                    if ($successStory->video_type == 'youtube') {
                                        $youtubeUrl = $successStory->video_link; // or your actual column
                                        preg_match(
                                            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/',
                                            $youtubeUrl,
                                            $matches,
                                        );

                                        $youtubeId = $matches[1] ?? '';
                                        $thumbnail = $youtubeId
                                            ? 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg'
                                            : '';
                                    } else {
                                        if (
                                            !blank($successStory->wedding_video_file) &&
                                            _checkStorageFileExists(
                                                'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                $successStory->wedding_video_file,
                                            )
                                        ) {
                                            $videoFileUrl =
                                                _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                $successStory->wedding_video_file;
                                        }

                                        if (
                                            !blank($successStory->wedding_video_thumbnail) &&
                                            _checkStorageFileExists(
                                                'upload_path.SUCCESS_STORY_IMAGE_URL',
                                                $successStory->wedding_video_thumbnail,
                                            )
                                        ) {
                                            $posterUrl =
                                                _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') .
                                                $successStory->wedding_video_thumbnail;
                                        }
                                    }
                                @endphp
                                <div class="storie-card">
                                    <h2 class="card-title">{{ $bridegroomName }}</h2>
                                    <div class="title-divider"></div>
                                    <div class="meta">
                                        <span>{{ _displayDate($successStory->created_at, 'j F, Y') }}</span>
                                    </div>
                                    <div class="vp-wrapper">
                                        @if ($successStory->video_type == 'youtube')
                                            <div class="vp-poster" style="background-image:url('{{ $thumbnail }}')">
                                                <div class="vp-play-btn">▶</div>
                                            </div>

                                            <iframe class="vp-youtube" src=""
                                                data-src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1"
                                                frameborder="0" allowfullscreen
                                                style="display:none;width:100%;height:auto; aspect-ratio: 16/9; border-radius:20px;">
                                            </iframe>
                                        @else
                                            <video class="vp-video" poster="{{ $posterUrl }}" preload="metadata"
                                                style="display:none;width:100%;border-radius:20px;">
                                                <source src="{{ $videoFileUrl }}" type="video/mp4">
                                            </video>
                                            <!-- Play Button -->
                                            <div class="vp-play-btn" id="vpPlayBtn">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                            <!-- Controls -->
                                            <div class="vp-controls" id="vpControls">
                                                <button class="ctrl-btn btnPlayPause">
                                                    <svg class="iconPlay" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z" />
                                                    </svg>
                                                    <svg class="iconPause" viewBox="0 0 24 24" style="display:none">
                                                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                                                    </svg>
                                                </button>
                                                <div class="vp-progress-wrap">
                                                    <input type="range" class="vp-progress" value="0"
                                                        min="0" max="100">
                                                </div>
                                                <span class="vp-time">0:00 / 0:00</span>
                                                <button class="ctrl-btn btnMute">
                                                    <svg class="iconVol" viewBox="0 0 24 24"></svg>
                                                    <svg class="iconMute" viewBox="0 0 24 24"
                                                        style="display:none"></svg>
                                                </button>
                                                <input type="range" class="vp-volume" value="100" min="0"
                                                    max="100">
                                                <button class="ctrl-btn btnFullscreen">
                                                    <svg viewBox="0 0 24 24"></svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="card-desc text-break mt-3">{!! $successStory->successmessage !!}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if ($recentStorys->isNotEmpty())
                <div class="success-stories-slider mt-3 mt-lg-4 px-2">
                    @foreach ($recentStorys as $story)
                        @php
                            $weddingImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                            if (
                                !blank($story->wedding_photo) &&
                                _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $story->wedding_photo)
                            ) {
                                $weddingImage =
                                    _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $story->wedding_photo;
                            }
                            $bridegroomName = $story->groomname . ' & ' . $story->bridename;
                            $storyDesc = Str::limit(strip_tags($story->successmessage), 120);
                        @endphp
                        <div class="single-success-stories mx-1 d-lg-flex gap-3">
                            <div class="single-success-couple">
                                <a href="{{ route('web.successStory.details', $story->id) }}">
                                    <img src="{{ $weddingImage }}" alt="{{ $bridegroomName }}" class="small-imgs">
                                </a>
                            </div>
                            <div class="stories-bottoms-box mt-3">
                                <h4 class="fts-18 fw-6 white-color-n">{{ $bridegroomName }}</h4>
                                <h5 class="fts-13 fw-4 white-color70-n mt-2 d-flex align-items-center flex-wrap">
                                    {{ _displayDate($story->marriagedate, 'j F, Y') }}</h5>
                                <p class="fts-14 fw-4 white-color-n mt-2 text-break">{{ $storyDesc }}</p>
                                <a href="{{ route('web.successStory.details', $story->id) }}"
                                    class="more-stories fts-14 mt-2">Read More</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@if ($successStory->story_type == 'Video Story')
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
@endif
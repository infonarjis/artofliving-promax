<div class="section-wrapper-1">
    <div class="row g-4">
        @forelse ($videoStories as $video_story)
            @php
                $bridegroomName = $video_story->groomname . ' & ' . $video_story->bridename;
                $videoStoryDesc = Str::limit(strip_tags($video_story->successmessage), 120);

                $videoFileUrl = '';
                $youtubeId = '';
                $posterUrl = '';
                if ($video_story->video_type == 'youtube') {
                    $youtubeUrl = $video_story->video_link; // or your actual column
                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $youtubeUrl, $matches);

                    $youtubeId = $matches[1] ?? '';
                    $thumbnail = $youtubeId ? 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg' : '';
                } else {
                    if (
                        !blank($video_story->wedding_video_file) &&
                        _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $video_story->wedding_video_file)
                    ) {
                        $videoFileUrl =
                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $video_story->wedding_video_file;
                    }

                    if (
                        !blank($video_story->wedding_video_thumbnail) &&
                        _checkStorageFileExists(
                            'upload_path.SUCCESS_STORY_IMAGE_URL',
                            $video_story->wedding_video_thumbnail,
                        )
                    ) {
                        $posterUrl =
                            _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $video_story->wedding_video_thumbnail;
                    }
                }
            @endphp
            <div class="col-12 col-md-6">
                <div class="storie-card">
                    <h2 class="card-title">{{ $bridegroomName }}</h2>
                    <div class="title-divider"></div>
                    <div class="meta">
                        <span>{{ _displayDate($video_story->created_at, 'j F, Y') }}</span>
                    </div>
                    <div class="vp-wrapper">
                        @if ($video_story->video_type == 'youtube')
                            <div class="vp-poster" style="background-image:url('{{ $thumbnail }}')">
                                <div class="vp-play-btn">▶</div>
                            </div>

                            <iframe class="vp-youtube" src=""
                                data-src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1" frameborder="0"
                                allowfullscreen
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
                                    <input type="range" class="vp-progress" value="0" min="0"
                                        max="100">
                                </div>
                                <span class="vp-time">0:00 / 0:00</span>
                                <button class="ctrl-btn btnMute">
                                    <svg class="iconVol" viewBox="0 0 24 24"></svg>
                                    <svg class="iconMute" viewBox="0 0 24 24" style="display:none"></svg>
                                </button>
                                <input type="range" class="vp-volume" value="100" min="0" max="100">
                                <button class="ctrl-btn btnFullscreen">
                                    <svg viewBox="0 0 24 24"></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <p class="card-desc text-break mt-3">{{ $videoStoryDesc }}</p>
                    <a href="{{ route('web.successStory.details', $video_story->id) }}"
                        class="btn-read-more">{{ __('messages.lbl_read_more') }}</a>
                </div>
            </div>
        @empty
            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.noDataFound', [
                'message' => __('messages.lbl_no_data_found'),
            ])
        @endforelse
    </div>
</div>
<div class="mt-0 mt-lg-4">
    <!-- pagination  -->
    @if ($videoStories->hasPages())
        {{ $videoStories->links(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination') }}
    @endif
</div>

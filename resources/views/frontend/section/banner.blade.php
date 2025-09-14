@php
    $youtubeLink = $event->youtube_link ?? null;
    $embedUrl = null;

    if ($youtubeLink) {
        // Extract YouTube video ID from both youtube.com and youtu.be links
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]+)/', $youtubeLink, $matches)) {
            $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
        }
    }
@endphp

@if(!empty($event) && !empty($event->photo))
    <section class="banner position-relative" style="background-image: url({{ $event->photo->file_path }})">
        <div class="container text-center">
            @if($embedUrl)
                <!-- Play button triggers modal -->
                <button class="position-absolute top-50 start-50 translate-middle rounded-circle overflow-hidden"
                        data-bs-toggle="modal" data-bs-target="#youtubeModal">
                    <img class="banner-play-btn" src="{{ asset('frontend/images/video-circle.png') }}" alt="Play Video">
                </button>
            @endif
        </div>
    </section>
@else
    <section class="banner position-relative" style="background-image: url({{ asset('frontend/images/banner.png') }})">
        <div class="container text-center">
            @if($embedUrl)
                <button class="position-absolute top-50 start-50 translate-middle rounded-circle overflow-hidden"
                        data-bs-toggle="modal" data-bs-target="#youtubeModal">
                    <img class="banner-play-btn" src="{{ asset('frontend/images/video-circle.png') }}" alt="Play Video">
                </button>
            @endif
        </div>
    </section>
@endif

<!-- Bootstrap Modal -->
<!-- Bootstrap Fullscreen Modal -->
@if($embedUrl)
<div class="modal fade" id="youtubeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content bg-dark">
      <div class="modal-body p-0 position-relative">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                data-bs-dismiss="modal" aria-label="Close"></button>
        
        <iframe id="youtubeIframe" width="100%" height="100%"
                src="{{ $embedUrl }}?autoplay=1"
                title="YouTube video"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
        </iframe>
      </div>
    </div>
  </div>
</div>
@endif


<!-- banner -->  
@if(!empty($event) && !empty($event->photo))
    <section class="banner position-relative" style="background-image: url({{$event->photo->file_path}})">
        <div class="container text-center">
            @if(!empty($event->youtube_link))
                <a href="{{ $event->youtube_link }}" target="_blank" class="position-absolute top-50 start-50 translate-middle rounded-circle overflow-hidden">
                    <img class="banner-play-btn" src="{{ asset('frontend/images/video-circle.png') }}" alt="Play Video">
                </a>
            @endif
        </div>
    </section>
@else
    <section class="banner position-relative" style="background-image: url({{ asset('frontend/images/banner.png') }})">
        <div class="container text-center">
            @if(!empty($event->youtube_link))
                <a href="{{ $event->youtube_link }}" target="_blank" class="position-absolute top-50 start-50 translate-middle rounded-circle overflow-hidden">
                    <img class="banner-play-btn" src="{{ asset('frontend/images/video-circle.png') }}" alt="Play Video">
                </a>
            @endif
        </div>
    </section>
@endif
<!-- banner end -->

    <!-- speakers -->
    <section class="speakers">
        <div class="container">
            <div class="d-block d-xl-flex align-items-center gap-4 gap-xl-0">
                <div class="d-block col-xl-4 pe-xl-5 ps-0">
                    <div class="mb-4 mb-xl-0">
                        <span class="small-heading-white">Speakers</span>
                        <h2 class="h2-white mb-4">Our Amazing & learned event Speakers</h2>
                        <span class="small-heading-white">
                            World’s most influential media, entertainment & technology show inspirational speakers
                            including game changing ideas.</span>
                        </span>
                    </div>
                    
                </div>
                
                <div class="col-xl-8 p-0">
                    <div class="swiper speakers-swiper">

                        <div class="swiper-wrapper">
                            @if(!empty($speakers))
                            @foreach($speakers as $speaker)
                            
                            <div class="swiper-slide">
                                <a href="{{ route('speaker', $speaker->slug) }}" class="text-decoration-none">
                                <div class="swiper-img-box">
                                    @if(!empty($speaker->photo))
                                     <img src="{{ $speaker->photo->file_path}}" alt="">
                                    @else
                                     <img src="{{asset('frontend/images/speaker-1.png')}}" alt="">
                                    @endif
                                </div>
                            </a>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">{{$speaker->full_name ? truncateString($speaker->full_name, 18) : ''}}</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                           

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- speakers end -->

    
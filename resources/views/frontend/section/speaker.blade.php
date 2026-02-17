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
                    
                    @if(count($speakers) > 10)
                    <div class="d-flex justify-content-left">
                      <a href="{{ route('speaker-index') }}" class="btn btn-outline-light px-4 py-2 fw-semibold btn-long">
                        View More
                      </a>
                    </div>
                    @endif

                </div>
                
                <div class="col-xl-8 p-0 mt-3 mt-xl-0">
                    <div class="swiper speakers-swiper">

                        <div class="swiper-wrapper">
                            @if(isset($speakers) && count($speakers) > 0)
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
                                    <span class="speakers-title">{{$speaker->company ?? ''}}</span>
                                     <small>{{$speaker->designation ?? ''}}</small>

                                </div>


                            </div>
                            @endforeach
                            @else
                            <div>
                                <p class="text-white">No speakers found.</p>
                            </div>
                            @endif
                        </div>
                           

                    </div>

                </div>


            </div>
        </div>
    </section>
    <!-- speakers end -->

    
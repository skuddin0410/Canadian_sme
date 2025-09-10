    <!-- sponsors -->
    <section class="sponsors">
        <div class="container">
            <span class="small-heading-white">Sponsors</span>
            <div class="row">
                <div class="col-lg-6">
                    <div>
                        <h2 class="h2-white mb-4">Meet The Proud Sponsors Supporting Us</h2>
                    </div>
                </div>
                <div class="col-lg-3"></div>
                <div class="col-lg-3">
                    <!-- Navigation Arrows -->
                    <div class="sponsors-nav d-flex gap-3 align-items-center justify-content-end">
                        <button class="sponsors-prev white-circle">
                            <img src="{{asset('frontend/images/arrow-left.png')}}" alt="">
                        </button>
                        <button class="sponsors-next white-circle">
                            <img src="{{asset('frontend/images/arrow-right.png')}}" alt="">
                        </button>
                    </div>
                </div>
                <div class="mt-2 mt-sm-4 mt-lg-5">
                    <div class="swiper sponsors-swiper">
                        <div class="swiper-wrapper">
                            @if(!empty($sponsors))
                             @foreach($sponsors as $sponsor)

                            <div class="swiper-slide">
                                 <a href="{{ route('sponsor', $sponsor->id) }}" class="text-decoration-none">
                                <div class="swiper-img-box">
                                     @if($sponsor->logo)
                                      <img src="{{$sponsor->logo->file_path}}" alt="">
                                     @else
                                       <img src="{{asset('frontend/images/sponsor-1.png')}}" alt="">
                                     @endif
                                </div>
                                </a>
                                <div class="swiper-img-text">
                                    <span class="sponsors-name">{{$sponsor->name ?? ''}}</span>
                                    <span class="sponsors-title gold">Sponsor</span>
                                </div>
                            </div>
                             @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <a class="heroBtn bg-transparent view-more" href="{{route('sponsor-index')}}">View More </a>
                </div>
</div>
            </div>
    </section>
    <!-- sponsors end -->
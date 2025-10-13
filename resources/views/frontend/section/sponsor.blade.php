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
                                 <a href="{{ route('sponsor', $sponsor->slug) }}" class="text-decoration-none">
                                <div class="swiper-img-box">
                                     @if($sponsor->logo)
                                      <img src="{{$sponsor->logo->file_path}}" alt="">
                                     @else
                                       <img src="{{asset('frontend/images/sponsor-1.png')}}" alt="">
                                     @endif
                                </div>
                                </a>
                                <div class="swiper-img-text">

                                 

                                    <span class="sponsors-name">{{$sponsor->name ? truncateString($sponsor->name, 15) : ''}}</span>
                                    <span class="sponsors-title {{typeColor($sponsor->type)}}" style="{{'background-color:'.typeColor($sponsor->type)}}">{{ $sponsor?->category?->name}}</span>

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
    <style>
        .sponsors-title {
    display: inline-block;
    padding: 0.35rem 1rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    color: #fff;
    border-radius: 4px; /* small radius for rectangle look */
}

/* Color themes */
.sponsors-title.gold {
    background: linear-gradient(45deg, #FFD700, #FFC300);
    color: #5a3e00;
}

.sponsors-title.silver {
    background: linear-gradient(45deg, #C0C0C0, #A8A8A8);
    color: #2c2c2c;
}

.sponsors-title.bronze {
    background: linear-gradient(45deg, #CD7F32, #B87333);
    color: #fff;
}

.sponsors-title.platinum {
    background: linear-gradient(45deg, #E5E4E2, #D4D4D4);
    color: #2C3E50;
}

.sponsors-title.majlislounge {
    background: linear-gradient(45deg, #8B4513, #5A2E0D);
    color: #fff;
}

.sponsors-title.general {
    background: #6c757d;
    color: #fff;
}

</style>
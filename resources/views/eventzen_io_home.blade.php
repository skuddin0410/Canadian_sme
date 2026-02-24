<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heroBanner->title ?? 'Home' }}</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="/frontend/css/style_new.css">
</head>

<body class="min-h-screen flex flex-col bg-gray-50">

    @include('partials_new.header')

     <!-- ======== HERO BANNER ========  -->
    @if($heroBanner)
    <section class="banner section position-relative">
        <div class="container">
            <div class="row align-items-center gap-3 gap-md-0">
                <div class="col-md-6">
                    <div class="banner-text">
                        @if($heroBanner->subtitle)
                            <h6>{{ $heroBanner->subtitle }}</h6>
                        @endif
                        <h1>{!! nl2br(e($heroBanner->title)) !!}</h1>
                        @if($heroBanner->description)
                            <div>{!! $heroBanner->description !!}</div>
                        @endif
                        @if($heroBanner->button_link)
                            <button onclick="window.location.href='{{ $heroBanner->button_link }}'" class="heroBtn btn-long">Request a Demo</button>
                        @endif
                    </div>
                </div>
                @if($heroBanner->mainImage)
                <div class="col-md-6 d-none d-md-block">
                    <div>
                        <img class="img-fluid banner-right-img" src="{{ $heroBanner->mainImage->file_path }}" alt="{{ $heroBanner->title }}">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif
    <!-- banner end -->

    <!-- ======== LOGOS SWIPER ======== -->
    @if($logos->count() > 0)
    <section class="banner-bottom-swiper swiper">
        <div class="swiper-wrapper">
            @foreach($logos as $logo)
                @if($logo->logoImage)
                    <div class="swiper-slide">
                        <img src="{{ $logo->logoImage->file_path }}" alt="{{ $logo->title }}">
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif
    <!-- logos end -->

    <!-- ======== ABOUT US ======== -->
    @if($about)
    <section class="about-us section">
        <div class="container">
            <div class="row align-items-center about-us-row">
                <div class="col-xl-6">
                    <div class="about-us-img-wrapper">
                        @if($about->bgBanner)
                        <div class="about-us-img-box about-us-img-box-1">
                            <img src="{{ $about->bannerImage->file_path }}" alt="">
                        </div>
                        @endif
                        @if($about->bannerImage)
                        <div class="about-us-img-box about-us-img-box-2">
                            <img src="{{ $about->frontImage->file_path }}" alt="">
                        </div>
                        @endif
                        @if($about->frontImage)
                        <div class="about-us-img-box about-us-img-box-3">
                            <img src="{{ $about->bgBanner->file_path }}" alt="">
                        </div>
                        @endif
                        @if($about->bannerButtonImage)
                        <button onclick="window.location.href='{{ $about->banner_button_link }}'" class="about-us-img-box about-us-img-box-4">
                            <img src="{{ $about->bannerButtonImage->file_path }}" alt="">
                        </button>
                        @endif
                    </div>
                </div>
                <div class="col-xl-6">
                    <div>
                        <div class="global-heading">
                            <h6>About Us</h6>
                            @if($about->heading)
                                <h2>{{ $about->heading }}</h2>
                            @endif
                            @if($about->sub_heading)
                                <p>{{ $about->sub_heading }}</p>
                            @endif
                            @if($about->description)
                                <div>{!! $about->description !!}</div>
                            @endif
                        </div>
                        @if($about->desc_points)
                        <div class="ul-wrapper">
                            <ul class="ul">
                                @foreach(array_filter(array_map('trim', explode("\n", $about->desc_points))) as $point)
                                    <li><img src="./images/blue-check.png" alt="">{{ $point }}</li>
                                @endforeach
                            </ul>
                            @if($about->exp_year || $about->exp_text)
                            <div class="years-of-exp">
                                @if($about->expImage)
                                    <img src="{{ $about->expImage->file_path }}" alt="">
                                @endif
                                @if($about->exp_year)
                                    <h3>{{ $about->exp_year }}</h3>
                                @endif
                                @if($about->exp_text)
                                    <p>{{ $about->exp_text }}</p>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                        @if($about->button_text || $about->button_link)
                        <button onclick="window.location.href='{{ $about->button_link ?? '#' }}'" class="heroBtn btn-long">
                            {{ $about->button_text ?? 'More About Us' }}
                            <img class="ms-2" src="./images/right-arrow.png" alt="">
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- about-us end -->

    <!-- ======== SPEAKERS (static - no CMS yet) ======== -->
    <section class="speakers">
        <div class="container">
            <div class="d-block d-xl-flex align-items-center gap-4 gap-xl-0">
                <div class="d-block col-xl-4 pe-xl-5 ps-0">
                    <div class="global-heading mb-4 mb-xl-0">
                        <h6 class="text-white">Speakers</h6>
                        <h2 class="text-white">Our Amazing &amp; learned event Speakers</h2>
                        <p class="text-white">
                            World's most influential media, entertainment &amp; technology show inspirational speakers
                            including game changing ideas.
                        </p>
                    </div>
                </div>
                <div class="col-xl-8 p-0">
                    <div class="swiper speakers-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="swiper-img-box"><img src="./images/speaker-1.png" alt=""></div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box"><img src="./images/speaker-2.png" alt=""></div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box"><img src="./images/speaker-1.png" alt=""></div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box"><img src="./images/speaker-2.png" alt=""></div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- speakers end -->

    <!-- ======== UNFORGETTABLE EVENTS (Event Types) ======== -->
    @if($eventBanner || $eventTypes->count() > 0)
    <section class="unforgettable-event section">
        <div class="container">
            @if($eventBanner)
            <div class="global-heading text-center">
                @if($eventBanner->heading)
                    <h2>{{ $eventBanner->heading }}</h2>
                @endif
                @if($eventBanner->sub_heading)
                    <p>{{ $eventBanner->sub_heading }}</p>
                @endif
            </div>
            @endif
            @if($eventTypes->count() > 0)
            <div class="unforgettable-event-row">
                @foreach($eventTypes as $type)
                <div class="unforgettable-event-col">
                    @if($type->typeImage)
                    <div class="unforgettable-event-col-imgbox">
                        <img src="{{ $type->typeImage->file_path }}" alt="{{ $type->heading }}">
                    </div>
                    @endif
                    <h3>{{ $type->heading }}</h3>
                    @if($type->text)
                    <ul class="ul">
                        @foreach(array_filter(array_map('trim', explode("\n", $type->text))) as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif
    <!-- Unforgettable Events end -->

    <!-- ======== US APART ======== -->
    @if($apartText || $apartCards->count() > 0)
    <section class="apart section">
        <div class="container">
            @if($apartText)
            <div class="global-heading text-center">
                @if($apartText->heading)
                    <h2>{{ $apartText->heading }}</h2>
                @endif
                @if($apartText->sub_heading)
                    <p>{{ $apartText->sub_heading }}</p>
                @endif
            </div>
            @endif
            @if($apartCards->count() > 0)
            <div class="apart-row">
                @foreach($apartCards as $card)
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>{{ $card->heading }}</h3>
                        @if($card->description)
                            <div>{!! $card->description !!}</div>
                        @endif
                        @if($card->text)
                        <ul class="ul">
                            @foreach(array_filter(array_map('trim', explode("\n", $card->text))) as $point)
                                <li>
                                    <span><img src="./images/green-tick.png" alt=""></span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @if($card->cardIcon)
                    <div>
                        <img class="apart-col-right-img" src="{{ $card->cardIcon->file_path }}" alt="{{ $card->heading }}">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif
    <!-- apart end -->

    <!-- ======== DEMO BOOKING ======== -->
    <section class="event-booking section">
        <div class="container">
            <div class="row align-items-center event-booking-row">
                <div class="col-lg-6">
                    @if($demoText)
                    <div class="global-heading">
                        @if($demoText->heading)
                            <h2 class="text-white">{{ $demoText->heading }}</h2>
                        @endif
                        @if($demoText->subtitle1)
                            <h5 class="text-white">{{ $demoText->subtitle1 }}</h5>
                        @endif
                        @if($demoText->subtitle2)
                            <p class="text-ca">{{ $demoText->subtitle2 }}</p>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="event-booking-right">
                        <h5>Request Demo</h5>
                        <form action="">
                            <div class="event-booking-right-cell">
                                <span class="input-label">Time zone</span>
                                <div class="position-relative">
                                    <img src="./images/global.png" class="select-icon" alt="">
                                    <select class="form-select with-img">
                                        <option selected>UTC +5:30, India Standard Time (04:00PM)</option>
                                        <option value="1">UTC +1:00</option>
                                        <option value="2">UTC +2:00</option>
                                        <option value="3">UTC +3:00</option>
                                    </select>
                                </div>
                            </div>
                            <div class="event-booking-right-cell">
                                <span class="input-label">Available Dates</span>
                                <div class="position-relative">
                                    <div class="swiper eventSwiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide"><div><span class="day">Sun</span><span class="date">08</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide"><div><span class="day">Mon</span><span class="date">09</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide"><div><span class="day">Tue</span><span class="date">10</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide"><div><span class="day">Wed</span><span class="date">11</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide"><div><span class="day">Thu</span><span class="date">12</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide current-date"><div><span class="day">Fri</span><span class="date">13</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide booking-date"><div><span class="day">Sat</span><span class="date">14</span><span class="month">Feb</span></div></div>
                                            <div class="swiper-slide"><div><span class="day">Sun</span><span class="date">15</span><span class="month">Feb</span></div></div>
                                        </div>
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>
                            <div class="event-booking-right-cell">
                                <span class="input-label">Time Slot</span>
                                <div class="position-relative">
                                    <select class="form-select">
                                        <option selected>Select a time slot</option>
                                        <option value="1">UTC +1:00</option>
                                        <option value="2">UTC +2:00</option>
                                        <option value="3">UTC +3:00</option>
                                    </select>
                                </div>
                            </div>
                            <div class="event-booking-right-cell">
                                <button type="button" class="heroBtn btn-long w-100">Confirm Booking</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- event-booking end -->

    <!-- ======== CUSTOMER TESTIMONIALS ======== -->
    @if($customerBanner || $homeReviews->count() > 0)
    <section class="testimonial section">
        <div class="container">
            @if($customerBanner)
            <div class="global-heading text-center">
                @if($customerBanner->title)
                    <h2>{{ $customerBanner->title }}</h2>
                @endif
                @if($customerBanner->description)
                    <div>{!! $customerBanner->description !!}</div>
                @endif
            </div>
            @endif

            @if($homeReviews->count() > 0)
            <div class="testimonial-swiper">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper first">
                        @foreach($homeReviews as $review)
                        <div class="swiper-slide {{ $loop->first ? 'active' : '' }}">
                            <div class="img-box">
                                @if($review->profileImage)
                                    <img src="{{ $review->profileImage->file_path }}" alt="{{ $review->customer_name }}">
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <!-- Duplicate slides for smooth Swiper loop based on the html provided -->
                        @foreach($homeReviews as $review)
                        <div class="swiper-slide">
                            <div class="img-box">
                                @if($review->profileImage)
                                    <img src="{{ $review->profileImage->file_path }}" alt="{{ $review->customer_name }}">
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="testimonial-caption position-relative">
                    <div class="swiper captionSwiper">
                        <div class="swiper-wrapper">
                            @foreach($homeReviews as $review)
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>{{ $review->customer_name }}</h4>
                                    <p>{!! $review->description !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-button-next">
                        <img src="./images/arrow-right-white.png">
                    </div>
                    <div class="swiper-button-prev">
                        <img src="./images/arrow-left-white.png">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif
    <!-- testimonial end -->

    <!-- maps -->
    <section class="maps">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d14736.20868280687!2d88.43091629999999!3d22.577151999999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1756386707058!5m2!1sen!2sin"
            width="100%" height="420px" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>
    <!-- maps end -->

    <!-- footer -->
    @include('partials_new.footer')
    <!-- footer end -->

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- js file -->
    <script src="/frontend/js/script_new.js"></script>
</body>

</html>
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
    <link rel="stylesheet" href="/frontend/css/developer.css">
 <style>
    /* ── Form Card Wrapper ── */
    
    .event-booking-right {
        background: #ffffff;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 8px 40px rgba(0, 79, 184, 0.10);
        border: 1px solid #e8edf8;
    }

    .event-booking-right h5 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a1d30;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f4ff;
    }

    /* ── Form Cells ── */
    .event-booking-right-cell {
        margin-bottom: 22px;
    }

    .input-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 8px;
    }

    /* ── Select Styling ── */
    .form-select.with-img {
        border-radius: 12px;
        border: 1.5px solid #e0e5f2;
        padding: 12px 16px 12px 44px;
        font-size: 0.9rem;
        color: #1a1d30;
        background-color: #f8faff;
        transition: border-color 0.2s, box-shadow 0.2s;
        appearance: none;
    }

    .form-select.with-img:focus {
        border-color: #004fb8;
        box-shadow: 0 0 0 3px rgba(0, 79, 184, 0.12);
        outline: none;
        background-color: #fff;
    }

    /* ── Date Swiper Slides ── */
    #availableDates .swiper-slide {
        text-align: center;
        cursor: pointer;
        padding: 0; /* small breathing space */
    box-sizing: border-box;
    }

   #availableDates .swiper-slide .date-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    width: 100%;
    height: 100%;

    padding: 14px 8px;
    border-radius: 14px;

    border: 1.5px solid #e0e5f2;
    background: #f8faff;

    transition: all 0.2s ease;
}

    #availableDates .swiper-slide .date-item .day-name {
        font-size: 0.7rem;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    #availableDates .swiper-slide .date-item .day-num {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a1d30;
        line-height: 1;
    }

    #availableDates .swiper-slide .date-item .month-name {
        font-size: 0.68rem;
        font-weight: 500;
        color: #9ca3af;
    }

    #availableDates .swiper-slide:hover:not(.disabled-date) .date-item {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.08);
    }

    /* ── ACTIVE DATE — full green card ── */
    #availableDates .swiper-slide.active-date .date-item {
        background: #b4ebc8 !important;          /* solid green fill */
        border-color: #22c55e !important;
        color: #fff !important;
    }

    #availableDates .swiper-slide.active-date .date-item .day-name,
    #availableDates .swiper-slide.active-date .date-item .day-num,
    #availableDates .swiper-slide.active-date .date-item .month-name {
        color: #ffffff !important;
    }

    /* ── Disabled (fully booked) date ── */
    #availableDates .swiper-slide.disabled-date {
        opacity: 0.35;
        filter: grayscale(1);
        cursor: not-allowed;
        pointer-events: none;
    }
   
    /* ── Swiper nav buttons ── */
    .swiper-button-next,
    .swiper-button-prev {
        color: #004fb8 !important;
        background: #fff;
        width: 30px !important;
        height: 30px !important;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        top: 50% !important;
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 12px !important;
        font-weight: 800 !important;
    }

    /* ── Time Slot Grid ── */
    .time-slot-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 6px;
    }

    .time-slot-btn {
        padding: 11px 12px;
        border-radius: 10px;
        border: 1.5px solid #e0e5f2;
        background: #f8faff;
        font-size: 0.85rem;
        font-weight: 500;
        color: #1a1d30;
        cursor: pointer;
        transition: all 0.18s ease;
        text-align: center;
        line-height: 1.3;
    }

    .time-slot-btn:hover:not(.slot-booked) {
        border-color: #004fb8;
        color: #004fb8;
        background: #f0f4ff;
    }

    .time-slot-btn.slot-selected {
        background: #004fb8;
        border-color: #004fb8;
        color: #fff;
        box-shadow: 0 4px 14px rgba(0, 79, 184, 0.25);
    }

    .time-slot-btn.slot-booked {
        background: #f3f4f8;
        border-color: #e0e2ec;
        color: #c0c4d4;
        cursor: not-allowed;
        opacity: 0.55;
        text-decoration: line-through;
    }

    .slot-booked-label {
        display: block;
        font-size: 0.68rem;
        color: #c0c4d4;
        margin-top: 2px;
    }

    /* ── Alerts ── */
    .alert {
        border-radius: 10px;
        font-size: 0.875rem;
        padding: 12px 16px;
    }

    /* ── Submit Button ── */
    .heroBtn.btn-long {
        border-radius: 12px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 600;
        background: linear-gradient(135deg, #004fb8 0%, #0069f5 100%);
        border: none;
        color: #fff;
        transition: all 0.2s ease;
        box-shadow: 0 4px 16px rgba(0, 79, 184, 0.28);
    }

    .heroBtn.btn-long:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(0, 79, 184, 0.38);
    }

    .heroBtn.btn-long:active {
        transform: translateY(0);
    }

    /* ── Modal ── */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modal-header {
        border-bottom: 1px solid #f0f4ff;
        padding: 20px 24px 16px;
    }

    .modal-body {
        padding: 20px 24px;
    }

    .modal-footer {
        border-top: 1px solid #f0f4ff;
        padding: 16px 24px 20px;
    }

    .modal-body .form-control {
        border-radius: 10px;
        border: 1.5px solid #e0e5f2;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .modal-body .form-control:focus {
        border-color: #004fb8;
        box-shadow: 0 0 0 3px rgba(0, 79, 184, 0.12);
    }
    
</style>
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
                        <form action="{{ route('demo.submit') }}" method="POST">
                            @csrf

                            {{-- Success Message --}}
                            @if(session('success'))
                            <div class="alert alert-success mb-3">
                                {{ session('success') }}
                            </div>
                            @endif
                            {{-- Validation Errors --}}
                            @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Timezone --}}
                            <div class="event-booking-right-cell">
                                <span class="input-label">Time zone</span>

                                <div class="position-relative">
                                    <img src="./images/global.png" class="select-icon" alt="">

                                    <select class="form-select with-img" name="timezone" required>
                                        <option value="">Select Timezone</option>
                                        <option value="UTC+5:30">UTC +5:30, India Standard Time</option>
                                        <option value="UTC+1:00">UTC +1:00</option>
                                        <option value="UTC+2:00">UTC +2:00</option>
                                        <option value="UTC+3:00">UTC +3:00</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Available Dates --}}
                            <div class="event-booking-right-cell">
                                <span class="input-label">Available Dates</span>

                                <div class="position-relative">
                                    <div class="swiper eventSwiper">
                                        <div class="swiper-wrapper" id="availableDates"></div>
                                    </div>

                                    <input type="hidden" name="booking_date" id="selectedDate" required>

                                    <!-- <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div> -->
                                </div>
                            </div>

                            {{-- Time Slot (custom grid replaces <select> for full visual control) --}}
                            <div class="event-booking-right-cell">
                                <span class="input-label">Time Slot</span>

                                {{-- Hidden input carries the actual value to the form --}}
                                <input type="hidden" name="time_slot" id="selectedTimeSlot" required>

                                <div class="time-slot-grid" id="timeSlotGrid">
                                    {{-- Populated by JS after a date is selected --}}
                                </div>
                            </div>

                            <input type="hidden" name="name" id="hiddenName">
                            <input type="hidden" name="email" id="hiddenEmail">
                            <input type="hidden" name="phone" id="hiddenPhone">

                            {{-- Submit --}}
                            <div class="event-booking-right-cell">
                                @auth
                                <button type="submit" class="heroBtn btn-long w-100">
                                    Confirm Booking
                                </button>
                                @else
                                <button type="button" class="heroBtn btn-long w-100"
                                    data-bs-toggle="modal" data-bs-target="#guestModal">
                                    Confirm Booking
                                </button>
                                @endauth
                            </div>

                        </form>
                        {{-- Guest Details Modal --}}
                        <div class="modal fade" id="guestModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Enter Your Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" id="guestName" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" id="guestEmail" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" id="guestPhone" class="form-control" required>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" onclick="submitGuestForm()">Submit Booking</button>
                                    </div>

                                </div>
                            </div>
                        </div>

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
    <script>
        /* Use "http:\/\/localhost:8000\/admin\/events\/1" in onclick above OR define once here for reuse */
        function copyLink(link) {
            // modern secure API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(link).then(() => {
                    alert("✅ Event link copied! Paste it into your Instagram bio or story.");
                }).catch(err => {
                    console.error("Clipboard error:", err);
                    fallbackCopy(link);
                });
            } else {
                // fallback for non-secure contexts or older browsers
                fallbackCopy(link);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            textarea.style.position = "fixed";
            textarea.style.opacity = 0;
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand("copy");
                alert("✅ Event link copied! Paste it into your Instagram bio or story.");
            } catch (err) {
                console.error("Fallback copy error:", err);
                alert("Could not copy link automatically. Please copy it manually: " + text);
            }
            document.body.removeChild(textarea);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* ===============================
               1️⃣ BOOKED DATA (Safe Fallback)
            =============================== */
            const bookedData = @json($bookings);

            /* ===============================
               2️⃣ TIME SLOT UPDATE
               Renders clickable slot cards instead of <select>
               so booked slots can be visually greyed/struck through
            =============================== */
            function updateTimeSlots(selectedDate) {

                const allSlots = ["10:00 AM", "12:00 PM", "03:00 PM", "05:00 PM"];
                const bookedSlots = bookedData[selectedDate] ?? [];

                const grid = document.getElementById("timeSlotGrid");
                const hiddenInput = document.getElementById("selectedTimeSlot");
                if (!grid || !hiddenInput) return;

                hiddenInput.value = "";
                grid.innerHTML = "";

                allSlots.forEach(slot => {
                    const isBooked = bookedSlots.includes(slot);

                    const btn = document.createElement("div");
                    btn.classList.add("time-slot-btn");

                    if (isBooked) {
                        btn.classList.add("slot-booked");
                        btn.innerHTML = `${slot}<span class="slot-booked-label"></span>`;
                    } else {
                        btn.textContent = slot;
                        btn.addEventListener("click", function() {
                            grid.querySelectorAll(".time-slot-btn").forEach(b => b.classList.remove("slot-selected"));
                            btn.classList.add("slot-selected");
                            hiddenInput.value = slot;
                        });
                    }

                    grid.appendChild(btn);
                });
            }

            /* ===============================
               3️⃣ GUEST FORM SUBMIT
            =============================== */
            window.submitGuestForm = function() {

                const name = document.getElementById('guestName')?.value.trim();
                const email = document.getElementById('guestEmail')?.value.trim();
                const phone = document.getElementById('guestPhone')?.value.trim();

                if (!name || !email || !phone) {
                    alert("Please fill all fields.");
                    return;
                }

                document.getElementById('hiddenName').value = name;
                document.getElementById('hiddenEmail').value = email;
                document.getElementById('hiddenPhone').value = phone;

                const bookingForm = document.querySelector("form[action='{{ route('demo.submit') }}']");
                if (bookingForm) bookingForm.submit();
            };

            /* ===============================
               4️⃣ DATE + TIME SLOT SYSTEM
            =============================== */
            const wrapper = document.getElementById("availableDates");
            const selectedDateInput = document.getElementById("selectedDate");

            if (wrapper && selectedDateInput) {

                const totalSlots = 4;
                const today = new Date();
                wrapper.innerHTML = "";

                for (let i = 0; i < 10; i++) {

                    let date = new Date();
                    date.setDate(today.getDate() + i);

                    let formattedDate =
                        date.getFullYear() + '-' +
                        String(date.getMonth() + 1).padStart(2, '0') + '-' +
                        String(date.getDate()).padStart(2, '0');
                    let bookedSlots = bookedData[formattedDate] ?? [];
                    let isFullyBooked = bookedSlots.length >= totalSlots;

                    let slide = document.createElement("div");
                    slide.classList.add("swiper-slide");

                    if (isFullyBooked) {
                        // Fully booked: grey out the entire date card
                        slide.classList.add("disabled-date");
                        slide.title = "Fully booked";
                    }

                    slide.innerHTML = `
                <div class="date-item">
                    <span class="day">${date.toLocaleDateString('en-US', { weekday: 'short' })}</span>
                    <span class="date">${date.getDate()}</span>
                    <span class="month">${date.toLocaleDateString('en-US', { month: 'short' })}</span>
                   
                </div>
            `;

                    if (!isFullyBooked) {
                        slide.addEventListener("click", function() {
                            document.querySelectorAll('#availableDates .swiper-slide')
                                .forEach(el => el.classList.remove('active-date'));

                            slide.classList.add('active-date');
                            selectedDateInput.value = formattedDate;

                            updateTimeSlots(formattedDate);
                        });
                    }

                    wrapper.appendChild(slide);
                }

                // Auto-select the first available date
                const firstAvailable = wrapper.querySelector(".swiper-slide:not(.disabled-date)");
                if (firstAvailable) firstAvailable.click();
            }

            /* ===============================
               5️⃣ COUNTDOWN
            =============================== */
            const startEl = document.getElementById('startInTime');

            if (startEl) {

                const daysEl = document.getElementById('days');
                const hoursEl = document.getElementById('hours');
                const minutesEl = document.getElementById('minutes');
                const secondsEl = document.getElementById('seconds');
                const messageEl = document.getElementById('message');

                if (daysEl && hoursEl && minutesEl && secondsEl && messageEl) {

                    let targetDate = startEl.dataset.start ?
                        new Date(startEl.dataset.start) :
                        null;

                    if (!targetDate || isNaN(targetDate.getTime())) {
                        const d = parseInt(startEl.dataset.days ?? '0', 10);
                        const h = parseInt(startEl.dataset.hours ?? '0', 10);
                        const m = parseInt(startEl.dataset.minutes ?? '0', 10);
                        const s = parseInt(startEl.dataset.seconds ?? '0', 10);
                        const ms = (((d * 24 + h) * 60 + m) * 60 + s) * 1000;
                        targetDate = new Date(Date.now() + ms);
                    }

                    function pad(n) {
                        return String(n).padStart(2, '0');
                    }

                    function updateCountdown() {

                        const now = new Date();
                        const diff = targetDate - now;

                        if (diff <= 0) {
                            daysEl.textContent = '0';
                            hoursEl.textContent = '00';
                            minutesEl.textContent = '00';
                            secondsEl.textContent = '00';
                            messageEl.textContent = "Event started!";
                            clearInterval(intervalId);
                            return;
                        }

                        const totalSeconds = Math.floor(diff / 1000);
                        const days = Math.floor(totalSeconds / (24 * 3600));
                        const hours = Math.floor((totalSeconds % (24 * 3600)) / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                        const seconds = totalSeconds % 60;

                        daysEl.textContent = days;
                        hoursEl.textContent = pad(hours);
                        minutesEl.textContent = pad(minutes);
                        secondsEl.textContent = pad(seconds);
                    }

                    updateCountdown();
                    const intervalId = setInterval(updateCountdown, 1000);
                }
            }

        });
    </script>
</body>

</html>
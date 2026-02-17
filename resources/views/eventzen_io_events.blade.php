<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon" />
    <title>explore events</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- icon -->
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- font family -->
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <!-- Swiper CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="./frontend/css/style_new.css">
</head>

<body>

    <!-- header -->
    <header>
        <nav class="navbar navbar-expand-lg bg-transparent p-0">
            <div class="container d-flex align-items-center justify-content-between">
                <!-- Logo -->
                <!-- <a class="navbar-brand" href="">
                <img class="logo" src="/frontend/images/logo.png" alt="logo">
            </a> -->

                <a class="navbar-brand" href="">
                    <img class="logo" src="/eventzen-logo.svg" alt="logo" width="50%">
                </a>

                <!-- Search Box -->
                <!-- <div class="header-search-div">
                    <form class=" d-md-flex ms-auto w-100 w-sm-25 position-relative" action="/search" method="GET">
                        <input
                            class="form-control rounded-pill ps-4 py-2 shadow-sm fs-6 header-search-input"
                            type="text"
                            name="q"
                            placeholder="Please enter search key..."
                            value="">
                        <button
                            class="btn position-absolute end-0 top-50 translate-middle-y me-2 px-3 py-1 border-0 bg-transparent text-secondary d-flex align-items-center gap-1"
                            type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div> -->

                <!-- Navbar Menu -->
                <div class="d-flex align-items-center">
                    <div class="ms-auto custom-nav">
                        <button class="custom-toggler-close position-absolute d-lg-none">
                            <img src='/frontend/images/cross.png' alt="">
                        </button>
                        <ul class="navbar-nav mb-2 mb-md-0 gap-3 gap-lg-5 align-items-center ms-lg-4">
                            <li class="nav-item">
                                <a class="nav-link p-0 active" href="">Why Us?</a>
                            </li>
                            <!-- <li class="nav-item">
                            <a class="nav-link p-0 " href="/venue">Venue Information</a>
                            </li> -->

                            <!-- <li class="nav-item">
                                <a class="nav-link p-0 " href="/venue">Venue</a>
                            </li> -->

                            <li class="nav-item">
                                <a class="nav-link p-0 " href="/events">Events</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link p-0 " href="/venue">Pricing</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link p-0 " href="/support">Contact Us</a>
                            </li>

                            <li class="nav-item">

                            </li>
                        </ul>
                    </div>

                    @if(!Auth::check())
                    <a class="heroBtn ms-3" href="{{ route('login') }}">
                        <img class="d-xl-none" src="{{ asset('frontend/images/login.png') }}" alt="">
                        <span class="d-none d-xl-block">Login</span>
                    </a>
                    @else
                    <a class="heroBtn ms-3" @if(auth()->user()->hasRole('Admin')) href="{{ route('home') }}" @else href="{{ route('user.home') }}" @endif>
                        <img class="d-xl-none" src="{{ asset('frontend/images/home-2.png') }}" alt="">
                        <span class="d-none d-xl-block">Dashboard</span>
                    </a>
                    @endif

                    <!-- Toggler -->
                    <button class="navbar-toggler heroBtn bg-transparent custom-toggler-open ms-3">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>
    </header>
    <!-- header end -->

    <!-- explore event start -->
    <section class="explore-event-page section">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                <h2>Explore Events</h2>
                <div class="explore-event-page-search">
                    <!-- <input type="search" placeholder="Search Events">
                    <button type="button"><img src="./images/search.png" alt=""></button> -->
                    <form class="explore-event-page-search" method="GET" action="{{ route('front.allEvents') }}">
                        <input type="search" name="q" placeholder="Search Events" value="{{ $q ?? '' }}">
                        <button type="submit">
                            <img src="{{ asset('images/search.png') }}" alt="">
                        </button>
                    </form>
                </div>
            </div>
            <div class="explore-event-page-row-wrapper">
                <ul class="explore-event-ul">
                    <li><button class="tab-btn active" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab1">Ongoing</button></li>
                    <li><button class="tab-btn" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab2">Upcoming</button></li>
                    <li><button class="tab-btn" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab3">Past</button></li>
                </ul>
                <div id="explore-event-tab-group-tab1" class="tab-content active">
                    <div class="row explore-event-page-row">
                        @forelse($ongoing as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No ongoing events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $ongoing->appends(request()->query())->links() }}
                    </div>
                </div>
                <div id="explore-event-tab-group-tab2" class="tab-content">
                    <!-- Lorem ipsum dolor sit, amet consectetur adipisicing elit. Illum, minus? -->
                    <div class="row explore-event-page-row">
                        @forelse($upcoming as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No upcoming events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $upcoming->appends(request()->query())->links() }}
                    </div>
                </div>
                <div id="explore-event-tab-group-tab3" class="tab-content">
                    <!-- Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem libero aspernatur, sunt aperiam quis tempore saepe voluptate laboriosam? Nesciunt, esse. -->
                    <div class="row explore-event-page-row">
                        @forelse($past as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No past events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $past->appends(request()->query())->links() }}
                    </div> 
                </div>
            </div>
        </div>
    </section>
    <!-- explore event end -->

    <!-- footer -->
    <footer>
        <div class="container">
            <div class="footer-top d-flex flex-column flex-sm-row gap-4 justify-content-sm-between align-items-center">
                <a href="javascript:void()">
                    <img class="logo" src="./images/footer-logo.png" alt="">
                </a>
                <div class=" d-lg-flex  align-items-center gap-4">
                    <span class="small-heading-white text-center text-sm-start">Share event information on</span>
                    <ul class=" footer-social-group p-0">
                        <li>
                            <a href="javascript:void()">
                                <i class="fa-brands fa-facebook-f text-light"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void()">
                                <i class="fa-brands fa-instagram text-light"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void()">
                                <i class="fa-brands fa-linkedin-in text-light"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void()">
                                <i class="fa-brands fa-x-twitter text-light"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="black-text-18 text-light text-center">© 2025
                    <a class="text-light" href="javascript:void()">Eventzen.io</a>
                </p>
            </div>
        </div>
    </footer>
    <!-- footer end -->



    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- add js file -->
    <script src="./frontend/js/script_new.js"></script>
</body>

</html>
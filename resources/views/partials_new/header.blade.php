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
                            <a class="nav-link p-0 @if(request()->routeIs('front.landing')) active @endif" href="{{ route('front.landing') }}">Why Us?</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link p-0 " href="/venue">Venue Information</a>
                            </li> -->

                        <!-- <li class="nav-item">
                                <a class="nav-link p-0 " href="/venue">Venue</a>
                            </li> -->

                        <li class="nav-item">
                            <a class="nav-link p-0 @if(request()->routeIs('front.allEvents')) active @endif" href="{{ route('front.allEvents') }}">Events</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link p-0 " href="/venue">Pricing</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link p-0 @if(request()->routeIs('contact-us')) active @endif" href="{{ route('contact-us') }}"
                            <a class="nav-link p-0"  href="/contact-us">Contact Us</a>
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
<header>
    <nav class="navbar navbar-expand-md bg-transparent p-0">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img class="logo" src="{{ asset('frontend/images/logo.png') }}" alt="logo">
            </a>

            <!-- Search Box -->
            <form class="d-none d-md-flex mx-auto w-25 position-relative" action="{{ route('front.landing.search') }}" method="GET">
                <input 
                    class="form-control rounded-pill ps-4 pe-5 py-2 shadow-sm fs-6"
                    type="text"
                    name="q"
                    placeholder="Please enter search key..."
                    value="{{ request('q') }}"
                >
                <button 
                    class="btn position-absolute end-0 top-50 translate-middle-y me-2 px-3 py-1 border-0 bg-transparent text-secondary d-flex align-items-center gap-1"
                    type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Search</span>
                </button>
            </form>

            <!-- Navbar Menu -->
            <div class="d-flex align-items-center">
                <div class="ms-auto custom-nav">
                    <button class="custom-toggler-close position-absolute d-md-none">
                        <img src='{{ asset("frontend/images/cross.png") }}' alt="">
                    </button>
                    <ul class="navbar-nav mb-2 mb-md-0 gap-3 gap-md-5">
                        <li class="nav-item">
                            <a class="nav-link p-0 {{ request()->is('/') ? 'active' : '' }}" href="{{ route('front.landing') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-0 {{ request()->is('venue*') ? 'active' : '' }}" href="{{ route('venue') }}">Venue Information</a>
                        </li>
                    </ul>
                </div>
                
                <a class="heroBtn ms-md-3" href="{{ route('login') }}">
                    <img class="d-md-none" src="{{ asset('frontend/images/login.png') }}" alt="">
                    <span class="d-none d-md-block">Login</span>
                </a>

                <!-- Buttons -->
                <a class="heroBtn ms-md-3" href="{{ route('registration') }}">
                    <img class="d-md-none" src="{{ asset('frontend/images/login.png') }}" alt="">
                    <span class="d-none d-md-block">Registration</span>
                </a>
            
                <!-- Toggler -->
                <button class="navbar-toggler heroBtn bg-transparent custom-toggler-open ms-3 ms-md-5">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>
</header>


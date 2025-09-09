<header>
    <nav class="navbar navbar-expand-md bg-transparent p-0">
        <div class="container">
            <a class="navbar-brand" href="{{url('/')}}">
                <img class="logo" src="{{asset('frontend/images/logo.png')}}" alt="logo">
            </a>
            <div class="d-flex align-items-center">
                <div class="ms-auto custom-nav">
                    <button class="custom-toggler-close position-absolute d-md-none">
                        <img src='{{asset("frontend/images/cross.png")}}' alt="">
                    </button>
                    <ul class="navbar-nav mb-2 mb-mg-0 gap-3 gap-md-5">
                        <li class="nav-item">
                            <a class="nav-link p-0 active" aria-current="page" href="{{route('front.landing')}}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-0" href="{{route('venue')}}">Venue Information</a>
                        </li>
                    </ul>
                </div>
                <a class="heroBtn ms-md-5" href="{{route('registration')}}">
                    <img class="d-md-none" src="{{asset('frontend/images/login.png')}}" alt="">
                    <span class="d-none d-md-block">Registration</span>
                </a>
                <button class="navbar-toggler heroBtn bg-transparent custom-toggler-open ms-3 ms-md-5">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>
</header>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SME') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand logo" href="{{ url('/') }}">
                    <img src="{{asset('sme-logo.png')}}" alt="{{ config('app.name', 'SME') }}" width="70%">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <!-- <ul class="navbar-nav me-auto">

                    </ul> -->

                    <!-- Right Side Of Navbar
                    <ul class="navbar-nav ms-auto">

                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>-->

                    <div class="ms-auto d-flex mt-3 mt-md-0 justify-content-between justify-content-md-end align-items-center">
                        @if(Auth::check())
                        <span class="navbar-text">
                            Welcome, {{ Auth::user()->name }} |
                        </span>
                        <a class="heroBtn ms-md-3" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        @endif
                    </div>
                    
                    <style>
                        a {
                            text-decoration: none;
                        }

                        .navbar-text {
                            margin-right: 10px;
                        }

                        /* for logout box design */
                        .nav-link.d-inline {
                            display: inline;
                            padding: 0;
                            margin: 0;
                            cursor: pointer;
                            color: #0d6efd;
                            text-decoration: underline;
                        }

                        .heroBtn {
                            height: 53px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            background-color: #004fb8;
                            ;
                            border-radius: 10px;
                            color: #fff;
                            font-size: 18px;
                            font-weight: 500;
                            padding: 0 30px;
                            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                            transition: 0.4s;
                        }

                        .heroBtn:hover {
                            background-color: #005fdb;
                        }
                    </style>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>
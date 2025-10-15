<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>landing</title>
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
    <!-- css -->
    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}?v={{ time() }}">
</head>

<body>

    <section class="landing-page">
        <div class="roow d-flex h-100">
            <div class="left">
                @php
                  $setting = getLandingPageSettings();
                @endphp
                <div class="left-inner">
                    <a href="javascript:void()">
                        <img class="landing-page-logo" src="{{asset('frontend/images/logo.png')}}" alt="">
                    </a>
                    <h2 class="h2-black mt-4 mb-3">
                       {{$setting->title ?? ''}}
                    </h2>
                    <ul class="d-flex flex-column gap-2 m-0 p-0">
                        <li class="d-flex align-items-center gap-3">
                            <div class="white-circle">
                                <img src="{{asset('frontend/images/calendar.svg')}}" alt="">
                            </div>
                            <p class="black-text-18">
                                 {{ \Carbon\Carbon::parse($setting->date)->format('M d, Y') }}
                            </p>
                        </li>
                        <li class="d-flex align-items-center gap-3">
                            <div class="white-circle">

                                <img src="{{asset('frontend/images/location.svg')}}" alt="">
                            </div>
                            <p class="black-text-18">
                               {{$setting->location ?? ''}}
                            </p>
                        </li>
                    </ul>
                </div>
                <div class="left-bottom mt-4">
                    <h2 class="h2-white">
                        Download the App
                    </h2>
                    <div class="app-grid mt-3">
                        <div class="cell">
                            <div>
                                <img class="h-100 w-100" src="{{asset('frontend/images/apple-store.svg')}}" alt="">
                            </div>
                        </div>
                        <div class="cell">
                            <div>
                                <img class="h-100 w-100" src="{{asset('frontend/images/android-store.svg')}}" alt="">
                            </div>
                        </div>
                        <div class="cell">
                            <div>
                                <img class="h-100 w-100" src="{{asset('frontend/images/apple-qr.svg')}}" alt="">
                            </div>
                        </div>
                        <div class="cell">
                            <div>
                                <img class="h-100 w-100" src="{{asset('frontend/images/android-qr.svg')}}" alt="">
                            </div>
                        </div>
                    </div>
                    <span class="small-heading-white mt-3">
                        Website - <a class="text-light" href="javascript:void()"> {{$setting->website ?? ''}}</a>
                    </span>
                </div>
            </div>
            <div class="right">
                <img class="mobile-img" src="{{asset('frontend/images/mobile-image.svg')}}" alt="">
            </div>
        </div>
    </section>



    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- add js file -->
    <script src="./js/script.js"></script>
</body>

</html>
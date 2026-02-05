<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon" />

    <title>Eventzen.io</title>


    <meta name="description" content="Your landing page description here.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/frontend/css/style.css?v=1770271652">




</head>

<body class="min-h-screen flex flex-col bg-gray-50">

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

    <section class="banner">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6">
                    <div>
                        <h1>
                            <!-- Lorem ipsum dolor sit amet consectetur. -->
                            Why Event Leaders Choose Eventzen.io?
                        </h1>
                        <p class="mt-3 mt-lg-4">
                            <!-- Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita omnis suscipit doloremque aliquid distinctio facere esse, ipsum ducimus quidem, rerum ipsam iure quas nihil aperiam dolorem laudantium autem reprehenderit provident. -->
                            From the most diverse events on the planet to simple business meetings, brands have placed their trust in us to create online and in-person events using our app. Thrilled with the outstanding results, event leaders find their way back to our platform to make every event sensational.
                        </p>
                        <a href="/support">
                        <button class="heroBtn mt-4 mt-lg-5">
                            Request a Demo
                        </button>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <img style="height: 500px;" class="banner-right-image" src="/images/community-concept-with-group-people.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div>
                         <img style="height: 500px; width: 100%; object-fit: cover; object-position: bottom; border-radius: 0 100px 100px 0;" src="/images/group-business-executives-smiling-camera.jpg" alt="">
                    </div>
                </div>
                <div class="col-lg-6" style="color: #0f1f3d;">
                    <div class="ps-3">
                        <h2 style="font-size: 45px; font-weight:600; color: #0f1f3d;">
                        <!-- Lorem, ipsum dolor sit amet consectetur adipisicing elit. Non, id iure. Exercitationem voluptates dolore maxime. -->
                         One-Stop Event Management Solution
                    </h2>
                    <ul class="mt-4">
                        <li style="list-style:circle; font-size: 20px;" class="mt-1">One platform for all your event needs</li>
                        <li style="list-style: circle; font-size: 20px;" class="mt-1">Easy-to-use interface</li>
                        <li style="list-style: circle; font-size: 20px;" class="mt-1">We offer prices you can’t refuse</li>
                        <li style="list-style: circle; font-size: 20px;" class="mt-1">Easy communication with support staff</li>
                        <li style="list-style: circle; font-size: 20px;" class="mt-1">Seamless AI integration</li>
                        <li style="list-style: circle; font-size: 20px;" class="mt-1">Data Protection</li>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>






    <!-- footer -->
    <footer>
        <div class="container">
            <div class="footer-top d-flex flex-column flex-sm-row gap-4 justify-content-sm-between align-items-center">
                <a href="">
                    <!-- <img class="logo" src="/frontend/images/footer-logo.png" alt=""> -->
                    <img class="logo" src="/eventzen-logo-white.svg" alt="">
                </a>

                <div class="d-lg-flex align-items-center gap-4">
                    <span class="small-heading-white text-center text-sm-start">
                        Share event information on
                    </span>



                    <ul class="footer-social-group p-0 d-flex gap-3">

                        <li>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A8000%2Fadmin%2Fevents%2F1"
                                target="_blank" rel="noopener">
                                <i class="fa-brands fa-facebook-f text-light"></i>
                            </a>
                        </li>


                        <li>
                            <a href="javascript:void(0)" onclick="copyLink(" http:\/\/localhost:8000\/admin\/events\/1")">
                                <i class="fa-brands fa-instagram text-light"></i>
                            </a>
                        </li>


                        <li>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=http%3A%2F%2Flocalhost%3A8000%2Fadmin%2Fevents%2F1"
                                target="_blank" rel="noopener">
                                <i class="fa-brands fa-linkedin-in text-light"></i>
                            </a>
                        </li>


                        <li>
                            <a href="https://twitter.com/intent/tweet?url=http%3A%2F%2Flocalhost%3A8000%2Fadmin%2Fevents%2F1&text=CanadianSME+Small+Business+Summit+2025"
                                target="_blank" rel="noopener">
                                <i class="fa-brands fa-x-twitter text-light"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="black-text-18 text-light text-center">© 2026
                    <a class="text-light" href="">Eventzen.io</a>
                </p>
            </div>
        </div>
    </footer>
    <!-- footer end -->

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

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- add js file -->
    <script src="/frontend/js/script.js"></script>
    <script>
        const startEl = document.getElementById('startInTime');

        // Prefer the real ISO datetime from PHP
        let targetDate = startEl?.dataset.start ? new Date(startEl.dataset.start) : null;

        // Fallback: if ISO missing, compute from diff parts you already output
        if (!targetDate || isNaN(targetDate.getTime())) {
            const d = parseInt(startEl?.dataset.days ?? '0', 10);
            const h = parseInt(startEl?.dataset.hours ?? '0', 10);
            const m = parseInt(startEl?.dataset.minutes ?? '0', 10);
            const s = parseInt(startEl?.dataset.seconds ?? '0', 10);
            const ms = (((d * 24 + h) * 60 + m) * 60 + s) * 1000;
            targetDate = new Date(Date.now() + ms);
        }

        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');
        const messageEl = document.getElementById('message');

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

            const secTotal = Math.floor(diff / 1000);
            const days = Math.floor(secTotal / (24 * 3600));
            const hours = Math.floor((secTotal % (24 * 3600)) / 3600);
            const minutes = Math.floor((secTotal % 3600) / 60);
            const seconds = secTotal % 60;

            daysEl.textContent = String(days);
            hoursEl.textContent = pad(hours);
            minutesEl.textContent = pad(minutes);
            secondsEl.textContent = pad(seconds);
        }

        updateCountdown();
        const intervalId = setInterval(updateCountdown, 1000);
    </script>
    <script>
        (() => {
            // ---------toggler header-----------
            const custom_toggler_open = document.querySelector(".custom-toggler-open");
            const custom_toggler_close = document.querySelector(".custom-toggler-close");
            const custom_nav = document.querySelector(".custom-nav");
            const body = document.querySelector("body");

            if (custom_toggler_open && custom_toggler_close && custom_nav) {
                custom_toggler_open.addEventListener("click", function() {
                    custom_nav.classList.add("open");
                    body.classList.add("lock");
                });

                custom_toggler_close.addEventListener("click", function() {
                    custom_nav.classList.remove("open");
                    body.classList.remove("lock");
                });
            }

            // ----------------speakers swiper--------------
            new Swiper('.speakers-swiper', {
                loop: true,
                slidesPerView: 2.5,
                spaceBetween: 20,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    650: {
                        slidesPerView: 2.5
                    },
                    575: {
                        slidesPerView: 2
                    },
                    0: {
                        slidesPerView: 1
                    }
                }
            });

            // ---------------sponsors---------------
            new Swiper('.sponsors-swiper', {
                loop: true,
                slidesPerView: 4,
                spaceBetween: 20,
                navigation: {
                    nextEl: '.sponsors-next',
                    prevEl: '.sponsors-prev',
                },
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    1199: {
                        slidesPerView: 4
                    },
                    991: {
                        slidesPerView: 3
                    },
                    575: {
                        slidesPerView: 2
                    },
                    0: {
                        slidesPerView: 1
                    }
                }
            });
            // ---------------sponsors end---------------
        })();
    </script>


</body>

</html>
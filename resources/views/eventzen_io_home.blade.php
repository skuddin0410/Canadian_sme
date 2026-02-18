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
    <link rel="stylesheet" href="/frontend/css/style_new.css?v=1770271652">




</head>

<body class="min-h-screen flex flex-col bg-gray-50">

    @include('partials_new.header')

    <!-- banner -->
    <section class="banner section position-relative">
        <div class="container">
            <div class="row align-items-center gap-3 gap-md-0">
                <div class="col-md-6">
                    <div class="banner-text">
                        <h6>Find Your Next Experience</h6>
                        <h1>
                            Why Event Leaders <br>
                            Choose Eventzen.io?
                        </h1>
                        <p>
                            World’s most influential media, entertainment & technology show inspirational speakers
                            including game changing ideas.
                        </p>
                        <button class="heroBtn btn-long">Request a Demo</button>
                    </div>
                </div>
                <div class="col-md-6 d-none d-md-block">
                    <div>
                        <img class="img-fluid banner-right-img" src="./images/banner.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner end -->

    <!-- banner-bottom-swiper start -->
    <section class="banner-bottom-swiper swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="./images/banner-bottom-1.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-2.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-3.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-4.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-5.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-6.png" alt=""></div>
            <div class="swiper-slide"><img src="./images/banner-bottom-7.png" alt=""></div>
        </div>
    </section>
    <!-- banner-bottom-swiper end -->

    <!-- about-us start -->
    <section class="about-us section">
        <div class="container">
            <div class="row align-items-center about-us-row">
                <div class="col-xl-6">
                    <div class="about-us-img-wrapper">
                        <div class="about-us-img-box about-us-img-box-1">
                            <img src="./images/about-us-1.png" alt="">
                        </div>
                        <div class="about-us-img-box about-us-img-box-2">
                            <img src="./images/about-us-2.png" alt="">
                        </div>
                        <div class="about-us-img-box about-us-img-box-3">
                            <img src="./images/about-us-3.png" alt="">
                        </div>
                        <button class="about-us-img-box about-us-img-box-4">
                            <img src="./images/Button.png" alt="">
                        </button>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div>
                        <div class="global-heading">
                            <h6>About Us</h6>
                            <h2>Lorem ipsum dolor sit amet consectetur volutpat</h2>
                            <p>Lorem ipsum dolor sit amet consectetur. Lorem congue lorem ultricies blandit placerat ac
                                id nulla aliquam. A enim id facilisi sit imperdiet et faucibus augue. Pulvinar nibh
                                feugiat a nisl. Urna phasellus urna odio in sed. Sit purus feugiat lacus amet feugiat
                                non dignissim orci.</p>
                        </div>
                        <div class="ul-wrapper">
                            <ul class="ul">
                                <li><img src="./images/blue-check.png" alt="">Lorem ipsum dolor sit.</li>
                                <li><img src="./images/blue-check.png" alt="">Lorem ipsum dolor sit amet.</li>
                                <li><img src="./images/blue-check.png" alt="">Lorem ipsum dolor sit amet consectetur
                                    adipisicing.</li>
                            </ul>
                            <div class="years-of-exp">
                                <img src="./images/trophy-svgrepo-com.png" alt="">
                                <h3>12+</h3>
                                <p>Years Experience</p>
                            </div>
                        </div>
                        <button class="heroBtn btn-long">More About Us <img class="ms-2" src="./images/right-arrow.png"
                                alt=""></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about-us end -->

    <!-- speakers -->
    <section class="speakers">
        <div class="container">
            <div class="d-block d-xl-flex align-items-center gap-4 gap-xl-0">
                <div class="d-block col-xl-4 pe-xl-5 ps-0">

                    <div class="global-heading mb-4 mb-xl-0">
                        <h6 class="text-white">Speakers</h6>
                        <h2 class="text-white">Our Amazing & learned event Speakrs</h2>
                        <p class="text-white">
                            World’s most influential media, entertainment & technology show inspirational speakers
                            including game changing ideas.
                        </p>
                    </div>
                    <!-- <div>
                        <span class="small-heading-white">Speakers</span>
                        <h2 class="h2-white mb-4">Our Amazing & learned event Speakrs</h2>
                        <span class="small-heading-white">
                            World’s most influential media, entertainment & technology show inspirational speakers
                            including game changing ideas.</span>
                        </span>
                    </div> -->
                </div>
                <div class="col-xl-8 p-0">
                    <div class="swiper speakers-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="swiper-img-box">
                                    <img src="./images/speaker-1.png" alt="">
                                </div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box">
                                    <img src="./images/speaker-2.png" alt="">
                                </div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box">
                                    <img src="./images/speaker-1.png" alt="">
                                </div>
                                <div class="swiper-img-text">
                                    <span class="speakers-name">Jenyfe loe</span>
                                    <span class="speakers-title">Speaker</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-img-box">
                                    <img src="./images/speaker-2.png" alt="">
                                </div>
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

    <!-- Unforgettable Events start -->
    <section class="unforgettable-event section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>We Roll Out the Red Carpet to Unforgettable Events</h2>
                <p>Lorem ipsum dolor sit amet consectetur. Quam cras scelerisque aliquam rhoncus. Aliquam velit
                    hendrerit tellus fermentum</p>
            </div>
            <div class="unforgettable-event-row">
                <div class="unforgettable-event-col">
                    <div class="unforgettable-event-col-imgbox">
                        <img src="./images/pre-event.png" alt="">
                    </div>
                    <h3>Pre-Event</h3>
                    <ul class="ul">
                        <li>Registration</li>
                        <li>Ticketing</li>
                        <li>Email Marketing</li>
                        <li>Event Budgeting</li>
                        <li>Venue Management</li>
                        <li>Social Boost</li>
                        <li>Agenda Builder</li>
                        <li>Interactive Maps</li>
                    </ul>
                </div>
                <div class="unforgettable-event-col">
                    <div class="unforgettable-event-col-imgbox">
                        <img src="./images/during-event.png" alt="">
                    </div>
                    <h3>During Event</h3>
                    <ul class="ul">
                        <li>Registration</li>
                        <li>Ticketing</li>
                        <li>Email Marketing</li>
                        <li>Event Budgeting</li>
                        <li>Venue Management</li>
                        <li>Social Boost</li>
                        <li>Agenda Builder</li>
                        <li>Interactive Maps</li>
                    </ul>
                </div>
                <div class="unforgettable-event-col">
                    <div class="unforgettable-event-col-imgbox">
                        <img src="./images/post-event.png" alt="">
                    </div>
                    <h3>Post-Event</h3>
                    <ul class="ul">
                        <li>Registration</li>
                        <li>Ticketing</li>
                        <li>Email Marketing</li>
                        <li>Event Budgeting</li>
                        <li>Venue Management</li>
                        <li>Social Boost</li>
                        <li>Agenda Builder</li>
                        <li>Interactive Maps</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- Unforgettable Events end -->

    <!-- apart start -->
    <section class="apart section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>What Sets Us Apart from the Pack?</h2>
                <p>Empowering Events with Ease, Scalability, and Security—Eventify Delivers World-Class Solutions for
                    Every Need.</p>
            </div>
            <div class="apart-row">
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>Ease of Use</h3>
                        <p>We know you're super busy, which is why we've created an easy-to-learn panel that lets you
                            design your event in minutes.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon1.png" alt="">
                    </div>
                </div>
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>Custom Branding</h3>
                        <p>Not happy with what you've created? No worries! We have dedicated designers ready to assist
                            you in crafting your event, keeping your branding front and center.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon2.png" alt="">
                    </div>
                </div>
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>AI Integration</h3>
                        <p>Seamless AI integration allows you to create events that look and feel world-class. Smart
                            automation handles the heavy lifting.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon3.png" alt="">
                    </div>
                </div>
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>Competitive Pricing</h3>
                        <p>We don't like haggling, but a little self-praise never hurt anyone! We offer rates that no
                            one else in the industry can match.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon4.png" alt="">
                    </div>
                </div>
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>Scalability</h3>
                        <p>Whether it's a global event or a small inhouse business meeting, you can rely on Eventify to
                            deliver an unmatched experience.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon5.png" alt="">
                    </div>
                </div>
                <div class="apart-col">
                    <div class="apart-col-overlay"></div>
                    <div>
                        <h3>Security First</h3>
                        <p>Integrity is what lies at the core of the company, making security and data protection our
                            number #1 Priority.</p>
                        <ul class="ul">
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                Intuitive Interface
                            </li>
                            <li><span><img src="./images/green-tick.png" alt=""></span> Quick Setup</li>
                            <li>
                                <span><img src="./images/green-tick.png" alt=""></span>
                                No Training Required
                            </li>
                        </ul>
                    </div>
                    <div>
                        <img class="apart-col-right-img" src="./images/apart-icon6.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- apart end -->

    <!-- event-booking start -->
    <section class="event-booking section">
        <div class="container">
            <div class="row align-items-center event-booking-row">
                <div class="col-lg-6">
                    <div class="global-heading">
                        <h2 class="text-white">
                            Love the Smell Of Events Every Morning Like Us?
                        </h2>
                        <h5 class="text-white">
                            We're Totally Obsessed To Make Your Event Success!
                        </h5>
                        <p class="text-ca">"So Why Not Lead Such An Eventify Life" (Soothing, ready for success)</p>
                    </div>
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

                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Sun</span>
                                                    <span class="date">08</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Mon</span>
                                                    <span class="date">09</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Tue</span>
                                                    <span class="date">10</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Wed</span>
                                                    <span class="date">11</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Thu</span>
                                                    <span class="date">12</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide current-date">
                                                <div>
                                                    <span class="day">Fri</span>
                                                    <span class="date">13</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide booking-date">
                                                <div>
                                                    <span class="day">Sat</span>
                                                    <span class="date">14</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div>
                                                    <span class="day">Sun</span>
                                                    <span class="date">15</span>
                                                    <span class="month">Feb</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- Arrows -->
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

    <!-- testimonial start -->
    <section class="testimonial section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>What Our Customer Say?</h2>
                <p>Lorem ipsum dolor sit amet consectetur. Quam cras scelerisque aliquam rhoncus. Aliquam velit
                    hendrerit tellus fermentum</p>
            </div>

            <div class="testimonial-swiper">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper first">
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-1.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-2.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-3.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-4.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide active">
                            <div class="img-box">
                                <img src="./images/testi-5.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-6.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-7.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-1.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-2.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-3.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-4.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="img-box">
                                <img src="./images/testi-5.jpg" alt="">
                            </div>
                        </div>
                    </div>

                    <!-- Arrows -->
                    <!-- <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div> -->
                </div>

                <div class="testimonial-caption position-relative">

                    <div class="swiper captionSwiper">

                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <h4>Emma Stone</h4>
                                    <p>
                                        Lorem ipsum dolor sit amet consectetur. Ut id ullamcorper faucibus tempor
                                        pharetra id praesent. Hendrerit et ultricies convallis id ut ultrices congue
                                        suspendisse enim. Dui aliquam faucibus enim iaculis pellentesque ut. Nec integer
                                        pharetra pretium suscipit amet suspendisse pulvinar lorem. Condimentum augue ut
                                        diam at nulla ornare pharetra. Sit accumsan et sollicitudin nibh nibh.
                                        Nunc quisque aliquam a fusce laoreet. In fusce nullam erat urna risus odio nibh.
                                        Eu ornare lorem sodales arcu non nunc. Cras tristique habitasse placerat metus
                                        vestibulum arcu sed purus tortor. Lorem aliquam parturient magna aliquam ac.
                                    </p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 7</p>
                                    <h4>Client 7</h4>
                                    <span>Designer</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 8</p>
                                    <h4>Client 8</h4>
                                    <span>Designer</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 9</p>
                                    <h4>Client 9</h4>
                                    <span>Designer</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 10</p>
                                    <h4>Client 10</h4>
                                    <span>Designer</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 11</p>
                                    <h4>Client 11</h4>
                                    <span>Designer</span>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="caption-card">
                                    <p>Testimonial text for image 12</p>
                                    <h4>Client 12</h4>
                                    <span>Designer</span>
                                </div>
                            </div>

                            <!-- total caption = total image -->

                        </div>

                    </div>

                    <!-- Arrows -->
                    <div class="swiper-button-next">
                        <img src="./images/arrow-right-white.png">
                    </div>

                    <div class="swiper-button-prev">
                        <img src="./images/arrow-left-white.png">
                    </div>

                </div>
            </div>



        </div>
    </section>
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
    <script src="/frontend/js/script_new.js"></script>
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
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>contact us</title>
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
    @include('partials_new.header')
    <!-- header end -->

    <!-- contact us start -->
    <section class="contact-us-page section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>Get in Touch with Us</h2>
                <p>We’re always here to assist you. Whether you have a question,</p>
            </div>
            <div class="contact-us-page-row">
                <div>
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="row gy-3 mb-4 mb-xl-5">
                        <div class="col-xl-6">
                            <div class="contact-fake-input">
                                <div>
                                    <img src="./images/phone-call.png" alt="">
                                </div>
                                <div>
                                    <h5>Phone</h5>
                                    <span>+91 81001 56789</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="contact-fake-input">
                                <div>
                                    <img src="./images/mail.png" alt="">
                                </div>
                                <div>
                                    <h5>Email ID</h5>
                                    <span>info@ocsedu.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('contact-submit') }}">
                        @csrf
                        <div class="row gy-2 gy-lg-3">

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="name" class="type-text" type="text" placeholder="Your Name" required>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="location" class="type-text" type="text" placeholder="Location" required>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="email" class="type-email" type="email" placeholder="Email Address" required>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="phone" class="type-phone" type="tel" placeholder="Phone" required>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="input-wrapper">
                                    <input name="subject" class="type-text" type="text" placeholder="Enter Your Subject" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <textarea name="description" class="contact-textarea" rows="4" placeholder="Type Your Message Here" required></textarea>
                            </div>

                        </div>

                        <button type="submit" class="heroBtn contact-from-submit-btn">Submit</button>
                    </form>

                </div>
                <div class="h-100">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3684.149264621701!2d88.4314066!3d22.57352!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0275bc55555555%3A0x223238264c3c4da7!2sDigital%20Aptech!5e0!3m2!1sen!2sin!4v1771409293159!5m2!1sen!2sin"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="iframe-maps"></iframe>
                </div>
            </div>
        </div>
    </section>
    <!-- contact us end -->

    <!-- footer -->
    @include('partials_new.footer')
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
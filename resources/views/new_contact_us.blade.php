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
    <link rel="stylesheet" href="{{ asset('frontend/css/developer.css') }}">
    <style>
        /* Honeypot: visually hidden from users, still present in DOM for bots */
        .contact-hp {
            position: absolute;
            left: -10000px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
        .contact-from-submit-btn.is-submitting {
            opacity: 0.85;
            pointer-events: none;
            cursor: wait;
        }
        .contact-from-submit-btn .btn-loader {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
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
                    @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
                    <form method="POST" action="{{ route('contact-submit') }}" id="contact-us-form">
                        @csrf
                        {{-- Spam honeypot: leave empty. Bots often fill this; humans never see it. --}}
                        <div class="contact-hp" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
                        </div>
                        <input type="hidden" name="_form_started" value="{{ encrypt(now()->timestamp) }}">
                        <div class="row gy-2 gy-lg-3">

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="name" class="type-text" type="text" placeholder="Your Name" required value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="location" class="type-text" type="text" placeholder="Location" required value="{{ old('location') }}">
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="email" class="type-email" type="email" placeholder="Email Address" required value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="input-wrapper">
                                    <input name="phone" class="type-phone" type="tel" placeholder="Phone" required value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="input-wrapper">
                                    <input name="subject" class="type-text" type="text" placeholder="Enter Your Subject" required value="{{ old('subject') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <textarea name="description" class="contact-textarea" rows="4" placeholder="Type Your Message Here" required>{{ old('description') }}</textarea>
                            </div>

                            @if(config('services.recaptcha.site_key'))
                            <div class="col-12">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                                @error('g-recaptcha-response')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                        </div>

                        <button type="submit" class="heroBtn contact-from-submit-btn" id="contact-submit-btn">
                            <span class="btn-label">Submit</span>
                            <span class="btn-loader d-none" aria-hidden="true">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Submitting...
                            </span>
                        </button>
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



    @if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <script>
      (function () {
        var form = document.getElementById('contact-us-form');
        var btn = document.getElementById('contact-submit-btn');
        if (!form || !btn) return;

        var label = btn.querySelector('.btn-label');
        var loader = btn.querySelector('.btn-loader');
        var hasRecaptcha = {{ config('services.recaptcha.site_key') ? 'true' : 'false' }};

        form.addEventListener('submit', function (e) {
          if (hasRecaptcha && typeof grecaptcha !== 'undefined' && !grecaptcha.getResponse()) {
            e.preventDefault();
            alert('Please complete the CAPTCHA and try again.');
            return;
          }

          if (btn.disabled) {
            e.preventDefault();
            return;
          }

          btn.disabled = true;
          btn.classList.add('is-submitting');
          if (label) label.classList.add('d-none');
          if (loader) loader.classList.remove('d-none');
        });
      })();
    </script>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- add js file -->
    <script src="./frontend/js/script_new.js"></script>

    {{-- Live Chat Support Widget --}}
    <script>
      (function() {
        var script = document.createElement('script');
        script.src = 'https://live-chat-csme.netlify.app/widget-loader.js';
        script.async = true;
        script.dataset.eventId = 'd934e068-fae5-421a-b079-8ca3c85bc0bc';
        script.dataset.baseUrl = 'https://live-chat-csme.netlify.app';
        document.head.appendChild(script);
      })();
    </script>
</body>

</html>
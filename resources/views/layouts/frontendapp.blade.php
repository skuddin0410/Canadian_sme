<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="icon" href="{{asset('images/favicon.png')}}" type="image/x-icon" /> -->
    <link rel="icon" href="{{asset('eventzen-fav-icon.png')}}" type="image/x-icon" />

    <title>@yield('title', config('app.name'))</title>

    {{-- SEO slots (optional) --}}
    @hasSection('meta')
        @yield('meta')
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}?v={{ time() }}">

    {{-- Vite (Laravel 10/11 default) --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Page Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- add js file -->
    <script src="{{asset('frontend/js/script.js')}}"></script>

    {{-- Global time localizer: converts server-rendered Toronto times to the visitor's local timezone --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Convert time elements (h:mm AM/PM)
        document.querySelectorAll('time.local-time').forEach(function(el) {
            var iso = el.getAttribute('datetime');
            if (!iso) return;
            var d = new Date(iso);
            if (isNaN(d.getTime())) return;
            el.textContent = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
        });

        // Convert short date elements (Mon DD, YYYY)
        document.querySelectorAll('time.local-date').forEach(function(el) {
            var iso = el.getAttribute('datetime');
            if (!iso) return;
            var d = new Date(iso);
            if (isNaN(d.getTime())) return;
            el.textContent = d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        });

        // Convert full date elements (Month DD, YYYY)
        document.querySelectorAll('time.local-date-full').forEach(function(el) {
            var iso = el.getAttribute('datetime');
            if (!iso) return;
            var d = new Date(iso);
            if (isNaN(d.getTime())) return;
            el.textContent = d.toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    @stack('scripts')
</body>
</html>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon" />
    <title>explore events</title>
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

    <!-- explore event start -->
    <section class="explore-event-page section">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                <h2>Explore Events</h2>
                <div class="explore-event-page-search">
                    <!-- <input type="search" placeholder="Search Events">
                    <button type="button"><img src="./images/search.png" alt=""></button> -->
                    <form class="explore-event-page-search" method="GET" action="{{ route('front.allEvents') }}">
                        <input type="hidden" name="tab" value="{{ $tab ?? 'ongoing' }}">
                        <input type="search" name="q" placeholder="Search Events" value="{{ $q ?? '' }}">
                        <button type="submit">
                            <img src="{{ asset('images/search.png') }}" alt="">
                        </button>
                    </form>
                </div>
            </div>
            <div class="explore-event-page-row-wrapper">
                <ul class="explore-event-ul">
                    <li><button class="tab-btn {{ ($tab ?? 'ongoing') === 'ongoing' ? 'active' : '' }}" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab1">Ongoing</button></li>
                    <li><button class="tab-btn {{ ($tab ?? 'upcoming') === 'upcoming' ? 'active' : '' }}" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab2">Upcoming</button></li>
                    <li><button class="tab-btn {{ ($tab ?? 'past') === 'past' ? 'active' : '' }}" data-group="explore-event-tab-group"
                            data-target="explore-event-tab-group-tab3">Past</button></li>
                </ul>
                <div id="explore-event-tab-group-tab1" class="tab-content {{ ($tab ?? 'ongoing') === 'ongoing' ? 'active' : '' }}">
                    <div class="row explore-event-page-row">
                        @forelse($ongoing as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No ongoing events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <!-- {{ $ongoing->appends(request()->query())->links() }} -->
                        {{ $ongoing->appends(['q' => $q, 'tab' => 'ongoing'])->links() }}
                    </div>
                </div>
                <div id="explore-event-tab-group-tab2" class="tab-content {{ ($tab ?? 'upcoming') === 'upcoming' ? 'active' : '' }}">
                    <!-- Lorem ipsum dolor sit, amet consectetur adipisicing elit. Illum, minus? -->
                    <div class="row explore-event-page-row">
                        @forelse($upcoming as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No upcoming events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <!-- {{ $upcoming->appends(request()->query())->links() }} -->
                        {{ $upcoming->appends(['q' => $q, 'tab' => 'upcoming'])->links() }}
                    </div>
                </div>
                <div id="explore-event-tab-group-tab3" class="tab-content {{ ($tab ?? 'past') === 'past' ? 'active' : '' }}">
                    <!-- Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem libero aspernatur, sunt aperiam quis tempore saepe voluptate laboriosam? Nesciunt, esse. -->
                    <div class="row explore-event-page-row">
                        @forelse($past as $event)
                            <div class="col-md-6 col-lg-4">
                                <a href="{{ route('front.events', $event->slug) }}" class="explore-event-page-col">
                                    <figure>
                                        <img
                                        src="{{ $event->photo?->file_path ? asset($event->photo->file_path) : asset('images/default.png') }}"
                                        alt="{{ $event->title }}"
                                        >
                                    </figure>
                                    <figcaption>
                                        <h3>{{ $event->title }}</h3>
                                        <h6 class="location">{{ $event->location }}</h6>
                                        <span class="date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($event->end_date ?? $event->start_date)->format('M d, Y') }}
                                        </span>
                                    </figcaption>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>No past events found.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <!-- {{ $past->appends(request()->query())->links() }} -->
                        {{ $past->appends(['q' => $q, 'tab' => 'past'])->links() }}
                    </div> 
                </div>
            </div>
        </div>
    </section>
    <!-- explore event end -->

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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.tab-btn[data-group="explore-event-tab-group"]');

        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-target'); // explore-event-tab-group-tab1/2/3

                let tab = 'ongoing';
                if (target === 'explore-event-tab-group-tab2') tab = 'upcoming';
                if (target === 'explore-event-tab-group-tab3') tab = 'past';

                // Keep existing query params like q, but update tab
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);

                // Optional: when switching tabs, reset other paginations
                url.searchParams.delete('ongoing_page');
                url.searchParams.delete('upcoming_page');
                url.searchParams.delete('past_page');

                window.history.replaceState({}, '', url.toString());
            });
        });
    });
    </script>

</body>

</html>
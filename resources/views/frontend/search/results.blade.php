@extends('layouts.frontendapp')

@section('title', 'Search Results')

@section('content')

<style>
    .exhibitor{
        padding:10px !important;
    }
</style>

    <div class="container py-3">
        @if (
            $schedules->count() === 0 &&
                $exhibitors->count() === 0 &&
                $speakers->count() === 0 &&
                $sponsors->count() === 0 &&
                $attendees->count() === 0)
            <div class="text-center py-5">
                <h3>No Results Found</h3>
                <p class="text-muted">
                    We couldn't find anything matching "<strong>{{ request('q') }}</strong>".
                </p>

                <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">
                    Go Back
                </a>
            </div>
        @else
            <h2 class="text-center">
                Search Results for: <strong>{{ request('q') }}</strong>
            </h2>



            {{-- ============================================
        1. SCHEDULES
    ============================================= --}}
            @if ($schedules->count())
                <section class="schedule mb-5" id="schedule">
                    <h3 class="mb-3">Sessions</h3>

                    <div class="attendee-box d-flex flex-column">
                        @foreach ($schedules as $schedule)
                            <div class="attendee-card shadow">

                                <div class="attendee-card-box">
                                    <div class="attendee-profile" style="background:#f5f5f5;">
                                        <span class="small-heading-blue">
                                            {{ $schedule->start_time?->format('d') }}
                                        </span>
                                    </div>

                                    <div class="abc">
                                        <span class="blue-text-18">Session</span>
                                        <span class="small-heading-black fw-semibold">
                                            {{ truncateString($schedule->title, 40) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="blue-text-18">Track</span>
                                    <span class="small-heading-black fw-semibold">{{ $schedule->track }}</span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Speaker</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ $schedule->speakers->pluck('name')->join(', ') ?: 'NA' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Time</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ $schedule->start_time?->format('h:i A') }} -
                                        {{ $schedule->end_time?->format('h:i A') }}
                                    </span>
                                </div>

                                <div>
                                    <a class="view-more d-flex align-items-center gap-2"
                                        href="{{ route('session', $schedule->slug) }}">
                                        View More
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>


                </section>
            @endif





            {{-- ============================================
        2. EXHIBITORS
    ============================================= --}}
            @if ($exhibitors->count())
                <section class="exhibitor mb-2">
                    <h3 class="mb-3">Exhibitors</h3>

                    <div class="attendee-box d-flex flex-column">
                        @foreach ($exhibitors as $exhibitor)
                            <div class="attendee-card shadow">

                                <div class="attendee-card-box">
                                    <div class="attendee-profile">
                                        @if ($exhibitor->contentIconFile?->file_path)
                                            <img src="{{ $exhibitor->contentIconFile->file_path }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @else
                                            <span class="small-heading-blue">{{ shortenName($exhibitor->name) }}</span>
                                        @endif
                                    </div>

                                    <div class="abc">
                                        <span class="blue-text-18">Exhibitor</span>
                                        <span class="small-heading-black fw-semibold">
                                            {{ truncateString($exhibitor->name, 25) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="blue-text-18">Booth No.</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ $exhibitor->booth ?? 'NA' }}
                                    </span>
                                </div>

                                <div>
                                    <a class="view-more d-flex align-items-center gap-2"
                                        href="{{ route('exhibitor', $exhibitor->slug) }}">
                                        View More
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>


                </section>
            @endif



            @if ($speakers->count())
                <section class="exhibitor mb-2">
                    <h3 class="mb-3">Speakers</h3>

                    <div class="attendee-box d-flex flex-column">
                        @foreach ($speakers as $speaker)
                            <div class="attendee-card shadow">

                                <div class="attendee-card-box">
                                    <div class="attendee-profile">
                                        @if ($speaker->photo?->file_path)
                                            <img src="{{ $speaker->photo->file_path }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @else
                                            <img src="{{ asset('frontend/images/speaker-1.png') }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @endif
                                    </div>

                                    <div class="abc">
                                        <span class="blue-text-18">Full Name</span>
                                        <span class="small-heading-black fw-semibold">
                                            {{ truncateString($speaker->full_name, 18) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="blue-text-18">Company</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ truncateString($speaker->company, 25) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Designation</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ truncateString($speaker->designation, 20) }}
                                    </span>
                                </div>

                                <div>
                                    <a class="view-more d-flex align-items-center gap-2"
                                        href="{{ route('speaker', $speaker->slug) }}">
                                        View More
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>


                </section>
            @endif




            @if ($sponsors->count())
                <section class="exhibitor mb-2">
                    <h3 class="mb-3">Sponsors</h3>

                    <div class="attendee-box d-flex flex-column">
                        @foreach ($sponsors as $sponsor)
                            <div class="attendee-card shadow">

                                <div class="attendee-card-box">
                                    <div class="attendee-profile">
                                        @if ($sponsor->logo?->file_path)
                                            <img src="{{ $sponsor->logo->file_path }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @else
                                            <img src="{{ asset('frontend/images/sponsor-1.png') }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @endif
                                    </div>

                                    <div class="abc">
                                        <span class="blue-text-18">Sponsor Name</span>
                                        <span class="small-heading-black fw-semibold">
                                            {{ truncateString($sponsor->name, 20) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="blue-text-18">Category</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ $sponsor->category->name ?? 'NA' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Type</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ ucfirst($sponsor->type ?? 'General') }}
                                    </span>
                                </div>

                                <div>
                                    <a class="view-more d-flex align-items-center gap-2"
                                        href="{{ route('sponsor', $sponsor->slug) }}">
                                        View More
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>


                </section>
            @endif


            @if ($attendees->count())
                <section class="exhibitor mb-2" >
                    <h3 class="mb-3">Attendees</h3>

                    <div class="attendee-box d-flex flex-column">
                        @foreach ($attendees as $attendee)
                            <div class="attendee-card shadow">

                                <div class="attendee-card-box">
                                    <div class="attendee-profile">
                                        @if ($attendee->photo?->file_path)
                                            <img src="{{ $attendee->photo->file_path }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                        @else
                                            <span class="small-heading-blue">{{ shortenName($attendee->name) }}</span>
                                        @endif
                                    </div>

                                    <div class="abc">
                                        <span class="blue-text-18">Full Name</span>
                                        <span class="small-heading-black fw-semibold">
                                            {{ truncateString($attendee->full_name, 14) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="blue-text-18">Company</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ truncateString($attendee->company, 25) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Job Title</span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ truncateString($attendee->designation, 20) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="blue-text-18">Email ID</span>
                                    <span class="small-heading-black fw-semibold">{{ $attendee->email }}</span>
                                </div>

                                <div>
                                    @if (!empty($attendee->slug))
                                        <a class="view-more d-flex align-items-center gap-2"
                                            href="{{ route('profile', $attendee->slug) }}">
                                            View More
                                        </a>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>


                </section>
            @endif

    </div>
    @endif

@endsection

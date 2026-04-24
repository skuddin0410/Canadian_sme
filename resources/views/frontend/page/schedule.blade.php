@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection

@section('content')
    <!-- Schedule -->
    <section class="schedule">
        <div class="container">
            <div class="text-center">
                <span class="small-heading-blue">Scheduled Event</span>
                <h2 class="h2-black d-inline">Our Events Scheduled Plan</h2>
                <p class="text-secondary mt-3">
                    Like previous year this year we are arranging world marketing summit
                </p>
            </div>

            <div class="schedule-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($schedules))
                    @foreach($schedules as $schedule)
                        <div class="schedule-card shadow">
                            <div class="d-flex align-items-center gap-4 date-col">
                                <div class="pe-4 pe-xxl-5 border-sm-end">
                                    <span class="blue-text-18 mb-2">
                                        @if(!empty($schedule->start_time))
                                            <time class="local-date" datetime="{{ $schedule->start_time->toIso8601String() }}">{{ $schedule->start_time->format('M d, Y') }}</time>
                                        @else
                                            -
                                        @endif
                                    </span>
                                    <span class="small-heading-black fw-semibold">
                                        @if(!empty($schedule->start_time))
                                            <time class="local-time" datetime="{{ $schedule->start_time->toIso8601String() }}">{{ $schedule->start_time->format('h:i A') }}</time>
                                        @endif
                                        - 
                                        @if(!empty($schedule->end_time))
                                            <time class="local-time" datetime="{{ $schedule->end_time->toIso8601String() }}">{{ $schedule->end_time->format('h:i A') }}</time>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="black-text-18 mb-2">{{ $schedule->track ?? '' }}</span>
                                <span class="small-heading-black fw-semibold">{{ $schedule->title ?? '' }}</span>
                            </div>
                            <div>
                                <span class="black-text-18 mb-2">Speaker</span>
                                <span class="small-heading-black fw-semibold">  
                                    @if($schedule->speakers->isNotEmpty())
                                        {{ $schedule->speakers->pluck('name')->join(', ') }}
                                    @else
                                        No speakers assigned
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="black-text-18 mb-2">Venue</span>
                                <span class="small-heading-black fw-semibold">{{ $schedule->location ?? '' }}</span>
                            </div>
                            <div>
                                <a class="view-more position-relative d-flex align-items-center gap-2" 
                                   href="{{ route('session', ['slug' => $schedule->slug, 'event' => $event->slug ?? '']) }}">
                                    View More
                                </a>
                            </div>
                        </div>
                    @endforeach
                     <div class="d-flex justify-content-center mt-4 ">
                         <div class="mt-4">
                        {{ $schedules->links() }}
                        </div>
                    </div>
                @endif
            </div>

           
        </div>
    </section>
@endsection
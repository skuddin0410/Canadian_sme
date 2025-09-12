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
                                        {{ !empty($schedule->start_time) ? $schedule->start_time?->format('M d, Y') : '-' }}
                                    </span>
                                    <span class="small-heading-black fw-semibold">
                                        {{ !empty($schedule->start_time) ? $schedule->start_time?->format('h:i A'): '' }}
                                        - 
                                        {{ !empty($schedule->start_time) ? $schedule->end_time?->format('h:i A') : '' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="black-text-18 mb-2">Agenda</span>
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
                                   href="{{ route('session',$schedule->id) }}">
                                    View More
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- View More Button at Bottom -->
            <div class="d-flex justify-content-center mt-4">
                <a href="{{route('schedule-index')}}" class="heroBtn btn-long">
                    View More
                </a>
            </div>
        </div>
    </section>
    <!-- Schedule end -->

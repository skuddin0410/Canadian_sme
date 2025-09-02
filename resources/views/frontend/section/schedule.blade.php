    <!-- Schedule -->
    <section class="schedule">
        <div class="container">
                <section class="py-5">
                  <div class="container">
                    <div class="row justify-content-center text-center">
                      <div class="col-lg-10 col-xl-8">
                        <span class="small-heading-blue d-inline-block mb-2">Scheduled Event</span>

                        <h2 class="mb-2">Our Events Scheduled Plan</h2>

                        <h3 class="fs-5 text-secondary fw-normal mb-4">
                          Like previous year this year we are arrnaging world marketing summit
                        </h3>
                      </div>
                    </div>
                  </div>
                </section>

            <div class="schedule-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($schedules))
                    @foreach($schedules as $schedule)

                    <div class="schedule-card shadow">
                        <div class="d-flex align-items-center gap-4">
                            <div class="pe-4 pe-xxl-5 border-sm-end">
                                <span class="blue-text-18 mb-2">{{!empty($schedule->start_time) ? $schedule->start_time?->format('M d, Y') : '-'}}</span>
                                <span class="small-heading-black fw-semibold">{{ !empty($schedule->start_time) ? $schedule->start_time?->format('h:i A'): '' }} - {{!empty($schedule->start_time) ? $schedule->end_time?->format('h:i A') : '' }}</span>
                            </div>
                        </div>
                        <div class="">
                            <span class="black-text-18 mb-2">Agenda</span>
                            <span class="small-heading-black fw-semibold">{{$schedule->title ?? ''}}</span>
                        </div>
                        <div class="">
                            <span class="black-text-18 mb-2">JSpeaker</span>
                            <span class="small-heading-black fw-semibold">Ashton Porter</span>
                        </div>
                        <div class="">
                            <span class="black-text-18 mb-2">Venue</span>
                            <span class="small-heading-black fw-semibold">{{$schedule->location ?? ''}}</span>
                        </div>
                        <div class="">
                            <a class="view-more position-relative d-flex
                            align-items-center gap-2" href="javascript:void()">
                                View More
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="d-flex justify-content-center mt-4 d-xl-none">
                <button class="heroBtn btn-long">View More</button>
            </div>
        </div>
    </section>
    <!-- Schedule end -->
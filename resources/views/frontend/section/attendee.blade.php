    <!-- attendee -->
    <section class="attendee">
        <div class="container">
            <span class="small-heading-blue">Attendee</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Meeting Attendee List with Designations Included
                </h2>
                <div class="d-none d-xl-block">
                    <button class="heroBtn btn-long">View More</button>
                </div>
            </div>

            <div class="attendee-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($attendees))
                    @foreach($attendees as $attendee)
                        <div class="attendee-card shadow">
                            <div class="attendee-card-box">
                                <div class="attendee-profile">
                                    @if(!empty($attendee->photo) &&  $attendee->photo->file_path)
                                    <img src="{{$attendee->photo->file_path}}" alt="">
                                    @else
                                    <span class="small-heading-blue mb-0">{{shortenName($attendee->name, $attendee->lastname)}}</span>
                                    @endif
                                </div>
                                <div class="abc">
                                    <span class="blue-text-18 mb-2">Full Name</span>
                                    <span class="small-heading-black fw-semibold">{{$attendee->full_name ?? ''}}</span>
                                </div>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Company</span>
                                <span class="small-heading-black fw-semibold">{{$attendee->company ?? 'NA'}}</span>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Job Title</span>
                                <span class="small-heading-black fw-semibold">{{$attendee->designation ?? 'NA'}}</span>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Email ID</span>
                                <span class="small-heading-black fw-semibold">{{$attendee->email ?? ''}}</span>
                            </div>
                            <div>
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
    <!-- attendee end -->
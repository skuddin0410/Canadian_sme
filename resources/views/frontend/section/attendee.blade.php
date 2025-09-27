    <!-- attendee -->
    <section class="attendee">
        <div class="container">
            <span class="small-heading-blue">Attendee</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Meeting Attendee List with Designations Included
                </h2>
                @if(request()->is('/'))
                <div class="d-none d-xl-block">
                    <a class="heroBtn btn-long" href="{{ route('profile-index') }}">
                        View More
                    </a>
                </div>
                @endif
                @if(request()->is('profile*'))
                <div class="d-flex justify-content-end mb-2">
                  <a href="javascript:history.back()" class="heroBtn ms-md-5">Back</a>
                </div>
                @endif
            </div>

            <div class="attendee-box mt-4 mt-lg-5 d-flex flex-column">
                @if (!empty($attendees))
                    @foreach ($attendees as $attendee)
                        <div class="attendee-card shadow">
                            <div class="attendee-card-box">
                                <div class="attendee-profile">
                                    @if (!empty($attendee->photo) && !empty($attendee->photo->file_path))
                                        <img src="{{ $attendee->photo->file_path }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">
                                    @else
                                        <span class="small-heading-blue mb-0">{{ shortenName($attendee->name) }}</span>
                                    @endif
                                </div>


                                <div class="abc">
                                    <span class="blue-text-18 mb-2">Full Name</span>
                                    <span
                                        class="small-heading-black fw-semibold">{{ $attendee->full_name ? truncateString($attendee->full_name, 13) : '' }}</span>
                                </div>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Company</span>
                                <span
                                    class="small-heading-black fw-semibold">{{ $attendee->company ? truncateString($attendee->company, 25) : '' }}</span>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Job Title</span>
                                <span
                                    class="small-heading-black fw-semibold">{{ $attendee->designation ? truncateString($attendee->designation, 20) : '' }}</span>
                            </div>
                            <div class="">
                                <span class="blue-text-18 mb-2">Email ID</span>
                                <span class="small-heading-black fw-semibold">{{ $attendee->email ?? '' }}</span>
                            </div>
                            <div>
                                @if (!empty($attendee->slug))
                                <a class="view-more position-relative d-flex align-items-center gap-2"
                                     href="{{ route('profile', ['slug' => $attendee->slug]) }}">
                                    View More
                                </a>
                                @endif
                            </div>

                        </div>
            
            @endforeach
            @endif
        </div>
        <div class="d-flex justify-content-center mt-4 d-xl-none">
            <a class="heroBtn btn-long" href="{{ route('profile-index') }}">
                View More
            </a>
        </div>
        </div>
    </section>
    <!-- attendee end -->

    <!-- attendee -->
    <section class="attendee">
        <div class="container">
            <span class="small-heading-blue">Attendee</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Meeting Attendee List with Designations Included
                </h2>
                <div class="d-none d-xl-block">
                    <a class="view-more position-relative d-flex
                        align-items-center gap-2" href="{{route('profile-index')}}">
                         View More
                    </a>
                </div>
            </div>

            <div class="attendee-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($attendees))
                    @foreach($attendees as $attendee)
                        <div class="attendee-card shadow">
                            <div class="attendee-card-box">
                                
                                <div class="text-left mb-2">
                                   <label for="profileImageInput">
                                   <img id="profileImagePreview" 
                                   src="{{!empty($attendee->photo) ? $attendee->photo->file_path : ''}}" 
                                   class="border border-2" 
                                   style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;">
                                   </label>
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
                                align-items-center gap-2" href="{{route('profile', $attendee->id)}}">
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
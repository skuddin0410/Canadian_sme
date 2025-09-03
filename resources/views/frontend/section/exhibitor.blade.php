 <!-- exhibitor -->
    <section class="exhibitor">
        <div class="container">
            <span class="small-heading-blue">Exhibitors</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Exhibitors Showcasing Innovation Across All Industries
                </h2>
                <div class="d-none d-xl-block">
                    <button class="heroBtn btn-long">View More</button>
                </div>
            </div>

            <div class="exhibitor-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($exhibitors))
                @foreach($exhibitors as $exhibitor)
                <div class="exhibitor-card shadow">
                    <div class="exhibitor-card-box">
                        <div class="exhibitor-profile">
                            @if(!empty($exhibitor->contentIconFile))
                            <img src="{{$exhibitor->contentIconFile->file_path}}" alt="">
                            @else
                              <span class="small-heading-blue mb-0">{{shortenName($exhibitor->name)}}</span>
                            @endif
                        </div>
                        <div class="abc">
                            <span class="blue-text-18 mb-2">Company</span>
                            <span class="small-heading-black fw-semibold">{{$exhibitor->name ?? ''}}</span>
                        </div>
                    </div>
                    <div class="">
                        <span class="blue-text-18 mb-2">Booth Number</span>
                        <span class="small-heading-black fw-semibold">{{$exhibitor->booth ?? ''}}</span>
                    </div>
                    <div class="">
                        <span class="blue-text-18 mb-2">Event Name</span>
                        <span class="small-heading-black fw-semibold">{{$session->title ?? ''}}</span>
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
    <!-- exhibitor end -->
 <!-- exhibitor -->


    <section class="exhibitor">
        <div class="container">
            <span class="small-heading-blue">Exhibitors</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Exhibitors Showcasing Innovation Across All Industries
                </h2>
                <div class="d-none d-xl-block">
                    <a class="heroBtn btn-long" href="{{route('exhibitor-index')}}">
                         View More
                    </a>
                    {{-- <button class="heroBtn btn-long">View More</button> --}}
                </div>
            </div>

            <div class="exhibitor-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($exhibitors))
                @foreach($exhibitors as $exhibitor)
                <div class="exhibitor-card shadow">
                    <div class="exhibitor-card-box">
                        <div class="exhibitor-profile">
                            @if(!empty($exhibitor->contentIconFile))
                              
  <img src="{{ $exhibitor->contentIconFile->file_path }}" 
       alt="Exhibitor Icon"
       style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">

                            @else
                              <span class="small-heading-blue mb-0">{{shortenName($exhibitor->name)}}</span>
                            @endif
                        </div>
                        <div class="abc">
                            <span class="blue-text-18 mb-2">Exhibitor 123</span>
                            <span class="small-heading-black fw-semibold">{{$exhibitor->name ? truncateString($exhibitor->name, 30) : ''}}</span>
                        </div>
                    </div>
                    <div class="">
                        <span class="blue-text-18 mb-2">Booth Number</span>
                        <span class="small-heading-black fw-semibold">{{$exhibitor->booth ?? 'NA'}}</span>
                    </div>
                   
                    <div class="">
                        <span class="blue-text-18 mb-2">Event Name</span>
                        <span class="small-heading-black fw-semibold">{{$session?->title ? truncateString($session->title, 40) : 'NA'}}</span>
                    </div>
                   
                    <div>
                        <a class="view-more position-relative d-flex
                        align-items-center gap-2" href="{{route('exhibitor',$exhibitor->slug)}}">
                            View More
                        </a>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <div class="d-flex justify-content-center mt-4 d-xl-none">
                 <a class="heroBtn btn-long" href="{{route('exhibitor-index')}}">
                         View More
                    </a>
            </div>
        </div>
    </section>
    <!-- exhibitor end -->
 
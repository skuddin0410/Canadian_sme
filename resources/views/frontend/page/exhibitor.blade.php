@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection

@section('content')

    <section class="exhibitor">
        <div class="container py-4 py-lg-5">
            <span class="small-heading-blue">Exhibitors</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Exhibitors Showcasing Innovation Across All Industries
                </h2>
                <div class="d-none d-xl-block">
                    <a class="heroBtn btn-long" href="{{url('/')}}">
                         Back
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
                              <img src="{{ $exhibitor->contentIconFile->file_path }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">

                            @else
                              <span class="small-heading-blue mb-0">{{shortenName($exhibitor->name)}}</span>
                              <img src="{{ $exhibitor->contentIconFile->file_path }}" style="display: none;">
                            @endif
                        </div>
                        <div class="abc">
                            <span class="blue-text-18 mb-2">Exhibitor</span>
                            <span class="small-heading-black fw-semibold">{{$exhibitor->name ? truncateString($exhibitor->name, 30) : ''}}</span>
                        </div>
                    </div>
                    <div class="">
                        <span class="blue-text-18 mb-2">Booth Number</span>
                        <span class="small-heading-black fw-semibold">{{$exhibitor->booth ?? 'NA'}}</span>
                    </div>
                    <div class="">
                        <span class="blue-text-18 mb-2">Event Name</span>
                        <span class="small-heading-black fw-semibold">{{$event->title ? truncateString($event->title, 40) : 'NA'}}</span>
                    </div>
                    <div>
                        <a class="view-more position-relative d-flex
                        align-items-center gap-2" href="{{route('exhibitor',$exhibitor->slug)}}">
                            View More
                        </a>
                    </div>
                </div>
                @endforeach
                <div class="d-flex justify-content-center mt-4">
                 <div class="mt-4">
                {{ $exhibitors->links() }}
                </div>
            </div>
                @endif
            </div>
            <div class="d-flex justify-content-center mt-4 d-xl-none">
                 <a class="heroBtn btn-long" href="{{route('exhibitor-index')}}">
                         View More
                    </a>
            </div>
        </div>
    </section>
@endsection
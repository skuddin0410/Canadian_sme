@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')

<div class="container py-5">
  <div class="row g-4">
    @foreach($speakers as $speaker)
      <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 p-3">
          <div class="row align-items-center">
            
            <!-- Left: Speaker Photo + Details -->
            <div class="col-md-4 text-center">
              <a href="{{ route('speaker', $speaker->slug) }}" class="text-decoration-none">
                <div class="speaker-img-box mb-3">
                  @if(!empty($speaker->photo))
                    <img src="{{ $speaker->photo->file_path }}" alt="{{ $speaker->full_name }}" 
                         class="img-fluid rounded-circle shadow" style="width: 160px; height: 160px; object-fit: cover;">
                  @else
                    <img src="{{ asset('frontend/images/speaker-1.png') }}" alt="{{ $speaker->full_name }}" 
                         class="img-fluid rounded-circle shadow" style="width: 160px; height: 160px; object-fit: cover;">
                  @endif
                </div>
              </a>
              
              <h5 class="fw-bold mb-1">{{ $speaker->full_name ? truncateString($speaker->full_name, 18) : '' }}</h5>
              <span class="speakers-title">{{ $speaker->company ?? '' }} </span>
              <p class="text-muted mb-2">{{ $speaker->designation ?? '' }}</p>

              <a href="{{ route('speaker', $speaker->slug) }}" class="btn btn-primary btn-sm px-4 rounded-pill">
                View More
              </a>
            </div>

            <!-- Right: Bio -->
            <div class="col-md-8">
              <p class="text-secondary mb-0" style="line-height: 1.6;">
                {{ $speaker->bio ?? 'No biography available.' }}
              </p>
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>
   <div class="d-flex justify-content-center mt-4 ">
         <div class="mt-4">
        {{ $speakers->links() }}
        </div>
    </div>
</div>

@endsection
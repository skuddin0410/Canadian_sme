@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')
<div class="container py-4 py-lg-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-sm-4">
          <div class="d-flex align-items-center mb-4">
            <div class="text-left mb-2" style="width:100%">
              <div class="venue-location my-4">
                <h4>{{ $title }}</h4>

                @if(!empty($content))
                  <div class="text-muted">{!! $content !!}</div>
                @else
                  <p class="text-muted mb-0">{{ $title }} not available.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      @if(!empty($sessions))
      <div class="list-group shadow-sm rounded-4 mt-3 mt-lg-0">
        <h6 class="list-group-item bg-light fw-bold py-2">Upcoming Sessions</h6>
        @forelse($sessions as $session)
          <div class="list-group-item list-group-item-action d-sm-flex d-lg-block d-xxl-flex justify-content-between align-items-start">
            <div class="me-auto">
              <div class="black-text-18 fw-medium">{{ $session->title ?? 'Untitled Session' }}</div>
              <small class="text-secondary d-block mt-2">
                <i class="fas fa-clock me-1 text-primary"></i>
                {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y h:i A') }}
              </small>
              <small class="text-secondary d-block mt-2">
                <i class="fas fa-location me-1 text-primary"></i>
                {{ $session->location ?? '' }}
              </small>
            </div>
          </div>
        @empty
          <div class="list-group-item text-muted">No sessions available</div>
        @endforelse
      </div>
      @endif

      @if($event)
        <div class="card mt-3 shadow-sm rounded-4">
          <div class="card-body text-left">
            <h6 class="fw-bold">{{ $event->title ?? 'Event' }}</h6>
            @if(!empty($event->photo) && $event->photo->file_path)
              <img src="{{ $event->photo->file_path }}" alt="Event" class="img-fluid rounded mt-2">
            @else
               <img src="{{ asset('frontend/images/banner.png') }}" alt="Event" class="img-fluid rounded mt-2">
            @endif
            <p class="fw-bold text-muted mt-2">{{ $event->location ?? '' }}</p>
            <p class="text-muted mt-2">{!! $event->description ?? '' !!}</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

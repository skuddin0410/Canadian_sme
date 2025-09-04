@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')

<div class="container py-5">
  <div class="row justify-content-center">
    <!-- Profile Info -->
    <div class="col-md-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-4">
            @if(!empty($attendee->photo) && $attendee->photo->file_path)
              <img src="{{ $attendee->photo->file_path }}" 
                   alt="{{ $attendee->full_name }}" 
                   class="rounded-circle me-3 border border-3 border-primary" width="100" height="100">
            @else
              <img src="https://via.placeholder.com/100" 
                   alt="Profile Picture" 
                   class="rounded-circle me-3 border border-3 border-primary" width="100" height="100">
            @endif

            <div>
              <h4 class="mb-1">{{ $attendee->full_name ?? 'N/A' }}</h4>
              <span class="badge bg-success">
                {{ $attendee->roles->pluck('name')->join(', ') }}
              </span>
            </div>
          </div>

          <!-- Profile Info Grid -->
          <div class="row g-3">
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-envelope me-2 text-primary"></i>Email</p>
              <p class="fw-semibold">{{ $attendee->email ?? 'N/A' }}</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-phone me-2 text-primary"></i>Phone</p>
              <p class="fw-semibold">{{ $attendee->phone ?? 'N/A' }}</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-briefcase me-2 text-primary"></i>Position</p>
              <p class="fw-semibold">{{ $attendee->designation ?? 'N/A' }}</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-building me-2 text-primary"></i>Company</p>
              <p class="fw-semibold">{{ $attendee->company ?? 'N/A' }}</p>
            </div>
            <div class="col-sm-12">
              <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location</p>
              <p class="fw-semibold">{{ $attendee->location ?? 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Session/Event Info -->
    <div class="col-md-4">
      <div class="list-group shadow-sm rounded-4">
        @if($session)
          <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
              <div class="fw-bold">{{ $session->title ?? 'Session' }}</div>
              <small class="text-muted"><i class="fas fa-clock me-1 text-primary"></i>
                {{ $session->start_time->format('h:i A') }} – {{ $session->end_time ? $session->end_time->format('h:i A') : 'TBD' }}
              </small>
            </div>
            <span class="badge bg-primary rounded-pill align-self-center">{{ $session->location ?? 'Hall' }}</span>
          </div>
        @else
          <div class="list-group-item text-muted">No upcoming session</div>
        @endif
      </div>

      @if($event)
        <div class="card mt-3 shadow-sm rounded-4">
          <div class="card-body text-center">
            <h6 class="fw-bold">{{ $event->title ?? 'Event' }}</h6>
            @if(!empty($event->photo) && $event->photo->file_path)
              <img src="{{ $event->photo->file_path }}" alt="Event" class="img-fluid rounded mt-2">
            @endif
            <p class="text-muted mt-2">{{ $event->description ?? '' }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection

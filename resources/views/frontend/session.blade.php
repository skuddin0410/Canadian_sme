@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-lg-8">

      <!-- Session Details Card -->
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">

          <!-- Title + Badge -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">{{ $session->title ?? 'Session Title' }}</h3>
            <span class="badge bg-success">{{ $session->location ?? 'TBD' }}</span>
          </div>

          <!-- Meta Info -->
          <ul class="list-unstyled mb-4">
            <li class="mb-2">
              <i class="fas fa-user me-2 text-primary"></i>
              <strong>Speakers:</strong> 
              @if($session->speakers && $session->speakers->count())
                {{ $session->speakers->pluck('full_name')->join(', ') }}
              @else
                N/A
              @endif
            </li>
            <li class="mb-2">
              <i class="fas fa-clock me-2 text-primary"></i>
              <strong>Time:</strong> 
              {{ $session->start_time ? $session->start_time->format('h:i A') : 'TBD' }} – 
              {{ $session->end_time ? $session->end_time->format('h:i A') : 'TBD' }}
            </li>
            <li>
              <i class="fas fa-calendar me-2 text-primary"></i>
              <strong>Date:</strong> 
              {{ $session->start_time ? $session->start_time->format('F d, Y') : 'TBD' }}
            </li>
          </ul>

          <!-- Description -->
          <h5 class="mb-2">About this Session</h5>
          <p class="text-muted">
            {{ $session->description ?? 'No description available.' }}
          </p>

        </div>
      </div>

    </div>

    <div class="col-lg-4">

      <!-- Speakers -->
      @if($session->speakers && $session->speakers->count())
        <div class="card shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white fw-bold">
            <i class="fas fa-users me-2 text-primary"></i>Speakers
          </div>
          <ul class="list-group list-group-flush">
            @foreach($session->speakers as $speaker)
              <li class="list-group-item d-flex align-items-center">
                <img src="{{ $speaker->photo->file_path ?? 'https://via.placeholder.com/40' }}" 
                     class="rounded-circle me-2" 
                     alt="{{ $speaker->full_name ?? 'Speaker' }}" 
                     width="40" height="40">
                <div>
                  <strong>{{ $speaker->full_name ?? 'N/A' }}</strong><br>
                  <small class="text-muted">{{ $speaker->designation ?? '' }}</small>
                </div>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Event Info -->
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

@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')
<div class="container py-4 py-lg-5">
  <div class="d-flex justify-content-end mb-2">
    <a href="javascript:history.back()" class="heroBtn ms-md-5">Back</a>
  </div>
  <div class="row">
    <div class="col-lg-8">

      <!-- Session Details Card -->
      <div class="card shadow-sm rounded-4">

          @if($session->photo && $session->photo->file_path)
        <div style="width: 100%; height: 400px; overflow: hidden; border-radius: 8px;">
              <img src="{{ !empty($session->photo) ? $session->photo->file_path :'' }}" 
                   class="border" 
                   style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" />
        </div>
        @endif

        <div class="card-body p-3 p-sm-4">

          <!-- Title + Badge -->
          <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">{{ $session->title ?? 'Session Title' }}</h3>
            <span class="badge bg-success px-2 mt-2 mt-sm-0">{{ $session->location ?? 'TBD' }}</span>
          </div>

          <!-- Meta Info -->
          <ul class="list-unstyled mb-4">
            <li class="mb-3 text-secondary black-text-18 fw-medium">
              <i class="fas fa-user me-2 text-primary"></i>
              <strong class="black-text-18 fw-medium">Speakers:</strong> 
              @if($session->speakers && $session->speakers->count())
                {{ $session->speakers->pluck('full_name')->join(', ') }}
              @else
                N/A
              @endif
            </li>
            <li class="mb-3 text-secondary black-text-18 fw-medium">
              <i class="fas fa-clock me-2 text-primary"></i>
              <strong class="black-text-18 fw-medium">Time:</strong> 
              {{ $session->start_time ? $session->start_time->format('h:i A') : 'TBD' }} – 
              {{ $session->end_time ? $session->end_time->format('h:i A') : 'TBD' }}
            </li>
            <li class="text-secondary black-text-18 fw-medium">
              <i class="fas fa-calendar me-2 text-primary"></i>
              <strong class="black-text-18 fw-medium">Date:</strong> 
              {{ $session->start_time ? $session->start_time->format('F d, Y') : 'TBD' }}
            </li>

            <li class="text-secondary black-text-18 fw-medium">
              <i class="fas fa-location me-2 text-primary"></i>
              <strong class="black-text-18 fw-medium">Location:</strong> 
              {{ $session->location ? $session->location : '' }}
            </li>
            <li class="text-secondary black-text-18 fw-medium">
               <i class="fas fa-music me-2 text-primary"></i>
              <strong class="black-text-18 fw-medium">Track:</strong> 
              {{ $session->track ? $session->track : '' }}
            </li>
          </ul>

          <!-- Description -->
          <h5 class="mb-2 small-heading-black fw-bold">About this Session: </h5>
          <p class="text-secondary">
            {{ $session->description ?? '' }}
          </p>


          <h5 class="mb-2 small-heading-black fw-bold">Key Note: </h5>
          <p class="text-secondary">
            {{ $session->keynote ?? '' }}
          </p>

          <h5 class="mb-2 small-heading-black fw-bold">Demoes: </h5>
          <p class="text-secondary">
            {{ $session->demoes ?? '' }}
          </p>

          <h5 class="mb-2 small-heading-black fw-bold">Panels: </h5>
          <p class="text-secondary">
            {{ $session->panels ?? '' }}
          </p>

        </div>
      </div>

    </div>

    <div class="col-lg-4">

      <!-- Speakers -->
      @if($session->speakers && $session->speakers->count())
        <div class="card shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white mb-2 small-heading-black fw-bold">
            <i class="fas fa-users me-2 text-primary"></i>Speakers
          </div>
          <ul class="list-group list-group-flush">
            @foreach($session->speakers as $speaker)
              <li class="list-group-item d-flex align-items-center">

                @if($speaker->photo && $speaker->photo->file_path)
                 <a href="{{ route('speaker', $speaker->slug) }}" class="text-decoration-none">
                <img src="{{ $speaker->photo->file_path ?? '' }}" 
                     class="rounded-circle me-2" 
                     alt="{{ $speaker->full_name ?? 'Speaker' }}" 
                     width="40" height="40"></a>
                @endif
                <div>
                  <strong class="black-text-18 fw-medium">{{ $speaker->full_name ?? 'N/A' }}</strong><br>
                  <small class="text-secondary">{{ $speaker->designation ?? '' }}</small>
                </div>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

    @if($session->exhibitors && $session->exhibitors->count() > 0)
        <div class="card shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white mb-2 small-heading-black fw-bold">
            <i class="fas fa-building me-2 text-primary"></i>Exhibitors
          </div>
          <ul class="list-group list-group-flush">
            @foreach($session->exhibitors as $exhibitor)
              <li class="list-group-item d-flex align-items-center">
              <a class="text-decoration-none" href="{{route('exhibitor',$exhibitor->slug)}}">
                @if($exhibitor->photo && $exhibitor->photo->file_path)
                <img src="{{ $exhibitor->photo->file_path ?? '' }}" 
                     class="rounded-circle me-2" 
                     alt="{{ $exhibitor->name ?? 'exhibitor' }}" 
                     width="40" height="40">
                @endif     
                <div>
                  <strong class="black-text-18 fw-medium">{{ $exhibitor->name ?? 'N/A' }}</strong><br>
                </div>
              </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

        @if($session->sponsors && $session->sponsors->count() > 0)
        <div class="card shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white mb-2 small-heading-black fw-bold">
            <i class="fas fa-handshake me-2 text-primary"></i>Sponsors
          </div>
          <ul class="list-group list-group-flush">
            @foreach($session->sponsors as $sponsor)
              <li class="list-group-item d-flex align-items-center">
               <a href="{{ route('sponsor', $sponsor->slug) }}" class="text-decoration-none">
                @if($sponsor->photo && $sponsor->photo->file_path)
                <img src="{{ $sponsor->photo->file_path ?? '' }}" 
                     class="rounded-circle me-2" 
                     alt="{{ $sponsor->name ?? 'sponsor' }}" 
                     width="40" height="40">
                @endif     
                <div>
                  <strong class="black-text-18 fw-medium">{{ $sponsor->name ?? 'N/A' }}</strong><br>
                </div>
              </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Event Info -->
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
            <p class="text-muted mt-2">{{ $event->description ?? '' }}</p>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>
@endsection

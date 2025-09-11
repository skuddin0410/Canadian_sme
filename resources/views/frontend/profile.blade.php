@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')

<div class="container py-4 py-lg-5">
  <div class="row justify-content-center">
    <!-- Profile Info -->
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-sm-4">
          <div class="d-flex flex-column d-sm-grid gap-2 gap-sm-4 align-items-center mb-4" style="grid-template-columns: 120px auto;">
           <div class="text-left mb-2 rounded-circle overflow-hidden border" 
           style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
    <label for="profileImageInput">
        @if(!empty($attendee->photo) && !empty($attendee->photo->file_path))
            <img id="profileImagePreview"
                 src="{{ $attendee->photo->file_path }}"
                 class="border border-2"
                 style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
        @else
            <div style="width: 120px; height: 120px; display: flex; 
                        align-items: center; justify-content: center; 
                        cursor: pointer;">
                <span class="small-heading-blue mb-0" style="font-size: 48px;">
                    {{ strtoupper(substr($attendee->name, 0, 1)) }}
                </span>
            </div>
        @endif
    </label>
</div>

            <div class="text-center text-sm-start">
              <h4 class="mb-1">{{ $attendee->full_name ?? 'N/A' }}</h4>
              <span class="badge bg-success">
                {{ $attendee->roles->pluck('name')->join(', ') }}
              </span>
            </div>
          </div>

          <!-- Profile Info Grid -->
          <div class="row g-3">
            <div class="col-sm-12 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-envelope me-2 text-primary"></i>Bio</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->bio ?? 'N/A' }}</p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-envelope me-2 text-primary"></i>Email</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->email ?? 'N/A' }}</p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-phone me-2 text-primary"></i>Phone</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->phone ?? 'N/A' }}</p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-briefcase me-2 text-primary"></i>Designation</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->designation ?? 'N/A' }}</p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-building me-2 text-primary"></i>Company</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->company ?? 'N/A' }}</p>
            </div>
               <div class="col-lg-6 mb-2">
                 <p class="mb-1 black-text-18 fw-medium"> <i class="fas fa-globe me-2 text-primary"></i>Website</p> 
                 <p class="text-secondary black-text-18 fw-medium">
              @if(!empty($attendee->website_url))
                <p>
                  <a href="{{ $attendee->website_url }}" target="_blank" class="text-primary black-text-18 fw-medium">
                     {{ $attendee->website_url }}
                  </a>
                </p>
              @endif
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $attendee->place ?? 'N/A' }}</p>
            </div>

            {{-- Social Section (only visible if gdpr_consent != 1) --}}
            @if($attendee->gdpr_consent != 1)
              <div class="col-sm-12 mt-4 mb-2">
                <h5 class="mb-1 small-heading-black fw-medium"><i class="fas fa-share-alt me-2 text-primary"></i>Social</h5>
              </div>

              <div class="col-lg-6 mb-2">
                <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-linkedin me-2 text-primary"></i>LinkedIn</p>
                <p class="fw-semibold">
                  @if(!empty($attendee->linkedin_url))
                    <a href="{{ $attendee->linkedin_url }}" target="_blank" class="text-primary black-text-18 fw-medium">
                      {{ $attendee->linkedin_url }}
                    </a>
                  @else
                    N/A
                  @endif
                </p>
              </div>

              <div class="col-lg-6 mb-2">
                <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-facebook me-2 text-primary"></i>Facebook</p>
                <p class="fw-semibold">
                  @if(!empty($attendee->facebook_url))
                    <a href="{{ $attendee->facebook_url }}" target="_blank" class="text-primary black-text-18 fw-medium">
                      {{ $attendee->facebook_url }}
                    </a>
                  @else
                    N/A
                  @endif
                </p>
              </div>

              <div class="col-lg-6 mb-2">
                <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-instagram me-2 text-primary"></i>Instagram</p>
                <p class="fw-semibold">
                  @if(!empty($attendee->instagram_url))
                    <a href="{{ $attendee->instagram_url }}" target="_blank" class="text-primary black-text-18 fw-medium">
                      {{ $attendee->instagram_url }}
                    </a>
                  @else
                    N/A
                  @endif
                </p>
              </div>

              <div class="col-lg-6 mb-2">
                <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-x-twitter me-2 text-primary"></i>Twitter</p>
                <p class="fw-semibold">
                  @if(!empty($attendee->twitter_url))
                    <a href="{{ $attendee->twitter_url }}" target="_blank" class="text-primary black-text-18 fw-medium">
                      {{ $attendee->twitter_url }}
                    </a>
                  @else
                    N/A
                  @endif
                </p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Session/Event Info -->
    <div class="col-lg-4">
      <div class="list-group shadow-sm rounded-4 mt-3 mt-lg-0">
        @if($session)
          <div class="list-group-item list-group-item-action d-xxl-flex justify-content-between align-items-start">
            <div class="me-auto">
              <div class="black-text-18 fw-medium">{{ $session->title ?? 'Session' }}</div>
              <small class="text-secondary d-block mt-2"><i class="fas fa-clock me-1 text-primary"></i>
                {{ $session->start_time->format('h:i A') }} – {{ $session->end_time ? $session->end_time->format('h:i A') : 'TBD' }}
              </small>
            </div>
            <span class="badge bg-primary rounded-pill align-self-center mt-2 mt-xxl-0 px-2">{{ $session->location ?? 'Hall' }}</span>
          </div>
        @else
          <div class="list-group-item text-muted">No upcoming session</div>
        @endif
      </div>

      @if($event)
        <div class="card mt-3 shadow-sm rounded-4">
          <div class="card-body text-start">
            <h6 class="black-text-18 fw-medium">{{ $event->title ?? 'Event' }}</h6>
            @if(!empty($event->photo) && $event->photo->file_path)
              <img src="{{ $event->photo->file_path }}" alt="Event" class="img-fluid rounded mt-2">
            @endif
            <p class="text-secondary mt-2">{{ $event->description ?? '' }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection

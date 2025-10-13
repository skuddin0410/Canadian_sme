@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection
@section('content')

<div class="container py-4 py-lg-5">
  <div class="d-flex justify-content-end mb-2">
  <a href="javascript:history.back()" class="heroBtn ms-md-5">Back</a>
</div>
  <div class="row justify-content-center">
    <!-- Company Info -->
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">

         @if($company->banner && $company->banner->file_path)
        <div style="width: 100%; height: 400px; overflow: hidden; border-radius: 8px;">
              <img src="{{ !empty($company->banner) ? $company->banner->file_path :'' }}" 
                   class="border" 
                   style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" />
        </div>
        @endif

        <div class="card-body p-3 p-sm-4">
          <div class="d-flex align-items-center mb-4">
             @if(!empty($company->logo) && !empty($company->logo->file_path))
  <div class="me-3">
    <img src="{{ $company->logo->file_path }}" 
         alt="{{ $company->name ?? 'Company Logo' }}"
         class="rounded-circle border border-3 shadow-sm"
         style="width: 120px; height: 120px; object-fit: cover; background: #f8f9fa;">
  </div>
@endif

<div>
  <h4 class="mb-2">{{ $company->name ?? 'N/A' }}</h4>
  
  <!-- Badges -->
  <div class="d-flex flex-wrap gap-2">
  
    @if($company->is_sponsor == 1 && !empty($company->type))

    <span class="badge rounded-pill px-3 py-2 text-white fs-6" 
  style="background-color: {{ typeColor($company->type) }};">
       {{ ucfirst(str_replace('-', ' ', $company->type)) }}
    </span>
    
    @endif
  </div>
</div>





          </div>

          <!-- Company Info Grid -->
          <div class="row g-3">
            <div class="col-12 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-align-left me-2 text-primary"></i>Bio</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $company->description ?? 'N/A' }}</p>
            </div>

            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-envelope me-2 text-primary"></i>Email</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $company->email ?? 'N/A' }}</p>
            </div>

            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-phone me-2 text-primary"></i>Phone</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $company->phone ?? 'N/A' }}</p>
            </div>

            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-building me-2 text-primary"></i>Sponsor</p>
                <p class="text-secondary black-text-18 fw-medium">{{ $company->name ?? 'N/A' }}</p>
              </div> 
              <div class="col-lg-6 mb-2"> 
                <p class="mb-1 black-text-18 fw-medium"> <i class="fas fa-globe me-2 text-primary"></i>Website</p> 
                <p class="fw-semibold">@if(!empty($company->website))<p>
                  <a href="{{ $company->website }}" target="_blank" class="text-primary black-text-18 fw-medium">
                     {{ $company->website }}
                  </a>
                </p>
              @endif
            </div>

            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location</p>
              <p class="text-secondary black-text-18 fw-medium">{{ $company->location ?? 'N/A' }}</p>
            </div>

            <!-- Social -->
            <div class="col-12 mt-4 mb-2">
              <h5 class="mb-1 small-heading-black fw-bold"><i class="fas fa-share-alt me-2 text-primary"></i>Social</h5>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-linkedin me-2 text-primary"></i>LinkedIn</p>
              <p class="fw-semibold">
                @if(!empty($company->linkedin))
                  <a href="{{ $company->linkedin }}" target="_blank" class="text-primary black-text-18 fw-medium">
                    {{ $company->linkedin }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-facebook me-2 text-primary"></i>Facebook</p>
              <p class="fw-semibold">
                @if(!empty($company->facebook))
                  <a href="{{ $company->facebook }}" target="_blank" class="text-primary black-text-18 fw-medium">
                    {{ $company->facebook }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-x-twitter me-2 text-primary"></i>Twitter</p>
              <p class="fw-semibold">
                @if(!empty($company->twitter))
                  <a href="{{ $company->twitter }}" target="_blank" class="text-primary black-text-18 fw-medium">
                    {{ $company->twitter }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-lg-6 mb-2">
              <p class="mb-1 black-text-18 fw-medium"><i class="fab fa-instagram me-2 text-primary"></i>Instagram</p>
              <p class="fw-semibold">
                @if(!empty($company->instagram))
                  <a href="{{ $company->instagram }}" target="_blank" class="text-primary black-text-18 fw-medium">
                    {{ $company->instagram }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sessions/Event Info (Right Sidebar) -->
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
            <p class="text-muted mt-2">{{ $event->description ?? '' }}</p>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>

@endsection

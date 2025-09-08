@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection
@section('content')

<div class="container py-5">
  <div class="row justify-content-center">
    <!-- Company Info -->
    <div class="col-md-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
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
    <span class="badge bg-success">Sponsors</span>
    
    @if($company->is_sponsor == 1 && !empty($company->type))
      {{-- <span class="badge bg-warning text-dark">
        {{ $company->type }}
      </span> --}}
       @php
        $typeColors = match(strtolower($company->type ?? 'general')) {
            'gold' => ['bg'=>'#FFD700','border'=>'#E6C200','text'=>'#8B4513'],
            'silver' => ['bg'=>'#C0C0C0','border'=>'#A8A8A8','text'=>'#495057'],
            'bronze' => ['bg'=>'#CD7F32','border'=>'#B8722C','text'=>'#FFFFFF'],
            'platinum' => ['bg'=>'#E5E4E2','border'=>'#D4D4D4','text'=>'#2C3E50'],
            'majlislounge' => ['bg'=>'#8B4513','border'=>'#6B3410','text'=>'#FFFFFF'],
            default => ['bg'=>'#f0f0f0','border'=>'#dee2e6','text'=>'#495057']
        };
        $displayType = $company->type ? ucfirst(strtolower($company->type)) : 'General';
        if(strtolower($company->type ?? '') === 'majlislounge') $displayType = 'Majlis Lounge';
    @endphp

    <span class="company-type-badge fw-bold">
        {{ $displayType }}
    </span>
    
    @endif
  </div>
</div>





          </div>

          <!-- Company Info Grid -->
          <div class="row g-3">
            <div class="col-sm-12">
              <p class="mb-1 text-muted"><i class="fas fa-align-left me-2 text-primary"></i>Bio</p>
              <p class="fw-semibold">{{ $company->description ?? 'N/A' }}</p>
            </div>

            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-envelope me-2 text-primary"></i>Email</p>
              <p class="fw-semibold">{{ $company->email ?? 'N/A' }}</p>
            </div>

            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-phone me-2 text-primary"></i>Phone</p>
              <p class="fw-semibold">{{ $company->phone ?? 'N/A' }}</p>
            </div>

            <div class="col-sm-12">
              <p class="mb-1 text-muted"><i class="fas fa-building me-2 text-primary"></i>Sponsor</p>
                <p class="fw-semibold">{{ $company->name ?? 'N/A' }}</p>
              </div> 
              <div class="col-sm-6"> <p class="mb-1 text-muted"> <i class="fas fa-globe me-2 text-primary"></i>Website</p> <p class="fw-semibold">
            
              @if(!empty($company->website))
                <p>
                  <a href="{{ $company->website }}" target="_blank" class="text-dark">
                     {{ $company->website }}
                  </a>
                </p>
              @endif
            </div>

            <div class="col-sm-12">
              <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location</p>
              <p class="fw-semibold">{{ $company->location ?? 'N/A' }}</p>
            </div>

            <!-- Social -->
            <div class="col-sm-12 mt-3">
              <h5 class="text-primary"><i class="fas fa-share-alt me-2"></i>Social</h5>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fab fa-linkedin me-2 text-primary"></i>LinkedIn</p>
              <p class="fw-semibold">
                @if(!empty($company->linkedin))
                  <a href="{{ $company->linkedin }}" target="_blank" class="text-dark">
                    {{ $company->linkedin }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fab fa-facebook me-2 text-primary"></i>Facebook</p>
              <p class="fw-semibold">
                @if(!empty($company->facebook))
                  <a href="{{ $company->facebook }}" target="_blank" class="text-dark">
                    {{ $company->facebook }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fab fa-x-twitter me-2 text-primary"></i>Twitter</p>
              <p class="fw-semibold">
                @if(!empty($company->twitter))
                  <a href="{{ $company->twitter }}" target="_blank" class="text-dark">
                    {{ $company->twitter }}
                  </a>
                @else
                  N/A
                @endif
              </p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fab fa-instagram me-2 text-primary"></i>Instagram</p>
              <p class="fw-semibold">
                @if(!empty($company->instagram))
                  <a href="{{ $company->instagram }}" target="_blank" class="text-dark">
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
    <div class="col-md-4">
      <div class="list-group shadow-sm rounded-4">
        <h6 class="list-group-item bg-light fw-bold">Upcoming Sessions</h6>
        @forelse($sessions as $session)
          <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
              <div class="fw-bold">{{ $session->title ?? 'Untitled Session' }}</div>
              <small class="text-muted">
                <i class="fas fa-clock me-1 text-primary"></i>
                {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y h:i A') }}
              </small>
            </div>
            <span class="badge bg-primary rounded-pill align-self-center">
              {{ $session->location ?? 'Hall' }}
            </span>
          </div>
        @empty
          <div class="list-group-item text-muted">No sessions available</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
 <style>
        .company-type-badge {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 6px;
            border: 2px solid {{ $typeColors['border'] }};
            background-color: {{ $typeColors['bg'] }};
            color: {{ $typeColors['text'] }};
            min-width: 120px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .company-type-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        @media (max-width: 768px) {
            .company-type-badge { min-width: 100px; font-size: 0.8rem; padding: 0.3rem 0.8rem; }
        }

        @media (max-width: 480px) {
            .company-type-badge { min-width: 90px; font-size: 0.75rem; padding: 0.25rem 0.6rem; }
        }
    </style>
@endsection

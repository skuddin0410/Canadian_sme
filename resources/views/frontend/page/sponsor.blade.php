@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Sponsors of {{ config('app.name') }}">
@endsection

@section('content')

    <!-- sponsor -->
    <section class="sponsor py-4 py-lg-5">
        <div class="container">
            <div class="d-flex justify-content-end mb-2">
  <a href="javascript:history.back()" class="heroBtn ms-md-5">Back</a>
</div>
            <span class="small-heading-blue">Sponsors</span>
            <div class="d-flex justify-content-between gap-5">
                <h2 class="h2-black">
                    Our Sponsors Powering the Event
                </h2>
               
            </div>

            <div class="exhibitor-box mt-4 mt-lg-5 d-flex flex-column">
                @if(!empty($sponsors))
                    @foreach($sponsors as $sponsor)
                        <div class="exhibitor-card shadow">
                            <div class="exhibitor-card-box">
                                <div class="exhibitor-profile">
                                    @if(!empty($sponsor->logo))
                                        <img src="{{ $sponsor->logo->file_path }}" alt="">
                                    @else
                                        <span class="small-heading-blue mb-0">{{ shortenName($sponsor->name) }}</span>
                                    @endif
                                </div>
                                <div class="abc">
                                    <span class="blue-text-18 mb-2">Sponsor</span>
                                    <span class="small-heading-black fw-semibold">{{ $sponsor->name ?? '' }}</span>
                                </div>
                            </div>
                            <div class="sponsor-type-container mb-3">
    <label class="sponsor-type-label text-primary fw-medium mb-2 d-block">
        Sponsor Type
    </label>
    
    @php
        $sponsorConfig = match(strtolower($sponsor->type ?? 'general')) {
            'platinum' => [
                'bg' => 'linear-gradient(135deg, #E5E4E2 0%, #F8F8F8 100%)',
                'color' => '#2C3E50',
                'border' => '2px solid #D4D4D4',
              
            ],
            'gold' => [
                'bg' => 'linear-gradient(135deg, #FFD700 0%, #FFC107 100%)',
                'color' => '#8B4513',
                'border' => '2px solid #E6C200',
              
            ],
            'silver' => [
                'bg' => 'linear-gradient(135deg, #C0C0C0 0%, #E8E8E8 100%)',
                'color' => '#495057',
                'border' => '2px solid #A8A8A8',
                
            ],
            'bronze' => [
                'bg' => 'linear-gradient(135deg, #CD7F32 0%, #D4A574 100%)',
                'color' => '#FFFFFF',
                'border' => '2px solid #B8722C',
             
            ],
            'majlislounge' => [
                'bg' => 'linear-gradient(135deg, #8B4513 0%, #A0522D 100%)',
                'color' => '#FFFFFF',
                'border' => '2px solid #6B3410',
               
            ],
            default => [
                'bg' => 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                'color' => '#495057',
                'border' => '2px solid #dee2e6',
              
            ]
        };
        
        $displayType = $sponsor->type ? ucfirst(strtolower($sponsor->type)) : 'General';
        if (strtolower($sponsor->type ?? '') === 'majlislounge') {
            $displayType = 'Majlis Lounge';
        }
    @endphp
    
    <div class="sponsor-type-badge d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm position-relative overflow-hidden"
         style="background: {{ $sponsorConfig['bg'] }};
                color: {{ $sponsorConfig['color'] }};
                border: {{ $sponsorConfig['border'] }};
                font-weight: 600;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;">
        
        {{-- Background shine effect --}}
        <div class="shine-effect position-absolute top-0 start-0 w-100 h-100"
             style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
                    animation: shine 2s infinite;
                    pointer-events: none;"></div>
        
        {{-- Icon --}}
        <span class="sponsor-icon" style="font-size: 1.1rem;">
            {{-- {{ $sponsorConfig['icon'] }} --}}
        </span>
        
        {{-- Type text --}}
        <span class="sponsor-text">
            {{ $displayType }}
        </span>
        
      
    </div>
</div>
                            <div>
                                <span class="blue-text-18 mb-2">Email</span>
                                <span class="small-heading-black fw-semibold">{{ $sponsor->email ?? 'NA' }}</span>
                            </div>
                            <div>
                                <a class="view-more position-relative d-flex align-items-center gap-2" 
                                   href="{{ route('sponsor', $sponsor->slug) }}">
                                    View More
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="d-flex justify-content-center mt-4 d-xl-none">
                <button class="heroBtn btn-long">View More</button>
            </div>
        </div>
    </section>
    <!-- sponsor end -->
<style>
@keyframes shine {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.sponsor-type-container {
    max-width: fit-content;
}

.sponsor-type-label {
    font-size: 0.95rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.sponsor-type-badge {
    min-width: 120px;
    text-align: center;
    font-size: 0.9rem;
    cursor: default;
    user-select: none;
}

.sponsor-type-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
}

.sponsor-icon {
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
}

.premium-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

{{-- Responsive design --}}
@media (max-width: 576px) {
    .sponsor-type-badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem !important;
        min-width: 100px;
    }
    
    .sponsor-icon {
        font-size: 1rem;
    }
}
</style>

@endsection

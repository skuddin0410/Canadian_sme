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
                                        <img src="{{ $sponsor->logo->file_path }}" alt=""   style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">
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
    <label class="sponsor-type-label text-primary fw-medium mb-2 d-block px-3">
        Sponsor Type
    </label>
    
    
                    <span class="badge rounded-pill px-3 py-2 text-white fs-6" 
                      style="background-color: {{ typeColor($sponsor->type) }};">
                           {{ ucfirst(str_replace('-', ' ', $sponsor->type)) }}
                        </span>
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

                    <div class="d-flex justify-content-center mt-4 ">
                         <div class="mt-4">
                        {{ $sponsors->links() }}
                        </div>
                    </div>
                @endif
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

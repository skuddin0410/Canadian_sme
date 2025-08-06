@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">Our Services</h1>
            <p class="lead text-muted">Professional services tailored to your business needs</p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter by Category</h5>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="{{ route('catalog.services') }}" 
                           class="btn {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All Services
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('catalog.services', ['category' => $category->slug]) }}" 
                               class="btn {{ request('category') == $category->slug ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row">
        @forelse($services as $service)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-card">
                    @if($service->image_url)
                        <img src="{{ $service->image_url }}" class="card-img-top" alt="{{ $service->name }}" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-cogs fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            @if($service->category)
                                <span class="badge badge-info">{{ $service->category->name }}</span>
                            @endif
                            @if($service->duration)
                                <span class="badge badge-secondary">{{ $service->duration }}</span>
                            @endif
                        </div>
                        
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($service->description, 120) }}</p>
                        
                        @if($service->capabilities && count($service->capabilities) > 0)
                            <div class="mb-3">
                                <small class="text-muted">Key Capabilities:</small>
                                <ul class="list-unstyled">
                                    @foreach(array_slice($service->capabilities, 0, 3) as $capability)
                                        <li><i class="fas fa-check text-success mr-1"></i> {{ $capability }}</li>
                                    @endforeach
                                    @if(count($service->capabilities) > 3)
                                        <li><small class="text-muted">and {{ count($service->capabilities) - 3 }} more...</small></li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <!-- Pricing Preview -->
                        @if($service->pricingTiers->count() > 0)
                            <div class="mb-3">
                                @php $minPrice = $service->pricingTiers->where('is_quote_based', false)->min('price'); @endphp
                                @if($minPrice)
                                    <div class="h5 text-primary mb-0">
                                        Starting at ${{ number_format($minPrice, 2) }}
                                    </div>
                                @else
                                    <div class="h6 text-info mb-0">Contact for Quote</div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="mt-auto">
                            <a href="{{ route('catalog.services.show', $service->slug) }}" 
                               class="btn btn-primary btn-block">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-cogs fa-4x text-muted mb-3"></i>
                    <h4>No Services Found</h4>
                    <p class="text-muted">We're working on adding more services. Please check back soon!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.service-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
@endsection
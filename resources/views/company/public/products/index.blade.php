@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">Our Products</h1>
            <p class="lead text-muted">Discover our comprehensive range of innovative solutions designed to meet your needs</p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter by Category</h5>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="{{ route('catalog.products') }}" 
                           class="btn {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All Products
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('catalog.products', ['category' => $category->slug]) }}" 
                               class="btn {{ request('category') == $category->slug ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 product-card">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-box fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            @if($product->category)
                                <span class="badge badge-secondary">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 120) }}</p>
                        
                        @if($product->features && count($product->features) > 0)
                            <div class="mb-3">
                                <small class="text-muted">Key Features:</small>
                                <ul class="list-unstyled">
                                    @foreach(array_slice($product->features, 0, 3) as $feature)
                                        <li><i class="fas fa-check text-success mr-1"></i> {{ $feature }}</li>
                                    @endforeach
                                    @if(count($product->features) > 3)
                                        <li><small class="text-muted">and {{ count($product->features) - 3 }} more...</small></li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <!-- Pricing Preview -->
                        @if($product->pricingTiers->count() > 0)
                            <div class="mb-3">
                                @php $minPrice = $product->pricingTiers->where('is_quote_based', false)->min('price'); @endphp
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
                            <a href="{{ route('catalog.products.show', $product->slug) }}" 
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
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">We're working on adding more products. Please check back soon!</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
@endsection

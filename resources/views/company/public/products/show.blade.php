@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.products') }}">Products</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('catalog.products', ['category' => $product->category->slug]) }}">
                        {{ $product->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-images">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="img-fluid rounded main-image" 
                         alt="{{ $product->name }}" id="mainImage">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center main-image" 
                         style="height: 400px;">
                        <i class="fas fa-box fa-5x text-muted"></i>
                    </div>
                @endif

                @if($product->gallery_images && count($product->gallery_images) > 0)
                    <div class="row mt-3">
                        @foreach($product->gallery_images as $image)
                            <div class="col-3">
                                <img src="{{ $image }}" class="img-fluid rounded gallery-thumb" 
                                     alt="{{ $product->name }}" onclick="changeMainImage('{{ $image }}')">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                @if($product->category)
                    <span class="badge badge-secondary mb-2">{{ $product->category->name }}</span>
                @endif
                
                <h1 class="h2 mb-3">{{ $product->name }}</h1>
                
                <div class="product-description mb-4">
                    {!! nl2br(e($product->description)) !!}
                </div>

                @if($product->features && count($product->features) > 0)
                    <div class="mb-4">
                        <h5>Key Features</h5>
                        <ul class="list-unstyled">
                            @foreach($product->features as $feature)
                                <li class="mb-1">
                                    <i class="fas fa-check text-success mr-2"></i>{{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($product->benefits && count($product->benefits) > 0)
                    <div class="mb-4">
                        <h5>Benefits</h5>
                        <ul class="list-unstyled">
                            @foreach($product->benefits as $benefit)
                                <li class="mb-1">
                                    <i class="fas fa-star text-warning mr-2"></i>{{ $benefit }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Pricing Tiers -->
                @if($product->pricingTiers->count() > 0)
                    <div class="pricing-section mb-4">
                        <h5>Pricing Options</h5>
                        <div class="row">
                            @foreach($product->pricingTiers as $tier)
                                <div class="col-md-6 mb-3">
                                    <div class="card {{ $tier->is_popular ? 'border-primary' : '' }}">
                                        @if($tier->is_popular)
                                            <div class="card-header bg-primary text-white text-center">
                                                <small>Most Popular</small>
                                            </div>
                                        @endif
                                        <div class="card-body text-center">
                                            <h6 class="card-title">{{ $tier->tier_name }}</h6>
                                            @if($tier->is_quote_based)
                                                <div class="h4 text-info">Contact for Quote</div>
                                            @else
                                                <div class="h4 text-primary">
                                                    ${{ number_format($tier->price, 2) }}
                                                    @if($tier->billing_period)
                                                        <small class="text-muted">/ {{ $tier->billing_period }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @if($tier->features && count($tier->features) > 0)
                                                <ul class="list-unstyled mt-3">
                                                    @foreach($tier->features as $feature)
                                                        <li><i class="fas fa-check text-success mr-1"></i> {{ $feature }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Call to Action -->
                <div class="cta-section">
                    <button class="btn btn-primary btn-lg mr-3" onclick="requestQuote()">
                        <i class="fas fa-envelope mr-1"></i> Request Quote
                    </button>
                    <button class="btn btn-outline-secondary btn-lg" onclick="contactUs()">
                        <i class="fas fa-phone mr-1"></i> Contact Us
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Specifications -->
    @if($product->technicalSpecs->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Technical Specifications</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $groupedSpecs = $product->technicalSpecs->groupBy('spec_category');
                        @endphp
                        
                        @if($groupedSpecs->has(''))
                            <div class="mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @foreach($groupedSpecs[''] as $spec)
                                            <tr>
                                                <td class="font-weight-bold" style="width: 30%;">{{ $spec->spec_name }}</td>
                                                <td>
                                                    {{ $spec->spec_value }}
                                                    @if($spec->spec_unit)
                                                        {{ $spec->spec_unit }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif

                        @foreach($groupedSpecs->except('') as $category => $specs)
                            <div class="mb-4">
                                <h6 class="text-primary">{{ $category }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @foreach($specs as $spec)
                                            <tr>
                                                <td class="font-weight-bold" style="width: 30%;">{{ $spec->spec_name }}</td>
                                                <td>
                                                    {{ $spec->spec_value }}
                                                    @if($spec->spec_unit)
                                                        {{ $spec->spec_unit }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">Related Products</h4>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100">
                                @if($relatedProduct->image_url)
                                    <img src="{{ $relatedProduct->image_url }}" class="card-img-top" 
                                         alt="{{ $relatedProduct->name }}" style="height: 150px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                    <p class="card-text text-muted">{{ Str::limit($relatedProduct->description, 80) }}</p>
                                    <a href="{{ route('catalog.products.show', $relatedProduct->slug) }}" 
                                       class="btn btn-outline-primary btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function changeMainImage(imageUrl) {
    document.getElementById('mainImage').src = imageUrl;
}

function requestQuote() {
    // Implement quote request functionality
    alert('Quote request functionality would be implemented here');
}

function contactUs() {
    // Implement contact us functionality
    alert('Contact us functionality would be implemented here');
}
</script>

<style>
.gallery-thumb {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.gallery-thumb:hover {
    opacity: 1;
}

.main-image {
    max-height: 400px;
    object-fit: cover;
}

.pricing-section .card {
    transition: transform 0.2s;
}

.pricing-section .card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Product Details</h4>
    <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        ← Back to Products
    </a>
</div>


    

    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ $product->name }}</h5>
        </div>
        <div class="card-body row">
            <div class="col-md-4">
                {{-- @if($product->image_url)
                    <img src="{{ asset($product->image_url) }}" class="img-fluid mb-3" alt="{{ $product->name }}">
                @endif

                @if($product->gallery_images)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->gallery_images as $img)
                            <img src="{{ asset($img) }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border">
                        @endforeach
                    </div>
                @endif --}}
                 {{-- Main Image --}}
        @if($product->image_url)
            <img src="{{ asset('storage/' . $product->image_url) }}" class="img-fluid mb-3" alt="{{ $product->name }}">
        @endif

        {{-- Gallery Images --}}
        @if($product->gallery_images)
            <div class="d-flex flex-wrap gap-2">
                @foreach($product->gallery_images as $img)
                    <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border">
                @endforeach
            </div>
        @endif
            </div>

            <div class="col-md-8">
                <p><strong>Description:</strong><br>{{ $product->description }}</p>

                <p><strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>

                <p><strong>Sort Order:</strong> {{ $product->sort_order }}</p>

                <p><strong>Meta Title:</strong> {{ $product->meta_title }}</p>
                <p><strong>Meta Description:</strong> {{ $product->meta_description }}</p>

                {{-- @if($product->features)
                    <p><strong>Features:</strong>
                        <ul>
                            @foreach($product->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </p>
                @endif --}}
                @if(is_array($product->features) && count($product->features))
    <p><strong>Features:</strong></p>
    <ul>
        @foreach($product->features as $feature)
            <li>{{ $feature }}</li>
        @endforeach
    </ul>
@elseif(is_string($product->features))
    <p><strong>Features:</strong></p>
    <ul>
        @foreach(json_decode($product->features, true) ?? [] as $feature)
            <li>{{ $feature }}</li>
        @endforeach
    </ul>
@endif
@if($product->benefits)
    <p><strong>Benefits:</strong></p>
    <ul>
        @foreach(is_array($product->benefits) ? $product->benefits : json_decode($product->benefits, true) ?? [] as $benefit)
            <li>{{ $benefit }}</li>
        @endforeach
    </ul>
@endif



                {{-- @if($product->benefits)
                    <p><strong>Benefits:</strong>
                        <ul>
                            @foreach($product->benefits as $benefit)
                                <li>{{ $benefit }}</li>
                            @endforeach
                        </ul>
                    </p>
                @endif --}}

                <p><strong>Created by:</strong> {{ $product->creator->name ?? 'System' }}</p>
                <p><strong>Updated by:</strong> {{ $product->updater->name ?? '—' }}</p>
                <p><strong>Created at:</strong> {{ $product->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Last updated:</strong> {{ $product->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

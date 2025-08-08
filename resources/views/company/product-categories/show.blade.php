@extends('layouts.admin')

@section('title')
    Admin | Product Category Details
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y">
    <h4 class="mb-4">Product Category Details</h4>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('product-categories.index') }}" class="btn btn-secondary mb-3">
                &larr; Back to List
            </a>

            <div class="row">
                {{-- Left column: Image --}}
                <div class="col-md-4 text-center">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-fluid rounded mb-3" />
                    @else
                        <img src="{{ asset('images/default-category.png') }}" alt="No Image" class="img-fluid rounded mb-3" />
                    @endif
                </div>

                {{-- Right column: Details --}}
                <div class="col-md-8">
                    <h3>{{ $category->name }}</h3>
                    {{-- <p><strong>Slug:</strong> {{ $category->slug }}</p> --}}

                    {{-- @if($category->parent)
                        <p><strong>Parent Category:</strong> {{ $category->parent->name }}</p>
                    @else
                        <p><strong>Parent Category:</strong> None</p>
                    @endif --}}

                    <p><strong>Description:</strong></p>
                    <p>{!! nl2br(e($category->description ?? 'No description available.')) !!}</p>

                    {{-- <p><strong>Status:</strong> 
                        @if($category->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </p> --}}

                    {{-- <p><strong>Sort Order:</strong> {{ $category->sort_order ?? 0 }}</p> --}}
                    <p><strong>Created At:</strong> {{ $category->created_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Updated At:</strong> {{ $category->updated_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

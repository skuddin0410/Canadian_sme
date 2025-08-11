@extends('layouts.admin')

@section('title', 'Admin | Service Category Details')

@section('content')
<div class="container flex-grow-1 container-p-y">
    <h4 class="mb-4">Service Category Details</h4>

    <div class="card">
         <div class="d-flex pt-3 justify-content-end">
    <a href="{{ route('service-categories.index') }}" class="btn btn-outline-primary me-2">Back</a>
    </div>
        <div class="card-body">

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

                    <p><strong>Description:</strong></p>
                    <p>{!! nl2br(e($category->description ?? 'No description available.')) !!}</p>

                    <p><strong>Created At:</strong> {{ $category->created_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Updated At:</strong> {{ $category->updated_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

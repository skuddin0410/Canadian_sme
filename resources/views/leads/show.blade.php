@extends('layouts.admin')

@section('title', 'Lead Details - PropertyConnect')

@push('styles')
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
<div class="container mt-3 py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 fw-bold text-dark">Lead Details</h1>
            <p class="text-muted">Information about <strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary me-2">
                <i class="fa fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            {{-- Personal Info --}}
            <h5 class="fw-bold mb-3">Personal Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>First Name:</strong> {{ $lead->first_name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Last Name:</strong> {{ $lead->last_name }}</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>Email:</strong> {{ $lead->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Phone:</strong> {{ $lead->phone }}</p>
                </div>
            </div>

            <hr class="my-4">

            {{-- Lead Details --}}
            <h5 class="fw-bold mb-3">Lead Details</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <p><strong>Status:</strong> {{ ucfirst($lead->status) }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Priority:</strong> {{ ucfirst($lead->priority) }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Source:</strong> {{ ucfirst(str_replace('_',' ', $lead->source)) }}</p>
                </div>
            </div>

            <hr class="my-4">

            {{-- Desired Amenities --}}
            <h5 class="fw-bold mb-3">Desired Amenities</h5>
            @php
                $amenities = $lead->desired_amenities ? json_decode($lead->desired_amenities, true) : [];
            @endphp
            @if(!empty($amenities))
                <ul class="list-inline">
                    @foreach($amenities as $amenity)
                        <li class="list-inline-item badge bg-dark me-1">
                            {{ ucfirst(str_replace('_',' ',$amenity)) }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No amenities selected</p>
            @endif

            <hr class="my-4">

            {{-- Lead Tags --}}
            <h5 class="fw-bold mb-3">Lead Tags</h5>
            @php
                $tags = $lead->tags ? json_decode($lead->tags, true) : [];
            @endphp
            @if(!empty($tags))
                <ul class="list-inline">
                    @foreach($tags as $tag)
                        <li class="list-inline-item badge bg-primary me-1">
                            {{ ucfirst($tag) }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No tags assigned</p>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

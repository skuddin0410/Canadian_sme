@extends('layouts.admin')

@section('title', 'Edit Lead ')

@push('styles')
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark">Edit Lead</h1>
        <p class="text-muted">Update the details of this lead</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please check the form below for errors.
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('leads.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Personal Info --}}
                <h5 class="fw-bold mb-3">Personal Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" 
                               value="{{ old('first_name', $lead->first_name) }}" 
                               class="form-control @error('first_name') is-invalid @enderror" required>
                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" 
                               value="{{ old('last_name', $lead->last_name) }}" 
                               class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" 
                               value="{{ old('email', $lead->email) }}" 
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" 
                               value="{{ old('phone', $lead->phone) }}" 
                               class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Lead Details --}}
                <h5 class="fw-bold mb-3">Lead Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['new','contacted','qualified','converted','lost'] as $status)
                                <option value="{{ $status }}" 
                                    {{ old('status', $lead->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            @foreach(['low','medium','high'] as $priority)
                                <option value="{{ $priority }}" 
                                    {{ old('priority', $lead->priority) == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Source</label>
                        <select name="source" class="form-select">
                            @foreach(['website','referral','social_media','walk_in','phone','advertisement'] as $src)
                                <option value="{{ $src }}" 
                                    {{ old('source', $lead->source) == $src ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_',' ',$src)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <hr class="my-4">

{{-- Lead Organization: Tagging System --}}
<h5 class="fw-bold mb-3">Lead Tags</h5>
@php
    $tags = ['hot', 'warm', 'cold', 'follow-up'];

    // Decode JSON from database, fallback to empty array
    $leadTags = $lead->tags ? json_decode($lead->tags, true) : [];

    // For old input after validation error, fallback to existing lead tags
    $selectedTags = old('tags', $leadTags);
@endphp

<div class="row">
   @foreach($tags as $tag)
    <div class="col-md-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" 
                   name="tags[]" 
                   value="{{ $tag }}" 
                   {{ in_array($tag, $selectedTags) ? 'checked' : '' }}>
            <label class="form-check-label">{{ ucfirst($tag) }}</label>
        </div>
    </div>
@endforeach

</div>


                <hr class="my-4">

                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Update Lead
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

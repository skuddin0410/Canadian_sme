@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Left section: profile/details -->
        <div class="col-md-8">
            <div class="card mb-4">
                
                <div class="card-header">
                    <h4>Exhibitor Details</h4>
                </div>
                
                
                <div class="card-body">
                    <!-- Company Profile Info -->
                    <p><strong>Name:</strong> {{ $company->name }}</p>
                    <p><strong>Email:</strong> {{ $company->email }}</p>
                    <p><strong>Phone:</strong> {{ $company->phone }}</p>
                    <p><strong>Description:</strong> {{ $company->description }}</p>
                    <p><strong>Website:</strong> <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></p>
                    <p><strong>LinkedIn:</strong> <a href="{{ $company->linkedin }}" target="_blank">{{ $company->linkedin }}</a></p>
                    <p><strong>Twitter:</strong> <a href="{{ $company->twitter }}" target="_blank">{{ $company->twitter }}</a></p>
                    <p><strong>Facebook:</strong> <a href="{{ $company->facebook }}" target="_blank">{{ $company->facebook }}</a></p>
                    <p><strong>Instagram:</strong> <a href="{{ $company->instagram }}" target="_blank">{{ $company->instagram }}</a></p>
                    
                </div>
                
            </div>
            <!-- New Card: Media & QR -->
<div class="card mt-4 col-12">
    <div class="card-header">
        <h4>Media & QR Code</h4>
    </div>
    <div class="card-body text-center">
        <div class="row">
            <!-- Content Icon -->
            <div class="col-md-6">
                <h6>Content Icon</h6>
                @if(!empty($company->contentIconFile) && !empty($company->contentIconFile->file_path))
                    <img src="{{$company->contentIconFile->file_path }}"
                         alt="Content Icon"
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 150px; object-fit: contain;">
                @else
                    <p class="text-muted">No Content Icon</p>
                @endif
            </div>

            <!-- Quick Link Icon -->
            <div class="col-md-6">

                <h6>Quick Link Icon</h6>
                @if(!empty($company->quickLinkIconFile) && !empty($company->quickLinkIconFile->file_path))
                    <img src="{{$company->quickLinkIconFile->file_path}}"
                         alt="Quick Link Icon"
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 150px; object-fit: contain;">
                @else
                    <p class="text-muted">No Quick Link Icon</p>
                @endif
            </div>
        </div>
    </div>
</div>

        </div>

        <!-- Right section: Booth assignment -->
        <div class="col-md-4">
            <!-- Assign Booth Form -->
             <div class="card">
                <div class="d-flex pt-3 justify-content-end">
                <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary me-2">Back</a>
                </div>
            <h4 class="ms-2">
                Booth No :
                <span class="badge bg-primary rounded-pill">
                    {{ $company->booth ?? 'N/A' }}
                </span>
            </h4>

                        <!-- QR Code -->
            <div class="col-md-12 ms-2">
                    @if($company->qr_code)
                        <img src="{{ asset($company->qr_code) }}"
                             alt="User QR Code"
                             class="rounded border"
                             style="max-height: 150px; object-fit:contain;">
                    @else
                        <p>No QR code generated.</p>
                    @endif
            </div>
         </div>

        </div>
    </div>
</div>
@endsection

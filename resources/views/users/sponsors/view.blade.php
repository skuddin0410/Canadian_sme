@extends('layouts.admin')

@section('title', 'Admin | Sponsors Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Sponsors</h4>

    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('sponsors.index') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-outline-primary btn-pill btn-streach font-book fs-14 me-2">
                <i class="fa fa-angle-left me-1"></i> Back
            </button>
        </form>
    </div>

    <div class="row">
        {{-- 📌 Left Column (Sponsor Details) --}}
        <div class="col-md-6">
            <div class="card mb-4 h-100">
                <div class="card-header">
                    <h5 class="mb-0">Sponsor Details: {{ $company->name }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <p><strong>Name:</strong> {{ $company->name }}</p>
                        <p><strong>Email:</strong> {{ $company->email }}</p>
                        <p><strong>Phone:</strong> {{ $company->phone }}</p>
                        <p><strong>Description:</strong> {{ $company->description ?? '-' }}</p>
                        <p><strong>Website:</strong> 
                            <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                        </p>
                        <p><strong>LinkedIn:</strong> 
                            <a href="{{ $company->linkedin }}" target="_blank">{{ $company->linkedin }}</a>
                        </p>
                        <p><strong>Twitter:</strong> 
                            <a href="{{ $company->twitter }}" target="_blank">{{ $company->twitter }}</a>
                        </p>
                        <p><strong>Facebook:</strong> 
                            <a href="{{ $company->facebook }}" target="_blank">{{ $company->facebook }}</a>
                        </p>
                        <p><strong>Instagram:</strong> 
                            <a href="{{ $company->instagram }}" target="_blank">{{ $company->instagram }}</a>
                        </p>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 📌 Right Column (Media + QR Code) --}}
        <div class="col-md-6">
            <div class="card mb-4 h-100">
                <div class="card-header">
                    <h5 class="mb-0">Media & QR Code</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                       

<div class="row">
    {{-- Row 2: Logo --}}
    
    <div class="col-md-12 mb-3">
        <div class="card p-3 h-100">
            <h6 class="mb-2">Banner</h6>
            @if($company->banner)
                <img src="{{$company->banner->file_path}}"
                     alt="User Banner"
                     class="rounded border w-100"
                     style="max-height:150px; object-fit:cover;">
            @else
                <p>No banner uploaded.</p>
            @endif
        </div>
    </div>
</div>
 <div class="row">
     <div class="col-md-6 mb-3">
        <div class="card p-3 h-100">
            <h6 class="mb-2">User QR Code</h6>
            @if($user->qr_code)
                <img src="{{ asset($user->qr_code) }}"
                     alt="User QR Code"
                     class="rounded border"
                     style="width:200px; height:200px; object-fit:contain;">
            @else
                <p>No QR code generated.</p>
            @endif
        </div>
    </div>
                            <div class="col-md-6 mb-3">
        <div class="card p-3">
            <h6 class="mb-2">Logo</h6>
            @if($company->logo)
                <img src="{{ $company->logo->file_path }}"
                     alt="User Logo"
                     class="rounded border"
                     style="width:200px; height:200px; object-fit:contain;">
            @else
                <p>No logo uploaded.</p>
            @endif
        </div>
    </div>
    
   

    
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

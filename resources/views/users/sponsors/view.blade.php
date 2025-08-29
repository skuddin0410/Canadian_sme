@extends('layouts.admin')

@section('title', 'Admin | Sponsors Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Sponsors</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">

                    <div class="d-flex justify-content-end">
                        <form action="{{ route('sponsors.index') }}" method="GET" class="d-inline">
                            <button type="submit" class="btn btn-outline-primary btn-pill btn-streach font-book fs-14 me-2">
                               <i class="fa fa-angle-left me-1"></i> Back
                            </button>
                        </form>
                    </div>

                    <h5 class="border-bottom mb-4">Sponsors Details: {{ $company->name }}</h5>

                    <div class="info-container">
                        <div class="row">   
                            <div class="col-6">
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

                </div>
            </div>

            {{-- 📌 Media & QR Section --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Media & QR Code</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">

                        {{-- Logo --}}
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-2">Logo</h6>
                             @if($company->banner)
                            <img src="{{ Storage::url('companies/'.$company->id.'/logo/'.$company->logo) }}" 
     width="120" height="120">
      @else
                                <span class="text-muted">No Logo</span>
                            @endif
                             

                        </div>

                        {{-- Banner --}}
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-2">Banner</h6>
                            @if($company->banner)
                                <img src="{{ Storage::url('companies/'.$company->id.'/banner/'.$company->banner) }}" 
     width="120" height="120">
                            @else
                                <span class="text-muted">No Banner</span>
                            @endif
                        </div>

                        {{-- QR Code --}}
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-2">QR Code</h6>
                            {{-- {!! QrCode::size(150)->generate($company->website ?? url('/')) !!} --}}
                            <div class="mt-2">
                                <a href="{{ route('sponsors.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-download me-1"></i> Download QR
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

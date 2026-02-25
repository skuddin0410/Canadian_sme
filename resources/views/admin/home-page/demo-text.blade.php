@extends('layouts.admin')

@section('title')
Admin | Landing Page Demo Text Section
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page /</span> Demo Text</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Demo Text CMS</h5>
                </div>
                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success mt-3">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger mt-3">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.home-page.demo-text.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('heading') is-invalid @enderror" id="heading" name="heading" value="{{ old('heading', $demoText->heading ?? '') }}" required>
                            @error('heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle1" class="form-label">Subtitle 1</label>
                            <input type="text" class="form-control @error('subtitle1') is-invalid @enderror" id="subtitle1" name="subtitle1" value="{{ old('subtitle1', $demoText->subtitle1 ?? '') }}">
                            @error('subtitle1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle2" class="form-label">Subtitle 2</label>
                            <input type="text" class="form-control @error('subtitle2') is-invalid @enderror" id="subtitle2" name="subtitle2" value="{{ old('subtitle2', $demoText->subtitle2 ?? '') }}">
                            @error('subtitle2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Demo Text
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

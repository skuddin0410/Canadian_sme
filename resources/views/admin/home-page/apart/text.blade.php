@extends('layouts.admin')

@section('title')
Admin | Landing Page Us Apart Text
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Us Apart /</span> Text Content</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Apart Text Details</h5>
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

                    <form action="{{ route('admin.home-page.apart.text.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('heading') is-invalid @enderror" id="heading" name="heading" value="{{ old('heading', $text->heading ?? '') }}" required>
                            @error('heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sub_heading" class="form-label">Sub-heading</label>
                            <input type="text" class="form-control @error('sub_heading') is-invalid @enderror" id="sub_heading" name="sub_heading" value="{{ old('sub_heading', $text->sub_heading ?? '') }}">
                            @error('sub_heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Apart Text
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

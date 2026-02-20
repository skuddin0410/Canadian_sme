@extends('layouts.admin')

@section('title')
Admin | Landing Page Main Section
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page /</span> Main</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Main Section CMS</h5>
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

                    <form action="{{ route('admin.home-page.main.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title Field -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $main->title ?? '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sub-title Field -->
                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">Sub-title</label>
                                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle', $main->subtitle ?? '') }}">
                                    @error('subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Button Link Field -->
                                <div class="mb-3">
                                    <label for="button_link" class="form-label">Button Link</label>
                                    <input type="text" class="form-control @error('button_link') is-invalid @enderror" id="button_link" name="button_link" value="{{ old('button_link', $main->button_link ?? '') }}" placeholder="https://example.com/register">
                                    @error('button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Image Field -->
                                <div class="mb-3">
                                    <label for="profileImageInput" class="form-label">Main Image</label>
                                    <div class="d-flex flex-column align-items-center border p-3 rounded bg-light">
                                        @if(empty($main->mainImage))
                                            <div id="imageNotFoundText" class="mb-2 text-muted fw-bold">Image not found</div>
                                        @endif
                                        <img id="profileImagePreview" 
                                             src="{{ !empty($main->mainImage) ? $main->mainImage->file_path : '' }}" 
                                             alt="Main Image" 
                                             class="img-fluid rounded mb-2 {{ empty($main->mainImage) ? 'd-none' : '' }}" 
                                             style="max-height: 200px;">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="profileImageInput" name="image" accept="image/*">
                                    </div>
                                    @error('image')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $main->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Main Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("profileImageInput");
    const preview = document.getElementById("profileImagePreview");
    const notFoundText = document.getElementById("imageNotFoundText");

    if (input && preview) {
        input.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    if (notFoundText) {
                        notFoundText.classList.add('d-none');
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection

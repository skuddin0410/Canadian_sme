@extends('layouts.admin')

@section('title')
Admin | Edit Logo
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Logos /</span> Edit</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Logo Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home-page.logos.update', $logo->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title Field -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $logo->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status Field -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="1" {{ old('status', $logo->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $logo->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Order By Field -->
                                <div class="mb-3">
                                    <label for="order_by" class="form-label">Order By</label>
                                    <input type="number" class="form-control @error('order_by') is-invalid @enderror" id="order_by" name="order_by" value="{{ old('order_by', $logo->order_by) }}" required>
                                    @error('order_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Image Field -->
                                <div class="mb-3">
                                    <label for="profileImageInput" class="form-label">Logo Image</label>
                                    <div class="d-flex flex-column align-items-center border p-3 rounded bg-light">
                                        @if(empty($logo->logoImage))
                                            <div id="imageNotFoundText" class="mb-2 text-muted fw-bold">Image not found</div>
                                        @endif
                                        <img id="profileImagePreview" 
                                             src="{{ !empty($logo->logoImage) ? $logo->logoImage->file_path : '' }}" 
                                             alt="Logo Preview" 
                                             class="img-fluid rounded mb-2 {{ empty($logo->logoImage) ? 'd-none' : '' }}" 
                                             style="max-height: 150px;">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="profileImageInput" name="image" accept="image/*">
                                        <small class="text-muted mt-1">Leave empty to keep existing image</small>
                                    </div>
                                    @error('image')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Logo
                            </button>
                            <a href="{{ route('admin.home-page.logos.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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

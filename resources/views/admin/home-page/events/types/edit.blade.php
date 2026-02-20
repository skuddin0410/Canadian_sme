@extends('layouts.admin')

@section('title')
Admin | Edit Event Type
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Events / Event Types /</span> Edit</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Event Type</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home-page.events.types.update', $type->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('heading') is-invalid @enderror" id="heading" name="heading" value="{{ old('heading', $type->heading) }}" required>
                                    @error('heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="text" class="form-label">Points (Press Enter for new point) <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('text') is-invalid @enderror" id="text" name="text" rows="5" required>{{ old('text', $type->text) }}</textarea>
                                    @error('text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="1" {{ old('status', $type->status) == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $type->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Order</label>
                                            <input type="number" class="form-control" id="order" name="order" value="{{ old('order', $type->order) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profileImageInput" class="form-label">Image</label>
                                    <div class="d-flex flex-column align-items-center border p-3 rounded bg-light">
                                        @if(empty($type->typeImage))
                                            <div id="imageNotFoundText" class="mb-2 text-muted fw-bold">Image not found</div>
                                        @endif
                                        <img id="profileImagePreview" src="{{ !empty($type->typeImage) ? $type->typeImage->file_path : '' }}" alt="Preview" class="img-fluid rounded mb-2 {{ empty($type->typeImage) ? 'd-none' : '' }}" style="max-height: 150px;">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="profileImageInput" name="image" accept="image/*">
                                        <small class="text-muted mt-1">Leave empty to keep existing image</small>
                                    </div>
                                    @error('image')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Event Type
                            </button>
                            <a href="{{ route('admin.home-page.events.types.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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

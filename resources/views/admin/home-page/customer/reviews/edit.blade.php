@extends('layouts.admin')

@section('title')
Admin | Edit Home Review
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Customer / Home Reviews /</span> Edit</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Review Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home-page.customer.reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $review->customer_name) }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Review Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $review->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="1" {{ old('status', $review->status) == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $review->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_by" class="form-label">Order By</label>
                                            <input type="number" class="form-control @error('order_by') is-invalid @enderror" id="order_by" name="order_by" value="{{ old('order_by', $review->order_by) }}" required>
                                            @error('order_by')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profileImageInput" class="form-label">Profile Image</label>
                                    <div class="d-flex flex-column align-items-center border p-3 rounded bg-light">
                                        @if(empty($review->profileImage))
                                            <div id="imageNotFoundText" class="mb-2 text-muted fw-bold">Image not found</div>
                                        @endif
                                        <img id="profileImagePreview" src="{{ !empty($review->profileImage) ? $review->profileImage->file_path : '' }}" alt="Preview" class="img-fluid rounded-circle mb-2 {{ empty($review->profileImage) ? 'd-none' : '' }}" style="height: 150px; width: 150px; object-fit: cover;">
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
                                <i class="bx bx-save me-1"></i> Update Review
                            </button>
                            <a href="{{ route('admin.home-page.customer.reviews.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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

@extends('layouts.admin')

@section('title')
Admin | Edit Apart Card
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Us Apart / Cards /</span> Edit</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Card Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home-page.apart.cards.update', $card->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('heading') is-invalid @enderror" id="heading" name="heading" value="{{ old('heading', $card->heading) }}" required>
                                    @error('heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $card->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="text" class="form-label">Points (Press Enter for new point)</label>
                                    <textarea class="form-control @error('text') is-invalid @enderror" id="text" name="text" rows="5" placeholder="Point 1&#10;Point 2">{{ old('text', $card->text) }}</textarea>
                                    <small class="text-muted">Enter each point on a new line.</small>
                                    @error('text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="1" {{ old('status', $card->status) == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $card->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_by" class="form-label">Order By</label>
                                            <input type="number" class="form-control" id="order_by" name="order_by" value="{{ old('order_by', $card->order_by) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profileImageInput" class="form-label">Icon</label>
                                    <div class="d-flex flex-column align-items-center border p-3 rounded bg-light">
                                        @if(empty($card->cardIcon))
                                            <div id="imageNotFoundText" class="mb-2 text-muted fw-bold">Icon not found</div>
                                        @endif
                                        <img id="profileImagePreview" src="{{ !empty($card->cardIcon) ? $card->cardIcon->file_path : '' }}" alt="Preview" class="img-fluid rounded mb-2 {{ empty($card->cardIcon) ? 'd-none' : '' }}" style="max-height: 150px;">
                                        <input type="file" class="form-control @error('icon') is-invalid @enderror" id="profileImageInput" name="icon" accept="image/*">
                                        <small class="text-muted mt-1">Leave empty to keep existing icon</small>
                                    </div>
                                    @error('icon')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Card
                            </button>
                            <a href="{{ route('admin.home-page.apart.cards.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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

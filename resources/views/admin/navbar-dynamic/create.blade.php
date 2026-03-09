@extends('layouts.admin')

@section('title')
Admin | Add New Navbar Item
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Admin / Dynamic Nav /</span> Add New</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Item Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.navbar-dynamic.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Title Field -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Slug Field (Read-only) -->
                                <div class="mb-3">
                                    <label for="slug_preview" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="slug_preview" readonly disabled>
                                    <div class="form-text text-info">Slug is automatically generated.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">

                                <!-- Category Field -->
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <div class="input-group">
                                        <select class="form-select @error('category') is-invalid @enderror" id="category_select" name="category">
                                            <option value="">No Category (General)</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                            @endforeach
                                            <option value="NEW_CATEGORY">-- Add New Category --</option>
                                        </select>
                                        <input type="text" class="form-control d-none" id="category_new" placeholder="Type new category name...">
                                        <button class="btn btn-outline-secondary d-none" type="button" id="cancel_new_cat"><i class="bx bx-x"></i></button>
                                    </div>
                                    <div class="form-text">Select an existing category or choose "Add New" to create one.</div>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Content Field -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Content</label>
                                    <textarea class="form-control description-cls @error('content') is-invalid @enderror" id="description" name="content">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status Field -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Publish</option>
                                        <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Order By Field -->
                                <div class="mb-3">
                                    <label for="order_by" class="form-label">Order By</label>
                                    <input type="number" class="form-control @error('order_by') is-invalid @enderror" id="order_by" name="order_by" value="{{ old('order_by', 0) }}" required>
                                    @error('order_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Save Item
                            </button>
                            <a href="{{ route('admin.navbar-dynamic.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
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
    // Slug Generation
    document.getElementById('title').addEventListener('input', function() {
        let title = this.value;
        let slug = title.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        document.getElementById('slug_preview').value = slug;
    });

    // Category Toggle Logic
    const categorySelect = document.getElementById('category_select');
    const categoryNew = document.getElementById('category_new');
    const cancelNewCat = document.getElementById('cancel_new_cat');

    categorySelect.addEventListener('change', function() {
        if (this.value === 'NEW_CATEGORY') {
            categorySelect.classList.add('d-none');
            categorySelect.removeAttribute('name');
            
            categoryNew.classList.remove('d-none');
            categoryNew.setAttribute('name', 'category');
            categoryNew.focus();
            
            cancelNewCat.classList.remove('d-none');
        }
    });

    cancelNewCat.addEventListener('click', function() {
        categorySelect.classList.remove('d-none');
        categorySelect.setAttribute('name', 'category');
        categorySelect.value = "";
        
        categoryNew.classList.add('d-none');
        categoryNew.removeAttribute('name');
        categoryNew.value = "";
        
        cancelNewCat.classList.add('d-none');
    });
</script>
@endsection

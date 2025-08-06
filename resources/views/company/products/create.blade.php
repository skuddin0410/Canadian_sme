@extends('layouts.admin')

@section('content')
<div class="container">
    <h4 class="py-3 mb-4">Add New Product</h4>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Product Name --}}
        <div class="mb-3">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
        </div>

        {{-- Features --}}
        <div class="mb-3">
            <label class="form-label">Features (one per line)</label>
            <textarea name="features" rows="3" class="form-control">{{ is_array(old('features')) ? implode("\n", old('features')) : '' }}</textarea>
        </div>

        {{-- Benefits --}}
        <div class="mb-3">
            <label class="form-label">Benefits (one per line)</label>
            <textarea name="benefits" rows="3" class="form-control">{{ is_array(old('benefits')) ? implode("\n", old('benefits')) : '' }}</textarea>
        </div>

        {{-- Image Upload --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Main Image</label>
                <input type="file" name="main_image" class="form-control" accept="image/*">
            </div>

            <div class="col-md-6">
                <label class="form-label">Gallery Images (Multiple)</label>
                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
            </div>
        </div>

        {{-- SEO Fields --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description') }}" class="form-control">
            </div>
        </div>

        {{-- Sort & Active --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Is Active?</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary">Create Product</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

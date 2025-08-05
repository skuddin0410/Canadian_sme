@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Product</h3>
    
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Features --}}
        <div class="mb-3">
            <label class="form-label">Features (one per line)</label>
            <textarea name="features" class="form-control" rows="3">{{ old('features', is_array($product->features) ? implode("\n", $product->features) : '') }}</textarea>
        </div>

        {{-- Benefits --}}
        <div class="mb-3">
            <label class="form-label">Benefits (one per line)</label>
            <textarea name="benefits" class="form-control" rows="3">{{ old('benefits', is_array($product->benefits) ? implode("\n", $product->benefits) : '') }}</textarea>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        {{-- Sort Order --}}
        <div class="mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
        </div>

        {{-- Meta Title --}}
        <div class="mb-3">
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title) }}">
        </div>

        {{-- Meta Description --}}
        <div class="mb-3">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
        </div>

        {{-- Main Image --}}
        <div class="mb-3">
            <label class="form-label">Main Image</label>
            @if($product->image_url)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image_url) }}" width="100" class="rounded border">
                </div>
            @endif
            <input type="file" name="main_image" class="form-control">
        </div>

        {{-- Gallery Images --}}
        <div class="mb-3">
            <label class="form-label">Gallery Images</label>
            @if(is_array($product->gallery_images))
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach($product->gallery_images as $img)
                        <img src="{{ asset('storage/' . $img) }}" width="80" height="80" class="rounded border">
                    @endforeach
                </div>
            @endif
            <input type="file" name="gallery_images[]" class="form-control" multiple>
        </div>

        {{-- Submit --}}
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

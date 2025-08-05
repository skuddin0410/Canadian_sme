@extends('layouts.admin')

@section('content')
div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
                
                <form method="POST" action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}">
                    @csrf
                    @if(isset($product))
                        @method('PUT')
                    @endif
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="6" required>{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Features -->
                                <div class="form-group">
                                    <label>Product Features</label>
                                    <div id="features-container">
                                        @if(isset($product) && $product->features)
                                            @foreach($product->features as $index => $feature)
                                                <div class="input-group mb-2 feature-item">
                                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 feature-item">
                                                <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-feature">
                                        <i class="fas fa-plus"></i> Add Feature
                                    </button>
                                </div>

                                <!-- Benefits -->
                                <div class="form-group">
                                    <label>Product Benefits</label>
                                    <div id="benefits-container">
                                        @if(isset($product) && $product->benefits)
                                            @foreach($product->benefits as $index => $benefit)
                                                <div class="input-group mb-2 benefit-item">
                                                    <input type="text" class="form-control" name="benefits[]" value="{{ $benefit }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-danger remove-benefit">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 benefit-item">
                                                <input type="text" class="form-control" name="benefits[]" placeholder="Enter benefit">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-danger remove-benefit">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-benefit">
                                        <i class="fas fa-plus"></i> Add Benefit
                                    </button>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image_url">Product Image URL</label>
                                    <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                           id="image_url" name="image_url" value="{{ old('image_url', $product->image_url ?? '') }}">
                                    @error('image_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" 
                                               name="is_active" value="1" 
                                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sort_order">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" min="0" 
                                           value="{{ old('sort_order', $product->sort_order ?? 0) }}">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- SEO Fields -->
                                <h5>SEO Settings</h5>
                                <div class="form-group">
                                    <label for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" name="meta_title" maxlength="255"
                                           value="{{ old('meta_title', $product->meta_title ?? '') }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="meta_description">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ isset($product) ? 'Update Product' : 'Create Product' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Feature functionality
    document.getElementById('add-feature').addEventListener('click', function() {
        const container = document.getElementById('features-container');
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2 feature-item';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newFeature);
    });

    // Remove Feature functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const container = document.getElementById('features-container');
            if (container.children.length > 1) {
                e.target.closest('.feature-item').remove();
            }
        }
    });

    // Add Benefit functionality
    document.getElementById('add-benefit').addEventListener('click', function() {
        const container = document.getElementById('benefits-container');
        const newBenefit = document.createElement('div');
        newBenefit.className = 'input-group mb-2 benefit-item';
        newBenefit.innerHTML = `
            <input type="text" class="form-control" name="benefits[]" placeholder="Enter benefit">
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger remove-benefit">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newBenefit);
    });

    // Remove Benefit functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-benefit')) {
            const container = document.getElementById('benefits-container');
            if (container.children.length > 1) {
                e.target.closest('.benefit-item').remove();
            }
        }
    });
});
</script>
@endsection

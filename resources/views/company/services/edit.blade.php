@extends('layouts.admin')

@section('content')
<div class="container">
    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Add New Service</h4>
                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back to Services
                </a>
            </div>

            <div class="card-body row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4"
                            required>{{ old('description') }}</textarea>
                    </div>

                  
                    <div class="mb-3">
                       <label class="form-label">Product Price <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <span id="title-icon" class="input-group-text">{{config('app.currency_sign')}}</span>
                        <input type="text" name="price" value="{{ old('price') }}" class="form-control" required>
                          @if ($errors->has('price'))
                            <span class="text-danger text-left">{{ $errors->first('price') }}</span>
                          @endif
                    </div>
                    </div>
           

                    <!-- Capabilities -->
                    <div class="mb-3">
                        <label class="form-label">Capabilities <small class="text-muted">(one per line)</small></label>
                        <textarea name="capabilities" class="form-control" rows="3">{{ old('capabilities') }}</textarea>
                    </div>

                    <!-- Deliverables -->
                    <div class="mb-3">
                        <label class="form-label">Deliverables <small class="text-muted">(one per line)</small></label>
                        <textarea name="deliverables" class="form-control" rows="3">{{ old('deliverables') }}</textarea>
                    </div>

                    <!-- Meta Title -->
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                    </div>

                    <!-- Meta Description -->
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control"
                            rows="2">{{ old('meta_description') }}</textarea>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" class="form-control"
                            rows="3">{{ old('meta_keywords') }}</textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="{{ old('duration') }}">
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                {{ old('is_active', true) ? 'checked' : '' }} value="1">
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    <!-- Main Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Main Image</label>
                        <input type="file" name="main_image" class="form-control" accept="image/*">
                    </div>

                    <!-- Gallery Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Gallery Images <small>(multiple files allowed)</small></label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-save"></i> Save Service
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@extends('layouts.admin')
@section('title')
    Admin | Add New Service
@endsection
@section('content')
<div class="container">
    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Add New Service</h4>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="bx bx-arrow-back"></i> Back to Services
                </a>
            </div>

            <div class="card-body row">
                <!-- Service Name -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Service Price -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Service Price <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span id="title-icon" class="input-group-text">{{ config('app.currency_sign') }}</span>
                            <input type="text" name="price" value="{{ old('price') }}" class="form-control">
                        </div>
                        @if ($errors->has('price'))
                            <span class="text-danger">{{ $errors->first('price') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Description (Full Width) -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" placeholder="Service Description" rows="8">{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="text-danger">{{ $errors->first('description') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Capabilities -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Capabilities <small class="text-muted">(one per line)</small></label>
                        <textarea name="capabilities" class="form-control" rows="8">{{ old('capabilities') }}</textarea>
                        @if ($errors->has('capabilities'))
                            <span class="text-danger">{{ $errors->first('capabilities') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Deliverables -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Deliverables <small class="text-muted">(one per line)</small></label>
                        <textarea name="deliverables" class="form-control" rows="8">{{ old('deliverables') }}</textarea>
                        @if ($errors->has('deliverables'))
                            <span class="text-danger">{{ $errors->first('deliverables') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Meta Title -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                        @if ($errors->has('meta_title'))
                            <span class="text-danger">{{ $errors->first('meta_title') }}</span>
                        @endif
                    </div>
                </div>
                  <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Category<span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Meta Description -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="8">{{ old('meta_description') }}</textarea>
                    </div>
                </div>

                <!-- Meta Keywords -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" class="form-control" rows="8">{{ old('meta_keywords') }}</textarea>
                    </div>
                </div>

                <!-- Category -->
              

                <!-- Main Image -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Main Image</label>
                        <input type="file" name="main_image" class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Gallery Images <small>(multiple files allowed)</small></label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('services.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

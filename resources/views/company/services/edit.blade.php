@extends('layouts.admin')
@section('title')
    Admin | Update Service
@endsection
@section('content')
<div class="container">
    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Update Service</h4>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="bx bx-arrow-back"></i> Back to Services
                </a>
            </div>

            <div class="card-body row">
                {{-- Service Name --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $service->name) }}">
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                {{-- Price --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service Price <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text">{{ config('app.currency_sign') }}</span>
                        <input type="text" name="price" value="{{ old('price', $service->price) }}" class="form-control">
                    </div>
                    @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                    @endif
                </div>

                {{-- Description --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" class="form-control" rows="6" placeholder="Service Description">{{ old('description', $service->description) }}</textarea>
                    @if ($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                </div>

                {{-- Capabilities --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Capabilities <small class="text-muted">(one per line)</small></label>
                    <textarea name="capabilities" class="form-control" rows="5">{{ old('capabilities', $service->capabilities) }}</textarea>
                    @if ($errors->has('capabilities'))
                        <span class="text-danger">{{ $errors->first('capabilities') }}</span>
                    @endif
                </div>

                {{-- Deliverables --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Deliverables <small class="text-muted">(one per line)</small></label>
                    <textarea name="deliverables" class="form-control" rows="5">{{ old('deliverables', $service->deliverables) }}</textarea>
                    @if ($errors->has('deliverables'))
                        <span class="text-danger">{{ $errors->first('deliverables') }}</span>
                    @endif
                </div>

                {{-- Meta Title --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $service->meta_title) }}">
                    @if ($errors->has('meta_title'))
                        <span class="text-danger">{{ $errors->first('meta_title') }}</span>
                    @endif
                </div>
                 {{-- Category --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Meta Description --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $service->meta_description) }}</textarea>
                </div>

                {{-- Meta Keywords --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <textarea name="meta_keywords" class="form-control" rows="3">{{ old('meta_keywords', $service->meta_keywords) }}</textarea>
                </div>

               

                {{-- Main Image --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Main Image</label>
                    <input type="file" name="main_image" class="form-control" accept="image/*">
                    @if(!empty($service->image_url))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $service->image_url) }}" alt="{{ $service->name }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border">
                        </div>
                    @endif
                </div>

                {{-- Gallery Images --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gallery Images <small>(multiple files allowed)</small></label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    @if($service->gallery_images)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($service->gallery_images as $img)
                                <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('services.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save"></i>Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

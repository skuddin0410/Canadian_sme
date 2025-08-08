@extends('layouts.admin')
@section('title')
    Admin | Update Service
@endsection
@section('content')
<div class="container">
    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
            @method('PUT')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Update Service</h4>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="bx bx-arrow-back"></i> Back to Services
                </a>
            </div>

            <div class="card-body row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name' ,$service->name) }}">
                          @if ($errors->has('name'))
                          <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                          @endif
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea
                          type="text"
                          name="description"
                          id="description"
                          class="form-control"
                          placeholder="Service Description"
                          rows="8" cols="50"
                        >{{ old('description',$service->description) }}</textarea>

                        @if ($errors->has('description'))
                            <span class="text-danger text-left">{{ $errors->first('description') }}</span>
                          @endif
                    </div>

                  
                    <div class="mb-3">
                       <label class="form-label">Product Price <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <span id="title-icon" class="input-group-text">{{config('app.currency_sign')}}</span>
                        <input type="text" name="price" value="{{ old('price',$service->price) }}" class="form-control">
                         
                    </div>
                     @if ($errors->has('price'))
                            <span class="text-danger text-left">{{ $errors->first('price') }}</span>
                          @endif
                    </div>
           

                    <!-- Capabilities -->
                    <div class="mb-3">
                        <label class="form-label">Capabilities <small class="text-muted">(one per line)</small></label>
                        <textarea name="capabilities" class="form-control" rows="8" cols="50">{{ old('capabilities',$service->capabilities) }}</textarea>
                        @if ($errors->has('capabilities'))
                          <span class="text-danger text-left">{{ $errors->first('capabilities') }}</span>
                        @endif
                    </div>

                    <!-- Deliverables -->
                    <div class="mb-3">
                        <label class="form-label">Deliverables <small class="text-muted">(one per line)</small></label>
                        <textarea name="deliverables" class="form-control" rows="8" cols="50">{{ old('deliverables',$service->deliverables) }}</textarea>
                        @if ($errors->has('deliverables'))
                          <span class="text-danger text-left">{{ $errors->first('deliverables') }}</span>
                        @endif
                    </div>

                    <!-- Meta Title -->
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title',$service->meta_title) }}">
                         @if ($errors->has('meta_title'))
                          <span class="text-danger text-left">{{ $errors->first('meta_title') }}</span>
                        @endif
                    </div>

                    <!-- Meta Description -->
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control"
                            rows="8" cols="50">{{ old('meta_description',$service->meta_description) }}</textarea>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <textarea name="meta_keywords" class="form-control"
                            rows="8" cols="50">{{ old('meta_keywords',$service->meta_keywords) }}</textarea>
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
                                {{ old('category_id',$service->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="{{ old('duration',$service->duration) }}">
                         @if ($errors->has('duration'))
                          <span class="text-danger text-left">{{ $errors->first('duration') }}</span>
                        @endif
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order' ,$service->sort_order) }}">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                {{ $service->is_active ==1 ? 'checked' : '' }} value="1">
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Main Image</label>
                        <input type="file" name="main_image" class="form-control" accept="image/*">

                        @if(!empty($service->image_url) )
                        <div class="d-flex flex-wrap gap-2 mt-2">
                          <span class="fw-medium me-2"><img src="{{ asset('storage/' . $service->image_url) }}"
                              alt="{{ $service->name }}" style="width: 80px; height: 80px; object-fit: cover;"
                              class="rounded border"></span>
                        </div>
                        @endif
                    </div>

                    <!-- Gallery Image Upload -->
                    <div class="mb-3">
                        <label class="form-label">Gallery Images <small>(multiple files allowed)</small></label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>

                         @if($service->gallery_images)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                          @foreach($service->gallery_images as $img)
                          <span class="fw-medium me-2">
                            <img src="{{ asset('storage/' . $img) }}" style="width: 80px; height: 80px; object-fit: cover;"
                              class="rounded border">
                            @endforeach
                        </div>
                        @endif
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                 <a href="{{route('services.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Update Service
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@extends('layouts.admin')

@section('title')
    Admin | Edit Service Category
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y">
    <h4 class="mb-4">Edit Service Category</h4>

    <div class="card">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('service-categories.update', ['service_category' => $serviceCategory->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ old('name', $serviceCategory->name) }}"
            required
            maxlength="255"
            placeholder="Enter category name"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea
            class="form-control @error('description') is-invalid @enderror"
            id="description"
            name="description"
            rows="5"
            placeholder="Enter description (optional)"
        >{{ old('description', $serviceCategory->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Category Image</label><br>
        @if($serviceCategory->image_url)
            <img src="{{ $serviceCategory->image_url }}" alt="{{ $serviceCategory->name }}" class="img-thumbnail mb-2" style="max-width: 150px;">
        @endif
        <input
            type="file"
            class="form-control @error('image') is-invalid @enderror"
            id="image"
            name="image"
            accept="image/*"
        >
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Upload a new image to replace the current one.</small>
    </div>

   
    <div class="d-flex pt-3 justify-content-end">
                <a href="{{route('service-categories.index')}}"
                  class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"><i class="bx bx-save"></i>Save</button>
              </div>
</form>

        </div>
    </div>
</div>
@endsection

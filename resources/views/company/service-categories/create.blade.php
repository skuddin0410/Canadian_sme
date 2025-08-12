@extends('layouts.admin')

@section('title', 'Admin | Create Service Category')

@section('content')
<div class="container flex-grow-1 container-p-y">
    <h4 class="mb-4">Create / Service Category</h4>

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

            <form action="{{ route('service-categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        class="form-control @error('name') is-invalid @enderror" 
                        required 
                        maxlength="255"
                        placeholder="Enter service category name"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (optional)</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="5" 
                        class="form-control @error('description') is-invalid @enderror" 
                        placeholder="Enter description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Category Image (optional)</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        accept="image/*"
                        class="form-control @error('image') is-invalid @enderror"
                    >
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

              

            <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('service-categories.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"> <i class="bx bx-save"></i>Save</button>
          </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container mt-3">
    <h2>Event Guide Gallery</h2>

    {{-- Upload Form --}}
    <form action="{{ route('event-guides.uploadGallery') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="images" class="form-label">Upload Images</label>
            <input type="file" name="images[]" multiple class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
    </form>

    <hr>

    {{-- Show Images --}}
   <div class="row mt-4">
    @if(!empty($images))
        @foreach($images as $image)
            <div class="col-md-3 mb-3">
                <div class="card position-relative shadow-sm border-0">
                    
                    {{-- Delete Button --}}
                    <form action="{{ route('event-guides.deleteGalleryImage') }}" method="POST" 
                          class="position-absolute" 
                          style="top:8px; right:8px; z-index:10;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="image" value="{{ $image }}">
                        <button type="submit" class="btn btn-sm btn-danger rounded-circle d-flex align-items-center justify-content-center" 
                                style="width:28px; height:28px; padding:0; font-size:16px; line-height:1;">
                            &times;
                        </button>
                    </form>

                    {{-- Image --}}
                    <img src="{{ asset('storage/' . $image) }}" 
                         class="card-img-top rounded" 
                         alt="Gallery Image" 
                         style="object-fit: cover; height:200px;">
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No images uploaded yet.</p>
    @endif
</div>

</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Branding & Media /</span> Image Gallery</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  {{-- Upload Form --}}
  
<div class="card mb-4">
  <div class="card-header">Upload Image</div>
  <div class="card-body">
    <form method="POST" action="{{ route('company.media.upload') }}" enctype="multipart/form-data">
      @csrf
      <div class="row align-items-end">
        <div class="col-md-6">
          <label for="media_files" class="form-label">Choose Images</label>
          <input type="file" name="media_files[]" class="form-control" accept="image/*" multiple>
        </div>

        <div class="col-md-3 mt-4 mt-md-0">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>


  {{-- Media Gallery --}}
  {{-- <div class="card">
    <div class="card-header">Uploaded Media</div>
    <div class="card-body">
      @if ($company->mediaGallery->count())
        <div class="row">
          @foreach ($company->mediaGallery as $image)
            <div class="col-md-3 mb-4">
              <div class="border rounded p-2">
                <img src="{{ asset('storage/' . $image->file_name) }}" alt="Gallery Image" class="img-fluid rounded mb-2">
                <p class="small text-muted text-break">{{ basename($image->file_name) }}</p>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-muted">No media uploaded yet.</p>
      @endif
    </div>
  </div> --}}
  {{-- Media Gallery --}}
<div class="card">
  <div class="card-header">Image Gallery</div>
  <div class="card-body">
    @if ($company->mediaGallery->count())
      <div class="row">
        @foreach ($company->mediaGallery as $image)
          <div class="col-md-3 mb-4">
            <div class="border rounded p-2 text-center">
              <a href="{{ asset('storage/' . $image->file_name) }}" data-lightbox="gallery" data-title="{{ basename($image->file_name) }}">
                <img src="{{ asset('storage/' . $image->file_name) }}" alt="Gallery Image" class="img-fluid rounded mb-2" style="max-height: 150px;">
              </a>
              <p class="small text-muted text-break">{{ basename($image->file_name) }}</p>
              <form method="POST" action="{{ route('company.media.delete', $image->id) }}" onsubmit="return confirm('Are you sure you want to delete this image?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
      </form>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-muted">No media uploaded yet.</p>
    @endif
  </div>
</div>

</div>
@endsection
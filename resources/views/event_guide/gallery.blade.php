@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  .gallery-card { transition: transform .18s ease, box-shadow .18s ease; }
  .gallery-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
  .gallery-thumb { width: 100%; height: 100%; object-fit: cover; transition: transform .25s ease; }
  .gallery-card:hover .gallery-thumb { transform: scale(1.02); }
</style>
<div class="container flex-grow-1 container-p-y pt-0">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h2 class="mb-0"> Gallery</h2>
    @if(!empty($images))
      <span class="badge bg-secondary">{{ count($images) }} image{{ count($images) > 1 ? 's' : '' }}</span>
    @endif
  </div>

  {{-- Upload Form (UI only changes) --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form action="{{ route('event-guides.uploadGallery') }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf
            <div class="row g-2 align-items-end">
            <div class="col-8">
            <label for="images" class="form-label fw-semibold">Upload Images</label>
            <input id="images" type="file" name="images[]" multiple class="form-control" required>
            
            </div>
            <div class="col-4">
            <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload
            </button>
            </div>
            <div class="form-text">You can select multiple files. JPG/PNG/GIF (max ~5MB each).</div>
            </div>

      </form>
    </div>
  </div>

  {{-- Gallery Grid --}}
  <div class="row g-3">
    @if(!empty($images))
      @foreach($images as $image)
        @php $src = asset('storage/' . $image); @endphp
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card gallery-card shadow-sm border-0 h-100 position-relative overflow-hidden">
            {{-- Delete Button (same route) --}}
            <form action="{{ route('event-guides.deleteGalleryImage') }}" method="POST"
                  class="position-absolute top-0 end-0 m-2 z-3">
              @csrf
              @method('DELETE')
              <input type="hidden" name="image" value="{{ $image }}">
              <button type="submit"
                      class="btn btn-sm btn-danger rounded-circle d-flex align-items-center justify-content-center shadow"
                      title="Delete image" aria-label="Delete image"
                      style="width:30px; height:30px;">
                <i class="bi bi-x-lg" style="font-size:12px;"></i>
              </button>
            </form>

            {{-- Image --}}
            <div class="ratio ratio-4x3">
              <img src="{{ $src }}" class="gallery-thumb" alt="Gallery Image">
            </div>

            {{-- Footer with actions (preview) --}}
            <div class="card-body p-2 d-flex justify-content-between align-items-center">
              <small class="text-truncate pe-2" title="{{ basename($image) }}">{{ basename($image) }}</small>
              <button type="button"
                      class="btn btn-sm btn-outline-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#previewModal"
                      data-src="{{ $src }}">
                <i class="bi bi-arrows-fullscreen me-1"></i> Preview
              </button>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="col-12">
        <div class="text-center text-muted py-5 border rounded bg-white">
          <i class="bi bi-images fs-1 d-block mb-2"></i>
          No images uploaded yet.
        </div>
      </div>
    @endif
  </div>
</div>

{{-- Preview Modal (UI only) --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header">
        <h5 class="modal-title">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-dark">
        <img src="" alt="Preview" id="previewImage" class="w-100" style="max-height:80vh; object-fit:contain;">
      </div>
    </div>
  </div>
</div>

<script>
  // Fill modal on Preview click
  const previewModal = document.getElementById('previewModal');
  previewModal?.addEventListener('show.bs.modal', (e) => {
    const btn = e.relatedTarget;
    const src = btn?.getAttribute('data-src') || '';
    document.getElementById('previewImage')?.setAttribute('src', src);
  });
</script>
@endsection

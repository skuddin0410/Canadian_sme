@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  .gallery-card { transition: transform .18s ease, box-shadow .18s ease; }
  .gallery-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
  .gallery-thumb { width: 100%; height: 100%; object-fit: cover; transition: transform .25s ease; }
  .gallery-card:hover .gallery-thumb { transform: scale(1.02); }
  .file-preview-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    color: #adb5bd;
    height: 100%;
    font-size: 3rem;
  }
  .nav-tabs-custom .nav-link { border: none; border-bottom: 3px solid transparent; font-weight: 500; color: #697a8d; padding: 1rem 1.5rem; }
  .nav-tabs-custom .nav-link.active { border-bottom-color: #696cff; color: #696cff; background: transparent; }
</style>

@php
    $approvedItems = $galleryItems->where('is_approved', true);
    $pendingItems = $galleryItems->where('is_approved', false);

    function getFileIcon($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf' => 'bi-file-earmark-pdf text-danger',
            default => 'bi-file-earmark-text text-secondary',
        };
    }
@endphp

<div class="container flex-grow-1 container-p-y pt-0">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h2 class="mb-0"> Gallery</h2>
    <span class="badge bg-secondary">{{ $galleryItems->count() }} file{{ $galleryItems->count() > 1 ? 's' : '' }}</span>
  </div>

  {{-- Tabs Header (Only if Superadmin to show Approval area) --}}
  @if(isSuperAdmin())
  <ul class="nav nav-tabs nav-tabs-custom mb-4" id="galleryTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-content" type="button" role="tab">
        <i class="bi bi-images me-1"></i> Public Gallery
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="approval-tab" data-bs-toggle="tab" data-bs-target="#approval-content" type="button" role="tab">
        <i class="bi bi-check-circle me-1"></i> Gallery Approval 
        @if($pendingItems->count() > 0)
          <span class="badge rounded-pill bg-danger ms-1">{{ $pendingItems->count() }}</span>
        @endif
      </button>
    </li>
  </ul>
  @endif

  <div class="tab-content p-0" id="galleryTabsContent">
    
    {{-- Main Gallery Content (Everyone) --}}
    <div class="tab-pane fade show active" id="gallery-content" role="tabpanel">
      
      {{-- Upload Form (Always above the gallery) --}}
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <form action="{{ route('event-guides.uploadGallery') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-8">
                    <label for="images" class="form-label fw-semibold">Upload Files</label>
                    <input id="images" type="file" name="images[]" multiple class="form-control" required>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload
                    </button>
                </div>
                <div class="form-text">
                  Supported: JPG, PNG, GIF, PDF, Video. Max 10MB each. 
                  @if(!isSuperAdmin())
                    <span class="text-warning fw-bold">Note: Uploads require approval before appearing in the public gallery.</span>
                  @endif
                </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Categorized Gallery --}}
      @php
          $cats = [
              'images' => $approvedItems->where('file_type', 'image'),
              'videos' => $approvedItems->where('file_type', 'video'),
              'documents' => $approvedItems->where('file_type', 'document')
          ];
      @endphp
      
      <div class="accordion" id="galleryAccordion">
        @foreach(['images' => ['icon' => 'bi-images', 'color' => 'text-primary', 'label' => 'Images'], 
                  'videos' => ['icon' => 'bi-play-btn', 'color' => 'text-danger', 'label' => 'Videos'], 
                  'documents' => ['icon' => 'bi-file-earmark-text', 'color' => 'text-success', 'label' => 'Documents']] as $key => $meta)
          <div class="accordion-item shadow-sm border-0 mb-3">
            <h2 class="accordion-header">
              <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key }}">
                <i class="bi {{ $meta['icon'] }} me-2 {{ $meta['color'] }}"></i> {{ $meta['label'] }} ({{ $cats[$key]->count() }})
              </button>
            </h2>
            <div id="collapse-{{ $key }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#galleryAccordion">
              <div class="accordion-body">
                <div class="row g-3">
                  @forelse($cats[$key] as $item)
                    <div class="col-6 col-md-4 col-lg-3">
                      @include('event_guide._gallery_item', ['item' => $item])
                    </div>
                  @empty
                    <div class="col-12 text-center text-muted py-3">No {{ strtolower($meta['label']) }} approved yet.</div>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- User's Own Pending Items (For non-superadmins) --}}
      @if(!isSuperAdmin())
        @php $myPending = $pendingItems->where('added_by', Auth::id()); @endphp
        @if($myPending->count() > 0)
          <div class="mt-5">
            <h4 class="mb-3 text-warning"><i class="bi bi-clock-history me-2"></i> My Pending Approvals</h4>
            <div class="row g-3">
              @foreach($myPending as $item)
                <div class="col-6 col-md-4 col-lg-3">
                  @include('event_guide._gallery_item', ['item' => $item])
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endif
    </div>

    {{-- Approval Tab Content (Superadmin only) --}}
    @if(isSuperAdmin())
    <div class="tab-pane fade" id="approval-content" role="tabpanel">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-hourglass-split me-2 text-warning"></i> Pending for Review</h5>
          @if($pendingItems->count() > 0)
            <form action="{{ route('event-guides.approveAllGalleryItems') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-check-all me-1"></i> Approve All
              </button>
            </form>
          @endif
        </div>
        <div class="card-body pt-4">
          @if($pendingItems->count() > 0)
            <div class="row g-3">
              @foreach($pendingItems as $item)
                <div class="col-6 col-md-4 col-lg-3">
                  @include('event_guide._gallery_item', ['item' => $item, 'is_review' => true])
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-5">
              <i class="bi bi-clipboard-check fs-1 text-success d-block mb-2"></i>
              <p class="text-muted">All clear! No pending items for approval.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
    @endif

  </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header">
        <h5 class="modal-title">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-dark text-center">
        <img src="" alt="Preview" id="previewImage" class="w-100" style="max-height:80vh; object-fit:contain;">
      </div>
    </div>
  </div>
</div>

<script>
  const previewModal = document.getElementById('previewModal');
  previewModal?.addEventListener('show.bs.modal', (e) => {
    const btn = e.relatedTarget;
    const src = btn?.getAttribute('data-src') || '';
    document.getElementById('previewImage')?.setAttribute('src', src);
  });
</script>
@endsection

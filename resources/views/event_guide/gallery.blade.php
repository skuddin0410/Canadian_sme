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
  .nav-pills-filetype .nav-link { background: #f0f0f5; color: #697a8d; font-weight: 500; border-radius: 8px; padding: 0.6rem 1.2rem; margin-right: 0.5rem; transition: all .2s ease; }
  .nav-pills-filetype .nav-link.active { background: #696cff; color: #fff; box-shadow: 0 4px 12px rgba(105,108,255,.3); }
  .nav-pills-filetype .nav-link:hover:not(.active) { background: #e2e2ea; }

  /* New Preview Styles */
  .preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
  .preview-card { width: 100px; position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 5px; background: #fff; }
  .preview-card img, .preview-card .file-icon { width: 100%; height: 80px; object-fit: cover; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 2rem; background: #f8f9fa; }
  .preview-card .remove-btn { position: absolute; top: -5px; right: -5px; background: #ff3e1d; color: #fff; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
  .preview-card .file-name { font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 5px; text-align: center; }
  .preview-card .file-size { font-size: 9px; color: #888; text-align: center; }
  #size-meter { font-size: 0.85rem; padding: 5px 10px; border-radius: 20px; background: #e7e7ff; color: #696cff; display: inline-block; }
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

  {{-- Top-level Tabs (Superadmin: Public Gallery + Gallery Approval) --}}
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
      
      {{-- Upload Form --}}
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <form id="uploadForm" action="{{ route('event-guides.uploadGallery') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="event_id" class="form-label fw-semibold">Select Event</label>
                    <select id="event_id" name="event_id" class="form-select" required>
                        <option value="">-- Choose Event --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="images" class="form-label fw-semibold">Upload Files</label>
                    <input id="images" type="file" name="images[]" multiple class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload
                    </button>
                </div>
            </div>

            <div id="preview-container" class="preview-grid d-none"></div>

            <div class="d-flex align-items-center justify-content-between flex-wrap mt-3 pt-2 border-top">
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i> 
                <strong>Limits:</strong> 
                <span class="badge bg-label-info ms-1">Images: 10MB</span>
                <span class="badge bg-label-danger ms-1">PDFs: 10MB</span>
                <span class="badge bg-label-warning ms-1">Videos: 10MB</span>
              </div>
              <div id="size-summary" class="d-none">
                <span id="size-meter">Total Selection: 0 MB</span>
              </div>
            </div>

            @if(!isSuperAdmin())
              <div class="form-text mt-2 text-warning fw-bold text-center">
                Note: Uploads require approval before appearing in the public gallery.
              </div>
            @endif
          </form>
        </div>
      </div>

      {{-- File Type Tabs (Images / Videos / Documents) --}}
      @php
          $imageItems = $approvedItems->where('file_type', 'image');
          $videoItems = $approvedItems->where('file_type', 'video');
          $docItems   = $approvedItems->where('file_type', 'document');

          $imagesByEvent = $imageItems->groupBy(fn($item) => $item->event->title ?? 'Uncategorized');
          $videosByEvent = $videoItems->groupBy(fn($item) => $item->event->title ?? 'Uncategorized');
          $docsByEvent   = $docItems->groupBy(fn($item) => $item->event->title ?? 'Uncategorized');
      @endphp

      <ul class="nav nav-pills nav-pills-filetype gap-2 mb-4" id="fileTypeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="images-tab" data-bs-toggle="pill" data-bs-target="#images-pane" type="button" role="tab">
            <i class="bi bi-images me-1"></i> Images <span class="badge bg-white text-primary ms-1">{{ $imageItems->count() }}</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="videos-tab" data-bs-toggle="pill" data-bs-target="#videos-pane" type="button" role="tab">
            <i class="bi bi-play-btn me-1"></i> Videos <span class="badge bg-white text-primary ms-1">{{ $videoItems->count() }}</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="docs-tab" data-bs-toggle="pill" data-bs-target="#docs-pane" type="button" role="tab">
            <i class="bi bi-file-earmark-text me-1"></i> Documents <span class="badge bg-white text-primary ms-1">{{ $docItems->count() }}</span>
          </button>
        </li>
      </ul>

      <div class="tab-content p-0" id="fileTypeTabsContent">

        {{-- Images Pane --}}
        <div class="tab-pane fade show active" id="images-pane" role="tabpanel">
          @if($imagesByEvent->count() > 0)
            <div class="accordion" id="imagesAccordion">
              @foreach($imagesByEvent as $eventName => $items)
                @php $imgId = 'img-' . Str::slug($eventName) . '-' . $loop->index; @endphp
                <div class="accordion-item shadow-sm border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $imgId }}">
                      <i class="bi bi-calendar-event me-2 text-primary"></i> {{ $eventName }} <span class="badge bg-primary ms-2">{{ $items->count() }}</span>
                    </button>
                  </h2>
                  <div id="{{ $imgId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#imagesAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        @foreach($items as $item)
                          <div class="col-6 col-md-4 col-lg-3">
                            @include('event_guide._gallery_item', ['item' => $item])
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-5 border rounded bg-white">
              <i class="bi bi-images fs-1 d-block mb-2"></i>
              No approved images yet.
            </div>
          @endif
        </div>

        {{-- Videos Pane --}}
        <div class="tab-pane fade" id="videos-pane" role="tabpanel">
          @if($videosByEvent->count() > 0)
            <div class="accordion" id="videosAccordion">
              @foreach($videosByEvent as $eventName => $items)
                @php $vidId = 'vid-' . Str::slug($eventName) . '-' . $loop->index; @endphp
                <div class="accordion-item shadow-sm border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $vidId }}">
                      <i class="bi bi-calendar-event me-2 text-primary"></i> {{ $eventName }} <span class="badge bg-primary ms-2">{{ $items->count() }}</span>
                    </button>
                  </h2>
                  <div id="{{ $vidId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#videosAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        @foreach($items as $item)
                          <div class="col-6 col-md-4 col-lg-3">
                            @include('event_guide._gallery_item', ['item' => $item])
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-5 border rounded bg-white">
              <i class="bi bi-play-btn fs-1 d-block mb-2"></i>
              No approved videos yet.
            </div>
          @endif
        </div>

        {{-- Documents Pane --}}
        <div class="tab-pane fade" id="docs-pane" role="tabpanel">
          @if($docsByEvent->count() > 0)
            <div class="accordion" id="docsAccordion">
              @foreach($docsByEvent as $eventName => $items)
                @php $docId = 'doc-' . Str::slug($eventName) . '-' . $loop->index; @endphp
                <div class="accordion-item shadow-sm border-0 mb-3">
                  <h2 class="accordion-header">
                    <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $docId }}">
                      <i class="bi bi-calendar-event me-2 text-primary"></i> {{ $eventName }} <span class="badge bg-primary ms-2">{{ $items->count() }}</span>
                    </button>
                  </h2>
                  <div id="{{ $docId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#docsAccordion">
                    <div class="accordion-body">
                      <div class="row g-3">
                        @foreach($items as $item)
                          <div class="col-6 col-md-4 col-lg-3">
                            @include('event_guide._gallery_item', ['item' => $item])
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-5 border rounded bg-white">
              <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
              No approved documents yet.
            </div>
          @endif
        </div>

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
            <form id="approveAllForm" action="{{ route('event-guides.approveAllGalleryItems') }}" method="POST">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const previewModal = document.getElementById('previewModal');
  previewModal?.addEventListener('show.bs.modal', (e) => {
    const btn = e.relatedTarget;
    const src = btn?.getAttribute('data-src') || '';
    document.getElementById('previewImage')?.setAttribute('src', src);
  });

  // Interactive Upload Preview System
  let selectedFiles = [];
  const fileInput = document.getElementById('images');
  const previewContainer = document.getElementById('preview-container');
  const sizeSummary = document.getElementById('size-summary');
  const sizeMeter = document.getElementById('size-meter');

  fileInput?.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    
    files.forEach(file => {
      // Basic client-side check
      if (file.size > 10 * 1024 * 1024) {
        Swal.fire({ icon: 'error', title: 'File too large', text: `${file.name} exceeds the 10MB limit.` });
        return;
      }
      // Avoid duplicates
      if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
        selectedFiles.push(file);
      }
    });

    updateFileDisplay();
  });

  function updateFileDisplay() {
    previewContainer.innerHTML = '';
    let totalSize = 0;

    if (selectedFiles.length > 0) {
      previewContainer.classList.remove('d-none');
      sizeSummary.classList.remove('d-none');
    } else {
      previewContainer.classList.add('d-none');
      sizeSummary.classList.add('d-none');
    }

    selectedFiles.forEach((file, index) => {
      totalSize += file.size;
      const card = document.createElement('div');
      card.className = 'preview-card';
      
      let previewHtml = '';
      if (file.type.startsWith('image/')) {
        previewHtml = `<img src="${URL.createObjectURL(file)}">`;
      } else if (file.type === 'application/pdf') {
        previewHtml = `<div class="file-icon"><i class="bi bi-file-earmark-pdf text-danger"></i></div>`;
      } else if (file.type.startsWith('video/')) {
        previewHtml = `<div class="file-icon"><i class="bi bi-play-circle text-primary"></i></div>`;
      } else {
        previewHtml = `<div class="file-icon"><i class="bi bi-file-earmark"></i></div>`;
      }

      card.innerHTML = `
        <button type="button" class="remove-btn" onclick="removeSelectedFile(${index})">&times;</button>
        ${previewHtml}
        <div class="file-name" title="${file.name}">${file.name}</div>
        <div class="file-size">${(file.size / (1024 * 1024)).toFixed(2)} MB</div>
      `;
      previewContainer.appendChild(card);
    });

    sizeMeter.textContent = `Total Selection: ${(totalSize / (1024 * 1024)).toFixed(2)} MB`;
    
    // Crucial: Update the actual input file list so the form submits correctly
    syncFileInput();
  }

  window.removeSelectedFile = function(index) {
    selectedFiles.splice(index, 1);
    updateFileDisplay();
  };

  function syncFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
  }

  // Upload confirmation updated to use local selectedFiles
  document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
    if (this.dataset.confirmed) return; 
    
    e.preventDefault();
    const form = this;
    const eventDropdown = document.getElementById('event_id');
    const eventName = eventDropdown.options[eventDropdown.selectedIndex]?.text || '';
    const isSuperAdmin = {{ isSuperAdmin() ? 'true' : 'false' }};

    if (!eventDropdown.value) {
        Swal.fire({ icon: 'error', title: 'Event Required', text: 'Please select an event first!' });
        return;
    }

    if (selectedFiles.length === 0) {
        Swal.fire({ icon: 'error', title: 'Files Required', text: 'Please select at least one file to upload!' });
        return;
    }

    let message = `This will upload <strong>${selectedFiles.length} file(s)</strong> (${sizeMeter.textContent}) to <strong>${eventName}</strong>.`;
    if (!isSuperAdmin) {
        message += '<br><small class="text-info mt-2 d-block">Note: These will require approval before appearing.</small>';
    }

    Swal.fire({
        title: 'Confirm Upload',
        html: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#8592a3',
        confirmButtonText: 'Yes, upload it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = "true";
            form.submit();
        }
    });
  });
  // Restore Approve All confirmation
  document.getElementById('approveAllForm')?.addEventListener('submit', function(e) {
    if (this.dataset.confirmed) return; 
    
    e.preventDefault();
    const form = this;

    Swal.fire({
        title: 'Approve All Items',
        text: 'Are you sure you want to approve all pending gallery items at once?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#71dd37',
        cancelButtonColor: '#8592a3',
        confirmButtonText: 'Yes, approve all!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = "true";
            form.submit();
        }
    });
  });
</script>
@endsection

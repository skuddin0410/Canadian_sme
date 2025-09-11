@extends('layouts.admin')

@section('title')
    Admin | Exhibitor 
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4">
      <span class="text-muted fw-light"> Exhibitor /</span> Create
  </h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Create Exhibitor </h5>
        </div>

        <div class="card-body">
          <form action="{{ route('exhibitor-users.store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
            @csrf
            {{-- Row for Content Icon & Quick Link Icon --}}
            <div class="row">
              <div class="row">
  {{-- Content Icon --}}
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Logo</label>

      @php
        $contentIconFile = !empty($company?->contentIconFile) && !empty($company->contentIconFile->file_path);
        $contentIconSrc = $contentIconFile 
            ? (Str::startsWith($company->contentIconFile->file_path, ['http://','https://'])
                ? $company->contentIconFile->file_path
                : Storage::url($company->contentIconFile->file_path))
            : '';
      @endphp

      <div id="content-icon-dropzone"
     class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
     style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

    {{-- Placeholder --}}
    <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ $contentIconFile ? 'd-none' : '' }}">
      <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
      <div>
        <strong>Drag & drop</strong> an image here, or
        <button type="button" id="dz-browse-content" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
      </div>
      <small class="text-muted d-block">Max 2048 KB</small>
    </div>

    {{-- Inline preview --}}
    <img id="dz-image-content"
         src="{{ $contentIconSrc }}"
         alt="Preview"
         class="{{ $contentIconFile ? '' : 'd-none' }} rounded"
         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

    {{-- Remove button --}}
    <button type="button"
            id="dz-remove-content"
            class="btn btn-sm btn-danger position-absolute {{ $contentIconFile ? '' : 'd-none' }}"
            style="top: .5rem; right: .5rem;">
      <i class="bx bx-x"></i> Remove
    </button>

    {{-- Hidden input --}}
    <input type="file" id="dz-input-content" name="content_icon" accept="image/*" class="d-none">
</div>

      @error('content_icon')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Quick Link Icon --}}
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Banner</label>

      @php
        $quickLinkFile = !empty($company?->quickLinkIconFile) && !empty($company->quickLinkIconFile->file_path);
        $quickLinkSrc = $quickLinkFile 
            ? (Str::startsWith($company->quickLinkIconFile->file_path, ['http://','https://'])
                ? $company->quickLinkIconFile->file_path
                : Storage::url($company->quickLinkIconFile->file_path))
            : '';
      @endphp

     <div id="quick-link-icon-dropzone"
     class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
     style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

    {{-- Placeholder --}}
    <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ $quickLinkFile ? 'd-none' : '' }}">
        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
        <div>
            <strong>Drag & drop</strong> an image here, or
            <button type="button" id="dz-browse-quick" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
        </div>
        <small class="text-muted d-block">Max 2048 KB</small>
    </div>

    {{-- Inline preview --}}
    <img id="dz-image-quick"
         src="{{ $quickLinkSrc }}"
         alt="Preview"
         class="{{ $quickLinkFile ? '' : 'd-none' }} rounded"
         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

    {{-- Remove button --}}
    <button type="button"
            id="dz-remove-quick"
            class="btn btn-sm btn-danger position-absolute {{ $quickLinkFile ? '' : 'd-none' }}"
            style="top: .5rem; right: .5rem;">
        <i class="bx bx-x"></i> Remove
    </button>

    {{-- Hidden input --}}
    <input type="file" id="dz-input-quick" name="quick_link_icon" accept="image/*" class="d-none">
</div>

      @error('quick_link_icon')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>
</div>

  
            </div>

      

            <div class="row">
              

              {{-- Company Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                    <input type="text" name="company_name" class="form-control"
                           value="{{ old('company_name') }}" placeholder="Company Name" required>
                  </div>
                </div>
              </div>

              {{-- Company Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Email <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input type="email" name="company_email" class="form-control"
                           value="{{ old('company_email') }}" placeholder="Company Email" required>
                  </div>
                </div>
              </div>

              {{-- Company Phone --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Phone</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                    <input type="text" name="company_phone" class="form-control"
                           value="{{ old('company_phone') }}" placeholder="Company Phone">
                  </div>
                </div>
              </div>

            
               {{-- Website --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website') }}" placeholder="https://example.com" required>
                  </div>
                </div>
              </div>

              {{-- LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                    <input type="url" name="linkedin" class="form-control"
                           value="{{ old('linkedin') }}" placeholder="https://linkedin.com/company/...">
                  </div>
                </div>
              </div>

              {{-- Twitter --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Twitter</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                    <input type="url" name="twitter" class="form-control"
                           value="{{ old('twitter') }}" placeholder="https://twitter.com/...">
                  </div>
                </div>
              </div>

              {{-- Facebook --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Facebook</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                    <input type="url" name="facebook" class="form-control"
                           value="{{ old('facebook') }}" placeholder="https://facebook.com/...">
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Instagram</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                    <input type="url" name="instagram" class="form-control"
                           value="{{ old('instagram') }}" placeholder="https://instagram.com/...">
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Booth</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-book"></i></span>
                    <input type="text" name="booth" class="form-control"
                           value="{{ old('booth') }}">
                  </div>
                </div>
              </div>
               <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Industry</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-book"></i></span>
                    <input type="text" name="industry" class="form-control"
                           value="{{ old('industry') }}">
                  </div>
                </div>
              </div>
              
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Company Description</label>
                  <textarea name="company_description" class="form-control" rows="4"
                            placeholder="Brief description about the company">{{ old('company_description') }}</textarea>
                </div>
              </div>

              {{-- Submit --}}
              <div class="col-12">
                <div class="d-flex pt-3 justify-content-end">
                  <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>
              </div>

            </div> {{-- row end --}}
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('content-icon-dropzone');
    const input = document.getElementById('dz-input-content');
    const preview = document.getElementById('dz-image-content');
    const placeholder = document.getElementById('dz-placeholder-content');
    const removeBtn = document.getElementById('dz-remove-content');

    // Trigger file input when dropzone is clicked
    dropzone.addEventListener('click', function() {
      input.click();
    });

    // Handle file input change (when user selects a file)
    input.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(event) {
          preview.src = event.target.result;
          preview.classList.remove('d-none');
          placeholder.classList.add('d-none');
          removeBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      }
    });

    // Handle drag over and drop events
    dropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', function() {
      dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', function(e) {
      e.preventDefault();
      dropzone.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(event) {
          preview.src = event.target.result;
          preview.classList.remove('d-none');
          placeholder.classList.add('d-none');
          removeBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      }
    });

    // Remove the selected image
    removeBtn.addEventListener('click', function() {
      preview.src = '';
      preview.classList.add('d-none');
      placeholder.classList.remove('d-none');
      removeBtn.classList.add('d-none');
      input.value = ''; // Clear the file input
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const dropzoneQuick = document.getElementById('quick-link-icon-dropzone');
    const inputQuick = document.getElementById('dz-input-quick');
    const previewQuick = document.getElementById('dz-image-quick');
    const placeholderQuick = document.getElementById('dz-placeholder-quick');
    const removeBtnQuick = document.getElementById('dz-remove-quick');
    let fileRemoved = false; // Flag to track if the file has been removed

    // Trigger file input when dropzone is clicked
    dropzoneQuick.addEventListener('click', function() {
      if (!fileRemoved) { // Only trigger file input if the file wasn't removed
        inputQuick.click();
      }
    });

    // Handle file input change (when user selects a file)
    inputQuick.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(event) {
          previewQuick.src = event.target.result;
          previewQuick.classList.remove('d-none');
          placeholderQuick.classList.add('d-none');
          removeBtnQuick.classList.remove('d-none');
          fileRemoved = false; // Reset the file removed flag
        };
        reader.readAsDataURL(file);
      }
    });

    // Handle drag over and drop events
    dropzoneQuick.addEventListener('dragover', function(e) {
      e.preventDefault();
      dropzoneQuick.classList.add('dragover');
    });

    dropzoneQuick.addEventListener('dragleave', function() {
      dropzoneQuick.classList.remove('dragover');
    });

    dropzoneQuick.addEventListener('drop', function(e) {
      e.preventDefault();
      dropzoneQuick.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(event) {
          previewQuick.src = event.target.result;
          previewQuick.classList.remove('d-none');
          placeholderQuick.classList.add('d-none');
          removeBtnQuick.classList.remove('d-none');
          fileRemoved = false; // Reset the file removed flag
        };
        reader.readAsDataURL(file);
      }
    });

    // Remove the selected image
    removeBtnQuick.addEventListener('click', function() {
      previewQuick.src = '';
      previewQuick.classList.add('d-none');
      placeholderQuick.classList.remove('d-none');
      removeBtnQuick.classList.add('d-none');
      inputQuick.value = ''; // Clear the file input
      fileRemoved = true; // Set flag to indicate file is removed
    });
  });
</script>
@endsection








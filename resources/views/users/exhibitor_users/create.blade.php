@extends('layouts.admin')

@section('title')
    Admin | Exhibitor 
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4">
      <span class="text-muted fw-light"> Exhibitor /</span> Create
  </h4>

  {{-- Validation Errors --}}
  @if($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  {{-- Success Message --}}
  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif

  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Create Exhibitor </h5>
        </div>

        <div class="card-body">
          <form action="{{ route('exhibitor-users.store') }}" method="POST" autocomplete="off">
            @csrf
            {{-- Row for Content Icon & Quick Link Icon --}}
            <div class="row">
              <div class="row">
  {{-- Content Icon --}}
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Content Icon</label>

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
      <label class="form-label">Quick Link Icon</label>

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
                  <label class="form-label">Company Phone <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                    <input type="text" name="company_phone" class="form-control"
                           value="{{ old('company_phone') }}" placeholder="Company Phone" required>
                  </div>
                </div>
              </div>

             

              {{-- Company Description --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Description</label>
                  <textarea name="company_description" class="form-control" rows="4"
                            placeholder="Brief description about the company">{{ old('company_description') }}</textarea>
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
@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  function setupDropzone(wrapperId, inputId, previewId, placeholderId, removeId, browseId) {
    // Add detailed logging to identify missing elements
    console.log(`Setting up dropzone: ${wrapperId}`);
    
    const wrapper = document.getElementById(wrapperId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    const removeBtn = document.getElementById(removeId);
    const browseBtn = document.getElementById(browseId);

    // Detailed error reporting
    const elements = {
      wrapper: { element: wrapper, id: wrapperId },
      input: { element: input, id: inputId },
      preview: { element: preview, id: previewId },
      placeholder: { element: placeholder, id: placeholderId },
      removeBtn: { element: removeBtn, id: removeId },
      browseBtn: { element: browseBtn, id: browseId }
    };

    // Check which elements are missing
    const missingElements = Object.entries(elements).filter(([key, obj]) => !obj.element);
    
    if (missingElements.length > 0) {
      console.error(`Missing elements for ${wrapperId}:`, missingElements.map(([key, obj]) => `${key} (ID: ${obj.id})`));
      return false;
    }

    console.log(`✅ All elements found for ${wrapperId}`);

    // Handle browse button click
    browseBtn.addEventListener("click", (e) => {
      console.log(`Browse button clicked for ${wrapperId}`);
      e.preventDefault();
      e.stopPropagation();
      input.click();
    });

    // Handle wrapper click (avoid conflicts with browse/remove buttons)
    wrapper.addEventListener("click", (e) => {
      // Check if click is on browse button, remove button, or their children
      if (e.target === browseBtn || e.target === removeBtn || 
          browseBtn.contains(e.target) || removeBtn.contains(e.target)) {
        return;
      }
      e.preventDefault();
      input.click();
    });

    // Handle input file selection
    input.addEventListener("change", (e) => {
      console.log(`File input changed for ${wrapperId}`);
      if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        
        // File size validation (2MB = 2048KB)
        if (file.size > 2 * 1024 * 1024) {
          alert('File size must be less than 2MB');
          input.value = '';
          return;
        }
        
        // File type validation
        if (!file.type.startsWith('image/')) {
          alert('Please select an image file');
          input.value = '';
          return;
        }
        
        showPreview(file);
      }
    });

    // Show preview helper
    function showPreview(file) {
      console.log(`Showing preview for ${wrapperId}`);
      const reader = new FileReader();
      reader.onload = (ev) => {
        preview.src = ev.target.result;
        preview.classList.remove("d-none");
        placeholder.classList.add("d-none");
        removeBtn.classList.remove("d-none");
      };
      reader.onerror = () => {
        console.error('Error reading file');
        alert('Error reading file. Please try again.');
      };
      reader.readAsDataURL(file);
    }

    // Remove file
    removeBtn.addEventListener("click", (e) => {
      console.log(`Remove button clicked for ${wrapperId}`);
      e.preventDefault();
      e.stopPropagation();
      
      input.value = "";
      preview.src = "";
      preview.classList.add("d-none");
      placeholder.classList.remove("d-none");
      removeBtn.classList.add("d-none");
    });

    // Drag and drop handlers
    wrapper.addEventListener("dragover", (e) => {
      e.preventDefault();
      e.stopPropagation();
      wrapper.style.borderColor = "var(--bs-primary)";
    });

    wrapper.addEventListener("dragleave", (e) => {
      e.preventDefault();
      e.stopPropagation();
      wrapper.style.borderColor = "var(--bs-border-color)";
    });

    wrapper.addEventListener("drop", (e) => {
      e.preventDefault();
      e.stopPropagation();
      wrapper.style.borderColor = "var(--bs-border-color)";

      if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        
        // File size validation
        if (file.size > 2 * 1024 * 1024) {
          alert('File size must be less than 2MB');
          return;
        }
        
        // File type validation
        if (!file.type.startsWith('image/')) {
          alert('Please select an image file');
          return;
        }

        // Set the files property correctly
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;

        showPreview(file);
      }
    });

    return true;
  }

  // Add a small delay to ensure all elements are rendered
  setTimeout(() => {
    console.log('🚀 Starting dropzone initialization...');
    
    // Initialize content icon dropzone
    const contentSuccess = setupDropzone(
      "content-icon-dropzone",
      "dz-input-content",
      "dz-image-content",
      "dz-placeholder-content",
      "dz-remove-content",
      "dz-browse-content"
    );

    // Initialize quick link icon dropzone
    const quickSuccess = setupDropzone(
      "quick-link-icon-dropzone",
      "dz-input-quick",
      "dz-image-quick",
      "dz-placeholder-quick",
      "dz-remove-quick",
      "dz-browse-quick"
    );

    if (contentSuccess && quickSuccess) {
      console.log('✅ All dropzones initialized successfully');
    } else {
      console.error('❌ Some dropzones failed to initialize');
    }
  }, 100);
});
</script>
@endpush





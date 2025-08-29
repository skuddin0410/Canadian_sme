@extends('layouts.admin')

@section('title')
    Admin | Exhibitor Edit
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Exhibitor /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Exhibitor</h5>
        </div>
        <div class="card-body">

          {{-- Main Update Form --}}
          <form 
            action="{{ route('exhibitor-users.update', $user->id) }}" 
            method="POST" 
            enctype="multipart/form-data"
          >
            @csrf
            @method('PUT')

            <div class="row">
               {{-- Content Icon --}}
  <div class="col-6">
    <div class="mb-3">
      <label class="form-label">Content Icon</label>

      @php
        $contentIconFile = !empty($user->company?->contentIconFile) && !empty($user->company->contentIconFile->file_path);
        $contentIconSrc = $contentIconFile 
            ? (Str::startsWith($user->company->contentIconFile->file_path, ['http://','https://'])
                ? $user->company->contentIconFile->file_path
                : Storage::url($user->company->contentIconFile->file_path))
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

        {{-- Preview --}}
        <img id="dz-image-content"
             src="{{ $contentIconSrc }}"
             alt="Preview"
             class="{{ $contentIconFile ? '' : 'd-none' }} rounded"
             style="max-height: 180px; max-width: 100%; object-fit: contain;" />

        {{-- Remove --}}
        <button type="button"
                id="dz-remove-content"
                class="btn btn-sm btn-danger position-absolute {{ $contentIconFile ? '' : 'd-none' }}"
                style="top: .5rem; right: .5rem;">
          <i class="bx bx-x"></i> Remove
        </button>

        {{-- Input --}}
        <input type="file" id="dz-input-content" name="content_icon" accept="image/*" class="d-none">
      </div>

      @error('content_icon')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Quick Link Icon --}}
  <div class="col-6">
    <div class="mb-3">
      <label class="form-label">Quick Link Icon</label>

      @php
        $quickLinkFile = !empty($user->company?->quickLinkIconFile) && !empty($user->company->quickLinkIconFile->file_path);
        $quickLinkSrc = $quickLinkFile 
            ? (Str::startsWith($user->company->quickLinkIconFile->file_path, ['http://','https://'])
                ? $user->company->quickLinkIconFile->file_path
                : Storage::url($user->company->quickLinkIconFile->file_path))
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

        {{-- Preview --}}
        <img id="dz-image-quick"
             src="{{ $quickLinkSrc }}"
             alt="Preview"
             class="{{ $quickLinkFile ? '' : 'd-none' }} rounded"
             style="max-height: 180px; max-width: 100%; object-fit: contain;" />

        {{-- Remove --}}
        <button type="button"
                id="dz-remove-quick"
                class="btn btn-sm btn-danger position-absolute {{ $quickLinkFile ? '' : 'd-none' }}"
                style="top: .5rem; right: .5rem;">
          <i class="bx bx-x"></i> Remove
        </button>

        {{-- Input --}}
        <input type="file" id="dz-input-quick" name="quick_link_icon" accept="image/*" class="d-none">
      </div>

      @error('quick_link_icon')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    
              
                </div>
              </div>
              {{-- Company Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Name <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control" 
                         name="company_name" 
                         value="{{ old('company_name', optional($user->company)->name) }}" 
                         placeholder="Company Name" required>
                  @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Email <span class="text-danger">*</span></label>
                  <input type="email" 
                         class="form-control" 
                         name="company_email" 
                         value="{{ old('company_email', optional($user->company)->email) }}" 
                         placeholder="Company Email" required>
                  @error('company_email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Phone --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Phone <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control" 
                         name="company_phone" 
                         value="{{ old('company_phone', optional($user->company)->phone) }}" 
                         placeholder="Company Phone" required>
                  @error('company_phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Description --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="company_description" 
                            class="form-control" 
                            rows="4"
                            placeholder="Company Description">{{ old('company_description', optional($user->company)->description) }}</textarea>
                  @error('company_description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Website --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website</label>
                  <input type="url" class="form-control" name="website" 
                         value="{{ old('website', optional($user->company)->website) }}" 
                         placeholder="https://example.com">
                  @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <input type="url" class="form-control" name="linkedin" 
                         value="{{ old('linkedin', optional($user->company)->linkedin) }}" 
                         placeholder="LinkedIn Profile URL">
                  @error('linkedin') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Twitter --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Twitter</label>
                  <input type="url" class="form-control" name="twitter" 
                         value="{{ old('twitter', optional($user->company)->twitter) }}" 
                         placeholder="Twitter Profile URL">
                  @error('twitter') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Facebook --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Facebook</label>
                  <input type="url" class="form-control" name="facebook" 
                         value="{{ old('facebook', optional($user->company)->facebook) }}" 
                         placeholder="Facebook Page URL">
                  @error('facebook') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

             

            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-end pt-3 gap-2">
              <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary px-4 py-2">
                Cancel
              </a>
              <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1">
                <i class="bx bx-save"></i> Save
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
<style>
.dropzone {
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 30px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.3s ease;
}
.dropzone.dragover {
  border-color: #4a90e2;
  background: #f0f8ff;
}
.dropzone img {
  max-width: 150px;
  margin-top: 10px;
  display: block;
}
.dropzone .remove-btn {
  margin-top: 5px;
  background: red;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 5px;
}
</style>

<div class="dropzone" id="dropzone">
  <p>Click or drag an image here</p>
  <input type="file" id="fileInput" accept="image/*" hidden />
  <img id="preview" style="display:none;" />
  <button type="button" class="remove-btn" id="removeBtn" style="display:none;">Remove</button>
</div>

<script>
const dropzone = document.getElementById("dropzone");
const fileInput = document.getElementById("fileInput");
const preview = document.getElementById("preview");
const removeBtn = document.getElementById("removeBtn");

// Click → open file picker
dropzone.addEventListener("click", () => fileInput.click());

// File picker change → preview
fileInput.addEventListener("change", () => {
  if (fileInput.files.length > 0) {
    showPreview(fileInput.files[0]);
  }
});

// Drag over
dropzone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropzone.classList.add("dragover");
});

// Drag leave
dropzone.addEventListener("dragleave", () => {
  dropzone.classList.remove("dragover");
});

// Drop → preview
dropzone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropzone.classList.remove("dragover");
  if (e.dataTransfer.files.length > 0) {
    fileInput.files = e.dataTransfer.files;
    showPreview(fileInput.files[0]);
  }
});

// Remove → reset
removeBtn.addEventListener("click", () => {
  fileInput.value = "";
  preview.src = "";
  preview.style.display = "none";
  removeBtn.style.display = "none";
});

// Preview helper
function showPreview(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    preview.src = e.target.result;
    preview.style.display = "block";
    removeBtn.style.display = "inline-block";
  };
  reader.readAsDataURL(file);
}
</script>

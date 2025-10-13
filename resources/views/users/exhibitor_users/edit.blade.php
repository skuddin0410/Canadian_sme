@extends('layouts.admin')

@section('title')
    Admin | Exhibitor Edit
@endsection

@section('content')
 <input type="file" id="profileImageInput" accept="image/*" class="d-none form-control">
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
      <label class="form-label">Logo (<span class="text-danger">600px (width) x 600px (height)</span>)</label>

      @php
        $contentIconFile = !empty($user->contentIconFile) && !empty($user->contentIconFile->file_path);
        $contentIconSrc = $user->contentIconFile->file_path ?? '';
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
            style="top: .5rem; right: .5rem;" data-photoid=" {{!empty($user->contentIconFile) ? $user->contentIconFile->id : ''}}">
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
      <label class="form-label">Banner (<span class="text-danger">1920px (width) x 1081px (height)</span>)</label>

      @php
        $quickLinkFile = !empty($user->quickLinkIconFile) && !empty($user->quickLinkIconFile->file_path);
        $quickLinkSrc = $user->quickLinkIconFile->file_path ?? '';
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
            style="top: .5rem; right: .5rem;"  data-photoid=" {{!empty($user->quickLinkIconFile) ? $user->quickLinkIconFile->id : ''}}">
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
                         value="{{ old('company_name', $user->name) }}" 
                         placeholder="Company Name" required>
                  @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Email</label>
                  <input type="email" 
                         class="form-control" 
                         name="company_email" 
                         value="{{ old('company_email', $user->email) }}" 
                         placeholder="Company Email">
                  @error('company_email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Phone --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Phone</label>
                  <input type="text" 
                         class="form-control" 
                         name="company_phone" 
                         value="{{ old('company_phone', $user->phone) }}" 
                         placeholder="Company Phone">
                  @error('company_phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

      
              {{-- Website --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website</label>
                  <input type="url" class="form-control" name="website" 
                         value="{{ old('website', $user->website) }}" 
                         placeholder="https://example.com">
                  @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <input type="url" class="form-control" name="linkedin" 
                         value="{{ old('linkedin', $user->linkedin) }}" 
                         placeholder="LinkedIn Profile URL">
                  @error('linkedin') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Twitter --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Twitter</label>
                  <input type="url" class="form-control" name="twitter" 
                         value="{{ old('twitter', $user->twitter) }}" 
                         placeholder="Twitter Profile URL">
                  @error('twitter') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Facebook --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Facebook</label>
                  <input type="url" class="form-control" name="facebook" 
                         value="{{ old('facebook', $user->facebook) }}" 
                         placeholder="Facebook Page URL">
                  @error('facebook') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

             <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Instagram</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                    <input type="url" name="instagram" class="form-control"
                           value="{{ old('instagram', $user->instagram) }}" placeholder="https://instagram.com/...">
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Booth</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-book"></i></span>
                    <input type="text" name="booth" class="form-control"
                           value="{{ old('booth', $user->booth) }}">
                  </div>
                </div>
              </div>
                <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Industry</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-book"></i></span>
                    <input type="text" name="industry" class="form-control"
                           value="{{ old('industry', $user->industry) }}">
                  </div>
                </div>
              </div>

                  {{-- Description --}}
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="company_description" 
                            class="form-control" 
                            rows="4"
                            placeholder="Company Description">{{ old('company_description', $user->description) }}</textarea>
                  @error('company_description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>


            </div>

            {{-- Buttons --}}
            <input type="hidden" name="company_id" value="{{$user->id}}">
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('content-icon-dropzone');
    const fileInput = document.getElementById('dz-input-content');
    const imagePreview = document.getElementById('dz-image-content');
    const removeButton = document.getElementById('dz-remove-content');
    const placeholder = document.getElementById('dz-placeholder-content');

    // Handle file selection (browse button)
    document.getElementById('dz-browse-content').addEventListener('click', function () {
      fileInput.click();
    });

    // Handle file drop
    dropzone.addEventListener('dragover', function (e) {
      e.preventDefault();
      dropzone.style.backgroundColor = '#eef6ff';  // Optional: Change background on drag over
    });

    dropzone.addEventListener('dragleave', function () {
      dropzone.style.backgroundColor = '';  // Reset background on drag leave
    });

    dropzone.addEventListener('drop', function (e) {
      e.preventDefault();
      handleFiles(e.dataTransfer.files);
    });

    // Handle file input change
    fileInput.addEventListener('change', function () {
      handleFiles(fileInput.files);
    });

    // Handle file removal
    removeButton.addEventListener('click', function () {
      fileInput.value = '';  // Clear the file input
      imagePreview.classList.add('d-none');  // Hide the preview image
      placeholder.classList.remove('d-none');  // Show the placeholder
      removeButton.classList.add('d-none');  // Hide the remove button
       const photoId = removeButton.dataset.photoid;
        $.ajax({
          url: `/delete/photo`, 
          type: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          data: { photo_id: photoId },
          success: function (res) {
              console.log('Image removed successfully:', res);
          },
          error: function (xhr) {
              console.error('Error removing image:', xhr.responseText);
          }
        });
    });

    // Function to handle the selected files
    function handleFiles(files) {
      if (files && files[0]) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('d-none');
            placeholder.classList.add('d-none');
            removeButton.classList.remove('d-none');
          };
          reader.readAsDataURL(file);
        } else {
          alert('Please select a valid image file.');
        }
      }
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('quick-link-icon-dropzone');
    const fileInput = document.getElementById('dz-input-quick');
    const imagePreview = document.getElementById('dz-image-quick');
    const removeButton = document.getElementById('dz-remove-quick');
    const placeholder = document.getElementById('dz-placeholder-quick');

    // Handle file selection (browse button)
    document.getElementById('dz-browse-quick').addEventListener('click', function () {
      fileInput.click();
    });

    // Handle file drop
    dropzone.addEventListener('dragover', function (e) {
      e.preventDefault();
      dropzone.style.backgroundColor = '#eef6ff';  // Optional: Change background on drag over
    });

    dropzone.addEventListener('dragleave', function () {
      dropzone.style.backgroundColor = '';  // Reset background on drag leave
    });

    dropzone.addEventListener('drop', function (e) {
      e.preventDefault();
      handleFiles(e.dataTransfer.files);
    });

    // Handle file input change
    fileInput.addEventListener('change', function () {
      handleFiles(fileInput.files);
    });

    // Handle file removal
    removeButton.addEventListener('click', function () {
      fileInput.value = '';  // Clear the file input
      imagePreview.classList.add('d-none');  // Hide the preview image
      placeholder.classList.remove('d-none');  // Show the placeholder
      removeButton.classList.add('d-none');  // Hide the remove button
       const photoId = removeButton.dataset.photoid;
        $.ajax({
          url: `/delete/photo`, 
          type: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          data: { photo_id: photoId },
          success: function (res) {
              console.log('Image removed successfully:', res);
          },
          error: function (xhr) {
              console.error('Error removing image:', xhr.responseText);
          }
        });
    });

    // Function to handle the selected files
    function handleFiles(files) {
      if (files && files[0]) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('d-none');
            placeholder.classList.add('d-none');
            removeButton.classList.remove('d-none');
          };
          reader.readAsDataURL(file);
        } else {
          alert('Please select a valid image file.');
        }
      }
    }
  });
</script>
@endsection


@extends('layouts.admin')

@section('title', 'Admin | Edit Sponsor')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sponsors /</span> Edit</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Sponsor</h5>
        </div>
        <div class="card-body">

          <form action="{{ route('sponsors.update', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

              {{-- Logo --}}
              <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Logo</label>
   
      @php
        $logo = !empty($company?->logo) && !empty($company->logo->file_path);
        $logoSrc = $company->logo->file_path ?? '';
      @endphp

      <div id="logo-dropzone"
     class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
     style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

    {{-- Placeholder (only visible if no logo) --}}
    <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ isset($logo) && $logo ? 'd-none' : '' }}">
        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
        <div>
            <strong>Drag & drop</strong> an image here, or
            <button type="button" id="dz-browse-content" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
        </div>
        <small class="text-muted d-block">Max 2048 KB</small>
    </div>

    {{-- Inline preview --}}
    <img id="dz-image-content"
         src="{{ $logoSrc ?? '' }}"
         alt="Preview"
         class="{{ isset($logo) && $logo ? '' : 'd-none' }} rounded"
         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

    {{-- Remove button --}}
    <button type="button"
            id="dz-remove-content"
            class="btn btn-sm btn-danger position-absolute {{ isset($logo) && $logo ? '' : 'd-none' }}"
            style="top: .5rem; right: .5rem;" data-photoid=" {{!empty($company->logo) ? $company->logo->id : ''}}">
        <i class="bx bx-x"></i> Remove
    </button>

    {{-- Hidden file input --}}
    <input type="file" id="dz-input-content" name="logo" accept="image/*" class="d-none">
</div>


      @error('logo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>
   <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Banner</label>

      @php
        $banner = !empty($company?->banner) && !empty($company->banner->file_path);
        $bannerSrc = $company->banner->file_path ?? '';
      @endphp

      <div id="banner-icon-dropzone"
     class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
     style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

    {{-- Placeholder --}}
    <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ isset($banner) && $banner ? 'd-none' : '' }}">
        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
        <div>
            <strong>Drag & drop</strong> an image here, or
            <button type="button" id="dz-browse-quick" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
        </div>
        <small class="text-muted d-block">Max 2048 KB</small>
    </div>

    {{-- Inline preview --}}
    <img id="dz-image-quick"
         src="{{ $bannerSrc ?? '' }}"
         alt="Preview"
         class="{{ isset($banner) && $banner ? '' : 'd-none' }} rounded"
         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

    {{-- Remove button --}}
    <button type="button"
            id="dz-remove-quick"
            class="btn btn-sm btn-danger position-absolute {{ isset($banner) && $banner ? '' : 'd-none' }}"
            style="top: .5rem; right: .5rem;" data-photoid=" {{!empty($company->banner) ? $company->banner->id : ''}}">
        <i class="bx bx-x"></i> Remove
    </button>

    {{-- Hidden input --}}
    <input type="file" id="dz-input-quick" name="banner" accept="image/*" class="d-none">
</div>

      @error('banner')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>



              {{-- Company Name --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="company_name"
                       value="{{ old('company_name', $company->name) }}" placeholder="Sponsor Name">
                @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Company Email --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="company_email"
                       value="{{ old('company_email', $company->email) }}" placeholder="Sponsor Email">
                @error('company_email') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Phone --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="company_phone"
                       value="{{ old('company_phone', $company->phone) }}" placeholder="Phone Number">
                @error('company_phone') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Website --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Website</label>
                <input type="url" class="form-control" name="website"
                       value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                @error('website') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- LinkedIn --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">LinkedIn</label>
                <input type="url" class="form-control" name="linkedin"
                       value="{{ old('linkedin', $company->linkedin) }}" placeholder="https://linkedin.com/sponsor">
                @error('linkedin') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Twitter --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Twitter</label>
                <input type="url" class="form-control" name="twitter"
                       value="{{ old('twitter', $company->twitter) }}" placeholder="https://twitter.com/sponsor">
                @error('twitter') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Facebook --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Facebook</label>
                <input type="url" class="form-control" name="facebook"
                       value="{{ old('facebook', $company->facebook) }}" placeholder="https://facebook.com/sponsor">
                @error('facebook') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Instagram --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Instagram</label>
                <input type="url" class="form-control" name="instagram"
                       value="{{ old('instagram', $company->instagram) }}" placeholder="https://instagram.com/sponsor">
                @error('instagram') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
              
              <div class="col-12"> 
              <label for="type"  class="form-label">Select Membership Type</label>             
              <select id="type" name="type" class="form-select mb-3">
                  <option value="">Select Membership Type</option>
                    <option value="gold" {{ old('type', $user->type ?? '') == 'gold' ? 'selected' : '' }}>GOLD</option>
                    <option value="majlislounge" {{ old('type', $user->type ?? '') == 'majlislounge' ? 'selected' : '' }}>MAJLISLOUNGE</option>
                    <option value="platinum" {{ old('type', $user->type ?? '') == 'platinum' ? 'selected' : '' }}>PLATINUM</option>
                    <option value="silver" {{ old('type', $user->type ?? '') == 'silver' ? 'selected' : '' }}>SILVER</option>
                    <option value="innovationpartner" {{ old('type', $user->type ?? '') == 'innovationpartner' ? 'selected' : '' }}>INNOVATIONPARTNER</option>
                    <option value="bronze" {{ old('type', $user->type ?? '') == 'bronze' ? 'selected' : '' }}>BRONZE</option>
                </select>
             
              </div>

              {{-- Description --}}
              <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="company_description" class="form-control" rows="4"
                          placeholder="Sponsor Description">{{ old('company_description', $company->description) }}</textarea>
                @error('company_description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Submit --}}
              <input type="hidden" name="sponsor_id" value="{{$company->id??''}}">
              <div class="col-12">
                <div class="d-flex justify-content-end">
                  <a href="{{ route('sponsors.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
                  <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save</button>
                </div>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const input     = document.getElementById("dz-input-content");
    const browseBtn = document.getElementById("dz-browse-content");
    const removeBtn = document.getElementById("dz-remove-content");
    const preview   = document.getElementById("dz-image-content");
    const placeholder = document.getElementById("dz-placeholder-content");

    // Browse button opens file dialog
    browseBtn.addEventListener("click", () => input.click());

    // Show preview when file is selected
    input.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.classList.remove("d-none");
                removeBtn.classList.remove("d-none");
                placeholder.classList.add("d-none");
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove preview & reset
    removeBtn.addEventListener("click", () => {
        input.value = ""; // clear file
        preview.src = "";
        preview.classList.add("d-none");
        removeBtn.classList.add("d-none");
        placeholder.classList.remove("d-none");
        const photoId = removeBtn.dataset.photoid;
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
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropzone    = document.getElementById("banner-icon-dropzone");
    const input       = document.getElementById("dz-input-quick");
    const browseBtn   = document.getElementById("dz-browse-quick");
    const removeBtn   = document.getElementById("dz-remove-quick");
    const preview     = document.getElementById("dz-image-quick");
    const placeholder = document.getElementById("dz-placeholder-quick");

    // Browse button opens file dialog
    browseBtn.addEventListener("click", () => input.click());

    // Show preview when file is selected
    input.addEventListener("change", (e) => handleFile(e.target.files[0]));

    // Remove preview & reset
    removeBtn.addEventListener("click", () => {
        input.value = "";
        preview.src = "";
        preview.classList.add("d-none");
        removeBtn.classList.add("d-none");
        placeholder.classList.remove("d-none");

        const photoId = removeBtn.dataset.photoid;
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

    // --- Optional: Drag & Drop support ---
    dropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropzone.style.background = "rgba(0,0,0,0.05)";
    });

    dropzone.addEventListener("dragleave", () => {
        dropzone.style.background = "var(--bs-body-bg)";
    });

    dropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropzone.style.background = "var(--bs-body-bg)";
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files; // set dropped file to input
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.classList.remove("d-none");
                removeBtn.classList.remove("d-none");
                placeholder.classList.add("d-none");
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endsection

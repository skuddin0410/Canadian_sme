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
                  <label class="form-label">Logo (<span class="text-danger">600px (width) x 600px (height)</span>)</label>
               
                  @php
                    $logo = !empty($company?->logo) && !empty($company->logo->file_path);
                    $logoSrc = $company->logo->file_path ?? '';
                  @endphp

                  <div id="logo-dropzone"
                       class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                       style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                    <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ isset($logo) && $logo ? 'd-none' : '' }}">
                        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                        <div>
                            <strong>Drag & drop</strong> an image here, or
                            <button type="button" id="dz-browse-content" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                        </div>
                        <small class="text-muted d-block">Max 2048 KB</small>
                    </div>

                    <img id="dz-image-content"
                         src="{{ $logoSrc ?? '' }}"
                         alt="Preview"
                         class="{{ isset($logo) && $logo ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-content"
                            class="btn btn-sm btn-danger position-absolute {{ isset($logo) && $logo ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;" data-photoid="{{!empty($company->logo) ? $company->logo->id : ''}}">
                        <i class="bx bx-x"></i> Remove
                    </button>

                    <input type="file" id="dz-input-content" name="logo" accept="image/*" class="d-none">
                  </div>
                  @error('logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Banner --}}
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Banner (<span class="text-danger">1920px (width) x 1081px (height)</span>)</label>

                  @php
                    $banner = !empty($company?->banner) && !empty($company->banner->file_path);
                    $bannerSrc = $company->banner->file_path ?? '';
                  @endphp

                  <div id="banner-icon-dropzone"
                       class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                       style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                    <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ isset($banner) && $banner ? 'd-none' : '' }}">
                        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                        <div>
                            <strong>Drag & drop</strong> an image here, or
                            <button type="button" id="dz-browse-quick" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                        </div>
                        <small class="text-muted d-block">Max 2048 KB</small>
                    </div>

                    <img id="dz-image-quick"
                         src="{{ $bannerSrc ?? '' }}"
                         alt="Preview"
                         class="{{ isset($banner) && $banner ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-quick"
                            class="btn btn-sm btn-danger position-absolute {{ isset($banner) && $banner ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;" data-photoid="{{!empty($company->banner) ? $company->banner->id : ''}}">
                        <i class="bx bx-x"></i> Remove
                    </button>

                    <input type="file" id="dz-input-quick" name="banner" accept="image/*" class="d-none">
                  </div>
                  @error('banner') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Company Name --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="company_name"
                       value="{{ old('company_name', $company->name) }}" placeholder="Sponsor Name" required>
                @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Company Email --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Email</label>
                <input type="email" class="form-control" name="company_email"
                       value="{{ old('company_email', $company->email) }}" placeholder="Sponsor Email">
                @error('company_email') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Phone --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone </label>
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
              
              {{-- Category --}}
              <div class="col-12"> 
                <label for="type"  class="form-label">Select Category</label>             
                <select id="type" name="type" class="form-select mb-3">
                    <option value="">Select Category</option>
                      @foreach(getCategory('sponsor') as $label)
                          <option value="{{ $label->slug }}" {{ old('type', $company->type ?? '') == $label->slug ? 'selected' : '' }}>
                              {{ $label->slug }}
                          </option>
                      @endforeach
                  </select>
              </div>

              {{-- Events --}}
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Events <span class="text-danger">*</span></label>
                  <select name="event_id[]" class="form-control select2" multiple data-placeholder="Select Events" required>
                    @foreach($events as $event)
                      <option value="{{ $event->id }}" {{ in_array($event->id, old('event_id', $selectedEvents ?? [])) ? 'selected' : '' }}>{{ $event->title }}</option>
                    @endforeach
                  </select>
                  @error('event_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
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
                <div class="d-flex justify-content-end gap-2">
                  <a href="{{ route('sponsors.index') }}" class="btn btn-outline-primary px-4">Cancel</a>
                  <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save"></i> Save</button>
                </div>
              </div>

            </div> {{-- end row --}}
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
  $('.select2').each(function() {
    $(this).select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $(this).data('placeholder'),
      closeOnSelect: false
    });
  });

  // Logo Dropzone
  const logoInput   = document.getElementById("dz-input-content");
  const logoPreview = document.getElementById("dz-image-content");
  const logoRemoveBtn = document.getElementById("dz-remove-content");
  const logoPlaceholder = document.getElementById("dz-placeholder-content");

  if(logoInput) {
    document.getElementById("dz-browse-content").addEventListener("click", () => logoInput.click());
    logoInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                logoPreview.src = event.target.result;
                logoPreview.classList.remove("d-none");
                logoRemoveBtn.classList.remove("d-none");
                logoPlaceholder.classList.add("d-none");
            };
            reader.readAsDataURL(file);
        }
    });

    logoRemoveBtn.addEventListener("click", function(e) {
        e.stopPropagation();
        logoInput.value = "";
        logoPreview.src = "";
        logoPreview.classList.add("d-none");
        logoRemoveBtn.classList.add("d-none");
        logoPlaceholder.classList.remove("d-none");
        const photoId = this.dataset.photoid;
        if(photoId) deletePhoto(photoId);
    });
  }

  // Banner Dropzone
  const inputQuick    = document.getElementById("dz-input-quick");
  const removeBtnQuick = document.getElementById("dz-remove-quick");
  const previewQuick  = document.getElementById("dz-image-quick");
  const placeholderQuick = document.getElementById("dz-placeholder-quick");

  if(inputQuick) {
    document.getElementById("dz-browse-quick").addEventListener("click", () => inputQuick.click());
    inputQuick.addEventListener("change", (e) => handleFileQuick(e.target.files[0]));

    removeBtnQuick.addEventListener("click", function(e) {
        e.stopPropagation();
        inputQuick.value = "";
        previewQuick.src = "";
        previewQuick.classList.add("d-none");
        removeBtnQuick.classList.add("d-none");
        placeholderQuick.classList.remove("d-none");
        const photoId = this.dataset.photoid;
        if(photoId) deletePhoto(photoId);
    });
  }

  function handleFileQuick(file) {
    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (event) => {
            previewQuick.src = event.target.result;
            previewQuick.classList.remove("d-none");
            removeBtnQuick.classList.remove("d-none");
            placeholderQuick.classList.add("d-none");
        };
        reader.readAsDataURL(file);
    }
  }

  function deletePhoto(photoId) {
    $.ajax({
      url: `/delete/photo`, 
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      data: { photo_id: photoId },
      success: (res) => console.log('Removed:', res),
      error: (xhr) => console.error('Error:', xhr.responseText)
    });
  }
});
</script>
@endsection

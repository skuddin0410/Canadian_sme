@extends('layouts.admin')

@section('title')
Admin | Add Sponsors
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sponsors/</span>Create</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Sponsors Create</h5>
        </div>
        
        <div class="card-body">
          <form action="{{ route('sponsors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
         
            <div class="row">
              {{-- Logo --}}
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Logo (<span class="text-danger">600px (width) x 600px (height)</span>)</label>

                  @php
                    $logoFile = !empty($company?->logoFile) && !empty($company->logoFile->file_path);
                    $logoSrc = $logoFile 
                        ? (Str::startsWith($company->logoFile->file_path, ['http://','https://'])
                            ? $company->logoFile->file_path
                            : Storage::url($company->logoFile->file_path))
                        : '';
                  @endphp

                  <div id="logo-dropzone"
                       class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                       style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                    <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ $logoFile ? 'd-none' : '' }}">
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
                         class="{{ $logoFile ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-content"
                            class="btn btn-sm btn-danger position-absolute {{ $logoFile ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;">
                      <i class="bx bx-x"></i> Remove
                    </button>

                    <input type="file" id="dz-input-content" name="logo" accept="image/*" class="d-none">
                  </div>

                  @error('logo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Banner --}}
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Banner (<span class="text-danger">1920px (width) x 1081px (height)</span>)</label>

                  @php
                    $bannerFile = !empty($company?->bannerFile) && !empty($company->bannerFile->file_path);
                    $bannerSrc = $bannerFile
                        ? (Str::startsWith($company->bannerFile->file_path, ['http://','https://'])
                            ? $company->bannerFile->file_path
                            : Storage::url($company->bannerFile->file_path))
                        : '';
                  @endphp

                  <div id="banner-icon-dropzone"
                       class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                       style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                    <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ $bannerFile ? 'd-none' : '' }}">
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
                         class="{{ $bannerFile ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-quick"
                            class="btn btn-sm btn-danger position-absolute {{ $bannerFile ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;">
                      <i class="bx bx-x"></i> Remove
                    </button>

                    <input type="file" id="dz-input-quick" name="banner" accept="image/*" class="d-none">
                  </div>

                  @error('banner')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Company Name --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                    <input type="text" name="company_name" class="form-control"
                           value="{{ old('company_name') }}" placeholder="Sponsor Name" required>
                  </div>
                </div>
              </div>

              {{-- Company Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Email </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input type="email" name="company_email" class="form-control"
                           value="{{ old('company_email') }}" placeholder="Sponsor Email">
                  </div>
                </div>
              </div>

              {{-- Company Phone --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Phone </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                    <input type="text" name="company_phone" class="form-control"
                           value="{{ old('company_phone') }}" placeholder="Sponsor Phone">
                  </div>
                </div>
              </div>

              {{-- Website --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website') }}" placeholder="https://example.com">
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
             
              {{-- Instagram --}}
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

              {{-- Category --}}
              <div class="col-6"> 
                <label for="type"  class="form-label">Select Category<span class="text-danger">*</span></label>             
                <select id="type" name="type" class="form-select mb-3">
                    <option value="">Select Category</option>
                   @foreach(getCategory('sponsor') as $label)
                    <option value="{{ $label->slug }}" {{ old('type') == $label->slug ? 'selected' : '' }}>
                        {{ $label->slug }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- Order By --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Order <small class="text-muted">(Lower numbers show first)</small></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-sort"></i></span>
                    <input type="number" name="order_by" class="form-control"
                           value="{{ old('order_by', 0) }}" min="0">
                  </div>
                  @error('order_by') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
              </div>

              {{-- Events --}}
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Events <span class="text-danger">*</span></label>
                  <select name="event_id[]" class="form-control select2" multiple data-placeholder="Select Events" required>
                    @foreach($events as $event)
                      <option value="{{ $event->id }}" {{ in_array($event->id, old('event_id', [])) ? 'selected' : '' }}>{{ $event->title }}</option>
                    @endforeach
                  </select>
                  @error('event_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
              </div>

              <input type="hidden" name="is_sponsor" value="true"/>

              {{-- Company Description --}}
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Company Description</label>
                  <textarea name="company_description" class="form-control" rows="4"
                            placeholder="Brief description about the Sponsor">{{ old('company_description') }}</textarea>
                </div>
              </div>

              {{-- Submit --}}
              <div class="col-12">
                <div class="d-flex pt-3 justify-content-end gap-2">
                  <a href="{{route('sponsors.index')}}" class="btn btn-outline-primary px-4">Cancel</a>
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

  // Slug logic
  $("#last-name-target, #slug-source").keyup(function() {
    let text = $('#slug-source').val();
    let last = $('#last-name-target').val();
    if (last !== undefined && text !== undefined) {
      $("#slug-target").val(slugify(text + " " + last));
    }
  });

  function slugify(str) {
    return str.toLowerCase().replace(/^\s+|\s+$/g, '').replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '');
  }

  // Logo Dropzone
  const logoInput = document.getElementById("dz-input-content");
  const logoPreview = document.getElementById("dz-image-content");
  const logoRemove = document.getElementById("dz-remove-content");
  const logoPlaceholder = document.getElementById("dz-placeholder-content");

  if(logoInput) {
    document.getElementById("dz-browse-content").addEventListener("click", () => logoInput.click());
    logoInput.addEventListener("change", function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
          logoPreview.src = e.target.result;
          logoPreview.classList.remove("d-none");
          logoRemove.classList.remove("d-none");
          logoPlaceholder.classList.add("d-none");
        };
        reader.readAsDataURL(this.files[0]);
      }
    });

    logoRemove.addEventListener("click", () => {
      logoInput.value = "";
      logoPreview.src = "";
      logoPreview.classList.add("d-none");
      logoRemove.classList.add("d-none");
      logoPlaceholder.classList.remove("d-none");
    });
  }

  // Banner Dropzone
  const bannerInput = document.getElementById("dz-input-quick");
  const bannerPreview = document.getElementById("dz-image-quick");
  const bannerRemove = document.getElementById("dz-remove-quick");
  const bannerPlaceholder = document.getElementById("dz-placeholder-quick");

  if(bannerInput) {
    document.getElementById("dz-browse-quick").addEventListener("click", () => bannerInput.click());
    bannerInput.addEventListener("change", function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
          bannerPreview.src = e.target.result;
          bannerPreview.classList.remove("d-none");
          bannerRemove.classList.remove("d-none");
          bannerPlaceholder.classList.add("d-none");
        };
        reader.readAsDataURL(this.files[0]);
      }
    });

    bannerRemove.addEventListener("click", () => {
      bannerInput.value = "";
      bannerPreview.src = "";
      bannerPreview.classList.add("d-none");
      bannerRemove.classList.add("d-none");
      bannerPlaceholder.classList.remove("d-none");
    });
  }
});
</script>
@endsection

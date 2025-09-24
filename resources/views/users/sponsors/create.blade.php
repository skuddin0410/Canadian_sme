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
          <h5 class="mb-0">Sponsors @if(!empty($user)) Update @else Create @endif</h5>
        </div>
        
        <div class="card-body">
          <form
            action="{{ route('sponsors.store') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
         


  <div class="row">
  <div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Logo</label>

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

  {{-- Placeholder --}}
  <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ $logoFile ? 'd-none' : '' }}">
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
       class="{{ $logoFile ? '' : 'd-none' }} rounded"
       style="max-height: 180px; max-width: 100%; object-fit: contain;" />

  {{-- Remove button --}}
  <button type="button"
          id="dz-remove-content"
          class="btn btn-sm btn-danger position-absolute {{ $logoFile ? '' : 'd-none' }}"
          style="top: .5rem; right: .5rem;">
    <i class="bx bx-x"></i> Remove
  </button>

  {{-- Hidden input --}}
  <input type="file" id="dz-input-content" name="logo" accept="image/*" class="d-none">
</div>

      @error('logo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Icon --}}
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Banner</label>

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

  {{-- Placeholder --}}
  <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ $bannerFile ? 'd-none' : '' }}">
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
       class="{{ $bannerFile ? '' : 'd-none' }} rounded"
       style="max-height: 180px; max-width: 100%; object-fit: contain;" />

  {{-- Remove button --}}
  <button type="button"
          id="dz-remove-quick"
          class="btn btn-sm btn-danger position-absolute {{ $bannerFile ? '' : 'd-none' }}"
          style="top: .5rem; right: .5rem;">
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
                  <label class="form-label">Company Email <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input type="email" name="company_email" class="form-control"
                           value="{{ old('company_email') }}" placeholder="Sponsor Email" required>
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
                           value="{{ old('company_phone') }}" placeholder="Sponsor Phone" required>
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
              <div class="col-12"> 
              <label for="type"  class="form-label">Select Category<span class="text-danger">*</span></label>             
              <select id="type" name="type" class="form-select mb-3">
                  <option value="">Select  Category</option>
                 @foreach(getCategory('sponsor') as  $label)
                  <option value="{{ $label->slug }}" {{ old('type') == $label->slug ? 'selected' : '' }}>
                      {{ $label->slug }}
                  </option>
                @endforeach
              </select>
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
            </div>

            <div class="col-12">
              <div class="mb-3">
                <div class="d-flex pt-3 justify-content-end">
                  <a href="{{route('sponsors.index')}}"
                    class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
                  <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user"><i
                      class="bx bx-save"></i>Save</button>
                </div>
              </div>
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
  $("#last-name-target").keyup(function() {
    var Text = $('#slug-source').val();
    var Last = $('#last-name-target').val();
    console.log(Text + " " + Last);
    if (Last != undefined && Text != undefined) {
      Text = Text + " " + Last;
      Text = slugify(Text);
      $("#slug-target").val(Text);
    }
  });
  $("#slug-source").keyup(function() {
    var Text = $('#slug-source').val();
    var Last = $('#last-name-target').val();
    console.log(Text + " " + Last);
    if (Last != undefined && Text != undefined) {
      Text = Text + " " + Last;
      Text = slugify(Text);
      $("#slug-target").val(Text);
    }
  });
  function slugify(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
    str = str.toLowerCase(); // convert string to lowercase
    str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
      .replace(/\s+/g, '-') // replace spaces with hyphens
      .replace(/-+/g, '-'); // remove consecutive hyphens
    return str.replace(/^-+|-+$/g, '');
  }
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropzone    = document.getElementById("logo-dropzone");
    const input       = document.getElementById("dz-input-content");
    const placeholder = document.getElementById("dz-placeholder-content");
    const preview     = document.getElementById("dz-image-content");
    const removeBtn   = document.getElementById("dz-remove-content");
    const browseBtn   = document.getElementById("dz-browse-content");

    // Browse button click → trigger file input
    browseBtn.addEventListener("click", () => input.click());

    // Handle file selection
    input.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove("d-none");
                removeBtn.classList.remove("d-none");
                placeholder.classList.add("d-none");
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Drag & Drop handling
    dropzone.addEventListener("dragover", e => {
        e.preventDefault();
        dropzone.style.borderColor = "var(--bs-primary)";
    });

    dropzone.addEventListener("dragleave", e => {
        dropzone.style.borderColor = "var(--bs-border-color)";
    });

    dropzone.addEventListener("drop", e => {
        e.preventDefault();
        dropzone.style.borderColor = "var(--bs-border-color)";
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event("change"));
        }
    });

    // Remove button
    removeBtn.addEventListener("click", () => {
        input.value = "";
        preview.src = "";
        preview.classList.add("d-none");
        removeBtn.classList.add("d-none");
        placeholder.classList.remove("d-none");
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropzone    = document.getElementById("banner-icon-dropzone");
    const input       = document.getElementById("dz-input-quick");
    const placeholder = document.getElementById("dz-placeholder-quick");
    const preview     = document.getElementById("dz-image-quick");
    const removeBtn   = document.getElementById("dz-remove-quick");
    const browseBtn   = document.getElementById("dz-browse-quick");

    // Browse button → open file dialog
    browseBtn.addEventListener("click", () => input.click());

    // Handle file input change
    input.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove("d-none");
                removeBtn.classList.remove("d-none");
                placeholder.classList.add("d-none");
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Drag over effect
    dropzone.addEventListener("dragover", e => {
        e.preventDefault();
        dropzone.style.borderColor = "var(--bs-primary)";
    });

    dropzone.addEventListener("dragleave", () => {
        dropzone.style.borderColor = "var(--bs-border-color)";
    });

    // Handle file drop
    dropzone.addEventListener("drop", e => {
        e.preventDefault();
        dropzone.style.borderColor = "var(--bs-border-color)";
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event("change"));
        }
    });

    // Remove image
    removeBtn.addEventListener("click", () => {
        input.value = "";
        preview.src = "";
        preview.classList.add("d-none");
        removeBtn.classList.add("d-none");
        placeholder.classList.remove("d-none");
    });
});
</script>
@endsection
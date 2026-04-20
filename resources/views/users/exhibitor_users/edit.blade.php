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
         
          <form 
            action="{{ route('exhibitor-users.update', $user->id) }}" 
            method="POST" 
            enctype="multipart/form-data"
          >
            @csrf
            @method('PUT')

            <div class="row">
              {{-- Logo --}}
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
              
                    <div id="dz-placeholder-content" class="d-flex flex-column align-items-center gap-2 {{ $contentIconFile ? 'd-none' : '' }}">
                        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                        <div>
                            <strong>Drag & drop</strong> an image here, or
                            <button type="button" id="dz-browse-content" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                        </div>
                        <small class="text-muted d-block">Max 2048 KB</small>
                    </div>

                    <img id="dz-image-content"
                         src="{{ $contentIconSrc }}"
                         alt="Preview"
                         class="{{ $contentIconFile ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-content"
                            class="btn btn-sm btn-danger position-absolute {{ $contentIconFile ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;" data-photoid="{{!empty($user->contentIconFile) ? $user->contentIconFile->id : ''}}">
                        <i class="bx bx-x"></i> Remove
                    </button>

                    <input type="file" id="dz-input-content" name="content_icon" accept="image/*" class="d-none">
                  </div>

                  @error('content_icon')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Banner --}}
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

                    <div id="dz-placeholder-quick" class="d-flex flex-column align-items-center gap-2 {{ $quickLinkFile ? 'd-none' : '' }}">
                        <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                        <div>
                            <strong>Drag & drop</strong> an image here, or
                            <button type="button" id="dz-browse-quick" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                        </div>
                        <small class="text-muted d-block">Max 2048 KB</small>
                    </div>

                    <img id="dz-image-quick"
                         src="{{ $quickLinkSrc }}"
                         alt="Preview"
                         class="{{ $quickLinkFile ? '' : 'd-none' }} rounded"
                         style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                    <button type="button"
                            id="dz-remove-quick"
                            class="btn btn-sm btn-danger position-absolute {{ $quickLinkFile ? '' : 'd-none' }}"
                            style="top: .5rem; right: .5rem;"  data-photoid="{{!empty($user->quickLinkIconFile) ? $user->quickLinkIconFile->id : ''}}">
                        <i class="bx bx-x"></i> Remove
                    </button>

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
                  <input type="text" class="form-control" name="company_name" 
                         value="{{ old('company_name', $user->name) }}" required>
                  @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Email --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Email</label>
                  <input type="email" class="form-control" name="company_email" 
                         value="{{ old('company_email', $user->email) }}">
                  @error('company_email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Company Phone --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Company Phone</label>
                  <input type="text" class="form-control" name="company_phone" 
                         value="{{ old('company_phone', $user->phone) }}">
                  @error('company_phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Website --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Website</label>
                  <input type="url" class="form-control" name="website" 
                         value="{{ old('website', $user->website) }}">
                  @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- LinkedIn --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">LinkedIn</label>
                  <input type="url" class="form-control" name="linkedin" 
                         value="{{ old('linkedin', $user->linkedin) }}">
                </div>
              </div>

              {{-- Twitter --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Twitter</label>
                  <input type="url" class="form-control" name="twitter" 
                         value="{{ old('twitter', $user->twitter) }}">
                </div>
              </div>

              {{-- Facebook --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Facebook</label>
                  <input type="url" class="form-control" name="facebook" 
                         value="{{ old('facebook', $user->facebook) }}">
                </div>
              </div>

              {{-- Instagram --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Instagram</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                    <input type="url" name="instagram" class="form-control"
                           value="{{ old('instagram', $user->instagram) }}">
                  </div>
                </div>
              </div>

              {{-- Booth --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Booth</label>
                  <input type="text" name="booth" class="form-control" value="{{ old('booth', $user->booth) }}">
                </div>
              </div>

              {{-- Industry --}}
              <div class="col-6">
                <div class="mb-3">
                  <label class="form-label">Industry</label>
                  <input type="text" name="industry" class="form-control" value="{{ old('industry', $user->industry) }}">
                </div>
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
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="company_description" class="form-control" rows="4"
                            placeholder="Company Description">{{ old('company_description', $user->description) }}</textarea>
                  @error('company_description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              {{-- Buttons --}}
              <input type="hidden" name="company_id" value="{{$user->id}}">
              <div class="col-12">
                <div class="d-flex justify-content-end pt-3 gap-2">
                  <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary px-4">Cancel</a>
                  <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-1">
                    <i class="bx bx-save"></i> Save
                  </button>
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
  const contentInput = document.getElementById('dz-input-content');
  const contentPreview = document.getElementById('dz-image-content');
  const contentRemoveBtn = document.getElementById('dz-remove-content');
  const contentPlaceholder = document.getElementById('dz-placeholder-content');

  if(contentInput) {
    document.getElementById('dz-browse-content').addEventListener('click', (e) => {
      e.stopPropagation();
      contentInput.click();
    });
    contentInput.addEventListener('change', () => handleFiles(contentInput.files, contentPreview, contentPlaceholder, contentRemoveBtn));
    
    contentRemoveBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      contentInput.value = '';
      contentPreview.classList.add('d-none');
      contentPlaceholder.classList.remove('d-none');
      contentRemoveBtn.classList.add('d-none');
      const photoId = this.dataset.photoid;
      if(photoId) deletePreview(photoId);
    });
  }

  // Banner Dropzone
  const quickInput = document.getElementById('dz-input-quick');
  const quickPreview = document.getElementById('dz-image-quick');
  const quickRemoveBtn = document.getElementById('dz-remove-quick');
  const quickPlaceholder = document.getElementById('dz-placeholder-quick');

  if(quickInput) {
    document.getElementById('dz-browse-quick').addEventListener('click', (e) => {
      e.stopPropagation();
      quickInput.click();
    });
    quickInput.addEventListener('change', () => handleFiles(quickInput.files, quickPreview, quickPlaceholder, quickRemoveBtn));

    quickRemoveBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      quickInput.value = '';
      quickPreview.classList.add('d-none');
      quickPlaceholder.classList.remove('d-none');
      quickRemoveBtn.classList.add('d-none');
      const photoId = this.dataset.photoid;
      if(photoId) deletePreview(photoId);
    });
  }

  function handleFiles(files, preview, placeholder, removeBtn) {
    if (files && files[0]) {
      const file = files[0];
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
          placeholder.classList.add('d-none');
          removeBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
      }
    }
  }

  function deletePreview(photoId) {
    $.ajax({
      url: `/delete/photo`, 
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      data: { photo_id: photoId },
      success: (res) => console.log('Image removed:', res),
      error: (xhr) => console.error('Error:', xhr.responseText)
    });
  }
});
</script>
@endsection


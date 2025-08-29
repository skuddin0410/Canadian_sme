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
          @if(Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          <form action="{{ route('sponsors.update', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

              {{-- Logo --}}
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
             src="{{ $logoSrc }}"
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
             src="{{ $bannerSrc }}"
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

              {{-- Description --}}
              <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="company_description" class="form-control" rows="4"
                          placeholder="Sponsor Description">{{ old('company_description', $company->description) }}</textarea>
                @error('company_description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>

              {{-- Submit --}}
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
@endsection

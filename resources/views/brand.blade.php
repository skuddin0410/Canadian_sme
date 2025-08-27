@extends('layouts.admin')

@section('title')
    Admin | Banners
@endsection
@section('content')

<form action="{{route('brand')}}" method="POST" enctype="multipart/form-data" class="container py-4">
   @csrf
  <!-- @method('PUT') -->

  <div class="row g-4">
     @php
        $logo = getKeyValue('cover'); 
        $hasLogoImage = !empty($logo->photo) && !empty($logo->photo->file_path);
        $imgLogoSrc = $hasLogoImage ? (Str::startsWith($logo->photo->file_path, ['http://','https://'])
                    ? $logo->photo->file_path
                    : Storage::url($logo->photo->file_path)) : '';
      @endphp
    <div class="col-lg-8">
      <!-- Event Logo -->
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Event Logo</div>
        <div class="card-body">
          <div class="dz"
               id="dz-logo"
               data-input="event_logo"
               style="--dz-height: 220px;">
            <div class="dz-placeholder text-center {{ $hasLogoImage ? 'd-none' : '' }}">
              <div class="mb-2">
                <i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i>
              </div>
              <div class="small text-muted mb-2">Drag & drop your logo here</div>
              <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
            </div>
            <img id="dz-image" 
             src="{{ $imgLogoSrc }}"
             alt="Preview"
             class="{{ $hasLogoImage ? '' : 'd-none' }} rounded"
             style="max-height: 180px; max-width: 100%; object-fit: contain;" />
            <button type="button" class="btn btn-sm btn-danger dz-remove d-none">
              <i class="bi bi-x-lg"></i> Remove
            </button>
            <input class="dz-input d-none" type="file" name="event_logo" accept="image/*">
          </div>
          <div class="form-text mt-2">PNG recommended. Transparent background preferred.</div>
        </div>
      </div>
      
      @php
        $brand = getKeyValue('cover'); 
        $hasBrandImage = !empty($brand->photo) && !empty($brand->photo->file_path);
        $imgBrandSrc = $hasBrandImage ? (Str::startsWith($brand->photo->file_path, ['http://','https://'])
                    ? $brand->photo->file_path
                    : Storage::url($brand->photo->file_path)) : '';
      @endphp

      <!-- Brand Cover -->
      <div class="card shadow-sm mt-4">
        <div class="card-header fw-semibold">Brand Cover</div>
        <div class="card-body">
          <div class="dz"
               id="dz-cover"
               data-input="brand_cover"
               style="--dz-height: 260px;">
            <div class="dz-placeholder text-center {{ $hasBrandImage ? 'd-none' : '' }}">
              <div class="mb-2">
                <i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i>
              </div>
              <div class="small text-muted mb-2">Drag & drop a wide cover image</div>
              <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
            </div>
            <img class="dz-image d-none" alt="Cover preview"/>

             <img id="dz-image" 
             src="{{ $imgBrandSrc }}"
             alt="Preview"
             class="{{ $hasBrandImage ? '' : 'd-none' }} rounded"
             style="max-height: 180px; max-width: 100%; object-fit: contain;" />

            <button type="button" class="btn btn-sm btn-danger dz-remove d-none">
              <i class="bi bi-x-lg"></i> Remove
            </button>
            <input class="dz-input d-none" type="file" name="brand_cover" accept="image/*">
          </div>
          <div class="form-text mt-2">Suggested size: 1600×600 or similar aspect ratio.</div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Theme Color -->
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Theme Color</div>
        <div class="card-body">
          <label for="themeColor" class="form-label">Choose your brand/theme color</label>
          <div class="d-flex align-items-center gap-3">
            <input type="color" id="themeColor" name="theme_color" class="form-control form-control-color"
                   value="{{getKeyValue('color')->value ?? '#0D6EFD' }}" title="Pick a color">
                   
            <span id="themeColorHex" class="fw-semibold">{{getKeyValue('color')->value ?? '#0D6EFD' }}</span>
            <span id="themeSwatch" class="rounded-circle border" style="width:24px;height:24px;background:{{getKeyValue('color')->value ?? '#0D6EFD' }};"></span>
          </div>
          <div class="form-text mt-2">
            This color accents the UI and outlines the dropzones for a quick preview.
          </div>
        </div>

        <div class="card-footer text-end">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
</form>



<style>
  .dz {
    position: relative;
    min-height: var(--dz-height, 220px);
    border: 2px dashed var(--bs-border-color);
    border-radius: .75rem;
    background: var(--bs-body-bg);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; padding: 1rem; cursor: pointer;
    transition: border-color .2s ease, box-shadow .2s ease;
  }
  .dz:hover { box-shadow: 0 .125rem .5rem rgba(0,0,0,.08); }
  .dz.dz-dragover { border-color: var(--bs-primary); background: rgba(13,110,253,.03); }
  .dz .dz-placeholder { pointer-events: none; }
  .dz .dz-browse { pointer-events: all; }
  .dz .dz-image {
    max-width: 100%;
    max-height: calc(var(--dz-height, 220px) - 16px);
    object-fit: contain;
    border-radius: .5rem;
  }
  .dz .dz-remove {
    position: absolute;
    top: .5rem; right: .5rem;
  }
</style>

<script>
  // Helper to wire a single dropzone
  function makeDropzone(root) {
    const input      = root.querySelector('.dz-input');
    const placeholder= root.querySelector('.dz-placeholder');
    const browseBtn  = root.querySelector('.dz-browse');
    const img        = root.querySelector('.dz-image');
    const removeBtn  = root.querySelector('.dz-remove');

    const showPreview = (fileOrUrl) => {
      if (typeof fileOrUrl === 'string') {
        img.src = fileOrUrl;
        img.classList.remove('d-none');
        placeholder.classList.add('d-none');
        removeBtn.classList.remove('d-none');
        return;
      }
      const file = fileOrUrl;
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        img.src = e.target.result;
        img.classList.remove('d-none');
        placeholder.classList.add('d-none');
        removeBtn.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    };

    const clearPreview = () => {
      img.src = '';
      img.classList.add('d-none');
      placeholder.classList.remove('d-none');
      removeBtn.classList.add('d-none');
      input.value = '';
    };

    // Click-to-browse
    browseBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      input.click();
    });

    // Clicking on dropzone also opens file
    root.addEventListener('click', (e) => {
      // prevent when clicking remove
      if (e.target.closest('.dz-remove')) return;
      input.click();
    });

    // Input change
    input.addEventListener('change', (e) => {
      const file = e.target.files?.[0];
      if (file) showPreview(file);
    });

    // Drag & drop
    ['dragenter','dragover'].forEach(ev => {
      root.addEventListener(ev, e => { e.preventDefault(); root.classList.add('dz-dragover'); });
    });
    ['dragleave','drop'].forEach(ev => {
      root.addEventListener(ev, e => { e.preventDefault(); root.classList.remove('dz-dragover'); });
    });
    root.addEventListener('drop', (e) => {
      const file = e.dataTransfer.files?.[0];
      if (file) {
        // set input files programmatically
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showPreview(file);
      }
    });

    // Remove
    removeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      clearPreview();
      // Optionally call your AJAX delete here for existing images
      // fetch('/events/123/photo', { method: 'DELETE', headers: {'X-CSRF-TOKEN': '...'} })
    });

    return { showPreview, clearPreview, input };
  }

  // Init both dropzones
  const dzLogo  = makeDropzone(document.getElementById('dz-logo'));
  const dzCover = makeDropzone(document.getElementById('dz-cover'));

  // Theme color live preview + outline accent
  const colorInput   = document.getElementById('themeColor');
  const colorHex     = document.getElementById('themeColorHex');
  const swatch       = document.getElementById('themeSwatch');

  const applyAccent = (hex) => {
    colorHex.textContent = hex.toUpperCase();
    swatch.style.background = hex;
    // Outline the dropzones with chosen color for a quick feel
    document.querySelectorAll('.dz').forEach(el => {
      el.style.borderColor = hex;
      el.style.boxShadow = `0 0 0 .1rem ${hex}20`;
    });
  };

  colorInput.addEventListener('input', (e) => applyAccent(e.target.value));
  applyAccent(colorInput.value); // initial

</script>
@endsection
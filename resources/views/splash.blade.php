
@extends('layouts.admin')

@section('title')
    Admin | Banners
@endsection
@section('content')

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<form action="/upload-splash" method="POST" enctype="multipart/form-data" class="container py-4">
  @csrf

  <h3 class="text-center mb-4">Upload Splash Screen Images</h3>

  <!-- Full-Width Cards Layout for iOS and Android Uploads -->
  <div class="row g-4">
    <!-- iOS Section -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">iOS Splash Screen Uploads</div>
        <div class="card-body">
          <div class="row g-4">
            <!-- iPhone Launch Screen (1125x2436) -->
            <div class="col-md-6">
              <div class="dz" id="dz-ios-iphone" style="--dz-height: 220px;" data-valid-width="1125" data-valid-height="2436">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop iPhone splash image (1125x2436)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="iPhone Preview" id="ios-iphone-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="ios-iphone-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="ios_iphone_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 1125x2436 px (Portrait)</div>
            </div>

            <!-- iPad Launch Screen (1536x2048) -->
            <div class="col-md-6">
              <div class="dz" id="dz-ios-ipad" style="--dz-height: 220px;" data-valid-width="1536" data-valid-height="2048">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop iPad splash image (1536x2048)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="iPad Preview" id="ios-ipad-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="ios-ipad-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="ios_ipad_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 1536x2048 px (Portrait)</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Android Section -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Android Splash Screen Uploads</div>
        <div class="card-body">
          <div class="row g-4">
            <!-- HDPI (720x1280) -->
            <div class="col-md-3">
              <div class="dz" id="dz-android-hdpi" style="--dz-height: 220px;" data-valid-width="720" data-valid-height="1280">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop HDPI splash image (720x1280)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="HDPI Preview" id="android-hdpi-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="android-hdpi-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="android_hdpi_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 720x1280 px</div>
            </div>

            <!-- MDPI (480x800) -->
            <div class="col-md-3">
              <div class="dz" id="dz-android-mdpi" style="--dz-height: 220px;" data-valid-width="480" data-valid-height="800">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop MDPI splash image (480x800)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="MDPI Preview" id="android-mdpi-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="android-mdpi-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="android_mdpi_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 480x800 px</div>
            </div>

            <!-- XHDPI (960x1600) -->
            <div class="col-md-3">
              <div class="dz" id="dz-android-xhdpi" style="--dz-height: 220px;" data-valid-width="960" data-valid-height="1600">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop XHDPI splash image (960x1600)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="XHDPI Preview" id="android-xhdpi-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="android-xhdpi-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="android_xhdpi_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 960x1600 px</div>
            </div>

            <!-- XXHDPI (1440x2560) -->
            <div class="col-md-3">
              <div class="dz" id="dz-android-xxhdpi" style="--dz-height: 220px;" data-valid-width="1440" data-valid-height="2560">
                <div class="dz-placeholder text-center">
                  <div class="mb-2">
                    <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                  </div>
                  <div class="small text-muted mb-2">Drag & drop XXHDPI splash image (1440x2560)</div>
                  <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                </div>
                <img class="dz-image d-none" alt="XXHDPI Preview" id="android-xxhdpi-preview" style="max-height:100%; max-width:100%; object-fit: contain;">
                <button type="button" class="btn btn-sm btn-danger dz-remove d-none" id="android-xxhdpi-remove">
                  <i class="fa fa-times"></i> Remove
                </button>
                <input class="dz-input d-none" type="file" name="android_xxhdpi_image" accept="image/*">
              </div>
              <div class="form-text mt-2">Required size: 1440x2560 px</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Submit Button -->
  <div class="text-center mt-4">
    <button type="submit" class="btn btn-primary">Upload Images</button>
  </div>
</form>

<!-- Bootstrap 5 JS and Popper.js (for modal etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Handle image upload and preview for each dropzone
  const handleImageUpload = (dzId, previewId, removeBtnId, inputName, validDimensions) => {
    const dz = document.getElementById(dzId);
    const input = dz.querySelector('.dz-input');
    const preview = document.getElementById(previewId);
    const removeBtn = document.getElementById(removeBtnId);

    const showPreview = (file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        removeBtn.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    };

    const validateImageSize = (file) => {
      const img = new Image();
      img.onload = () => {
        if (img.width !== validDimensions.width || img.height !== validDimensions.height) {
          alert(`Invalid image dimensions. Required: ${validDimensions.width}x${validDimensions.height}px`);
          preview.classList.add('d-none');
          removeBtn.classList.add('d-none');
          input.value = '';
        } else {
          showPreview(file);
        }
      };
      img.src = URL.createObjectURL(file);
    };

    input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        validateImageSize(file);
      }
    });

    // Make the Browse button open the file input
    const browseBtn = dz.querySelector('.dz-browse');
    browseBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      input.click();
    });

    // Drag and Drop functionality
    dz.addEventListener('dragover', (e) => {
      e.preventDefault();
      dz.classList.add('dz-dragover');
    });

    dz.addEventListener('dragleave', (e) => {
      dz.classList.remove('dz-dragover');
    });

    dz.addEventListener('drop', (e) => {
      e.preventDefault();
      dz.classList.remove('dz-dragover');
      const file = e.dataTransfer.files[0];
      if (file) {
        input.files = e.dataTransfer.files;
        validateImageSize(file);
      }
    });

    removeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      preview.src = '';
      preview.classList.add('d-none');
      removeBtn.classList.add('d-none');
      input.value = '';
    });
  };

  // Initialize image upload and validation for iOS and Android fields
  handleImageUpload('dz-ios-iphone', 'ios-iphone-preview', 'ios-iphone-remove', 'ios_iphone_image', { width: 1125, height: 2436 });
  handleImageUpload('dz-ios-ipad', 'ios-ipad-preview', 'ios-ipad-remove', 'ios_ipad_image', { width: 1536, height: 2048 });
  handleImageUpload('dz-android-hdpi', 'android-hdpi-preview', 'android-hdpi-remove', 'android_hdpi_image', { width: 720, height: 1280 });
  handleImageUpload('dz-android-mdpi', 'android-mdpi-preview', 'android-mdpi-remove', 'android_mdpi_image', { width: 480, height: 800 });
  handleImageUpload('dz-android-xhdpi', 'android-xhdpi-preview', 'android-xhdpi-remove', 'android_xhdpi_image', { width: 960, height: 1600 });
  handleImageUpload('dz-android-xxhdpi', 'android-xxhdpi-preview', 'android-xxhdpi-remove', 'android_xxhdpi_image', { width: 1440, height: 2560 });
</script>

<style>
  /* Style for Dropzones */
  .dz {
    position: relative;
    min-height: 220px;
    border: 2px dashed var(--bs-border-color);
    border-radius: .75rem;
    background: var(--bs-body-bg);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; padding: 1rem; cursor: pointer;
    transition: border-color .2s ease, box-shadow .2s ease;
  }

  .dz:hover { box-shadow: 0 .125rem .5rem rgba(0,0,0,.08); }
  .dz .dz-placeholder { pointer-events: none; }
  .dz .dz-browse { pointer-events: all; }

  .dz .dz-image {
    max-width: 100%;
    max-height: calc(100% - 16px);
    object-fit: contain;
    border-radius: .5rem;
  }

  .dz .dz-remove {
    position: absolute;
    top: .5rem; right: .5rem;
  }

  .dz.dz-dragover {
    border-color: #007bff;
    background: rgba(0, 123, 255, 0.1);
  }
</style>

@endsection
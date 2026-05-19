
@extends('layouts.admin')

@section('title')
    Admin | Splash Screen
@endsection
@section('content')

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Splash Screen Configuration</h3>
    <div class="col-md-4">
        <label for="event_selector" class="form-label fw-bold">Select Event</label>
        <select id="event_selector" class="form-select" onchange="window.location.href='{{ route('splash') }}?event_id=' + this.value">
            <option value="">-- Select Event --</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
            @endforeach
        </select>
    </div>
  </div>

  @if($eventId)
  <form action="{{route('splash')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="event_id" value="{{ $eventId }}">
    
    <!-- Full-Width Cards Layout for iOS and Android Uploads -->
    <div class="row g-4">
      <!-- iOS Section -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header fw-semibold bg-light">iOS Splash Screen Uploads</div>
          <div class="card-body">
            <div class="row g-4">
              <!-- iPhone Launch Screen (1125x2436) -->
              @php
                $has_ios_iphone_image = !empty($splashScreen->iosIphone) && !empty($splashScreen->iosIphone->file_name);
                $ios_iphone_image_src = $has_ios_iphone_image ? $splashScreen->iosIphone->file_path : '';
              @endphp
              <div class="col-md-6">
                <div class="dz" id="dz-ios-iphone" style="--dz-height: 220px;" data-valid-width="1125" data-valid-height="2436">
                  <div class="dz-placeholder text-center {{ $has_ios_iphone_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop iPhone splash image (1125x2436)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>

                  <img src="{{ $ios_iphone_image_src }}" class="{{ $has_ios_iphone_image ? '' : 'd-none' }} rounded" alt="iPhone Preview" id="ios-iphone-preview" style="max-height:200px; max-width:100%; object-fit: contain;">

                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_ios_iphone_image ? '' : 'd-none' }}" id="ios-iphone-remove" data-key="ios_iphone_image" data-eventid="{{ $eventId }}">
                    <i class="fa fa-times"></i> Remove
                  </button>
                  <input class="dz-input d-none" type="file" name="ios_iphone_image" accept="image/*">
                </div>
                <div class="form-text mt-2">Required size: 1125x2436 px (Portrait)</div>
              </div>

              <!-- iPad Launch Screen (1536x2048) -->
            @php
              $has_ios_ipad_image = !empty($splashScreen->iosIpad) && !empty($splashScreen->iosIpad->file_name);
              $ios_ipad_image_src = $has_ios_ipad_image ? $splashScreen->iosIpad->file_path : '';
            @endphp
              <div class="col-md-6">
                <div class="dz" id="dz-ios-ipad" style="--dz-height: 220px;" data-valid-width="1536" data-valid-height="2048">
                  <div class="dz-placeholder text-center {{ $has_ios_ipad_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop iPad splash image (1536x2048)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>
                  <img src="{{ $ios_ipad_image_src }}" class="{{ $has_ios_ipad_image ? '' : 'd-none' }} rounded" alt="iPad Preview" id="ios-ipad-preview" style="max-height:200px; max-width:100%; object-fit: contain;">
                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_ios_ipad_image ? '' : 'd-none' }}" id="ios-ipad-remove" data-key="ios_ipad_image" data-eventid="{{ $eventId }}">
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
          <div class="card-header fw-semibold bg-light">Android Splash Screen Uploads</div>
          <div class="card-body">
            <div class="row g-4">
              <!-- HDPI (720x1280) -->
              @php
                $has_android_hdpi_image = !empty($splashScreen->androidHdpi) && !empty($splashScreen->androidHdpi->file_name);
                $android_hdpi_image_src = $has_android_hdpi_image ? $splashScreen->androidHdpi->file_path : '';
              @endphp

              <div class="col-md-3">
                <div class="dz" id="dz-android-hdpi" style="--dz-height: 220px;" data-valid-width="720" data-valid-height="1280">
                  <div class="dz-placeholder text-center {{ $has_android_hdpi_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop HDPI splash image (720x1280)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>
                  <img src="{{ $android_hdpi_image_src }}" class="{{ $has_android_hdpi_image ? '' : 'd-none' }} rounded" alt="HDPI Preview" id="android-hdpi-preview" style="max-height:200px; max-width:100%; object-fit: contain;">
                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_android_hdpi_image ? '' : 'd-none' }}" id="android-hdpi-remove" data-key="android_hdpi_image" data-eventid="{{ $eventId }}">
                    <i class="fa fa-times"></i> Remove
                  </button>
                  <input class="dz-input d-none" type="file" name="android_hdpi_image" accept="image/*">
                </div>
                <div class="form-text mt-2">Required size: 720x1280 px</div>
              </div>

              <!-- MDPI (480x800) -->
              @php
                $has_android_mdpi_image = !empty($splashScreen->androidMdpi) && !empty($splashScreen->androidMdpi->file_name);
                $android_mdpi_image_src = $has_android_mdpi_image ? $splashScreen->androidMdpi->file_path : '';
              @endphp

              <div class="col-md-3">
                <div class="dz" id="dz-android-mdpi" style="--dz-height: 220px;" data-valid-width="480" data-valid-height="800">
                  <div class="dz-placeholder text-center {{ $has_android_mdpi_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop MDPI splash image (480x800)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>
                  <img src="{{ $android_mdpi_image_src }}" class="{{ $has_android_mdpi_image ? '' : 'd-none' }} rounded" alt="MDPI Preview" id="android-mdpi-preview" style="max-height:200px; max-width:100%; object-fit: contain;">
                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_android_mdpi_image ? '' : 'd-none' }}" id="android-mdpi-remove" data-key="android_mdpi_image" data-eventid="{{ $eventId }}">
                    <i class="fa fa-times"></i> Remove
                  </button>
                  <input class="dz-input d-none" type="file" name="android_mdpi_image" accept="image/*">
                </div>
                <div class="form-text mt-2">Required size: 480x800 px</div>
              </div>

              <!-- XHDPI (960x1600) -->
              @php
                $has_android_xhdpi_image = !empty($splashScreen->androidXhdpi) && !empty($splashScreen->androidXhdpi->file_name);
                $android_xhdpi_image_src = $has_android_xhdpi_image ? $splashScreen->androidXhdpi->file_path : '';
              @endphp
              <div class="col-md-3">
                <div class="dz" id="dz-android-xhdpi" style="--dz-height: 220px;" data-valid-width="960" data-valid-height="1600">
                  <div class="dz-placeholder text-center {{ $has_android_xhdpi_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop XHDPI splash image (960x1600)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>
                  <img src="{{ $android_xhdpi_image_src }}" class="{{ $has_android_xhdpi_image ? '' : 'd-none' }} rounded" alt="XHDPI Preview" id="android-xhdpi-preview" style="max-height:200px; max-width:100%; object-fit: contain;">
                  
                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_android_xhdpi_image ? '' : 'd-none' }}" id="android-xhdpi-remove" data-key="android_xhdpi_image" data-eventid="{{ $eventId }}">
                    <i class="fa fa-times"></i> Remove
                  </button>
                  <input class="dz-input d-none" type="file" name="android_xhdpi_image" accept="image/*">
                </div>
                <div class="form-text mt-2">Required size: 960x1600 px</div>
              </div>

              <!-- XXHDPI (1440x2560) -->
               @php
                $has_android_xxhdpi_image = !empty($splashScreen->androidXxhdpi) && !empty($splashScreen->androidXxhdpi->file_name);
                $android_xxhdpi_image_src = $has_android_xxhdpi_image ? $splashScreen->androidXxhdpi->file_path : '';
              @endphp

              <div class="col-md-3">
                <div class="dz" id="dz-android-xxhdpi" style="--dz-height: 220px;" data-valid-width="1440" data-valid-height="2560">
                  <div class="dz-placeholder text-center {{ $has_android_xxhdpi_image ? 'd-none' : '' }}">
                    <div class="mb-2">
                      <i class="fa fa-cloud-upload" style="font-size:2rem;"></i>
                    </div>
                    <div class="small text-muted mb-2">Drag & drop XXHDPI splash image (1440x2560)</div>
                    <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                  </div>
                  <img src="{{ $android_xxhdpi_image_src }}" class="{{ $has_android_xxhdpi_image ? '' : 'd-none' }} rounded" alt="XXHDPI Preview" id="android-xxhdpi-preview" style="max-height:200px; max-width:100%; object-fit: contain;">
                  <button type="button" class="btn btn-sm btn-danger dz-remove {{ $has_android_xxhdpi_image ? '' : 'd-none' }}" id="android-xxhdpi-remove" data-key="android_xxhdpi_image" data-eventid="{{ $eventId }}">
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
    <div class="text-end mt-4">
      <input type="hidden" name="mode" value="save">
      <button type="submit" class="btn btn-primary btn-lg px-5">Save Splash Screen</button>
    </div>
  </form>
  @else
    <div class="alert alert-info text-center py-5">
        <i class="fa fa-info-circle fa-3x mb-3"></i>
        <h4>Please select an event to configure its splash screen.</h4>
    </div>
  @endif
</div>

<script>
  // Handle image upload and preview for each dropzone
  const handleImageUpload = (dzId, previewId, removeBtnId, inputName, validDimensions) => {
    const dz = document.getElementById(dzId);
    if(!dz) return;
    const input = dz.querySelector('.dz-input');
    const preview = document.getElementById(previewId);
    const removeBtn = document.getElementById(removeBtnId);
    const browseBtn = dz.querySelector('.dz-browse');
    const dzPlaceholder = dz.querySelector('.dz-placeholder');

    const showPreview = (file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        removeBtn.classList.remove('d-none');
        browseBtn.classList.add('d-none');  // Hide the Browse button
        dzPlaceholder.classList.add('d-none');  // Hide the drag and drop placeholder
      };
      reader.readAsDataURL(file);
    };

    const validateImageSize = (file) => {
      const img = new Image();
      img.onload = () => {
        showPreview(file);
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
      if(!confirm('Are you sure you want to remove this image?')) return;
      
      const key = removeBtn.dataset.key;
      const eventId = removeBtn.dataset.eventid;
      
      $.ajax({
        url: `{{ route('splash.deletePhoto') }}`, 
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: { key: key, event_id: eventId },
        success: function (res) {
            if(res.success) {
                preview.src = '';
                preview.classList.add('d-none');
                removeBtn.classList.add('d-none');
                browseBtn.classList.remove('d-none');
                dzPlaceholder.classList.remove('d-none');
                input.value = '';
                alertify.success(res.message);
            } else {
                alertify.error(res.message);
            }
        },
        error: function (xhr) {
            alertify.error('Error removing image');
            console.error('Error removing image:', xhr.responseText);
        }
      });
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
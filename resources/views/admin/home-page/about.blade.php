@extends('layouts.admin')

@section('title')
Admin | Landing Page About Section
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page /</span> About Us</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">About Section CMS</h5>
                </div>
                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success mt-3">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger mt-3">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.home-page.about.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column: Text Content -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('heading') is-invalid @enderror" id="heading" name="heading" value="{{ old('heading', $about->heading ?? '') }}" required>
                                    @error('heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sub_heading" class="form-label">Sub-heading</label>
                                    <input type="text" class="form-control @error('sub_heading') is-invalid @enderror" id="sub_heading" name="sub_heading" value="{{ old('sub_heading', $about->sub_heading ?? '') }}">
                                    @error('sub_heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $about->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="desc_points" class="form-label">Description Points (Press Enter for new point)</label>
                                    <textarea class="form-control @error('desc_points') is-invalid @enderror" id="desc_points" name="desc_points" rows="4" placeholder="Point 1&#10;Point 2&#10;Point 3">{{ old('desc_points', $about->desc_points ?? '') }}</textarea>
                                    <small class="text-muted">Enter each point on a new line.</small>
                                    @error('desc_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_text" class="form-label">Button Text</label>
                                            <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $about->button_text ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_link" class="form-label">Button Link</label>
                                            <input type="text" class="form-control" id="button_link" name="button_link" value="{{ old('button_link', $about->button_link ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="banner_button_link" class="form-label">Banner Button Link</label>
                                    <input type="text" class="form-control" id="banner_button_link" name="banner_button_link" value="{{ old('banner_button_link', $about->banner_button_link ?? '') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exp_year" class="form-label">Experience Year</label>
                                            <input type="text" class="form-control" id="exp_year" name="exp_year" value="{{ old('exp_year', $about->exp_year ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exp_text" class="form-label">Experience Text</label>
                                            <input type="text" class="form-control" id="exp_text" name="exp_text" value="{{ old('exp_text', $about->exp_text ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Images -->
                            <div class="col-md-4">
                                <!-- BG Banner -->
                                <div class="mb-4 border p-2 rounded">
                                    <label class="form-label fw-bold">BG Banner</label>
                                    <div class="d-flex flex-column align-items-center">
                                        @if(empty($about->bgBanner))
                                            <div class="mb-2 text-muted small">No image found</div>
                                        @endif
                                        <img id="bgBannerPreview" src="{{ !empty($about->bgBanner) ? $about->bgBanner->file_path : '' }}" class="img-fluid rounded mb-2 {{ empty($about->bgBanner) ? 'd-none' : '' }}" style="max-height: 100px;">
                                        @if(!empty($about->bgBanner))
                                            <button type="button" class="btn btn-label-danger btn-sm mb-2" onclick="removeImage('bg_banner', 'bgBannerPreview', this)">
                                                <i class="bx bx-trash me-1"></i> Remove
                                            </button>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="bg_banner" onchange="previewImage(this, 'bgBannerPreview')">
                                    </div>
                                </div>

                                <!-- Banner Image -->
                                <div class="mb-4 border p-2 rounded">
                                    <label class="form-label fw-bold">Banner Image</label>
                                    <div class="d-flex flex-column align-items-center">
                                        @if(empty($about->bannerImage))
                                            <div class="mb-2 text-muted small">No image found</div>
                                        @endif
                                        <img id="bannerImagePreview" src="{{ !empty($about->bannerImage) ? $about->bannerImage->file_path : '' }}" class="img-fluid rounded mb-2 {{ empty($about->bannerImage) ? 'd-none' : '' }}" style="max-height: 100px;">
                                        @if(!empty($about->bannerImage))
                                            <button type="button" class="btn btn-label-danger btn-sm mb-2" onclick="removeImage('banner_image', 'bannerImagePreview', this)">
                                                <i class="bx bx-trash me-1"></i> Remove
                                            </button>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="banner_image" onchange="previewImage(this, 'bannerImagePreview')">
                                    </div>
                                </div>

                                <!-- Front Image -->
                                <div class="mb-4 border p-2 rounded">
                                    <label class="form-label fw-bold">Front Image</label>
                                    <div class="d-flex flex-column align-items-center">
                                        @if(empty($about->frontImage))
                                            <div class="mb-2 text-muted small">No image found</div>
                                        @endif
                                        <img id="frontImagePreview" src="{{ !empty($about->frontImage) ? $about->frontImage->file_path : '' }}" class="img-fluid rounded mb-2 {{ empty($about->frontImage) ? 'd-none' : '' }}" style="max-height: 100px;">
                                        @if(!empty($about->frontImage))
                                            <button type="button" class="btn btn-label-danger btn-sm mb-2" onclick="removeImage('front_image', 'frontImagePreview', this)">
                                                <i class="bx bx-trash me-1"></i> Remove
                                            </button>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="front_image" onchange="previewImage(this, 'frontImagePreview')">
                                    </div>
                                </div>

                                <!-- Banner Button Image -->
                                <div class="mb-4 border p-2 rounded">
                                    <label class="form-label fw-bold">Banner Button Image</label>
                                    <div class="d-flex flex-column align-items-center">
                                        @if(empty($about->bannerButtonImage))
                                            <div class="mb-2 text-muted small">No image found</div>
                                        @endif
                                        <img id="bannerButtonImagePreview" src="{{ !empty($about->bannerButtonImage) ? $about->bannerButtonImage->file_path : '' }}" class="img-fluid rounded mb-2 {{ empty($about->bannerButtonImage) ? 'd-none' : '' }}" style="max-height: 100px;">
                                        @if(!empty($about->bannerButtonImage))
                                            <button type="button" class="btn btn-label-danger btn-sm mb-2" onclick="removeImage('banner_button_image', 'bannerButtonImagePreview', this)">
                                                <i class="bx bx-trash me-1"></i> Remove
                                            </button>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="banner_button_image" onchange="previewImage(this, 'bannerButtonImagePreview')">
                                    </div>
                                </div>

                                <!-- Experience Image -->
                                <div class="mb-4 border p-2 rounded">
                                    <label class="form-label fw-bold">Experience Image</label>
                                    <div class="d-flex flex-column align-items-center">
                                        @if(empty($about->expImage))
                                            <div class="mb-2 text-muted small">No image found</div>
                                        @endif
                                        <img id="expImagePreview" src="{{ !empty($about->expImage) ? $about->expImage->file_path : '' }}" class="img-fluid rounded mb-2 {{ empty($about->expImage) ? 'd-none' : '' }}" style="max-height: 100px;">
                                        @if(!empty($about->expImage))
                                            <button type="button" class="btn btn-label-danger btn-sm mb-2" onclick="removeImage('exp_image', 'expImagePreview', this)">
                                                <i class="bx bx-trash me-1"></i> Remove
                                            </button>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="exp_image" onchange="previewImage(this, 'expImagePreview')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update About Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const notFoundText = input.parentElement.querySelector('.text-muted');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (notFoundText) {
                notFoundText.classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(type, previewId, button) {
    if (!confirm('Are you sure you want to remove this image?')) return;

    fetch("{{ route('admin.home-page.about.remove-image') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(previewId).src = '';
            document.getElementById(previewId).classList.add('d-none');
            button.classList.add('d-none');
            
            // Show "No image found" text
            const container = button.closest('.d-flex');
            let noImageText = container.querySelector('.text-muted');
            if (!noImageText) {
                noImageText = document.createElement('div');
                noImageText.className = 'mb-2 text-muted small';
                noImageText.textContent = 'No image found';
                container.prepend(noImageText);
            } else {
                noImageText.classList.remove('d-none');
            }
        } else {
            alert(data.message || 'Failed to remove image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the image');
    });
}
</script>
@endsection

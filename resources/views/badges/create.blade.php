@extends('layouts.admin')
@section('title', 'Create Badge')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus me-2"></i>Create New Badge</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('badges.store') }}" method="POST" enctype="multipart/form-data" id="badgeForm">
                        @csrf
                        
                        {{-- Field Selection --}}
                        <div class="mb-4">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="badge_name" class="form-label">
                                        Badge name
                                    </label>
                                    <input type="text" class="form-control" id="badge_name" name="badge_name" placeholder="Enter badge name">
                                </div>
                            </div>
                            <label class="form-label fw-bold">Select Fields to Include:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="selected_fields[]" value="name" id="field_name">
                                        <label class="form-check-label" for="field_name">
                                            <i class="fas fa-user me-1"></i>Name
                                        </label>
                                    </div>
                                    <div class="form-check mb-2" >
                                        <input class="form-check-input" type="checkbox" name="selected_fields[]" value="company_name" id="field_company">
                                        <label class="form-check-label" for="field_company">
                                            <i class="fas fa-building me-1"></i>Company Name
                                        </label>
                                    </div>
                                    <div class="form-check mb-2 d-none">
                                        <input class="form-check-input" type="checkbox" name="selected_fields[]" value="designation" id="field_designation">
                                        <label class="form-check-label" for="field_designation">
                                            <i class="fas fa-briefcase me-1"></i>Designation
                                        </label>
                                    </div>
                                      <div class="mb-3">
                                      <label for="width" class="form-label">
                                        <i class="fas fa-arrows-alt-h me-1"></i> Width (in)
                                      </label>
                                      <input type="text" class="form-control" name="width" id="width"  placeholder="Enter width in cm" value="3">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="selected_fields[]" value="logo" id="field_logo">
                                        <label class="form-check-label" for="field_logo">
                                            <i class="fas fa-image me-1"></i>Logo
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="selected_fields[]" value="qr_code" id="field_qr">
                                        <label class="form-check-label" for="field_qr">
                                            <i class="fas fa-qrcode me-1"></i>QR Code
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                      <label for="height" class="form-label">
                                        <i class="fas fa-arrows-alt-v me-1"></i> Height (in)
                                      </label>
                                      <input type="text" class="form-control" name="height" id="height"  placeholder="Enter height in cm" value="2.2">
                                    </div>

                                </div>
                            </div>
                            @error('selected_fields')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- Conditional Fields --}}
                        <div class="conditional-field d-none" id="name_field">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name">
                            </div>
                        </div>

                        <div class="conditional-field d-none" id="company_name_field">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-building me-1"></i>Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name">
                            </div>
                        </div>

                        <div class="conditional-field d-none" id="designation_field">
                            <div class="mb-3">
                                <label for="designation" class="form-label">
                                    <i class="fas fa-briefcase me-1"></i>Designation <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter designation">
                            </div>
                        </div>

                        <div class="conditional-field d-none" id="logo_field">
                            <div class="mb-3">
                                <label for="logo" class="form-label">
                                    <i class="fas fa-image me-1"></i>Logo <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB)</div>
                            </div>
                        </div>

                        <div class="conditional-field d-none" id="qr_code_field">
                            <div class="mb-3">
                                <label for="qr_code_data" class="form-label">
                                    <i class="fas fa-qrcode me-1"></i>QR Code Data <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="qr_code_data" name="qr_code_data" rows="3" placeholder="Enter data for QR code (URL, text, contact info, etc.)"></textarea>
                            </div>
                        </div> 

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('badges.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-magic me-1"></i>Generate Badge
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced Preview Panel (Right side) -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-eye me-2"></i>Live Preview</h5>
                </div>


                <div class="card-body">
                    <!-- Badge Preview Container -->
                    <div class="badge-preview-container" style="border: 2px solid #333; border-radius: 10px; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 300px; position: relative;">
                        
                       <div class="row">
                        <div class="col-md-6 mt-2">
                               <div class="badge-code-section">
                                <span class="badge bg-primary text-white" id="preview_badge_code"></span>
                            </div>
                  
                            <!-- Logo Section -->
                            <div class="preview-section d-none" id="preview_logo_section" style="text-align: center; margin-bottom: 15px;">
                                <img id="preview_logo" class="img-fluid" alt="Logo" style="object-fit: contain; border-radius: 8px;" src="{{asset('sme-logo.png')}}">
                            </div>

                            <!-- Name Section -->
                            <div class="preview-section d-none" id="preview_name_section" style="text-align: center; margin-bottom: 10px;">
                                <h4 id="preview_name" style="margin: 0; font-weight: bold; color: #333; font-size: 1.2rem;">John Doe</h4>
                            </div>

                            <!-- Company Name Section -->
                            <div class="preview-section d-none" id="preview_company_name_section" style="text-align: center; margin-bottom: 8px;">
                                <p id="preview_company_name" style="margin: 0; color: #666; font-size: 1rem;">Company Name</p>
                            </div>
                 

                            <div class="preview-section d-none" id="preview_designation_section" style="text-align: center; margin-bottom: 15px;">
                                <p id="preview_designation" style="margin: 0; color: #888; font-size: 0.9rem; font-style: italic;">Designation</p>
                            </div>
                          
                        </div>

                        <div class="col-md-6 mt-2">
                            <!-- QR Code Section -->
                            <div class="preview-section d-none" id="preview_qr_section" style="text-align: center;">
                                <img id="preview_qr_code" src="https://api.qrserver.com/v1/create-qr-code/?data=Sample-QR-Code" alt="QR Code" style="width: 100%; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>

                        <!-- Empty State -->
                        <div class="empty-preview" id="empty_preview" style="text-align: center; color: #999; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <i class="fas fa-id-badge" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;"></i>
                            <p style="margin: 0;">Select fields to see preview</p>
                            <small>Your badge will appear here</small>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-3 d-none">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Selected Fields</small>
                                <div class="fw-bold" id="selected_count">0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Completed</small>
                                <div class="fw-bold" id="completed_count">0</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Progress</small>
                                <div class="fw-bold" id="progress_percent">0%</div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;" id="progress_bar"></div>
                        </div>
                    </div>

                    <!-- Preview Controls -->
                    <div class="mt-3 d-grid d-none">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshPreview()">
                            <i class="fas fa-refresh me-1"></i>Refresh Preview
                        </button>
                    </div>
                </div>


            </div>

        </div>
    </div>

    </div>
</div>

<style>
.preview-section {
    transition: all 0.3s ease;
}

.conditional-field {
    transition: all 0.3s ease;
}

.badge-preview-container {
    transition: all 0.3s ease;
}

.form-check {
    transition: all 0.2s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_fields[]"]');
    
    // Initialize preview
    updatePreview();
    updateStats();
    
    // Handle field selection
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleFieldToggle(this);
            updatePreview();
            updateStats();
        });
    });
    
    // Handle input changes for real-time preview
    document.addEventListener('input', function(e) {
        if (e.target.matches('#name, #company_name, #designation, #qr_code_data, #badge_name')) {
            updatePreview();
            updateStats();
        }
    });
    
    // Handle file upload
    document.getElementById('logo').addEventListener('change', function(e) {
        handleLogoUpload(e);
        updatePreview();
        updateStats();
    });
    
    function handleFieldToggle(checkbox) {
        const fieldValue = checkbox.value;
        const fieldElement = document.getElementById(fieldValue + '_field');
        const previewSection = document.getElementById('preview_' + fieldValue.replace('_', '_') + '_section');
        console.log(previewSection,fieldValue)
        if (checkbox.checked) {
            // Show form field
           // fieldElement.classList.remove('d-none');
            
            // Show preview section if it exists
            if (previewSection) {
                previewSection.classList.remove('d-none');
            }
            
            // Special handling for logo
            if (fieldValue === 'logo') {
                document.getElementById('preview_logo_section').classList.remove('d-none');
            }
            
            // Special handling for QR code
            if (fieldValue === 'qr_code') {
                document.getElementById('preview_qr_section').classList.remove('d-none');
            }

            if (fieldValue === 'company_name') {
                document.getElementById('preview_company_name').classList.remove('d-none');
            }
            
            
        } else {
            // Hide form field
            fieldElement.classList.add('d-none');
            
            // Clear form field value
            const input = fieldElement.querySelector('input, textarea');
            if (input) {
                input.value = '';
            }
            
            // Hide preview section
            if (previewSection) {
                previewSection.classList.add('d-none');
            }
            
            // Special handling for logo
            if (fieldValue === 'logo') {
                document.getElementById('preview_logo_section').classList.add('d-none');
            }
            
            // Special handling for QR code
            if (fieldValue === 'qr_code') {
                document.getElementById('preview_qr_section').classList.add('d-none');
            }

            if (fieldValue === 'company_name') {
                document.getElementById('preview_company_name').classList.add('d-none');
            }
        }
    }
    
    function updatePreview() {
        const selectedFields = Array.from(checkboxes).filter(cb => cb.checked);
        const emptyPreview = document.getElementById('empty_preview');
        
        if (selectedFields.length === 0) {
            emptyPreview.style.display = 'block';
        } else {
            emptyPreview.style.display = 'none';
        }
        
        // Update name
        if (document.getElementById('field_name').checked) {
            document.getElementById('preview_name').textContent = '{'+'{'+'name'+'}'+'}';
        }
        
        // Update company name
        if (document.getElementById('field_company').checked) {
            document.getElementById('preview_company_name').textContent = '{'+'{'+'company_name'+'}'+'}';
        }
        
        // Update designation
        if (document.getElementById('field_designation').checked) {
            document.getElementById('preview_designation').textContent = '{'+'{'+'designation'+'}'+'}';
        }
        
        // Update QR code
        if (document.getElementById('field_qr').checked) {
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(qrValue)}&size=80x80`;
            document.getElementById('preview_qr_code').src = qrUrl;
        }
        
        // Update badge code
        const badgeName = document.getElementById('badge_name').value || '';
        document.getElementById('preview_badge_code').textContent = badgeName.toUpperCase();
    }
    
    function handleLogoUpload(event) {
        const file = event.target.files[0];
        const previewLogo = document.getElementById('preview_logo');
        
        if (file && document.getElementById('field_logo').checked) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewLogo.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
    
    function updateStats() {
        const selectedCheckboxes = Array.from(checkboxes).filter(cb => cb.checked);
        const selectedCount = selectedCheckboxes.length;
        
        // Count completed fields
        let completedCount = 0;
        selectedCheckboxes.forEach(checkbox => {
            const fieldValue = checkbox.value;
            if (fieldValue === 'logo') {
                if (document.getElementById('logo').files.length > 0) {
                    completedCount++;
                }
            } else {
                const input = document.getElementById(fieldValue === 'qr_code' ? 'qr_code_data' : fieldValue);
                if (input && input.value.trim() !== '') {
                    completedCount++;
                }
            }
        });
        
        // Always count badge name if filled
        if (document.getElementById('badge_name').value.trim() !== '') {
            completedCount++;
        }
        
        const progressPercent = selectedCount > 0 ? Math.round((completedCount / (selectedCount + 1)) * 100) : 0;
        
        document.getElementById('selected_count').textContent = selectedCount;
        document.getElementById('completed_count').textContent = completedCount;
        document.getElementById('progress_percent').textContent = progressPercent + '%';
        document.getElementById('progress_bar').style.width = progressPercent + '%';
    }
    
    // Global function for refresh button
    window.refreshPreview = function() {
        updatePreview();
        updateStats();
        
        // Show success feedback
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Refreshed!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 1000);
    };
});
</script>
@endsection
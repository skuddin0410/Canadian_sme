@extends('layouts.admin')

@section('title', 'View Badge')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-id-badge me-2"></i>Badge Details</h4>
                <div>
                    <a href="{{ route('badges.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body mt-2">
                @if($badge->badge_path)
                    <div class="text-center mb-4">
                        <img src="{{ Storage::url($badge->badge_path) }}" alt="Generated Badge" class="img-fluid" style="max-width: 100%; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mt-4">
                        <h5>Selected Fields:</h5>
                        <ul class="list-unstyled">
                            @foreach($badge->selected_fields as $field)
                                <li><i class="fas fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $field)) }}</li>
                            @endforeach
                        </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                         <div class="preview-badge" id="badgePreview"></div>


                            <div class="card-body">
                                <div class="badge-preview-container" style="border: 2px solid #333; border-radius: 10px; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 300px; position: relative;">
                                    
                                   <div class="row">
                                    <div class="col-md-6 mt-2">
                                        <div class="preview-section" id="preview_logo_section" style="text-align: center; margin-bottom: 15px;">
                                            <img id="preview_logo" class="img-fluid" alt="Logo" style="object-fit: contain; border-radius: 8px;" src="{{asset('sme-logo.png')}}">
                                        </div>

                                        <!-- Name Section -->
                                        <div class="preview-section" id="preview_name_section" style="text-align: center; margin-bottom: 10px;">
                                            <h4 id="preview_name" style="margin: 0; font-weight: bold; color: #333; font-size: 1.2rem;">{{'name'}}</h4>
                                        </div>

                                        <!-- Company Name Section -->
                                        <div class="preview-section" id="preview_company_section" style="text-align: center; margin-bottom: 8px;">
                                            <p id="preview_company_name" style="margin: 0; color: #666; font-size: 1rem;">{{'company_name'}}</p>
                                        </div>
                             

                                        <div class="preview-section" id="preview_designation_section" style="text-align: center; margin-bottom: 15px;">
                                            <p id="preview_designation" style="margin: 0; color: #888; font-size: 0.9rem; font-style: italic;">{{'designation'}}</p>
                                        </div>
                                      
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <div class="preview-section" id="preview_qr_section" style="text-align: center;">
                                            <img id="preview_qr_code" src="https://api.qrserver.com/v1/create-qr-code/?data=Sample-QR-Code" alt="QR Code" style="width: 100%; border: 1px solid #ddd;">
                                        </div>
                                    </div>
                                </div>
                                </div>

                            </div>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cog me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
              {{--       <a href="{{ route('badges.print') }}" class="btn btn-success">
                        <i class="fas fa-print me-2"></i>Print Badge
                    </a> --}}
                    <a href="{{ route('badges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Badge
                    </a>
                    @if($badge->qr_code_path)
                        <a href="{{ Storage::url($badge->qr_code_path) }}" target="_blank" class="btn btn-outline-info">
                            <i class="fas fa-qrcode me-2"></i>View QR Code
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_fields[]"]');
    const preview = document.getElementById('badgePreview');
    
    // Handle field visibility
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const fieldId = this.value + '_field';
            const field = document.getElementById(fieldId);
            
            if (this.checked) {
                field.classList.add('active');
            } else {
                field.classList.remove('active');
                // Clear the field value when unchecked
                const input = field.querySelector('input, textarea');
                if (input) input.value = '';
            }
            
            updatePreview();
        });
    });
    
    // Handle input changes for preview
    document.addEventListener('input', updatePreview);
    document.addEventListener('change', updatePreview);
    
    function updatePreview() {
        const selectedFields = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selectedFields.length === 0) {
            preview.innerHTML = `
                <i class="fas fa-id-badge fa-3x text-muted mb-3"></i>
                <p class="text-muted text-center">Select fields to see preview</p>
            `;
            return;
        }
        
        let previewContent = '<div class="text-center">';
        
        // Add logo placeholder
        if (selectedFields.includes('logo')) {
            previewContent += '<div class="mb-2"><i class="fas fa-image text-muted" style="font-size: 2rem;"></i></div>';
        }
        
        // Add name
        if (selectedFields.includes('name')) {
            const name = document.getElementById('name').value || 'Your Name';
            previewContent += `<h4 class="mb-2">${name}</h4>`;
        }
        
        // Add company
        if (selectedFields.includes('company_name')) {
            const company = document.getElementById('company_name').value || 'Company Name';
            previewContent += `<p class="text-muted mb-1">${company}</p>`;
        }
        
        // Add designation
        if (selectedFields.includes('designation')) {
            const designation = document.getElementById('designation').value || 'Designation';
            previewContent += `<p class="text-muted mb-3">${designation}</p>`;
        }
        
        // Add QR code placeholder
        if (selectedFields.includes('qr_code')) {
            previewContent += '<div class="mt-3"><i class="fas fa-qrcode text-muted" style="font-size: 2rem;"></i></div>';
        }
        
        previewContent += '</div>';
        preview.innerHTML = previewContent;
    }
});
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Create Lead - PropertyConnect')

@push('styles')
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark">Create New Lead</h1>
        <p class="text-muted">Fill in the details to add a new lead</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please check the form below for errors.
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf

                {{-- Personal Info --}}
                <h5 class="fw-bold mb-3">Personal Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" 
                               class="form-control @error('first_name') is-invalid @enderror" required>
                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" 
                               class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Lead Details --}}
                <h5 class="fw-bold mb-3">Lead Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['new','contacted','qualified','converted','lost'] as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            @foreach(['low','medium','high'] as $priority)
                                <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Source</label>
                        <select name="source" class="form-select">
                            @foreach(['website','referral','social_media','walk_in','phone','advertisement'] as $src)
                                <option value="{{ $src }}" {{ old('source') == $src ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_',' ',$src)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

</div>

<hr class="my-4">

{{-- Lead Organization: Tagging System --}}
<h5 class="fw-bold mb-3">Lead Tags</h5>
@php
    $tags = ['hot', 'warm', 'cold', 'follow-up'];
    $selectedTags = old('tags', []);
@endphp
<div class="row">
    @foreach($tags as $tag)
        <div class="col-md-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" 
                       name="tags[]" 
                       value="{{ $tag }}" 
                       {{ in_array($tag, $selectedTags) ? 'checked' : '' }}>
                <label class="form-check-label">{{ ucfirst($tag) }}</label>
            </div>
        </div>
    @endforeach
</div>
<hr class="my-4">

{{-- Custom Fields --}}
<h5 class="fw-bold mb-3">Custom Fields</h5>
 <p class="text-black">Add Company-specific information and notes:</p>
<div id="custom-fields-wrapper">
    <div class="row g-3 mb-2 custom-field">
        <div class="col-md-5">
            <input type="text" name="custom_fields[0][key]" class="form-control" placeholder="Field Name">
        </div>
        <div class="col-md-5">
            <input type="text" name="custom_fields[0][value]" class="form-control" placeholder="Field Value">
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-success btn-sm add-field me-2">
                <i class="fa fa-plus"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm remove-field">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
</div>






                {{-- <hr class="my-4"> --}}

                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Save Lead
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

{{-- @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let fieldIndex = 1;

    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-field')) {
            e.preventDefault();
            let wrapper = document.getElementById('custom-fields-wrapper');

            let newField = document.createElement('div');
            newField.classList.add('row', 'g-3', 'mb-2', 'custom-field');
            newField.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][key]" class="form-control" placeholder="Field Name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][value]" class="form-control" placeholder="Field Value">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-success btn-sm add-field me-2">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm remove-field">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            `;
            wrapper.appendChild(newField);
            fieldIndex++;
        }

        if (e.target.closest('.remove-field')) {
            e.preventDefault();
            let field = e.target.closest('.custom-field');
            if (field.parentNode.children.length > 1) {
                field.remove();
            }
        }
    });
</script>
<script>
    let fieldIndex = 1;

    function refreshButtons() {
        let fields = document.querySelectorAll('#custom-fields-wrapper .custom-field');
        fields.forEach((field, idx) => {
            let addBtn = field.querySelector('.add-field');
            let removeBtn = field.querySelector('.remove-field');

            // Only last row shows +
            if (addBtn) addBtn.style.display = (idx === fields.length - 1) ? 'inline-block' : 'none';

            // First row cannot be removed
            if (removeBtn) removeBtn.style.display = (fields.length > 1) ? 'inline-block' : 'none';
        });
    }

    document.addEventListener('click', function(e) {
        // Add field
        if (e.target.matches('.add-field, .add-field *')) {
            e.preventDefault();
            let wrapper = document.getElementById('custom-fields-wrapper');

            let newField = document.createElement('div');
            newField.classList.add('row', 'g-3', 'mb-2', 'custom-field');
            newField.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][key]" class="form-control" placeholder="Field Name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][value]" class="form-control" placeholder="Field Value">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-success btn-sm add-field me-2">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm remove-field">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            `;
            wrapper.appendChild(newField);
            fieldIndex++;

            refreshButtons();
        }

        // Remove field
        if (e.target.matches('.remove-field, .remove-field *')) {
            e.preventDefault();
            let field = e.target.closest('.custom-field');
            if (field && document.querySelectorAll('#custom-fields-wrapper .custom-field').length > 1) {
                field.remove();
            }
            refreshButtons();
        }
    });

    // Initial setup
    refreshButtons();
</script>

@endpush --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let fieldIndex = 1;

    function refreshRemoveButtons() {
        let fields = document.querySelectorAll('#custom-fields-wrapper .custom-field');
        fields.forEach((field, index) => {
            let removeBtn = field.querySelector('.remove-field');
            if (removeBtn) {
                removeBtn.style.display = (index === 0) ? 'none' : 'inline-block';
            }
        });
    }

    document.getElementById('custom-fields-wrapper').addEventListener('click', function (e) {
        // Add new field
        if (e.target.closest('.add-field')) {
            e.preventDefault();
            let wrapper = document.getElementById('custom-fields-wrapper');
            let newField = document.createElement('div');
            newField.classList.add('row', 'g-3', 'mb-2', 'custom-field');
            newField.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][key]" class="form-control" placeholder="Field Name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][value]" class="form-control" placeholder="Field Value">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-success btn-sm add-field me-2">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm remove-field">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            `;
            wrapper.appendChild(newField);
            fieldIndex++;
            refreshRemoveButtons();
        }

        // Remove field
        if (e.target.closest('.remove-field')) {
            e.preventDefault();
            e.target.closest('.custom-field').remove();
            refreshRemoveButtons();
        }
    });

    // Run once on load
    refreshRemoveButtons();
});
</script>



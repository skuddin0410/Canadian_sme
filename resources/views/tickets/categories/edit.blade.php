@extends('layouts.admin')

@section('title', 'Edit Category - ' . $ticketCategory->name)

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Ticket Category</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.ticket-categories.index') }}">Ticket Categories</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ticket-categories.show', $ticketCategory) }}">{{ $ticketCategory->name }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.ticket-categories.show', $ticketCategory) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('admin.ticket-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>

            @if($ticketCategory->ticketTypes->count() > 0)
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> This category is used by {{ $ticketCategory->ticketTypes->count() }} ticket type(s). 
                    Changes to the color will affect how these tickets are displayed.
                </div>
            @endif

            <form action="{{ route('admin.ticket-categories.update', $ticketCategory) }}" method="POST" id="categoryForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Form -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $ticketCategory->name) }}" 
                                                   placeholder="e.g., VIP, General Admission, Student" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Describe this category and what makes it special...">{{ old('description', $ticketCategory->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="color" class="form-label">Category Color <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                                       id="color" name="color" value="{{ old('color', $ticketCategory->color) }}" required>
                                                <input type="text" class="form-control" id="colorHex" 
                                                       value="{{ old('color', $ticketCategory->color) }}" placeholder="#000000">
                                            </div>
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">This color will be used to identify tickets in this category</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" 
                                                   value="{{ old('sort_order', $ticketCategory->sort_order) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Color Presets -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Color Presets</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Click on a color below to use it as your category color:</p>
                                <div class="color-presets">
                                    @php
                                        $presetColors = [
                                            '#007bff' => 'Primary Blue',
                                            '#28a745' => 'Success Green',
                                            '#dc3545' => 'Danger Red',
                                            '#ffc107' => 'Warning Yellow',
                                            '#17a2b8' => 'Info Cyan',
                                            '#6f42c1' => 'Purple',
                                            '#e83e8c' => 'Pink',
                                            '#fd7e14' => 'Orange',
                                            '#20c997' => 'Teal',
                                            '#6c757d' => 'Gray',
                                            '#343a40' => 'Dark',
                                            '#f8f9fa' => 'Light'
                                        ];
                                    @endphp
                                    @foreach($presetColors as $colorValue => $colorName)
                                        <button type="button" class="btn color-preset-btn me-2 mb-2" 
                                                style="background-color: {{ $colorValue }}; width: 50px; height: 50px; border: 2px solid #dee2e6;" 
                                                data-color="{{ $colorValue }}" 
                                                title="{{ $colorName }}"
                                                onclick="selectPresetColor('{{ $colorValue }}')">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Associated Ticket Types -->
                        @if($ticketCategory->ticketTypes->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Associated Ticket Types ({{ $ticketCategory->ticketTypes->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Event</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ticketCategory->ticketTypes->take(5) as $ticketType)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="color-dot me-2" id="preview-{{ $loop->index }}"
                                                                     style="background-color: {{ $ticketCategory->color }}; width: 8px; height: 8px; border-radius: 50%;"></div>
                                                                {{ $ticketType->name }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $ticketType->event->name }}</td>
                                                        <td>${{ number_format($ticketType->base_price, 2) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $ticketType->is_active ? 'success' : 'secondary' }}">
                                                                {{ $ticketType->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.ticket-types.show', $ticketType) }}" 
                                                               class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if($ticketCategory->ticketTypes->count() > 5)
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            ... and {{ $ticketCategory->ticketTypes->count() - 5 }} more ticket types
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_active" 
                                               name="is_active" value="1" 
                                               {{ old('is_active', $ticketCategory->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Category</strong>
                                            <div class="form-text">Category is available for use</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Live Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="preview-section">
                                    <h6>Category Badge:</h6>
                                    <div class="mb-3">
                                        <span class="badge preview-badge" id="badgePreview" 
                                              style="background-color: {{ $ticketCategory->color }}">
                                            <span id="badgeText">{{ $ticketCategory->name }}</span>
                                        </span>
                                    </div>
                                    
                                    <h6>In Ticket List:</h6>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="color-indicator me-3" id="listPreview"
                                                 style="background-color: {{ $ticketCategory->color }}; width: 20px; height: 20px; border-radius: 50%;"></div>
                                            <div>
                                                <strong>Sample Ticket Type</strong>
                                                <div class="small text-muted">$299.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Stats -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Current Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-primary mb-1">{{ $ticketCategory->ticketTypes->count() }}</h4>
                                        <small class="text-muted">Ticket Types</small>
                                    </div>
                                    <div class="col-6">
                                        @php
                                            $activeTypes = $ticketCategory->ticketTypes->where('is_active', true)->count();
                                        @endphp
                                        <h4 class="text-success mb-1">{{ $activeTypes }}</h4>
                                        <small class="text-muted">Active Types</small>
                                    </div>
                                </div>
                                
                                @if($ticketCategory->ticketTypes->count() > 0)
                                    <hr>
                                    <div class="small text-muted">
                                        <strong>Last used:</strong> 
                                        {{ $ticketCategory->ticketTypes->max('updated_at')->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-info" onclick="resetToDefaults()">
                                        <i class="fas fa-undo"></i> Reset to Defaults
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="duplicateCategory()">
                                        <i class="fas fa-copy"></i> Duplicate Category
                                    </button>
                                    @if($ticketCategory->ticketTypes->count() == 0)
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash"></i> Delete Category
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.ticket-categories.show', $ticketCategory) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category "{{ $ticketCategory->name }}"?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.ticket-categories.destroy', $ticketCategory) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.color-preset-btn {
    transition: all 0.2s ease;
}

.color-preset-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.color-preset-btn.selected {
    border-color: #000 !important;
    border-width: 3px !important;
}

.cursor-pointer {
    cursor: pointer;
}

.preview-section h6 {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color picker synchronization
    const colorPicker = document.getElementById('color');
    const colorHex = document.getElementById('colorHex');
    const badgePreview = document.getElementById('badgePreview');
    const listPreview = document.getElementById('listPreview');
    
    // Name input for live preview
    const nameInput = document.getElementById('name');
    const badgeText = document.getElementById('badgeText');
    
    // Sync color picker with hex input
    colorPicker.addEventListener('input', function() {
        colorHex.value = this.value;
        updatePreviews();
        updateTicketTypePreviews();
    });
    
    colorHex.addEventListener('input', function() {
        if (isValidHex(this.value)) {
            colorPicker.value = this.value;
            updatePreviews();
            updateTicketTypePreviews();
        }
    });
    
    // Live name preview
    nameInput.addEventListener('input', function() {
        badgeText.textContent = this.value || 'Category Name';
    });
    
    function updatePreviews() {
        const color = colorPicker.value;
        badgePreview.style.backgroundColor = color;
        listPreview.style.backgroundColor = color;
    }
    
    function updateTicketTypePreviews() {
        const color = colorPicker.value;
        // Update all preview dots in the ticket types table
        document.querySelectorAll('[id^="preview-"]').forEach(dot => {
            dot.style.backgroundColor = color;
        });
    }
    
    function isValidHex(hex) {
        return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
    }
});

function selectPresetColor(color) {
    document.getElementById('color').value = color;
    document.getElementById('colorHex').value = color;
    
    // Update previews
    document.getElementById('badgePreview').style.backgroundColor = color;
    document.getElementById('listPreview').style.backgroundColor = color;
    
    // Update ticket type previews
    document.querySelectorAll('[id^="preview-"]').forEach(dot => {
        dot.style.backgroundColor = color;
    });
    
    // Visual feedback for selected preset
    document.querySelectorAll('.color-preset-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    document.querySelector(`[data-color="${color}"]`).classList.add('selected');
}

function resetToDefaults() {
    if (confirm('Reset category to default values? This will lose any unsaved changes.')) {
        document.getElementById('name').value = '{{ $ticketCategory->name }}';
        document.getElementById('description').value = '{{ $ticketCategory->description }}';
        document.getElementById('color').value = '{{ $ticketCategory->color }}';
        document.getElementById('colorHex').value = '{{ $ticketCategory->color }}';
        document.getElementById('sort_order').value = '{{ $ticketCategory->sort_order }}';
        document.getElementById('is_active').checked = {{ $ticketCategory->is_active ? 'true' : 'false' }};
        
        // Update previews
        document.getElementById('badgePreview').style.backgroundColor = '{{ $ticketCategory->color }}';
        document.getElementById('listPreview').style.backgroundColor = '{{ $ticketCategory->color }}';
        document.getElementById('badgeText').textContent = '{{ $ticketCategory->name }}';
        
        // Update ticket type previews
        document.querySelectorAll('[id^="preview-"]').forEach(dot => {
            dot.style.backgroundColor = '{{ $ticketCategory->color }}';
        });
    }
}

function duplicateCategory() {
    if (confirm('Create a copy of this category?')) {
        window.location.href = '{{ route("admin.ticket-categories.create") }}?duplicate_from={{ $ticketCategory->id }}';
    }
}

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Form validation
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const color = document.getElementById('color').value;
    
    if (!name) {
        e.preventDefault();
        alert('Category name is required.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (!isValidHex(color)) {
        e.preventDefault();
        alert('Please select a valid color.');
        document.getElementById('color').focus();
        return false;
    }
});

function isValidHex(hex) {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S for save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.getElementById('categoryForm').submit();
    }
    
    // Escape to cancel
    if (e.key === 'Escape') {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            window.location.href = '{{ route("admin.ticket-categories.show", $ticketCategory) }}';
        }
    }
});

// Auto-save draft functionality (optional)
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        // Implementation for auto-saving draft
        console.log('Auto-saving draft...');
        // You could save to localStorage or send AJAX request
    }, 30000); // Save every 30 seconds
}

// Trigger auto-save on form changes
document.querySelectorAll('#categoryForm input, #categoryForm textarea').forEach(element => {
    element.addEventListener('input', autoSave);
});

// Warn about unsaved changes
let formChanged = false;
document.querySelectorAll('#categoryForm input, #categoryForm textarea, #categoryForm select').forEach(element => {
    element.addEventListener('input', function() {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        const confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
        e.returnValue = confirmationMessage;
        return confirmationMessage;
    }
});

// Remove warning when form is submitted
document.getElementById('categoryForm').addEventListener('submit', function() {
    formChanged = false;
});
</script>
@endpush
                                
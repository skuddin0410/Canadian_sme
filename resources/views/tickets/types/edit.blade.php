@extends('layouts.admin')

@section('title', 'Edit Ticket Type')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Ticket Type</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.ticket-types.index') }}">Ticket Types</a></li>
                            <li class="breadcrumb-item active">{{ $ticketType->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.ticket-types.show', $ticketType) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            @if($ticketType->eventTickets()->count() > 0)
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This ticket type has associated ticket sales. Some changes may affect existing bookings.
                </div>
            @endif

            <form action="{{ route('admin.ticket-types.update', $ticketType) }}" method="POST" id="ticketTypeForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="event_id" class="form-label">Event <span class="text-danger">*</span></label>
                                            <select class="form-select @error('event_id') is-invalid @enderror" 
                                                    id="event_id" name="event_id" required>
                                                <option value="">Select Event</option>
                                                @foreach($events as $event)
                                                    <option value="{{ $event->id }}" 
                                                            {{ old('event_id', $ticketType->event_id) == $event->id ? 'selected' : '' }}>
                                                        {{ $event->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('event_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id">
                                                <option value="">No Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            data-color="{{ $category->color }}"
                                                            {{ old('category_id', $ticketType->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Ticket Type Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $ticketType->name) }}" 
                                           placeholder="e.g., General Admission, VIP Pass" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Describe what this ticket includes...">{{ old('description', $ticketType->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Inventory -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Pricing & Inventory</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inventoryModal">
                                    <i class="fas fa-edit"></i> Quick Inventory Update
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="base_price" class="form-label">Base Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                                       id="base_price" name="base_price" 
                                                       value="{{ old('base_price', $ticketType->base_price) }}" 
                                                       step="0.01" min="0" placeholder="0.00" required>
                                                @error('base_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="total_quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('total_quantity') is-invalid @enderror" 
                                                   id="total_quantity" name="total_quantity" 
                                                   value="{{ old('total_quantity', $ticketType->total_quantity) }}" 
                                                   min="1" placeholder="100" required>
                                            @error('total_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Current available: {{ $ticketType->available_quantity }}
                                                @if($ticketType->total_quantity != $ticketType->available_quantity)
                                                    | Sold: {{ $ticketType->total_quantity - $ticketType->available_quantity }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_quantity_per_order" class="form-label">Min Quantity Per Order <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('min_quantity_per_order') is-invalid @enderror" 
                                                   id="min_quantity_per_order" name="min_quantity_per_order" 
                                                   value="{{ old('min_quantity_per_order', $ticketType->min_quantity_per_order) }}" 
                                                   min="1" required>
                                            @error('min_quantity_per_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_quantity_per_order" class="form-label">Max Quantity Per Order</label>
                                            <input type="number" class="form-control @error('max_quantity_per_order') is-invalid @enderror" 
                                                   id="max_quantity_per_order" name="max_quantity_per_order" 
                                                   value="{{ old('max_quantity_per_order', $ticketType->max_quantity_per_order) }}" 
                                                   min="1" placeholder="10">
                                            @error('max_quantity_per_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave blank for no limit</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Period -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Sale Period</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sale_start_date" class="form-label">Sale Start Date <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('sale_start_date') is-invalid @enderror" id="sale_start_date" name="sale_start_date" 
                                                   value="{{ old('sale_start_date', $ticketType->sale_start_date ? $ticketType->sale_start_date->format('Y-m-d\TH:i') : '') }}" required>
                                            @error('sale_start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sale_end_date" class="form-label">Sale End Date</label>
                                            <input type="datetime-local" class="form-control @error('sale_end_date') is-invalid @enderror" 
                                                   id="sale_end_date" name="sale_end_date" 
                                                   value="{{ old('sale_end_date' , $ticketType->sale_end_date) }}">
                                            @error('sale_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave blank to sell until event date</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4" id="earlyBird">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-bolt me-2"></i>Early Bird Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_earlybird"
                                               name="is_earlybird" value="1" {{ old('is_earlybird', $ticketType->is_earlybird) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="is_earlybird">
                                            Enable Early Bird Pricing
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Offer discounted pricing for a limited number of attendees.</small>
                                </div>

                                <div id="earlyBirdSettings" style="display: {{ old('is_earlybird', $ticketType->is_earlybird) ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="earlybird_amount" class="font-weight-bold">Early Bird Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('earlybird_amount') is-invalid @enderror"
                                                           id="earlybird_amount" name="earlybird_amount"
                                                           value="{{ old('earlybird_amount', $ticketType->earlybird_amount) }}" step="0.01" min="0">
                                                </div>
                                                @error('earlybird_amount')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="earlybird_quantity" class="font-weight-bold">Early Bird Quantity</label>
                                                <input type="number" class="form-control @error('earlybird_quantity') is-invalid @enderror"
                                                       id="earlybird_quantity" name="earlybird_quantity"
                                                       value="{{ old('earlybird_quantity', $ticketType->earlybird_quantity) }}" min="1">
                                                <small class="form-text text-muted">Only this many attendees can get early bird price.</small>
                                                @error('earlybird_quantity')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4" id="grouptTicket">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-users me-2"></i>Group Ticket Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_group" name="is_group"
                                               value="1" {{ old('is_group', $ticketType->is_group) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="is_group">
                                            Enable Group Discount
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Apply a percentage discount when the team size reaches the configured threshold.</small>
                                </div>

                                <div id="groupSettings" style="display: {{ old('is_group', $ticketType->is_group) ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="group_size" class="font-weight-bold">Minimum Group Size</label>
                                                <input type="number" class="form-control @error('group_size') is-invalid @enderror"
                                                       id="group_size" name="group_size" value="{{ old('group_size', $ticketType->group_size) }}"
                                                       min="2">
                                                <small class="form-text text-muted">Discount starts when attendee count reaches this size.</small>
                                                @error('group_size')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount_percentage" class="font-weight-bold">Group Discount (%)</label>
                                                <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror"
                                                       id="discount_percentage" name="discount_percentage"
                                                       value="{{ old('discount_percentage', $ticketType->discount_percentage) }}"
                                                       min="0" max="100" step="0.01">
                                                <small class="form-text text-muted">Discount percentage applied to the calculated ticket price.</small>
                                                @error('discount_percentage')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Sidebar -->
                    <div class="col-lg-4">
                        <!-- Status & Options -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', $ticketType->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong>
                                            <div class="form-text">Ticket type is available for purchase</div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="requires_approval" 
                                               name="requires_approval" value="1" {{ old('requires_approval', $ticketType->requires_approval) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_approval">
                                            <strong>Requires Approval</strong>
                                            <div class="form-text">Bookings need manual approval</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Access Permissions -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Access Permissions</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Who can purchase this ticket?</label>
                                    
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_all" 
                                               name="access_permissions[]" value="all" 
                                               {{ in_array('all', old('access_permissions', ['all'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_all">
                                            Everyone (Public)
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_members" 
                                               name="access_permissions[]" value="members"
                                               {{ in_array('members', old('access_permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_members">
                                            Registered Members Only
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_students" 
                                               name="access_permissions[]" value="students"
                                               {{ in_array('students', old('access_permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_students">
                                            Students (with verification)
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_corporate" 
                                               name="access_permissions[]" value="corporate"
                                               {{ in_array('corporate', old('access_permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_corporate">
                                            Corporate Groups
                                        </label>
                                    </div>

                                    @error('access_permissions')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Preview</h5>
                            </div>
                            <div class="card-body">
                                <div id="ticketPreview" class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1" id="previewName">Ticket Type Name : {{$ticketType->name}}</h6>
                                            <small class="text-muted" id="previewEvent">Event : 
                                            {{ $event->title }}</small>
                                        </div>
                                        <span class="badge" id="previewCategory" style="display: none;"></span>
                                    </div>
                                    <p class="text-muted small mb-2" id="previewDescription">Description : {{$ticketType->description}}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary" id="previewPrice">${{$ticketType->base_price}}</strong>
                                        <small class="text-muted" id="previewQuantity"> Available Quantity: {{ $ticketType->available_quantity }}
                                                @if($ticketType->total_quantity != $ticketType->available_quantity)
                                                    | Sold: {{ $ticketType->total_quantity - $ticketType->available_quantity }}
                                                @endif</small>
                                    </div>
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
                                    <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Ticket Type
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const eventSelect = document.getElementById('event_id');
    const categorySelect = document.getElementById('category_id');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const priceInput = document.getElementById('base_price');
    const quantityInput = document.getElementById('total_quantity');
    
    // Preview elements
    const previewName = document.getElementById('previewName');
    const previewEvent = document.getElementById('previewEvent');
    const previewCategory = document.getElementById('previewCategory');
    const previewDescription = document.getElementById('previewDescription');
    const previewPrice = document.getElementById('previewPrice');
    const previewQuantity = document.getElementById('previewQuantity');
    const isGroupInput = document.getElementById('is_group');
    const isEarlyBirdInput = document.getElementById('is_earlybird');
    const groupSettings = document.getElementById('groupSettings');
    const earlyBirdSettings = document.getElementById('earlyBirdSettings');
    
    // Update preview in real-time
    function updatePreview() {
        // Name
        previewName.textContent = nameInput.value || 'Ticket Type Name';
        
        // Event
        const selectedEvent = eventSelect.options[eventSelect.selectedIndex];
        previewEvent.textContent = selectedEvent.value ? selectedEvent.text : 'Select an event';
        
        // Category
        const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
        if (selectedCategory.value) {
            previewCategory.textContent = selectedCategory.text;
            previewCategory.style.backgroundColor = selectedCategory.dataset.color || '#6c757d';
            previewCategory.style.display = 'inline-block';
        } else {
            previewCategory.style.display = 'none';
        }
        
        // Description
        previewDescription.textContent = descriptionInput.value || 'Add a description...';
        
        // Price
        const price = parseFloat(priceInput.value) || 0;
        previewPrice.textContent = '$' + price.toFixed(2);
        
        // Quantity
        const quantity = parseInt(quantityInput.value) || 0;
        previewQuantity.textContent = quantity.toLocaleString() + ' available';
    }
    
    // Attach event listeners
    [eventSelect, categorySelect, nameInput, descriptionInput, priceInput, quantityInput].forEach(element => {
        element.addEventListener('input', updatePreview);
        element.addEventListener('change', updatePreview);
    });
    
    // Initial preview update
    updatePreview();

    function syncPricingToggles() {
        if (groupSettings && isGroupInput) {
            groupSettings.style.display = isGroupInput.checked ? 'block' : 'none';
        }
        if (earlyBirdSettings && isEarlyBirdInput) {
            earlyBirdSettings.style.display = isEarlyBirdInput.checked ? 'block' : 'none';
        }
    }

    syncPricingToggles();
    isGroupInput?.addEventListener('change', syncPricingToggles);
    isEarlyBirdInput?.addEventListener('change', syncPricingToggles);
    
    // Form validation
    document.getElementById('ticketTypeForm').addEventListener('submit', function(e) {
        const maxQuantity = document.getElementById('max_quantity_per_order');
        const minQuantity = document.getElementById('min_quantity_per_order');
        
        if (maxQuantity.value && minQuantity.value) {
            if (parseInt(maxQuantity.value) < parseInt(minQuantity.value)) {
                e.preventDefault();
                alert('Maximum quantity per order cannot be less than minimum quantity per order.');
                maxQuantity.focus();
                return false;
            }
        }
        
        const startDate = document.getElementById('sale_start_date');
        const endDate = document.getElementById('sale_end_date');
        
        if (startDate.value && endDate.value) {
            if (new Date(startDate.value) >= new Date(endDate.value)) {
                e.preventDefault();
                alert('Sale end date must be after sale start date.');
                endDate.focus();
                return false;
            }
        }

        if (isGroupInput.checked) {
            const groupSize = document.getElementById('group_size');
            const discountPercentage = document.getElementById('discount_percentage');
            if (!groupSize.value || parseInt(groupSize.value, 10) < 2) {
                e.preventDefault();
                alert('Please set a valid minimum group size of at least 2.');
                groupSize.focus();
                return false;
            }
            if (!discountPercentage.value) {
                e.preventDefault();
                alert('Please set the group discount percentage.');
                discountPercentage.focus();
                return false;
            }
        }

        if (isEarlyBirdInput.checked) {
            const earlyBirdAmount = document.getElementById('earlybird_amount');
            const earlyBirdQuantity = document.getElementById('earlybird_quantity');
            if (!earlyBirdAmount.value) {
                e.preventDefault();
                alert('Please set the early bird price.');
                earlyBirdAmount.focus();
                return false;
            }
            if (!earlyBirdQuantity.value || parseInt(earlyBirdQuantity.value, 10) < 1) {
                e.preventDefault();
                alert('Please set the early bird quantity.');
                earlyBirdQuantity.focus();
                return false;
            }
        }
    });
    
    // Access permissions logic
    const allPermission = document.getElementById('perm_all');
    const otherPermissions = document.querySelectorAll('input[name="access_permissions[]"]:not(#perm_all)');
    
    allPermission.addEventListener('change', function() {
        if (this.checked) {
            otherPermissions.forEach(perm => perm.checked = false);
        }
    });
    
    otherPermissions.forEach(perm => {
        perm.addEventListener('change', function() {
            if (this.checked) {
                allPermission.checked = false;
            }
        });
    });
});
</script>
@endsection

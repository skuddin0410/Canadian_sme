@extends('layouts.admin')

@section('title', 'Create Ticket Type')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create Ticket Type</h1>
                <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Ticket Types
                </a>
            </div>

            <form action="{{ route('admin.ticket-types.store') }}" method="POST" id="ticketTypeForm">
                @csrf
                
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
                                                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
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
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="name" class="form-label">Ticket Type Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., General Admission, VIP Pass" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>

                                           <div class="col-md-6">
    <div class="form-group">
    <label for="session_id" class="font-weight-bold">
        Session <span class="text-danger">*</span>
    </label>
    <select name="session_id" id="session_id" class="form-control" required>
        <option value="">Select Session</option>
    </select>
    @error('session_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

</div>


                               </div>

                                



                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Describe what this ticket includes...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
             
                        <!-- Pricing & Inventory -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pricing & Inventory</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="base_price" class="form-label">Base Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                                       id="base_price" name="base_price" value="{{ old('base_price') }}" 
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
                                                   id="total_quantity" name="total_quantity" value="{{ old('total_quantity') }}" 
                                                   min="1" placeholder="100" required>
                                            @error('total_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Total number of tickets available for sale</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_quantity_per_order" class="form-label">Min Quantity Per Order <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('min_quantity_per_order') is-invalid @enderror" 
                                                   id="min_quantity_per_order" name="min_quantity_per_order" 
                                                   value="{{ old('min_quantity_per_order', 1) }}" min="1" required>
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
                                                   value="{{ old('max_quantity_per_order') }}" min="1" placeholder="10">
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
                                            <label for="sale_start_date" class="form-label">Sale Start Date</label>
                                            <input type="datetime-local" class="form-control @error('sale_start_date') is-invalid @enderror" 
                                                   id="sale_start_date" name="sale_start_date" 
                                                   value="{{ old('sale_start_date') }}">
                                            @error('sale_start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave blank to start selling immediately</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sale_end_date" class="form-label">Sale End Date</label>
                                            <input type="datetime-local" class="form-control @error('sale_end_date') is-invalid @enderror" 
                                                   id="sale_end_date" name="sale_end_date" 
                                                   value="{{ old('sale_end_date') }}">
                                            @error('sale_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave blank to sell until event date</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                           <!-- Early Bird Pricing -->
                <div class="card shadow mb-4" id="earlyBird">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>Early Bird Pricing
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enable_early_bird" 
                                       name="enable_early_bird" value="1" {{ old('enable_early_bird') ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="enable_early_bird">
                                    Enable Early Bird Pricing
                                </label>
                            </div>
                            <small class="form-text text-muted">Offer discounted pricing for early bookings</small>
                        </div>

                        <div id="earlyBirdSettings" style="display: {{ old('enable_early_bird') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="early_bird_price" class="font-weight-bold">Early Bird Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control @error('early_bird_price') is-invalid @enderror" 
                                                   id="early_bird_price" name="early_bird_price" 
                                                   value="{{ old('early_bird_price', '0.00') }}" step="0.01" min="0">
                                        </div>
                                        @error('early_bird_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="early_bird_quantity" class="font-weight-bold">Early Bird Quantity</label>
                                        <input type="number" class="form-control @error('early_bird_quantity') is-invalid @enderror" 
                                               id="early_bird_quantity" name="early_bird_quantity" 
                                               value="{{ old('early_bird_quantity', '50') }}" min="1">
                                        <small class="form-text text-muted">Limited quantity at early bird price</small>
                                        @error('early_bird_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_group" name="is_group" 
                                       value="1" {{ old('is_group') ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_group">
                                    Enable Group Ticket
                                </label>
                            </div>
                            <small class="form-text text-muted">Allow this ticket to be purchased as a group package</small>
                        </div>

                        <div id="groupSettings" style="display: {{ old('is_group') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_size" class="font-weight-bold">Group Size</label>
                                        <input type="number" class="form-control @error('group_size') is-invalid @enderror" 
                                               id="group_size" name="group_size" value="{{ old('group_size', '5') }}" 
                                               min="2">
                                        <small class="form-text text-muted">Number of people included in group ticket</small>
                                        @error('group_size')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_discount" class="font-weight-bold">Group Discount (%)</label>
                                        <input type="number" class="form-control @error('group_discount') is-invalid @enderror" 
                                               id="group_discount" name="group_discount" value="{{ old('group_discount', '10') }}" 
                                               min="0" max="100" step="0.01">
                                        <small class="form-text text-muted">Discount percentage for group bookings</small>
                                        @error('group_discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


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
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong>
                                            <div class="form-text">Ticket type is available for purchase</div>
                                        </label>
                                    </div>
                                </div>

                                {{-- <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="requires_approval" 
                                               name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_approval">
                                            <strong>Requires Approval</strong>
                                            <div class="form-text">Bookings need manual approval</div>
                                        </label>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Access Permissions -->
                        <!---<div class="card mb-4">
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
                        </div>--->

                        <!-- Preview -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Preview</h5>
                            </div>
                            <div class="card-body">
                                <div id="ticketPreview" class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1" id="previewName">Ticket Type Name</h6>
                                            <small class="text-muted" id="previewEvent">Select an event</small>
                                        </div>
                                        <span class="badge" id="previewCategory" style="display: none;"></span>
                                    </div>
                                    <p class="text-muted small mb-2" id="previewDescription">Add a description...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary" id="previewPrice">$0.00</strong>
                                        <small class="text-muted" id="previewQuantity">0 available</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const events = @json($events); 

    document.getElementById('event_id').addEventListener('change', function() {
        const eventId = this.value;
        const sessionSelect = document.getElementById('session_id');

        // Clear old options
        sessionSelect.innerHTML = '<option value="">Select Session</option>';

        if (eventId) {
            const selectedEvent = events.find(e => e.id == eventId);
            if (selectedEvent && selectedEvent.sessions.length) {
                selectedEvent.sessions.forEach(session => {
                    const opt = document.createElement('option');
                    opt.value = session.id;
                    opt.textContent = session.title;
                    sessionSelect.appendChild(opt);
                });
            }
        }
    });
</script>

<script>
$(document).ready(function() {

  $('#event_id').on('change', function () {
    var eventId = $(this).val();
    if (eventId) {
        $.ajax({
            url: '/admin/events/' + eventId + '/sessions',
            type: 'GET',
            success: function (data) {
                $('#session_id').empty().append('<option value="">Select Session</option>');
                $.each(data, function (key, session) {
                    $('#session_id').append('<option value="'+ session.id +'">'+ session.title +'</option>');
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    } else {
        $('#session_id').empty().append('<option value="">Select Session</option>');
    }
});


    // Initialize toggles based on old input
    $('#groupSettings').toggle($('#is_group').is(':checked'));
    $('#earlyBirdSettings').toggle($('#enable_early_bird').is(':checked'));

    // Group ticket toggle
    $('#is_group').change(function() {
        $('#groupSettings').slideToggle(this.checked);
    });

    // Early bird toggle
    $('#enable_early_bird').change(function() {
        $('#earlyBirdSettings').slideToggle(this.checked);
    });

    // Category-based visibility
    $('#category_id').change(function() {
        const categoryName = $.trim($("#category_id option:selected").text());

        $('#grouptTicket, #earlyBird').hide();

        if (categoryName === 'Early Bird') {
            $('#earlyBird').show();
        } else if (categoryName === 'Group') {
            $('#grouptTicket').show();
        }
    });

    // Access permissions toggle
    const allPermission = $('#perm_all');
    const otherPermissions = $('input[name="access_permissions[]"]:not(#perm_all)');

    allPermission.change(function() {
        if (this.checked) otherPermissions.prop('checked', false);
    });

    otherPermissions.change(function() {
        if (this.checked) allPermission.prop('checked', false);
    });

});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const events = @json($events);
    const eventSelect = document.getElementById('event_id');
    const sessionSelect = document.getElementById('session_id');

    function populateSessions(eventId) {
        sessionSelect.innerHTML = '<option value="">Select Session</option>';
        const selectedEvent = events.find(e => e.id == eventId);

        if (selectedEvent && selectedEvent.sessions.length > 0) {
            selectedEvent.sessions.forEach(session => {
                const opt = document.createElement('option');
                opt.value = session.id;
                opt.textContent = session.title;
                if (session.id == "{{ old('session_id') }}") {
                    opt.selected = true;
                }
                sessionSelect.appendChild(opt);
            });
        }
    }

    // Load sessions when event changes
    eventSelect.addEventListener('change', function () {
        populateSessions(this.value);
    });

    // Restore sessions if old value exists
    if (eventSelect.value) {
        populateSessions(eventSelect.value);
    }
});
</script>




@endsection

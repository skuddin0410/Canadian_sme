@extends('layouts.admin')

@section('title', 'Create Ticket Type')

@section('content')
<div class="container">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle mr-2"></i>Create Ticket Type
        </h1>
        <a href="{{ route('admin.ticket-pricing.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Back to Tickets
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.ticket-pricing.store') }}" method="POST" id="ticketForm">
        @csrf
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle mr-2"></i>Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        Ticket Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g. VIP Pass, General Admission" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_id" class="font-weight-bold">
                                        Event <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('event_id') is-invalid @enderror" 
                                            id="event_id" name="event_id" required>
                                        <option value="">Select Event</option>
                                        @foreach($events ?? [] as $event)
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="session_id" class="font-weight-bold">
                                        Session <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('session_id') is-invalid @enderror" 
                                            id="session_id" name="session_id" required>
                                        <option value="">Select Event First</option>
                                    </select>
                                    @error('session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="font-weight-bold">
                                        Price <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', '0.00') }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of what this ticket includes">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-boxes mr-2"></i>Inventory Management
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity" class="font-weight-bold">
                                        Total Quantity <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity', '100') }}" 
                                           min="1" required>
                                    <small class="form-text text-muted">Total number of tickets available</small>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_per_booking" class="font-weight-bold">Max Per Booking</label>
                                    <input type="number" class="form-control @error('max_per_booking') is-invalid @enderror" 
                                           id="max_per_booking" name="max_per_booking" value="{{ old('max_per_booking', '10') }}" 
                                           min="1">
                                    <small class="form-text text-muted">Maximum tickets per single booking (0 = no limit)</small>
                                    @error('max_per_booking')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_start_date" class="font-weight-bold">Sale Start Date</label>
                                    <input type="datetime-local" class="form-control @error('sale_start_date') is-invalid @enderror" 
                                           id="sale_start_date" name="sale_start_date" value="{{ old('sale_start_date') }}">
                                    <small class="form-text text-muted">When tickets become available for purchase</small>
                                    @error('sale_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_end_date" class="font-weight-bold">Sale End Date</label>
                                    <input type="datetime-local" class="form-control @error('sale_end_date') is-invalid @enderror" 
                                           id="sale_end_date" name="sale_end_date" value="{{ old('sale_end_date') }}">
                                    <small class="form-text text-muted">When ticket sales close</small>
                                    @error('sale_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group Ticket Configuration -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users mr-2"></i>Group Ticket Configuration
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

                <!-- Early Bird Pricing -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock mr-2"></i>Early Bird Pricing
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
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="early_bird_end_date" class="font-weight-bold">Early Bird End Date</label>
                                        <input type="datetime-local" class="form-control @error('early_bird_end_date') is-invalid @enderror" 
                                               id="early_bird_end_date" name="early_bird_end_date" 
                                               value="{{ old('early_bird_end_date') }}">
                                        @error('early_bird_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
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

                <!-- Access Control & Perks -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-key mr-2"></i>Access Control & Perks
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="access_level" class="font-weight-bold">Access Level</label>
                                    <select class="form-control @error('access_level') is-invalid @enderror" 
                                            id="access_level" name="access_level">
                                        <option value="general" {{ old('access_level', 'general') == 'general' ? 'selected' : '' }}>
                                            General Access
                                        </option>
                                        <option value="vip" {{ old('access_level') == 'vip' ? 'selected' : '' }}>
                                            VIP Access
                                        </option>
                                        <option value="premium" {{ old('access_level') == 'premium' ? 'selected' : '' }}>
                                            Premium Access
                                        </option>
                                        <option value="backstage" {{ old('access_level') == 'backstage' ? 'selected' : '' }}>
                                            Backstage Access
                                        </option>
                                    </select>
                                    @error('access_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Included Perks</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="includes_meal" 
                                                       name="perks[]" value="meal" 
                                                       {{ in_array('meal', old('perks', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="includes_meal">Meal Included</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="includes_parking" 
                                                       name="perks[]" value="parking"
                                                       {{ in_array('parking', old('perks', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="includes_parking">Free Parking</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="includes_materials" 
                                                       name="perks[]" value="materials"
                                                       {{ in_array('materials', old('perks', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="includes_materials">Event Materials</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="includes_networking" 
                                                       name="perks[]" value="networking"
                                                       {{ in_array('networking', old('perks', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="includes_networking">Networking Session</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Ticket Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog mr-2"></i>Ticket Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="status_active" name="status" value="active" 
                                       class="custom-control-input" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status_active">
                                    <span class="badge badge-success">Active</span> - Available for purchase
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="status_inactive" name="status" value="inactive" 
                                       class="custom-control-input" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status_inactive">
                                    <span class="badge badge-secondary">Inactive</span> - Not available for purchase
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold">Ticket Options</label>
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="is_transferable" 
                                       name="is_transferable" value="1" {{ old('is_transferable', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_transferable">
                                    Allow Transfer
                                </label>
                            </div>
                            <small class="form-text text-muted mb-3">Allow ticket holders to transfer tickets</small>

                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="is_refundable" 
                                       name="is_refundable" value="1" {{ old('is_refundable') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_refundable">
                                    Allow Refunds
                                </label>
                            </div>
                            <small class="form-text text-muted mb-3">Allow ticket refunds before event</small>

                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="requires_approval" 
                                       name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requires_approval">
                                    Requires Approval
                                </label>
                            </div>
                            <small class="form-text text-muted">Admin approval required for bookings</small>
                        </div>
                    </div>
                </div>

                <!-- Price Preview -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calculator mr-2"></i>Price Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="pricing-preview">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Regular Price:</span>
                                <span class="font-weight-bold" id="regularPricePreview">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="earlyBirdPreview" style="display: none;">
                                <span>Early Bird Price:</span>
                                <span class="font-weight-bold text-success" id="earlyBirdPricePreview">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="groupPreview" style="display: none;">
                                <span>Group Price:</span>
                                <span class="font-weight-bold text-info" id="groupPricePreview">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold">Current Active Price:</span>
                                <span class="font-weight-bold text-primary h5" id="activePricePreview">$0.00</span>
                            </div>
                            <small class="text-muted" id="priceNote">Price customers will see</small>
                        </div>
                    </div>
                </div>

                <!-- Inventory Summary -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie mr-2"></i>Inventory Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="inventory-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Tickets:</span>
                                <span class="font-weight-bold" id="totalTicketsPreview">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="earlyBirdInventoryPreview" style="display: none;">
                                <span>Early Bird Tickets:</span>
                                <span class="font-weight-bold text-success" id="earlyBirdTicketsPreview">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Max Per Booking:</span>
                                <span class="font-weight-bold" id="maxPerBookingPreview">10</span>
                            </div>
                            <hr>
                            <div class="text-center">
                                <div class="h4 text-primary mb-0" id="potentialRevenuePreview">$0.00</div>
                                <small class="text-muted">Potential Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg" id="saveBtn">
                            <i class="fas fa-save mr-3"></i>Create Ticket Type
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-block mt-3" id="saveAndCreateAnother">
                            <i class="fas fa-plus mr-2"></i>Save & Create Another
                        </button>

                        <a href="{{ route('admin.ticket-pricing.index') }}" class="btn btn-secondary btn-block mt-3">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        
                        <hr>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#previewModal">
                                <i class="fas fa-eye mr-1"></i>Preview Ticket
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ticket Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="ticket-preview-card">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0" id="previewTicketName">Ticket Name</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p id="previewDescription">Ticket description will appear here...</p>
                                    <div id="previewPerks"></div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="h3 text-primary" id="previewPrice">$0.00</div>
                                    <div id="previewPriceDetails"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load sessions when event is selected
    $('#event_id').change(function() {
        const eventId = $(this).val();
        const sessionSelect = $('#session_id');
        
        sessionSelect.html('<option value="">Loading sessions...</option>').prop('disabled', true);
        
        if (eventId) {
            $.ajax({
                url: `/admin/events/${eventId}/sessions`,
                method: 'GET',
                success: function(response) {
                    sessionSelect.html('<option value="">Select Session</option>').prop('disabled', false);
                    
                    if (response.sessions && response.sessions.length > 0) {
                        response.sessions.forEach(function(session) {
                            sessionSelect.append(`<option value="${session.id}">${session.title}</option>`);
                        });
                    } else {
                        sessionSelect.append('<option value="" disabled>No sessions available</option>');
                    }
                },
                error: function() {
                    sessionSelect.html('<option value="">Error loading sessions</option>').prop('disabled', false);
                }
            });
        } else {
            sessionSelect.html('<option value="">Select Event First</option>').prop('disabled', false);
        }
    });

    // Group ticket toggle
    $('#is_group').change(function() {
        if ($(this).is(':checked')) {
            $('#groupSettings').slideDown();
        } else {
            $('#groupSettings').slideUp();
        }
        updateAllPreviews();
    });

    // Early bird toggle
    $('#enable_early_bird').change(function() {
        if ($(this).is(':checked')) {
            $('#earlyBirdSettings').slideDown();
        } else {
            $('#earlyBirdSettings').slideUp();
        }
        updateAllPreviews();
    });

    // Update previews when values change
    $('#price, #early_bird_price, #group_discount, #group_size, #quantity, #early_bird_quantity, #max_per_booking, #name, #description').on('input', updateAllPreviews);
    
    // Update previews when checkboxes change
    $('input[name="perks[]"]').change(updateAllPreviews);

    function updateAllPreviews() {
        updatePricePreview();
        updateInventoryPreview();
        updateTicketPreview();
    }

    function updatePricePreview() {
        const regularPrice = parseFloat($('#price').val()) || 0;
        const earlyBirdPrice = parseFloat($('#early_bird_price').val()) || 0;
        const groupDiscount = parseFloat($('#group_discount').val()) || 0;
        const groupSize = parseInt($('#group_size').val()) || 1;

        // Update regular price
        $('#regularPricePreview').text(' + regularPrice.toFixed(2));

        // Update early bird price
        if ($('#enable_early_bird').is(':checked') && earlyBirdPrice > 0) {
            $('#earlyBirdPreview').show();
            $('#earlyBirdPricePreview').text(' + earlyBirdPrice.toFixed(2));
        } else {
            $('#earlyBirdPreview').hide();
        }

        // Update group price
        if ($('#is_group').is(':checked') && groupDiscount > 0) {
            const groupPrice = regularPrice * (1 - groupDiscount / 100);
            $('#groupPreview').show();
            $('#groupPricePreview').text(' + groupPrice.toFixed(2) + ' per person');
        } else {
            $('#groupPreview').hide();
        }

        // Update active price (current applicable price)
        let activePrice = regularPrice;
        let priceNote = 'Regular price';
        
        if ($('#enable_early_bird').is(':checked') && earlyBirdPrice > 0) {
            activePrice = earlyBirdPrice;
            priceNote = 'Early bird price currently active';
        }
        
        $('#activePricePreview').text(' + activePrice.toFixed(2));
        $('#priceNote').text(priceNote);
    }

    function updateInventoryPreview() {
        const totalQuantity = parseInt($('#quantity').val()) || 0;
        const earlyBirdQuantity = parseInt($('#early_bird_quantity').val()) || 0;
        const maxPerBooking = parseInt($('#max_per_booking').val()) || 10;
        const regularPrice = parseFloat($('#price').val()) || 0;

        $('#totalTicketsPreview').text(totalQuantity.toLocaleString());
        $('#maxPerBookingPreview').text(maxPerBooking === 0 ? 'No limit' : maxPerBooking);

        // Early bird inventory
        if ($('#enable_early_bird').is(':checked') && earlyBirdQuantity > 0) {
            $('#earlyBirdInventoryPreview').show();
            $('#earlyBirdTicketsPreview').text(earlyBirdQuantity.toLocaleString());
        } else {
            $('#earlyBirdInventoryPreview').hide();
        }

        // Calculate potential revenue
        const potentialRevenue = totalQuantity * regularPrice;
        $('#potentialRevenuePreview').text(' + potentialRevenue.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }

        ));
    }

    function updateTicketPreview() {
        const ticketName = $('#name').val() || 'Ticket Name';
        const description = $('#description').val() || 'Ticket description will appear here...';
        const regularPrice = parseFloat($('#price').val()) || 0;
        const earlyBirdPrice = parseFloat($('#early_bird_price').val()) || 0;

        $('#previewTicketName').text(ticketName);
        $('#previewDescription').text(description);

        // Update price display
        let priceDisplay = ' + regularPrice.toFixed(2);
        let priceDetails = '';

        if ($('#enable_early_bird').is(':checked') && earlyBirdPrice > 0) {
            priceDisplay = ' + earlyBirdPrice.toFixed(2);
            priceDetails = `<small class="text-muted">Early Bird Price<br>Regular: ${regularPrice.toFixed(2)}</small>`;
        }

        $('#previewPrice').text(priceDisplay);
        $('#previewPriceDetails').html(priceDetails);

        // Update perks
        const selectedPerks = $('input[name="perks[]"]:checked').map(function() {
            return $(this).next('label').text();
        }).get();

        let perksHtml = '';
        if (selectedPerks.length > 0) {
            perksHtml = '<div class="mt-3"><strong>Includes:</strong><ul class="mb-0">';
            selectedPerks.forEach(perk => {
                perksHtml += `<li>${perk}</li>`;
            });
            perksHtml += '</ul></div>';
        }

        $('#previewPerks').html(perksHtml);
    }

    // Form validation
    $('#ticketForm').submit(function(e) {
        let isValid = true;
        let errors = [];

        // Validate sale dates
        const saleStart = $('#sale_start_date').val();
        const saleEnd = $('#sale_end_date').val();
        
        if (saleStart && saleEnd && new Date(saleStart) >= new Date(saleEnd)) {
            errors.push('Sale end date must be after sale start date');
            isValid = false;
        }

        // Validate early bird settings
        if ($('#enable_early_bird').is(':checked')) {
            const earlyBirdEnd = $('#early_bird_end_date').val();
            const earlyBirdPrice = parseFloat($('#early_bird_price').val());
            const regularPrice = parseFloat($('#price').val());
            const earlyBirdQuantity = parseInt($('#early_bird_quantity').val());
            const totalQuantity = parseInt($('#quantity').val());

            if (!earlyBirdEnd) {
                errors.push('Early bird end date is required when early bird pricing is enabled');
                isValid = false;
            }

            if (earlyBirdPrice <= 0) {
                errors.push('Early bird price must be greater than 0');
                isValid = false;
            }

            if (earlyBirdPrice >= regularPrice) {
                errors.push('Early bird price must be less than regular price');
                isValid = false;
            }

            if (earlyBirdQuantity > totalQuantity) {
                errors.push('Early bird quantity cannot exceed total quantity');
                isValid = false;
            }

            if (saleStart && earlyBirdEnd && new Date(earlyBirdEnd) > new Date(saleStart)) {
                errors.push('Early bird period must end before or when regular sales start');
                isValid = false;
            }
        }

        // Validate group settings
        if ($('#is_group').is(':checked')) {
            const groupSize = parseInt($('#group_size').val());
            const groupDiscount = parseFloat($('#group_discount').val());
            
            if (groupSize < 2) {
                errors.push('Group size must be at least 2');
                isValid = false;
            }

            if (groupDiscount < 0 || groupDiscount > 100) {
                errors.push('Group discount must be between 0 and 100 percent');
                isValid = false;
            }
        }

        // Validate max per booking
        const maxPerBooking = parseInt($('#max_per_booking').val());
        const totalQuantity = parseInt($('#quantity').val());
        
        if (maxPerBooking > 0 && maxPerBooking > totalQuantity) {
            errors.push('Max per booking cannot exceed total quantity');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
        }
    });

    // Save and create another
    $('#saveAndCreateAnother').click(function() {
        $('<input>').attr({
            type: 'hidden',
            name: 'create_another',
            value: '1'
        }).appendTo('#ticketForm');
        
        $('#ticketForm').submit();
    });

    // Auto-set early bird end date when sale start is set
    $('#sale_start_date').change(function() {
        const saleStart = $(this).val();
        const earlyBirdEnd = $('#early_bird_end_date');
        
        if (saleStart && !earlyBirdEnd.val()) {
            // Set early bird end to 7 days before sale start
            const saleDate = new Date(saleStart);
            saleDate.setDate(saleDate.getDate() - 7);
            
            const year = saleDate.getFullYear();
            const month = String(saleDate.getMonth() + 1).padStart(2, '0');
            const day = String(saleDate.getDate()).padStart(2, '0');
            const hours = String(saleDate.getHours()).padStart(2, '0');
            const minutes = String(saleDate.getMinutes()).padStart(2, '0');
            
            earlyBirdEnd.val(`${year}-${month}-${day}T${hours}:${minutes}`);
        }
    });

    // Auto-calculate early bird price based on discount
    $('#price').on('input', function() {
        const regularPrice = parseFloat($(this).val()) || 0;
        const earlyBirdPrice = $('#early_bird_price');
        
        if (!earlyBirdPrice.val() && regularPrice > 0) {
            // Set early bird price to 20% discount by default
            const discountedPrice = regularPrice * 0.8;
            earlyBirdPrice.val(discountedPrice.toFixed(2));
        }
    });

    // Smart quantity suggestions based on event type
    $('#event_id').change(function() {
        const eventSelect = $(this);
        const selectedOption = eventSelect.find('option:selected');
        const eventTitle = selectedOption.text().toLowerCase();
        
        // Auto-suggest quantities based on event type keywords
        if (eventTitle.includes('workshop') || eventTitle.includes('training')) {
            $('#quantity').val(30);
        } else if (eventTitle.includes('conference') || eventTitle.includes('summit')) {
            $('#quantity').val(500);
        } else if (eventTitle.includes('networking') || eventTitle.includes('meetup')) {
            $('#quantity').val(100);
        }
        
        updateAllPreviews();
    });

    // Initialize previews
    updateAllPreviews();

    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();

    // Auto-save draft functionality (optional)
    let autoSaveTimer;
    $('#ticketForm input, #ticketForm select, #ticketForm textarea').on('input change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // You could implement auto-save draft functionality here
            console.log('Auto-saving draft...');
        }, 5000);
    });
});
</script>
@endpush
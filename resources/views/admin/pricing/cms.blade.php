@extends('layouts.admin')

@section('title')
Admin | Pricing CMS
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Pricing /</span> CMS</h4>
    
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main CMS Section -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pricing Page Content</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pricing.cms.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="main_heading" class="form-label">Main Heading</label>
                                <input type="text" class="form-control" id="main_heading" name="main_heading" value="{{ old('main_heading', $cms->main_heading ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="Feature_heading" class="form-label">Feature Table Heading</label>
                                <input type="text" class="form-control" id="Feature_heading" name="Feature_heading" value="{{ old('Feature_heading', $cms->Feature_heading ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="main_description" class="form-label">Main Description</label>
                                <textarea class="form-control" id="main_description" name="main_description" rows="2">{{ old('main_description', $cms->main_description ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="Feature_description" class="form-label">Feature Table Description</label>
                                <textarea class="form-control" id="Feature_description" name="Feature_description" rows="2">{{ old('Feature_description', $cms->Feature_description ?? '') }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Update Content</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Features Comparison Section -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Feature Comparison</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                        <i class="bx bx-plus me-1"></i> Add Feature
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Feature Name</th>
                                    @foreach($pricings as $plan)
                                        <th class="text-center">{{ $plan->name }}</th>
                                    @endforeach
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($features as $feature)
                                    <tr>
                                        <td>{{ $feature->order_by }}</td>
                                        <td><strong>{{ $feature->name }}</strong></td>
                                        @foreach($pricings as $plan)
                                            <td class="text-center">
                                                @php $val = $feature->getValueForPlan($plan->id); @endphp
                                                @if($val == 1)
                                                    <span class="badge bg-label-success">Yes</span>
                                                @else
                                                    <span class="badge bg-label-secondary">No</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            @if($feature->status)
                                                <span class="badge bg-label-primary">Active</span>
                                            @else
                                                <span class="badge bg-label-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-feature-btn" href="javascript:void(0);" 
                                                       data-id="{{ $feature->id }}" 
                                                       data-name="{{ $feature->name }}" 
                                                       data-order="{{ $feature->order_by }}" 
                                                       data-status="{{ $feature->status }}"
                                                       data-values='@json($feature->values->pluck("value", "pricing_id"))'>
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item delete-feature-btn" href="javascript:void(0);" data-url="{{ route('admin.pricing.features.destroy', $feature->id) }}">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($pricings) + 4 }}" class="text-center">No features added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Feature Modal -->
<div class="modal fade" id="addFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.pricing.features.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label for="name" class="form-label">Feature Title</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. 24/7 Support" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="order_by" class="form-label">Order</label>
                            <input type="number" id="order_by" name="order_by" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <h6 class="mt-3 border-bottom pb-2">Plan Availability</h6>
                    <div class="row">
                        @foreach($pricings as $plan)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ $plan->name }}</label>
                                <select name="values[{{ $plan->id }}]" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Feature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Feature Modal -->
<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editFeatureForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="edit_name" class="form-label">Feature Title</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_order_by" class="form-label">Order</label>
                            <input type="number" id="edit_order_by" name="order_by" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="edit_status" name="status" value="1">
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mt-3 border-bottom pb-2">Plan Availability</h6>
                    <div class="row">
                        @foreach($pricings as $plan)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ $plan->name }}</label>
                                <select name="values[{{ $plan->id }}]" id="plan_val_{{ $plan->id }}" class="form-select plan-val-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Feature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Edit Feature Modal Populate
    $('.edit-feature-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const order = $(this).data('order');
        const status = $(this).data('status');
        const values = $(this).data('values'); // Object {pricing_id: value, ...}

        $('#editFeatureForm').attr('action', `/admin/pricing/features/${id}`);
        $('#edit_name').val(name);
        $('#edit_order_by').val(order);
        $('#edit_status').prop('checked', status == 1);

        // Reset all plan selects to NO first
        $('.plan-val-select').val(0);

        // Set recorded values
        if(values) {
            Object.keys(values).forEach(pricingId => {
                $(`#plan_val_${pricingId}`).val(values[pricingId]);
            });
        }

        $('#editFeatureModal').modal('show');
    });

    // Delete Confirmation with SweetAlert2
    $('.delete-feature-btn').on('click', function() {
        const url = $(this).data('url');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This feature will be permanently removed!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#82868b',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        });
    });
});
</script>
@endsection

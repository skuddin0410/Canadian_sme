@extends('layouts.admin')
@section('title', 'Ticket Inventory Management')
@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Ticket Inventory Management</h1>
                <div>
                    <a href="{{ route('admin.ticket-inventory.logs') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-history"></i> View Logs
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                        <i class="fas fa-edit"></i> Bulk Update
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white">Total Tickets</h6>
                                    <h3 class="mb-0 text-white">{{ number_format($totalTickets) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-ticket-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white">Available</h6>
                                    <h3 class="mb-0 text-white">{{ number_format($availableTickets) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white">Sold</h6>
                                    <h3 class="mb-0 text-white">{{ number_format($soldTickets) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-white">Low Stock</h6>
                                    <h3 class="mb-0 text-white">{{ $lowStockCount }}  <small>+ {{ $soldOutCount }} sold out</small></h3>
                                   
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="event_id" class="form-label">Event</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">All Events</option>
                                <!-- Add event options here -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.ticket-inventory.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Ticket Type</th>
                                    <th>Event</th>
                                    <th>Category</th>
                                    <th>Total Quantity</th>
                                    <th>Available</th>
                                    <th>Sold</th>
                                    <th>Stock Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketTypes as $ticketType)
                                    @php
                                        $soldQuantity = $ticketType->total_quantity - $ticketType->available_quantity;
                                        $stockPercentage = $ticketType->total_quantity > 0 ? ($ticketType->available_quantity / $ticketType->total_quantity) * 100 : 0;
                                        $stockStatus = $stockPercentage == 0 ? 'sold-out' : ($stockPercentage <= 10 ? 'low-stock' : 'available');
                                    @endphp
                                    <tr class="inventory-row" data-ticket-type-id="{{ $ticketType->id }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input ticket-checkbox" value="{{ $ticketType->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $ticketType->name }}</strong>
                                            <small class="text-muted d-block">${{ number_format($ticketType->base_price, 2) }}</small>
                                        </td>
                                        <td>{{ $ticketType->event->name }}</td>
                                        <td>
                                            @if($ticketType->category)
                                                <span class="badge" style="background-color: {{ $ticketType->category->color }}">
                                                    {{ $ticketType->category->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($ticketType->total_quantity) }}</td>
                                        <td>
                                            <span class="fw-bold text-{{ $stockPercentage > 20 ? 'success' : ($stockPercentage > 0 ? 'warning' : 'danger') }}">
                                                {{ number_format($ticketType->available_quantity) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($soldQuantity) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $stockPercentage > 50 ? 'success' : ($stockPercentage > 20 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $stockPercentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ round($stockPercentage) }}%</small>
                                            </div>
                                            @if($stockStatus == 'sold-out')
                                                <span class="badge badge-danger">Sold Out</span>
                                            @elseif($stockStatus == 'low-stock')
                                                <span class="badge badge-warning">Low Stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="showInventoryModal({{ $ticketType->id }}, '{{ $ticketType->name }}', {{ $ticketType->available_quantity }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('admin.ticket-types.show', $ticketType) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No ticket types found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $ticketTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single Inventory Update Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="inventoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ticket Type</label>
                        <input type="text" class="form-control" id="ticketTypeName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Available Quantity</label>
                        <input type="text" class="form-control" id="currentQuantity" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="action" class="form-label">Action</label>
                        <select class="form-select" id="action" name="action" required>
                            <option value="">Select Action</option>
                            <option value="increase">Increase Quantity</option>
                            <option value="decrease">Decrease Quantity</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Inventory Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ticket-inventory.bulk-update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulkReason" class="form-label">Reason for Update</label>
                        <textarea class="form-control" id="bulkReason" name="reason" rows="2"></textarea>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket Type</th>
                                    <th>Current</th>
                                    <th>Action</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="bulkUpdateTable">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedTicketTypes = [];

function showInventoryModal(id, name, currentQuantity) {
    document.getElementById('ticketTypeName').value = name;
    document.getElementById('currentQuantity').value = currentQuantity;
    document.getElementById('inventoryForm').action = `/admin/ticket-types/${id}/update-inventory`;
    
    const modal = new bootstrap.Modal(document.getElementById('inventoryModal'));
    modal.show();
}

// Handle select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedTicketTypes();
});

// Handle individual checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('ticket-checkbox')) {
        updateSelectedTicketTypes();
    }
});

function updateSelectedTicketTypes() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox:checked');
    selectedTicketTypes = Array.from(checkboxes).map(checkbox => {
        const row = checkbox.closest('.inventory-row');
        return {
            id: checkbox.value,
            name: row.querySelector('strong').textContent,
            currentQuantity: row.querySelector('td:nth-child(6) span').textContent.replace(/,/g, '')
        };
    });
    
    // Update select all checkbox state
    const selectAllCheckbox = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.ticket-checkbox');
    selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedTicketTypes.length === allCheckboxes.length;
    selectAllCheckbox.indeterminate = selectedTicketTypes.length > 0 && selectedTicketTypes.length < allCheckboxes.length;
}

// Show bulk update modal
document.querySelector('[data-bs-target="#bulkUpdateModal"]').addEventListener('click', function() {
    if (selectedTicketTypes.length === 0) {
        alert('Please select ticket types to update.');
        return;
    }
    
    const tableBody = document.getElementById('bulkUpdateTable');
    tableBody.innerHTML = '';
    
    selectedTicketTypes.forEach((ticketType, index) => {
        const row = `
            <tr>
                <td>
                    ${ticketType.name}
                    <input type="hidden" name="updates[${index}][ticket_type_id]" value="${ticketType.id}">
                </td>
                <td>${ticketType.currentQuantity}</td>
                <td>
                    <select class="form-select form-select-sm" name="updates[${index}][action]" required>
                        <option value="">Select</option>
                        <option value="set">Set to</option>
                        <option value="increase">Increase by</option>
                        <option value="decrease">Decrease by</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           name="updates[${index}][quantity]" min="0" required>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
});
</script>
@endpush 
@push('scripts')

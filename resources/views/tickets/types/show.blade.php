@extends('layouts.admin')

@section('title', $ticketType->name . ' - Ticket Type Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-3">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <h1 class="h3 mb-0 me-3">{{ $ticketType->name }}</h1>
                        @if($ticketType->category)
                            <span class="badge me-2" style="background-color: {{ $ticketType->category->color }}">
                                {{ $ticketType->category->name }}
                            </span>
                        @endif
                        <span class="badge badge-{{ $ticketType->is_active ? 'success' : 'secondary' }}">
                            {{ $ticketType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($ticketType->requires_approval)
                            <span class="badge badge-warning ms-1">Requires Approval</span>
                        @endif
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.ticket-types.index') }}">Ticket Types</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $ticketType->event->name }}</a></li>
                            <li class="breadcrumb-item active">{{ $ticketType->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.ticket-types.edit', $ticketType) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                            data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.ticket-pricing.create', ['ticket_type_id' => $ticketType->id]) }}">
                            <i class="fas fa-percentage"></i> Add Pricing Rule
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="duplicateTicketType()">
                            <i class="fas fa-copy"></i> Duplicate
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.ticket-types.index') }}">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a></li>
                    </ul>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($ticketType->available_quantity == 0)
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Sold Out:</strong> This ticket type has no available inventory.
                </div>
            @endif

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Overview Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2 class="mb-1 text-white">${{ number_format($ticketType->base_price, 2) }}</h2>
                                    <small>Base Price</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2 class="mb-1 text-white">{{ number_format($ticketType->total_quantity) }}</h2>
                                    <small>Total Quantity</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2 class="mb-1 text-white">{{ number_format($ticketType->available_quantity) }}</h2>
                                    <small>Available</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    @php
                                        $soldQuantity = $ticketType->total_quantity - $ticketType->available_quantity;
                                    @endphp
                                    <h2 class="mb-1 text-white">{{ number_format($soldQuantity) }}</h2>
                                    <small>Sold</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Performance Chart -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Sales Performance</h5>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary active" data-period="7">7 Days</button>
                                <button type="button" class="btn btn-outline-primary" data-period="30">30 Days</button>
                                <button type="button" class="btn btn-outline-primary" data-period="90">90 Days</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="300"></canvas>
                        </div>
                    </div>

                    <!-- Pricing Rules -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pricing Rules</h5>
                            <a href="{{ route('admin.ticket-pricing.create', ['ticket_type_id' => $ticketType->id]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Rule
                            </a>
                        </div>
                        <div class="card-body">
                            @if($ticketType->pricingRules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Price</th>
                                                <th>Valid Period</th>
                                                <th>Usage</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ticketType->pricingRules as $rule)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $rule->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ ucwords(str_replace('_', ' ', $rule->type)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        ${{ number_format($rule->price, 2) }}
                                                        @if($rule->price < $ticketType->base_price)
                                                            <small class="text-success">
                                                                (-${{ number_format($ticketType->base_price - $rule->price, 2) }})
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($rule->start_date && $rule->end_date)
                                                            <small>
                                                                {{ $rule->start_date->format('M j') }} - 
                                                                {{ $rule->end_date->format('M j, Y') }}
                                                            </small>
                                                        @elseif($rule->start_date)
                                                            <small>From {{ $rule->start_date->format('M j, Y') }}</small>
                                                        @elseif($rule->end_date)
                                                            <small>Until {{ $rule->end_date->format('M j, Y') }}</small>
                                                        @else
                                                            <small class="text-muted">Always</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($rule->usage_limit)
                                                            {{ $rule->usage_count }}/{{ $rule->usage_limit }}
                                                            @php
                                                                $usagePercent = ($rule->usage_count / $rule->usage_limit) * 100;
                                                            @endphp
                                                            <div class="progress mt-1" style="height: 4px;">
                                                                <div class="progress-bar" style="width: {{ $usagePercent }}%"></div>
                                                            </div>
                                                        @else
                                                            {{ $rule->usage_count }} uses
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $rule->is_active ? 'success' : 'secondary' }}">
                                                            {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.ticket-pricing.edit', $rule) }}" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('admin.ticket-pricing.toggle', $rule) }}" 
                                                                  method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-warning">
                                                                    <i class="fas fa-{{ $rule->is_active ? 'pause' : 'play' }}"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-percentage fa-3x mb-3 opacity-25"></i>
                                    <p>No pricing rules configured.</p>
                                    <a href="{{ route('admin.ticket-pricing.create', ['ticket_type_id' => $ticketType->id]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Create First Pricing Rule
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Inventory Changes -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Inventory Changes</h5>
                            <a href="{{ route('admin.ticket-inventory.logs', ['ticket_type_id' => $ticketType->id]) }}" 
                               class="btn btn-sm btn-outline-info">
                                View All Logs
                            </a>
                        </div>
                        <div class="card-body">
                            @if($ticketType->inventoryLogs->count() > 0)
                                <div class="timeline">
                                    @foreach($ticketType->inventoryLogs->take(5) as $log)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-{{ $log->action == 'increase' ? 'success' : ($log->action == 'decrease' ? 'danger' : 'info') }}"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">
                                                    {{ ucfirst($log->action) }} by {{ $log->quantity }}
                                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                                </h6>
                                                <p class="mb-1">
                                                    {{ $log->previous_quantity }} → {{ $log->new_quantity }}
                                                    @if($log->reason)
                                                        <br><small class="text-muted">{{ $log->reason }}</small>
                                                    @endif
                                                </p>
                                                @if($log->user)
                                                    <small class="text-muted">by {{ $log->user->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                    <p>No inventory changes recorded.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $sellThroughRate = $ticketType->total_quantity > 0 ? (($soldQuantity / $ticketType->total_quantity) * 100) : 0;
                                $stockLevel = $ticketType->total_quantity > 0 ? (($ticketType->available_quantity / $ticketType->total_quantity) * 100) : 0;
                            @endphp
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Sell-through Rate</small>
                                    <small>{{ round($sellThroughRate) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $sellThroughRate }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Stock Level</small>
                                    <small>{{ round($stockLevel) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $stockLevel > 50 ? 'success' : ($stockLevel > 20 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $stockLevel }}%"></div>
                                </div>
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary mb-0">{{ $ticketType->min_quantity_per_order }}</h4>
                                    <small class="text-muted">Min Order</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-primary mb-0">{{ $ticketType->max_quantity_per_order ?? '∞' }}</h4>
                                    <small class="text-muted">Max Order</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ticket Details</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-5">Event:</dt>
                                <dd class="col-sm-7">
                                    <a href="#">{{ $ticketType->event->name }}</a>
                                </dd>

                                <dt class="col-sm-5">SKU:</dt>
                                <dd class="col-sm-7">
                                    <code>{{ $ticketType->slug }}</code>
                                </dd>

                                @if($ticketType->description)
                                    <dt class="col-sm-5">Description:</dt>
                                    <dd class="col-sm-7">{{ $ticketType->description }}</dd>
                                @endif

                                <dt class="col-sm-5">Created:</dt>
                                <dd class="col-sm-7">
                                    {{ $ticketType->created_at->format('M j, Y g:i A') }}
                                </dd>

                                <dt class="col-sm-5">Last Updated:</dt>
                                <dd class="col-sm-7">
                                    {{ $ticketType->updated_at->format('M j, Y g:i A') }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Sale Period -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sale Period</h5>
                        </div>
                        <div class="card-body">
                            @if($ticketType->sale_start_date || $ticketType->sale_end_date)
                                <dl class="row">
                                    @if($ticketType->sale_start_date)
                                        <dt class="col-sm-4">Start:</dt>
                                        <dd class="col-sm-8">
                                            {{ $ticketType->sale_start_date->format('M j, Y g:i A') }}
                                            @if($ticketType->sale_start_date->isFuture())
                                                <small class="text-warning">(Future)</small>
                                            @elseif($ticketType->sale_start_date->isPast())
                                                <small class="text-success">(Active)</small>
                                            @endif
                                        </dd>
                                    @endif

                                    @if($ticketType->sale_end_date)
                                        <dt class="col-sm-4">End:</dt>
                                        <dd class="col-sm-8">
                                            {{ $ticketType->sale_end_date->format('M j, Y g:i A') }}
                                            @if($ticketType->sale_end_date->isFuture())
                                                <small class="text-success">({{ $ticketType->sale_end_date->diffForHumans() }})</small>
                                            @elseif($ticketType->sale_end_date->isPast())
                                                <small class="text-danger">(Expired)</small>
                                            @endif
                                        </dd>
                                    @endif
                                </dl>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-infinity me-2"></i>
                                    Always available for sale
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Access Permissions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Access Permissions</h5>
                        </div>
                        <div class="card-body">
                            @if($ticketType->access_permissions && count($ticketType->access_permissions) > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($ticketType->access_permissions as $permission)
                                        <div class="list-group-item px-0 py-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ ucwords(str_replace('_', ' ', $permission)) }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-globe me-2"></i>
                                    Everyone (Public)
                                </p>
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
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inventoryModal">
                                    <i class="fas fa-boxes"></i> Update Inventory
                                </button>
                                <a href="{{ route('admin.ticket-pricing.create', ['ticket_type_id' => $ticketType->id]) }}" 
                                   class="btn btn-outline-success">
                                    <i class="fas fa-percentage"></i> Add Pricing Rule
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="exportData()">
                                    <i class="fas fa-download"></i> Export Data
                                </button>
                                @if($ticketType->is_active)
                                    <form action="{{ route('admin.ticket-types.update', $ticketType) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Deactivate this ticket type?')">
                                            <i class="fas fa-pause"></i> Deactivate
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.ticket-types.update', $ticketType) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-play"></i> Activate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Update Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ticket-types.update-inventory', $ticketType) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Available Quantity</label>
                        <input type="text" class="form-control" value="{{ $ticketType->available_quantity }}" readonly>
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
                        <div class="form-text" id="quantityHelp"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" 
                                  placeholder="Optional reason for this change..."></textarea>
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
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -16px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -20px;
    top: 4px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sales chart
    initSalesChart();
    
    // Inventory modal logic
    const actionSelect = document.querySelector('#inventoryModal select[name="action"]');
    const quantityInput = document.querySelector('#inventoryModal input[name="quantity"]');
    const quantityHelp = document.getElementById('quantityHelp');
    const currentAvailable = {{ $ticketType->available_quantity }};
    
    actionSelect.addEventListener('change', function() {
        if (this.value === 'decrease') {
            quantityInput.max = currentAvailable;
            quantityHelp.textContent = `Maximum decrease: ${currentAvailable}`;
            quantityHelp.className = 'form-text text-warning';
        } else if (this.value === 'increase') {
            quantityInput.removeAttribute('max');
            quantityHelp.textContent = 'Enter the quantity to add';
            quantityHelp.className = 'form-text';
        } else {
            quantityInput.removeAttribute('max');
            quantityHelp.textContent = '';
        }
    });
});

let salesChart;

function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Daily Sales',
                data: [],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }, {
                label: 'Cumulative Sales',
                data: [],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
    
    // Load initial data
    loadSalesData(7);
    
    // Handle period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            loadSalesData(parseInt(this.dataset.period));
        });
    });
}

function loadSalesData(days) {
    // Mock data for demonstration - replace with actual API call
    const labels = [];
    const dailySales = [];
    const cumulativeSales = [];
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        
        // Mock data - replace with real data
        const sales = Math.floor(Math.random() * 10);
        dailySales.push(sales);
        
        const cumulative = i === days - 1 ? sales : (cumulativeSales[cumulativeSales.length - 1] || 0) + sales;
        cumulativeSales.push(cumulative);
    }
    
    salesChart.data.labels = labels;
    salesChart.data.datasets[0].data = dailySales;
    salesChart.data.datasets[1].data = cumulativeSales;
    salesChart.update();
}

function duplicateTicketType() {
    if (confirm('Create a copy of this ticket type?')) {
        window.location.href = '{{ route("admin.ticket-types.create") }}?duplicate_from={{ $ticketType->id }}';
    }
}

function exportData() {
    // Create export options modal or direct download
    const exportOptions = [
        { label: 'Sales Report (CSV)', url: `/admin/ticket-types/${{{ $ticketType->id }}}/export/sales` },
        { label: 'Inventory Log (CSV)', url: `/admin/ticket-types/${{{ $ticketType->id }}}/export/inventory` },
        { label: 'Pricing Rules (CSV)', url: `/admin/ticket-types/${{{ $ticketType->id }}}/export/pricing` }
    ];
    
    // Simple implementation - you can make this more sophisticated
    const choice = prompt('Export options:\n1. Sales Report\n2. Inventory Log\n3. Pricing Rules\n\nEnter your choice (1-3):');
    
    if (choice && choice >= 1 && choice <= 3) {
        window.open(exportOptions[choice - 1].url, '_blank');
    }
}

// Real-time updates (optional - using WebSocket or polling)
function startRealTimeUpdates() {
    setInterval(function() {
        fetch(`/api/admin/ticket-types/{{ $ticketType->id }}/stats`)
            .then(response => response.json())
            .then(data => {
                // Update stats cards
                document.querySelector('.bg-success .card-body h2').textContent = data.available_quantity.toLocaleString();
                document.querySelector('.bg-warning .card-body h2').textContent = data.sold_quantity.toLocaleString();
                
                // Update progress bars
                const sellThroughRate = (data.sold_quantity / data.total_quantity) * 100;
                const stockLevel = (data.available_quantity / data.total_quantity) * 100;
                
                document.querySelector('.progress .progress-bar').style.width = sellThroughRate + '%';
                document.querySelector('.progress .progress-bar').previousElementSibling.textContent = Math.round(sellThroughRate) + '%';
            })
            .catch(error => console.error('Error fetching real-time data:', error));
    }, 30000); // Update every 30 seconds
}

// Initialize real-time updates if needed
// startRealTimeUpdates();

// Auto-refresh page data every 5 minutes
setInterval(function() {
    // Only refresh if user is active on the page
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000);

// Print functionality
function printTicketDetails() {
    window.print();
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + E for edit
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        window.location.href = '{{ route("admin.ticket-types.edit", $ticketType) }}';
    }
    
    // Ctrl/Cmd + D for duplicate
    if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        duplicateTicketType();
    }
    
    // Ctrl/Cmd + I for inventory
    if ((e.ctrlKey || e.metaKey) && e.key === 'i') {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('inventoryModal'));
        modal.show();
    }
});

// Tooltip initialization for better UX
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Copy to clipboard functionality
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success toast or notification
            showToast('Copied to clipboard!', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Copied to clipboard!', 'success');
    }
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to toast container or create one
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Enhanced table interactions
document.querySelectorAll('table tbody tr').forEach(row => {
    row.addEventListener('click', function(e) {
        // Don't trigger on button clicks
        if (!e.target.closest('button') && !e.target.closest('a')) {
            // Add row selection or detail view
            this.classList.toggle('table-active');
        }
    });
});

// Advanced search and filter (if implementing)
function initAdvancedFilters() {
    // Date range picker for filtering logs
    // Status filters for pricing rules
    // Search functionality
}

// Performance monitoring
let pageLoadTime = performance.now();
window.addEventListener('load', function() {
    console.log('Page loaded in', Math.round(performance.now() - pageLoadTime), 'ms');
});

// Error handling for AJAX requests
function handleAjaxError(xhr, status, error) {
    console.error('AJAX Error:', status, error);
    showToast('An error occurred. Please try again.', 'danger');
}

// Confirmation dialogs with better UX
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Initialize all interactive elements
document.querySelectorAll('[data-action="copy"]').forEach(element => {
    element.addEventListener('click', function() {
        copyToClipboard(this.dataset.text);
    });
});

// Add click handlers for SKU copying
document.querySelectorAll('code').forEach(code => {
    code.style.cursor = 'pointer';
    code.title = 'Click to copy';
    code.addEventListener('click', function() {
        copyToClipboard(this.textContent);
    });
});
</script>
@endpush
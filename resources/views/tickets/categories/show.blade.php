@extends('layouts.admin')

@section('title', $ticketCategory->name . ' - Category Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-3">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="color-indicator me-3" 
                             style="background-color: {{ $ticketCategory->color }}; width: 30px; height: 30px; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                        <h1 class="h3 mb-0 me-3">{{ $ticketCategory->name }}</h1>
                        <span class="badge badge-{{ $ticketCategory->is_active ? 'success' : 'secondary' }}">
                            {{ $ticketCategory->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.ticket-categories.index') }}">Ticket Categories</a></li>
                            <li class="breadcrumb-item active">{{ $ticketCategory->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.ticket-categories.edit', $ticketCategory) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Category
                    </a>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                            data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="duplicateCategory()">
                            <i class="fas fa-copy"></i> Duplicate Category
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.ticket-types.create', ['category_id' => $ticketCategory->id]) }}">
                            <i class="fas fa-plus"></i> Create Ticket Type
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.ticket-categories.index') }}">
                            <i class="fas fa-arrow-left"></i> Back to Categories
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

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Overview Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center" style="border-left: 4px solid {{ $ticketCategory->color }};">
                                <div class="card-body">
                                    <h2 class="text-primary mb-1">{{ $ticketCategory->ticketTypes->count() }}</h2>
                                    <small class="text-muted">Ticket Types</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center" style="border-left: 4px solid {{ $ticketCategory->color }};">
                                <div class="card-body">
                                    @php
                                        $totalTickets = $ticketCategory->ticketTypes->sum('total_quantity');
                                    @endphp
                                    <h2 class="text-info mb-1">{{ number_format($totalTickets) }}</h2>
                                    <small class="text-muted">Total Tickets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center" style="border-left: 4px solid {{ $ticketCategory->color }};">
                                <div class="card-body">
                                    @php
                                        $avgPrice = $ticketCategory->ticketTypes->avg('base_price');
                                    @endphp
                                    <h2 class="text-success mb-1">${{ number_format($avgPrice, 2) }}</h2>
                                    <small class="text-muted">Avg Price</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Types in this Category -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Ticket Types in this Category</h5>
                            <a href="{{ route('admin.ticket-types.create', ['category_id' => $ticketCategory->id]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Add Ticket Type
                            </a>
                        </div>
                        <div class="card-body">
                            @if($ticketCategory->ticketTypes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Event</th>
                                                <th>Price</th>
                                                <th>Inventory</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ticketCategory->ticketTypes as $ticketType)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="color-dot me-2" 
                                                                 style="background-color: {{ $ticketCategory->color }}; width: 8px; height: 8px; border-radius: 50%;"></div>
                                                            <div>
                                                                <strong>{{ $ticketType->name }}</strong>
                                                                @if($ticketType->description)
                                                                    <small class="text-muted d-block">{{ Str::limit($ticketType->description, 50) }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="text-decoration-none">{{ $ticketType->event->name }}</a>
                                                    </td>
                                                    <td>${{ number_format($ticketType->base_price, 2) }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2">{{ $ticketType->available_quantity }}/{{ $ticketType->total_quantity }}</span>
                                                            @php
                                                                $percentage = $ticketType->total_quantity > 0 ? ($ticketType->available_quantity / $ticketType->total_quantity) * 100 : 0;
                                                                $colorClass = $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger');
                                                            @endphp
                                                            <div class="progress" style="width: 60px; height: 6px;">
                                                                <div class="progress-bar bg-{{ $colorClass }}" 
                                                                     style="width: {{ $percentage }}%"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $ticketType->is_active ? 'success' : 'secondary' }}">
                                                            {{ $ticketType->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        @if($ticketType->available_quantity == 0)
                                                            <span class="badge badge-danger">Sold Out</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.ticket-types.show', $ticketType) }}" 
                                                               class="btn btn-outline-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.ticket-types.edit', $ticketType) }}" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-ticket-alt fa-3x mb-3 opacity-25"></i>
                                    <h5>No Ticket Types</h5>
                                    <p>This category doesn't have any ticket types yet.</p>
                                    <a href="{{ route('admin.ticket-types.create', ['category_id' => $ticketCategory->id]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Create First Ticket Type
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Category Performance Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Category Performance Over Time</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Category Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Category Details</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-5">Name:</dt>
                                <dd class="col-sm-7">{{ $ticketCategory->name }}</dd>

                                <dt class="col-sm-5">Slug:</dt>
                                <dd class="col-sm-7">
                                    <code class="cursor-pointer" onclick="copyToClipboard('{{ $ticketCategory->slug }}')" 
                                          title="Click to copy">{{ $ticketCategory->slug }}</code>
                                </dd>

                                <dt class="col-sm-5">Color:</dt>
                                <dd class="col-sm-7">
                                    <div class="d-flex align-items-center">
                                        <div class="color-sample me-2" 
                                             style="background-color: {{ $ticketCategory->color }}; width: 20px; height: 20px; border-radius: 4px; border: 1px solid #dee2e6;"></div>
                                        <code class="cursor-pointer" onclick="copyToClipboard('{{ $ticketCategory->color }}')" 
                                              title="Click to copy">{{ $ticketCategory->color }}</code>
                                    </div>
                                </dd>

                                @if($ticketCategory->description)
                                    <dt class="col-sm-5">Description:</dt>
                                    <dd class="col-sm-7">{{ $ticketCategory->description }}</dd>
                                @endif

                                <dt class="col-sm-5">Sort Order:</dt>
                                <dd class="col-sm-7">{{ $ticketCategory->sort_order }}</dd>

                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-{{ $ticketCategory->is_active ? 'success' : 'secondary' }}">
                                        {{ $ticketCategory->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>

                                <dt class="col-sm-5">Created:</dt>
                                <dd class="col-sm-7">
                                    {{ $ticketCategory->created_at->format('M j, Y g:i A') }}
                                </dd>

                                <dt class="col-sm-5">Last Updated:</dt>
                                <dd class="col-sm-7">
                                    {{ $ticketCategory->updated_at->format('M j, Y g:i A') }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Category Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistics</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $activeTicketTypes = $ticketCategory->ticketTypes->where('is_active', true)->count();
                                $totalInventory = $ticketCategory->ticketTypes->sum('total_quantity');
                                $availableInventory = $ticketCategory->ticketTypes->sum('available_quantity');
                                $soldInventory = $totalInventory - $availableInventory;
                                $minPrice = $ticketCategory->ticketTypes->min('base_price');
                                $maxPrice = $ticketCategory->ticketTypes->max('base_price');
                            @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Active Ticket Types</small>
                                    <small>{{ $activeTicketTypes }}/{{ $ticketCategory->ticketTypes->count() }}</small>
                                </div>
                                @if($ticketCategory->ticketTypes->count() > 0)
                                    @php $activePercentage = ($activeTicketTypes / $ticketCategory->ticketTypes->count()) * 100; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $activePercentage }}%"></div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Inventory Utilization</small>
                                    <small>{{ $totalInventory > 0 ? round(($soldInventory / $totalInventory) * 100) : 0 }}%</small>
                                </div>
                                @if($totalInventory > 0)
                                    @php $utilizationPercentage = ($soldInventory / $totalInventory) * 100; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-info" style="width: {{ $utilizationPercentage }}%"></div>
                                    </div>
                                @endif
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <h6 class="text-muted mb-1">Price Range</h6>
                                    @if($minPrice && $maxPrice)
                                        <p class="mb-0">
                                            ${{ number_format($minPrice, 2) }}
                                            @if($minPrice != $maxPrice)
                                                - ${{ number_format($maxPrice, 2) }}
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted mb-1">Total Revenue</h6>
                                    @php $totalRevenue = $soldInventory * $avgPrice; @endphp
                                    <p class="mb-0">${{ number_format($totalRevenue, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.ticket-types.create', ['category_id' => $ticketCategory->id]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-plus"></i> Add Ticket Type
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="exportCategoryData()">
                                    <i class="fas fa-download"></i> Export Data
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="duplicateCategory()">
                                    <i class="fas fa-copy"></i> Duplicate Category
                                </button>
                                @if($ticketCategory->is_active)
                                    <form action="{{ route('admin.ticket-categories.update', $ticketCategory) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="btn btn-outline-warning w-100" 
                                                onclick="return confirm('Deactivate this category?')">
                                            <i class="fas fa-pause"></i> Deactivate
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.ticket-categories.update', $ticketCategory) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-play"></i> Activate
                                        </button>
                                    </form>
                                @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initPerformanceChart();
});

function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Mock data - replace with actual API data
    const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Tickets Sold',
            data: [12, 19, 8, 15, 20, 25],
            borderColor: '{{ $ticketCategory->color }}',
            backgroundColor: '{{ $ticketCategory->color }}33',
            tension: 0.4
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: chartData,
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
                    display: false
                }
            }
        }
    });
}

function duplicateCategory() {
    if (confirm('Create a copy of this category?')) {
        window.location.href = '{{ route("admin.ticket-categories.create") }}?duplicate_from={{ $ticketCategory->id }}';
    }
}

function exportCategoryData() {
    const exportUrl = `/admin/ticket-categories/{{ $ticketCategory->id }}/export`;
    window.open(exportUrl, '_blank');
}

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard!', 'success');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Copied to clipboard!', 'success');
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
</script>
@endpush
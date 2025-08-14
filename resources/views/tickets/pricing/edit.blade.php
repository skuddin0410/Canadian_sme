@extends('layouts.admin')


@section('title', 'Ticket Details - ' . $ticket->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-ticket-alt mr-2"></i>{{ $ticket->name }}
            @if($ticket->is_group)
                <span class="badge badge-info ml-2">Group Ticket</span>
            @endif
            @php
                $available = $ticket->quantity - ($ticket->bookings_sum_quantity ?? 0);
            @endphp
            @if($available <= 0)
                <span class="badge badge-danger ml-2">Sold Out</span>
            @elseif($available <= 10)
                <span class="badge badge-warning ml-2">Low Stock</span>
            @else
                <span class="badge badge-success ml-2">Available</span>
            @endif
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.ticket-pricing.edit', $ticket->id) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit Ticket
            </a>
            <a href="{{ route('admin.ticket-pricing.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('admin.ticket-pricing.edit', $ticket->id) }}">
                                <i class="fas fa-edit mr-2"></i>Edit Ticket
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.tickets.duplicate', $ticket->id) }}">
                                <i class="fas fa-copy mr-2"></i>Duplicate Ticket
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.tickets.export', ['ticket_id' => $ticket->id]) }}">
                                <i class="fas fa-download mr-2"></i>Export Bookings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" onclick="deleteTicket({{ $ticket->id }})">
                                <i class="fas fa-trash mr-2"></i>Delete Ticket
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%" class="text-muted">Ticket Name:</th>
                                    <td class="font-weight-bold">{{ $ticket->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Event:</th>
                                    <td>
                                        <a href="{{ route('admin.events.show', $ticket->session->event->id) }}" 
                                           class="text-decoration-none">
                                            {{ $ticket->session->event->title }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Session:</th>
                                    <td>
                                        <a href="{{ route('admin.sessions.show', $ticket->session->id) }}" 
                                           class="text-decoration-none">
                                            {{ $ticket->session->title }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Access Level:</th>
                                    <td>
                                        <span class="badge badge-{{ $ticket->access_level == 'vip' ? 'warning' : ($ticket->access_level == 'premium' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($ticket->access_level ?? 'general') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Created:</th>
                                    <td>{{ $ticket->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%" class="text-muted">Current Price:</th>
                                    <td>
                                        <span class="h5 text-primary font-weight-bold">
                                            ${{ number_format($ticket->current_price ?? $ticket->price, 2) }}
                                        </span>
                                        @if(isset($ticket->early_bird_price) && $ticket->early_bird_price < $ticket->price)
                                            <br><small class="text-success">Early Bird Active</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Regular Price:</th>
                                    <td>${{ number_format($ticket->price, 2) }}</td>
                                </tr>
                                @if($ticket->is_group)
                                <tr>
                                    <th class="text-muted">Group Size:</th>
                                    <td>{{ $ticket->group_size }} people</td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="text-muted">Max Per Booking:</th>
                                    <td>{{ $ticket->max_per_booking ?? 'No limit' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Updated:</th>
                                    <td>{{ $ticket->updated_at->format('M j, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($ticket->description)
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Description:</h6>
                        <p class="text-muted">{{ $ticket->description }}</p>
                    </div>
                    @endif

                    @if($ticket->perks && count($ticket->perks) > 0)
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Included Perks:</h6>
                        <div class="d-flex flex-wrap">
                            @foreach($ticket->perks as $perk)
                                <span class="badge badge-outline-primary mr-2 mb-2">
                                    <i class="fas fa-check mr-1"></i>{{ ucfirst($perk) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
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
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h3 text-primary mb-0">{{ $ticket->quantity }}</div>
                                <small class="text-muted">Total Tickets</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                @php $sold = $ticket->bookings_sum_quantity ?? 0; @endphp
                                <div class="h3 text-success mb-0">{{ $sold }}</div>
                                <small class="text-muted">Sold</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                @php $available = $ticket->quantity - $sold; @endphp
                                <div class="h3 text-{{ $available <= 0 ? 'danger' : ($available <= 10 ? 'warning' : 'info') }} mb-0">
                                    {{ $available }}
                                </div>
                                <small class="text-muted">Available</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                @php $percentage = $ticket->quantity > 0 ? ($sold / $ticket->quantity) * 100 : 0; @endphp
                                <div class="h3 text-secondary mb-0">{{ number_format($percentage, 1) }}%</div>
                                <small class="text-muted">Sold</small>
                            </div>
                        </div>
                    </div>

                    <div class="progress mt-3" style="height: 15px;">
                        <div class="progress-bar 
                            @if($percentage >= 90) bg-danger 
                            @elseif($percentage >= 70) bg-warning 
                            @else bg-success @endif" 
                            role="progressbar" style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>

                    @if($ticket->sale_start_date || $ticket->sale_end_date)
                    <div class="row mt-4">
                        @if($ticket->sale_start_date)
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Sale Start Date:</h6>
                            <p class="text-muted">{{ \Carbon\Carbon::parse($ticket->sale_start_date)->format('M j, Y g:i A') }}</p>
                        </div>
                        @endif
                        @if($ticket->sale_end_date)
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Sale End Date:</h6>
                            <p class="text-muted">{{ \Carbon\Carbon::parse($ticket->sale_end_date)->format('M j, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sales Analytics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Sales Analytics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="text-center">
                                        @php $totalRevenue = $sold * $ticket->price; @endphp
                                        <div class="h4 mb-0">${{ number_format($totalRevenue, 2) }}</div>
                                        <small>Total Revenue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="text-center">
                                        @php $potentialRevenue = $ticket->quantity * $ticket->price; @endphp
                                        <div class="h4 mb-0">${{ number_format($potentialRevenue, 2) }}</div>
                                        <small>Potential Revenue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="text-center">
                                        @php $avgBookingSize = $ticket->bookings_count > 0 ? $sold / $ticket->bookings_count : 0; @endphp
                                        <div class="h4 mb-0">{{ number_format($avgBookingSize, 1) }}</div>
                                        <small>Avg. Booking Size</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="mt-4">
                        <h6 class="font-weight-bold mb-3">Sales Over Time</h6>
                        <canvas id="salesChart" height="60"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                    <a href="{{ route('admin.bookings.index', ['ticket_id' => $ticket->id]) }}" class="btn btn-sm btn-primary">
                        View All Bookings
                    </a>
                </div>
                <div class="card-body">
                    @if($recentBookings && $recentBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Booking Code</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="font-weight-bold">
                                            {{ $booking->booking_code }}
                                        </a>
                                    </td>
                                    <td>{{ $booking->user->name ?? 'Guest' }}</td>
                                    <td>{{ $booking->quantity }}</td>
                                    <td>${{ number_format($booking->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->booked_at->format('M j, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No bookings yet for this ticket.</p>
                    </div>
                    @endif
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
                    <div class="mb-3">
                        <label class="font-weight-bold">Status:</label>
                        <div>
                            @if($available <= 0)
                                <span class="badge badge-danger badge-lg">Sold Out</span>
                            @elseif($ticket->status == 'active')
                                <span class="badge badge-success badge-lg">Active</span>
                            @else
                                <span class="badge badge-secondary badge-lg">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Options:</label>
                        <div class="mt-2">
                            @if($ticket->is_transferable)
                                <span class="badge badge-outline-success mr-2 mb-1">
                                    <i class="fas fa-exchange-alt mr-1"></i>Transferable
                                </span>
                            @endif
                            @if($ticket->is_refundable)
                                <span class="badge badge-outline-info mr-2 mb-1">
                                    <i class="fas fa-undo mr-1"></i>Refundable
                                </span>
                            @endif
                            @if($ticket->requires_approval)
                                <span class="badge badge-outline-warning mr-2 mb-1">
                                    <i class="fas fa-check-circle mr-1"></i>Requires Approval
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Quick Actions:</label>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary btn-block" onclick="toggleTicketStatus({{ $ticket->id }}, '{{ $ticket->status }}')">
                                @if($ticket->status == 'active')
                                    <i class="fas fa-pause mr-1"></i>Deactivate Ticket
                                @else
                                    <i class="fas fa-play mr-1"></i>Activate Ticket
                                @endif
                            </button>
                            <button class="btn btn-sm btn-outline-info btn-block" data-toggle="modal" data-target="#adjustInventoryModal">
                                <i class="fas fa-edit mr-1"></i>Adjust Inventory
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-block" data-toggle="modal" data-target="#adjustPriceModal">
                                <i class="fas fa-dollar-sign mr-1"></i>Update Price
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Early Bird Information -->
            @if($ticket->early_bird_price && $ticket->early_bird_end_date)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-clock mr-2"></i>Early Bird Pricing
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold">Early Bird Price:</label>
                        <div class="h5 text-success">${{ number_format($ticket->early_bird_price, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="font-weight-bold">End Date:</label>
                        <div>{{ \Carbon\Carbon::parse($ticket->early_bird_end_date)->format('M j, Y g:i A') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="font-weight-bold">Available:</label>
                        <div>{{ $ticket->early_bird_quantity ?? 'Unlimited' }}</div>
                    </div>
                    @php
                        $now = \Carbon\Carbon::now();
                        $endDate = \Carbon\Carbon::parse($ticket->early_bird_end_date);
                        $isActive = $now->lt($endDate);
                    @endphp
                    <div class="alert alert-{{ $isActive ? 'success' : 'warning' }} mb-0">
                        @if($isActive)
                            <i class="fas fa-check-circle mr-1"></i>Early bird pricing is currently active
                        @else
                            <i class="fas fa-clock mr-1"></i>Early bird period has ended
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Group Ticket Information -->
            @if($ticket->is_group)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-users mr-2"></i>Group Ticket Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <label class="font-weight-bold">Group Size:</label>
                            <div class="h5 text-info">{{ $ticket->group_size }}</div>
                        </div>
                        <div class="col-6">
                            <label class="font-weight-bold">Discount:</label>
                            <div class="h5 text-success">{{ $ticket->group_discount ?? 0 }}%</div>
                        </div>
                    </div>
                    @if($ticket->group_discount)
                    <div class="mt-3">
                        <label class="font-weight-bold">Group Price Per Person:</label>
                        @php $groupPrice = $ticket->price * (1 - ($ticket->group_discount / 100)); @endphp
                        <div class="h5 text-info">${{ number_format($groupPrice, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Booking Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Booking Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Bookings:</span>
                            <span class="font-weight-bold">{{ $ticket->bookings_count ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Today's Sales:</span>
                            <span class="font-weight-bold">{{ $todayBookings ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>This Week:</span>
                            <span class="font-weight-bold">{{ $weekBookings ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>This Month:</span>
                            <span class="font-weight-bold">{{ $monthBookings ?? 0 }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h5 text-primary mb-0">${{ number_format($totalRevenue, 2) }}</div>
                        <small class="text-muted">Total Revenue Generated</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Inventory Modal -->
<div class="modal fade" id="adjustInventoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Inventory</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tickets.adjust-inventory', $ticket->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_quantity">New Total Quantity</label>
                        <input type="number" class="form-control" id="new_quantity" name="quantity" 
                               value="{{ $ticket->quantity }}" min="{{ $sold }}" required>
                        <small class="form-text text-muted">
                            Minimum: {{ $sold }} (already sold)
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason for Change</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                  placeholder="Explain why you're adjusting the inventory"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Price Modal -->
<div class="modal fade" id="adjustPriceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Price</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tickets.adjust-price', $ticket->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_price">New Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="new_price" name="price" 
                                   value="{{ $ticket->price }}" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price_reason">Reason for Change</label>
                        <textarea class="form-control" id="price_reason" name="reason" rows="3" 
                                  placeholder="Explain why you're changing the price"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        This will only affect new bookings. Existing bookings will retain their original price.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize sales chart
    initSalesChart();
});

function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Sample data - replace with actual data from controller
    const salesData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Tickets Sold',
            data: [12, 19, 3, 5, 2, 3],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function toggleTicketStatus(ticketId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this ticket?`)) {
        $.ajax({
            url: `/admin/tickets/pricing/${ticketId}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while updating the ticket status.');
            }
        });
    }
}

function deleteTicket(ticketId) {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone and will affect all related bookings.')) {
        $.ajax({
            url: `/admin/tickets/pricing/${ticketId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("admin.ticket-pricing.index") }}';
                } else {
                    alert('Error deleting ticket: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the ticket.');
            }
        });
    }
}

// Real-time updates for inventory changes
$('#new_quantity').on('input', function() {
    const newQuantity = parseInt($(this).val());
    const sold = {{ $sold }};
    const available = newQuantity - sold;
    
    if (newQuantity < sold) {
        $(this).addClass('is-invalid');
        $(this).siblings('.form-text').text(`Cannot be less than ${sold} (already sold)`).addClass('text-danger');
    } else {
        $(this).removeClass('is-invalid');
        $(this).siblings('.form-text').text(`Available tickets will be: ${available}`).removeClass('text-danger');
    }
});

// Price change preview
$('#new_price').on('input', function() {
    const newPrice = parseFloat($(this).val());
    const oldPrice = {{ $ticket->price }};
    const difference = newPrice - oldPrice;
    const percentChange = oldPrice > 0 ? ((difference / oldPrice) * 100).toFixed(1) : 0;
    
    let changeText = '';
    if (difference > 0) {
        changeText = `<span class="text-success">+${difference.toFixed(2)} (+${percentChange}%)</span>`;
    } else if (difference < 0) {
        changeText = `<span class="text-danger">-${Math.abs(difference).toFixed(2)} (${percentChange}%)</span>`;
    } else {
        changeText = '<span class="text-muted">No change</span>';
    }
    
    // Add preview element if it doesn't exist
    if (!$('#priceChangePreview').length) {
        $(this).parent().after('<small id="priceChangePreview" class="form-text"></small>');
    }
    
    $('#priceChangePreview').html(`Change: ${changeText}`);
});
</script>
@endpush
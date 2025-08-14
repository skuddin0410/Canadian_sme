@extends('layouts.admin')

@section('title', 'Ticket Management')

@section('content')
<div class="container">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-ticket-alt mr-2"></i>Ticket Management
        </h1>
        <div class="btn-group">
           {{--  <a href="{{ route('admin.ticket-pricing.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i>Create Ticket Type
            </a> --}}
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#bulkImportModal">
                <i class="fas fa-file-import mr-1"></i>Bulk Import
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tickets-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Tickets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bookings Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayBookings ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filter Tickets
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ticket-pricing.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="event_filter">Event</label>
                        <select name="event_id" id="event_filter" class="form-control">
                            <option value="">All Events</option>
                            @foreach($events ?? [] as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="session_filter">Session</label>
                        <select name="session_id" id="session_filter" class="form-control">
                            <option value="">All Sessions</option>
                            @foreach($sessions ?? [] as $session)
                                <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter">Status</label>
                        <select name="status" id="status_filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.ticket-pricing.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Ticket Types</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                     aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-file-csv mr-2"></i>Export as CSV
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-file-excel mr-2"></i>Export as Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ticketsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>Ticket Name</th>
                            <th>Event</th>
                            <th>Session</th>
                            <th>Price</th>
                            <th>Available / Total</th>
                            <th>Sold</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets ?? [] as $ticket)
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ticket-checkbox" 
                                           id="ticket{{ $ticket->id }}" value="{{ $ticket->id }}">
                                    <label class="custom-control-label" for="ticket{{ $ticket->id }}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong>{{ $ticket->name }}</strong>
                                        @if($ticket->is_group)
                                            <span class="badge badge-info ml-1">Group ({{ $ticket->group_size }})</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $ticket->session->event->title ?? 'N/A' }}</td>
                            <td>{{ $ticket->session->title ?? 'N/A' }}</td>
                            <td>
                                <span class="font-weight-bold">${{ number_format($ticket->price, 2) }}</span>
                            </td>
                            <td>
                                <div class="progress mb-1" style="height: 10px;">
                                    @php
                                        $sold = $ticket->bookings_sum_quantity ?? 0;
                                        $total = $ticket->quantity;
                                        $percentage = $total > 0 ? ($sold / $total) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar 
                                        @if($percentage >= 90) bg-danger 
                                        @elseif($percentage >= 70) bg-warning 
                                        @else bg-success @endif" 
                                        role="progressbar" style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                                <small>{{ $total - $sold }} / {{ $total }}</small>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $sold }}</span>
                            </td>
                            <td>
                                @if($total - $sold <= 0)
                                    <span class="badge badge-danger">Sold Out</span>
                                @elseif($total - $sold <= 10)
                                    <span class="badge badge-warning">Low Stock</span>
                                @else
                                    <span class="badge badge-success">Available</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.ticket-pricing.show', $ticket->id) }}" 
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ticket-pricing.edit', $ticket->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteTicket({{ $ticket->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No tickets found. <a href="{{ route('admin.ticket-pricing.create') }}">Create your first ticket</a></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($tickets) && $tickets->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} results
                </div>
                {{ $tickets->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bulk Actions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success" onclick="bulkAction('activate')">
                        <i class="fas fa-check mr-1"></i>Activate Selected
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="bulkAction('deactivate')">
                        <i class="fas fa-times mr-1"></i>Deactivate Selected
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger" onclick="bulkAction('delete')">
                        <i class="fas fa-trash mr-1"></i>Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#ticketsTable').DataTable({
        "paging": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ]
    });

    // Select all functionality
    $('#selectAll').change(function() {
        $('.ticket-checkbox').prop('checked', $(this).is(':checked'));
        toggleBulkActions();
    });

    // Individual checkbox functionality
    $('.ticket-checkbox').change(function() {
        toggleBulkActions();
        if (!$(this).is(':checked')) {
            $('#selectAll').prop('checked', false);
        }
    });

    function toggleBulkActions() {
        const checkedBoxes = $('.ticket-checkbox:checked');
        if (checkedBoxes.length > 0) {
            $('#bulkActions').show();
        } else {
            $('#bulkActions').hide();
        }
    }
});

function deleteTicket(ticketId) {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/tickets/pricing/${ticketId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
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


</script>
@endpush
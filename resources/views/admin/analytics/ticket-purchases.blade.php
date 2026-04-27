@extends('layouts.admin')

@section('title', 'Ticket Purchase Analytics')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-1">Ticket Purchase Analytics</h1>
                    <p class="text-muted mb-0">Track ticket sales, revenue, status mix, and top-performing events and ticket types.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="event_id" class="form-label">Event</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.analytics.ticket-purchases') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Total Purchases</div>
                            <div class="fs-3 fw-bold">{{ number_format($kpis['total_purchases']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Completed Purchases</div>
                            <div class="fs-3 fw-bold text-success">{{ number_format($kpis['completed_purchases']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Pending Payments</div>
                            <div class="fs-3 fw-bold text-warning">{{ number_format($kpis['pending_purchases']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Completed Revenue</div>
                            <div class="fs-3 fw-bold text-primary">${{ number_format($kpis['completed_revenue'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Purchase Status Chart</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrap">
                                <canvas id="ticketPurchaseStatusChart"></canvas>
                                <div id="ticketPurchaseStatusChartNoData" class="no-data-placeholder" style="display: none;">
                                    <i class="bx bx-pie-chart-alt-2 fs-1 mb-2"></i>
                                    <p class="mb-0">No status data available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Top Ticket Types Chart</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrap">
                                <canvas id="ticketTypeChart"></canvas>
                                <div id="ticketTypeChartNoData" class="no-data-placeholder" style="display: none;">
                                    <i class="bx bx-bar-chart-alt-2 fs-1 mb-2"></i>
                                    <p class="mb-0">No ticket type data available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Top Events Chart</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrap">
                                <canvas id="ticketEventChart"></canvas>
                                <div id="ticketEventChartNoData" class="no-data-placeholder" style="display: none;">
                                    <i class="bx bx-calendar-event fs-1 mb-2"></i>
                                    <p class="mb-0">No event data available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Status Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Purchases</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($statusBreakdown as $row)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_', ' ', $row->status)) }}</td>
                                                <td>{{ number_format($row->purchases) }}</td>
                                                <td>${{ number_format((float) $row->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Top Ticket Types</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Ticket Type</th>
                                            <th>Purchases</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ticketBreakdown as $row)
                                            <tr>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ number_format($row->purchases) }}</td>
                                                <td>${{ number_format((float) $row->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Top Events</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Purchases</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($eventBreakdown as $row)
                                            <tr>
                                                <td>{{ $row->title }}</td>
                                                <td>{{ number_format($row->purchases) }}</td>
                                                <td>${{ number_format((float) $row->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Purchases</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User</th>
                                            <th>Event</th>
                                            <th>Ticket Type</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentPurchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->id }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ trim(($purchase->user->name ?? '') . ' ' . ($purchase->user->lastname ?? '')) ?: 'N/A' }}</div>
                                                    <small class="text-muted">{{ $purchase->user->email ?? 'No email' }}</small>
                                                </td>
                                                <td>{{ $purchase->event->title ?? 'N/A' }}</td>
                                                <td>{{ $purchase->ticketType->name ?? 'N/A' }}</td>
                                                <td>{{ ucwords(str_replace('_', ' ', $purchase->status)) }}</td>
                                                <td>${{ number_format((float) $purchase->amount, 2) }}</td>
                                                <td>{{ optional($purchase->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No recent purchases found.</td>
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
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusData = @json($statusChartData);
    const ticketTypeData = @json($ticketTypeChartData);
    const eventData = @json($eventChartData);

    const palette = {
        primary: 'rgba(105, 108, 255, 0.85)',
        success: 'rgba(113, 221, 55, 0.85)',
        warning: 'rgba(255, 171, 0, 0.85)',
        danger: 'rgba(255, 62, 29, 0.85)',
        info: 'rgba(3, 195, 236, 0.85)',
        secondary: 'rgba(133, 146, 163, 0.85)'
    };

    if (statusData.length) {
        new Chart(document.getElementById('ticketPurchaseStatusChart'), {
            // ... chart config ...
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.label),
                datasets: [{
                    data: statusData.map(item => item.purchases),
                    backgroundColor: [palette.success, palette.warning, palette.danger, palette.info, palette.secondary]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        document.getElementById('ticketPurchaseStatusChart').style.display = 'none';
        document.getElementById('ticketPurchaseStatusChartNoData').style.display = 'flex';
    }

    if (ticketTypeData.length) {
        new Chart(document.getElementById('ticketTypeChart'), {
            type: 'bar',
            data: {
                labels: ticketTypeData.map(item => item.label),
                datasets: [{
                    label: 'Purchases',
                    data: ticketTypeData.map(item => item.purchases),
                    backgroundColor: palette.primary,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        document.getElementById('ticketTypeChart').style.display = 'none';
        document.getElementById('ticketTypeChartNoData').style.display = 'flex';
    }

    if (eventData.length) {
        new Chart(document.getElementById('ticketEventChart'), {
            type: 'bar',
            data: {
                labels: eventData.map(item => item.label),
                datasets: [{
                    label: 'Purchases',
                    data: eventData.map(item => item.purchases),
                    backgroundColor: palette.info,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        document.getElementById('ticketEventChart').style.display = 'none';
        document.getElementById('ticketEventChartNoData').style.display = 'flex';
    }
});
</script>
<style>
.chart-wrap {
    position: relative;
    min-height: 320px;
    display: flex;
    flex-direction: column;
}
.no-data-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #a1acb8;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px dashed #d9dee3;
}
</style>
@endsection

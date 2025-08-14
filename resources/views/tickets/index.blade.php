@extends('layouts.admin')

@section('title', 'Ticket Types')

@section('content')
<div class="container">
<div class="row mb-4 mt-3">
    <!-- Ticket Sales Overview -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Ticket Sales Overview</h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active" data-period="7">7 Days</button>
                    <button type="button" class="btn btn-outline-primary" data-period="30">30 Days</button>
                    <button type="button" class="btn btn-outline-primary" data-period="90">90 Days</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="ticketSalesChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-xl-4">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Total Revenue</h6>
                                <h3 class="mb-0 text-white" id="totalRevenue">$0</h3>
                                <small class="text-white">This month</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Tickets Sold</h6>
                                <h3 class="mb-0 text-white" id="ticketsSold">0</h3>
                                <small class="text-white">This month</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-ticket-alt fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-white">Low Stock Alerts</h6>
                                <h3 class="mb-0 text-white" id="lowStockCount">0</h3>
                                <small class="text-white text-white">Tickets running low</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Top Selling Tickets -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Selling Tickets</h5>
            </div>
            <div class="card-body">
                <div id="topSellingTickets">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Ticket Activity</h5>
                <a href="{{ route('admin.ticket-inventory.logs') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div id="recentActivity">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.ticket-types.create') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                <span>Create Ticket Type</span>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.ticket-categories.index') }}" class="btn btn-outline-info w-100 d-flex flex-column align-items-center py-3">
                <i class="fas fa-tags fa-2x mb-2"></i>
                <span>Manage Categories</span>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.ticket-inventory.index') }}" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center py-3">
                <i class="fas fa-boxes fa-2x mb-2"></i>
                <span>Inventory Management</span>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.ticket-pricing.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <span>Pricing Rules</span>
            </a>
        </div>
    </div>
</div>

        </div>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize ticket sales chart
    initTicketSalesChart();
    
    // Load dashboard data
    loadDashboardStats();
    loadTopSellingTickets();
    loadRecentActivity();
    
    // Refresh data every 5 minutes
    setInterval(function() {
        loadDashboardStats();
        loadTopSellingTickets();
        loadRecentActivity();
    }, 300000);
});

let ticketSalesChart;

function initTicketSalesChart() {
    const ctx = document.getElementById('ticketSalesChart').getContext('2d');
    
    ticketSalesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue',
                data: [],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Tickets Sold',
                data: [],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    ticks: {
                        callback: function(value) {
                            //return ' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Tickets Sold'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.yAxisID === 'y') {
                               // label += ' + context.parsed.y.toLocaleString();
                            } else {
                               // label += context.parsed.y.toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    
    // Load initial chart data
    loadTicketSalesData(7);
    
    // Handle period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            loadTicketSalesData(parseInt(this.dataset.period));
        });
    });
}

function loadTicketSalesData(days) {
    fetch(`/admin/dashboard/ticket-sales-data?days=${days}`)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => new Date(item.date).toLocaleDateString());
            const revenue = data.map(item => parseFloat(item.revenue || 0));
            const bookings = data.map(item => parseInt(item.bookings || 0));
            
            ticketSalesChart.data.labels = labels;
            ticketSalesChart.data.datasets[0].data = revenue;
            ticketSalesChart.data.datasets[1].data = bookings;
            ticketSalesChart.update();
        })
        .catch(error => console.error('Error loading sales data:', error));
}

function loadDashboardStats() {
    fetch('/admin/inventory/stats')
        .then(response => response.json())
        .then(data => {
           // document.getElementById('totalRevenue').textContent = ' + (data.total_revenue || 0).toLocaleString();
           // document.getElementById('ticketsSold').textContent = data.sold_tickets.toLocaleString();
           // document.getElementById('lowStockCount').textContent = data.low_stock_count;
        })
        .catch(error => console.error('Error loading stats:', error));
}

function loadTopSellingTickets() {
    fetch('/admin/dashboard/top-selling-tickets')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('topSellingTickets');
            
            if (data.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No sales data available</p>';
                return;
            }
            
            let html = '<div class="list-group list-group-flush">';
            data.forEach((ticket, index) => {
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">${ticket.name}</div>
                            <small class="text-muted">${parseFloat(ticket.price).toFixed(2)}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">${ticket.total_sold || 0} sold</span>
                            <div class="text-muted small">
                                ${((ticket.total_sold || 0) * parseFloat(ticket.price)).toLocaleString()}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading top selling tickets:', error);
            document.getElementById('topSellingTickets').innerHTML = 
                '<p class="text-danger text-center">Error loading data</p>';
        });
}

function loadRecentActivity() {
    fetch('/admin/dashboard/recent-ticket-activity')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recentActivity');
            
            if (data.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No recent activity</p>';
                return;
            }
            
            let html = '<div class="list-group list-group-flush">';
            data.forEach(activity => {
                const actionIcon = getActionIcon(activity.action);
                const actionColor = getActionColor(activity.action);
                const timeAgo = formatTimeAgo(activity.created_at);
                
                html += `
                    <div class="list-group-item px-0">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-sm bg-${actionColor}-subtle text-${actionColor}">
                                    <i class="fas ${actionIcon}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${activity.ticket_type?.name || 'Unknown Ticket'}</div>
                                <small class="text-muted">
                                    ${formatActivityDescription(activity)} by ${activity.user?.name || 'System'}
                                </small>
                                <div class="text-muted small">${timeAgo}</div>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <span class="badge bg-${actionColor}">${activity.quantity}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
            document.getElementById('recentActivity').innerHTML = 
                '<p class="text-danger text-center">Error loading data</p>';
        });
}

function getActionIcon(action) {
    const icons = {
        'increase': 'fa-arrow-up',
        'decrease': 'fa-arrow-down',
        'reserve': 'fa-lock',
        'release': 'fa-unlock'
    };
    return icons[action] || 'fa-edit';
}

function getActionColor(action) {
    const colors = {
        'increase': 'success',
        'decrease': 'danger',
        'reserve': 'warning',
        'release': 'info'
    };
    return colors[action] || 'secondary';
}

function formatActivityDescription(activity) {
    const descriptions = {
        'increase': 'Inventory increased',
        'decrease': 'Inventory decreased',
        'reserve': 'Quantity reserved',
        'release': 'Quantity released'
    };
    return descriptions[activity.action] || 'Inventory updated';
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}
</script>
@endsection
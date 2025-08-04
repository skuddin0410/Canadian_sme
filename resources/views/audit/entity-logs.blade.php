@extends('layouts.admin')

@section('title', 'Entity Audit Logs')

@section('header')
    <h2 class="fw-semibold h3 text-dark">
        {{ __('Audit Logs for') }} 
        <span class="badge bg-primary">{{ class_basename($entityType) }}</span>
        <code class="bg-light text-dark px-2 py-1 rounded">#{{ $entityId }}</code>
    </h2>
@endsection

@section('content')
<div class="container">
    <!-- Navigation and Entity Info -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('audit.index') }}" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>All Audit Logs
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ class_basename($entityType) }} #{{ $entityId }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Entity Summary Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header ">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Entity Audit History
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark">Total Logs: {{ $logs->total() }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-text me-2"></i>CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box text-primary me-2"></i>
                        <div>
                            <small class="text-muted">Entity Type</small>
                            <div class="fw-medium">{{ class_basename($entityType) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-hash text-success me-2"></i>
                        <div>
                            <small class="text-muted">Entity ID</small>
                            <div class="fw-medium">#{{ $entityId }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-activity text-warning me-2"></i>
                        <div>
                            <small class="text-muted">Total Events</small>
                            <div class="fw-medium">{{ $logs->total() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock text-info me-2"></i>
                        <div>
                            <small class="text-muted">Last Activity</small>
                            <div class="fw-medium">
                                @if($logs->count() > 0)
                                    {{ $logs->first()->created_at->diffForHumans() }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <div class="card-body p-0">
    @if($logs->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="bi bi-bar-chart me-2"></i>Quick Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-success mb-1">{{ $logs->where('event', 'created')->count() }}</h4>
                                <small class="text-muted">Created Events</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $logs->where('event', 'updated')->count() }}</h4>
                                <small class="text-muted">Updated Events</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-danger mb-1">{{ $logs->where('event', 'deleted')->count() }}</h4>
                                <small class="text-muted">Deleted Events</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info mb-1">{{ $logs->groupBy('user_id')->count() }}</h4>
                            <small class="text-muted">Unique Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div> 

    <!-- Audit Logs Table -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>Chronological History
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="sortOrder" id="newest" checked>
                    <label class="btn btn-outline-primary" for="newest">
                        <i class="bi bi-sort-down me-1"></i>Newest First
                    </label>
                    <input type="radio" class="btn-check" name="sortOrder" id="oldest">
                    <label class="btn btn-outline-primary" for="oldest">
                        <i class="bi bi-sort-up me-1"></i>Oldest First
                    </label>
                </div>
            </div>
        </div>   
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap mb-0" id="auditTable">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-3">
                                <i class="bi bi-activity me-1"></i>Event
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <i class="bi bi-person me-1"></i>User
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <i class="bi bi-calendar me-1"></i>Date/Time
                            </th>
                            <th scope="col" class="px-4 py-3 text-center">
                                <i class="bi bi-gear me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $index => $log)
                            <tr class="audit-row" data-timestamp="{{ $log->created_at->timestamp }}">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($log->event == 'created')
                                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-plus" style="font-size: 14px;"></i>
                                                </div>
                                            @elseif($log->event == 'updated')
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-pencil" style="font-size: 14px;"></i>
                                                </div>
                                            @elseif($log->event == 'deleted')
                                                <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-trash" style="font-size: 14px;"></i>
                                                </div>
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-question" style="font-size: 14px;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            @if($log->event == 'created')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-plus-circle me-1"></i>Created
                                                </span>
                                            @elseif($log->event == 'updated')
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-pencil me-1"></i>Updated
                                                </span>
                                            @elseif($log->event == 'deleted')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-trash me-1"></i>Deleted
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-question-circle me-1"></i>{{ ucfirst($log->event) }}
                                                </span>
                                            @endif
                                            <!-- Timeline connector -->
                                            @if(!$loop->last)
                                                <div class="timeline-connector"></div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        @if($log->user)
                                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 11px;">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $log->user->name }}</div>
                                                <small class="text-muted">{{ $log->user->email }}</small>
                                            </div>
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                                                <i class="bi bi-robot" style="font-size: 11px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-muted">System</div>
                                                <small class="text-muted">Automated process</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <div class="fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    </div>
                                    <small class="badge bg-light text-dark mt-1">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('audit.show', $log) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                        <i class="bi bi-eye"></i>
                                        <span class="visually-hidden">View Details</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-5 text-center">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-3"></i>
                                        <h5 class="mt-3">No audit logs found</h5>
                                        <p class="mb-0">This entity has no recorded audit history.</p>
                                        @if(request()->has('page') && request('page') > 1)
                                            <div class="mt-3">
                                                <a href="{{ route('audit.edit', ['entityType' => $entityType, 'entityId' => $entityId]) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-arrow-left me-1"></i>Go to First Page
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($logs->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Timeline connector styling */
.timeline-connector {
    position: relative;
    margin-top: 10px;
    margin-left: 16px;
    width: 2px;
    height: 30px;
    background: linear-gradient(to bottom, #6c757d, transparent);
}

.timeline-connector::before {
    content: '';
    position: absolute;
    top: 0;
    left: -2px;
    width: 6px;
    height: 6px;
    background-color: #6c757d;
    border-radius: 50%;
}

/* Hover effects */
.audit-row:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

.card {
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Badge animations */
.badge {
    transition: transform 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Button group styling */
.btn-check:checked + .btn {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .timeline-connector {
        display: none;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .card-body .row.text-center .col-md-3 {
        margin-bottom: 1rem;
    }
    
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
    }
}

/* Print styles */
@media print {
    .btn, .dropdown, .breadcrumb, .card-footer {
        display: none !important;
    }
    
    .timeline-connector {
        display: none;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}

/* Loading animation */
.table tbody tr.loading {
    opacity: 0.5;
    pointer-events: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.audit-row {
    animation: fadeIn 0.3s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sort functionality
    const newestBtn = document.getElementById('newest');
    const oldestBtn = document.getElementById('oldest');
    const tableBody = document.querySelector('#auditTable tbody');
    
    function sortTable(order) {
        const rows = Array.from(tableBody.querySelectorAll('.audit-row'));
        
        rows.sort((a, b) => {
            const timestampA = parseInt(a.dataset.timestamp);
            const timestampB = parseInt(b.dataset.timestamp);
            
            return order === 'newest' ? timestampB - timestampA : timestampA - timestampB;
        });
        
        rows.forEach(row => {
            tableBody.appendChild(row);
        });
        
        // Re-animate rows
        rows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.1}s`;
        });
    }
    
    newestBtn.addEventListener('change', () => {
        if (newestBtn.checked) sortTable('newest');
    });
    
    oldestBtn.addEventListener('change', () => {
        if (oldestBtn.checked) sortTable('oldest');
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add loading state to action buttons
    document.querySelectorAll('.btn[href]').forEach(button => {
        button.addEventListener('click', function() {
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            this.disabled = true;
            
            // Re-enable after navigation (fallback)
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 3000);
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape to go back
        if (e.key === 'Escape') {
            window.history.back();
        }
        
        // Ctrl/Cmd + R for refresh
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            location.reload();
        }
        
        // Arrow keys for navigation
        if (e.key === 'ArrowLeft' && (e.ctrlKey || e.metaKey)) {
            const backLink = document.querySelector('a[href*="audit.index"]');
            if (backLink) backLink.click();
        }
    });
    
    // Auto-refresh functionality (optional)
    let autoRefreshInterval;
    
    function toggleAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        } else {
            autoRefreshInterval = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    location.reload();
                }
            }, 30000); // Refresh every 30 seconds
        }
    }
    
    // Add context menu for additional options
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.audit-row')) {
            e.preventDefault();
            // Could add custom context menu here
        }
    });
});
</script>

@endsection
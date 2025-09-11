@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('header')
    <h2 class="fw-semibold h3 text-dark">
        {{ __('Audit Logs') }}
    </h2>
@endsection

@section('content')
<div class="container">
    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 mt-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filter Audit Logs
            </h5>
        </div>
        <div class="card-body mt-1">
            <form method="GET" action="{{ route('audit.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="event" class="form-label fw-medium">Event Type</label>
                        <select name="event" id="event" class="form-select">
                            <option value="">All Events</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="type" class="form-label fw-medium">Entity Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="App\Models\Project" {{ request('type') == 'App\Models\Project' ? 'selected' : '' }}>Project</option>
                            <option value="App\Models\InvestmentClass" {{ request('type') == 'App\Models\InvestmentClass' ? 'selected' : '' }}>Investment Class</option>
                            <option value="App\Models\Investment" {{ request('type') == 'App\Models\Investment' ? 'selected' : '' }}>Investment</option>
                            <option value="App\Models\Distribution" {{ request('type') == 'App\Models\Distribution' ? 'selected' : '' }}>Distribution</option>
                            <option value="App\Models\WaterfallModel" {{ request('type') == 'App\Models\WaterfallModel' ? 'selected' : '' }}>Waterfall Model</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="from" class="form-label fw-medium">From Date</label>
                        <input type="date" name="from" id="from" value="{{ request('from') ?? '' }}" class="form-control">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="to" class="form-label fw-medium">To Date</label>
                        <input type="date" name="to" id="to" value="{{ request('to') ?? '' }}" class="form-control">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter Results
                    </button>
                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>Audit Logs
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-secondary me-2">Total: {{ $logs->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
                    <thead >
                        <tr>
                            <th scope="col" class="px-4 py-3">
                                <i class="bi bi-activity me-1"></i>Event
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <i class="bi bi-box me-1"></i>Entity
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
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3">
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
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-medium text-dark">{{ class_basename($log->auditable_type) }}</div>
                                    <small class="text-muted">#{{ $log->auditable_id }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @if($log->user)
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ substr($log->user->full_name, 0, 2) }}
                                                </div>
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-robot"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $log->user ? $log->user->full_name : 'System' }}</div>
                                            <small class="text-muted">{{ $log->user ? $log->user->email : 'Automated process' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    <br>
                                    <small class="badge bg-light text-dark">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-icon btn-primary" title="View Details">
                                            <i class="bx bxs-show"></i>
                                        </a>
                                        <a href="{{ route('audit.edit', ['entityType'=>$log->auditable_type, 'entityId'=>$log->auditable_id]) }}" class="btn btn-sm btn-icon btn-primary" title="View History">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-1"></i>
                                        <h5 class="mt-3">No audit logs found</h5>
                                        <p class="mb-0">Try adjusting your filters or check back later.</p>
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
                    {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Custom styles for better visual appeal */
.table-responsive {
    border-radius: 0.375rem;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.025);
}

/* Custom pagination styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #6c757d;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 0.25rem;
    }
}

/* Loading state */
.table tbody tr.loading {
    opacity: 0.5;
    pointer-events: none;
}

/* Status badges with better contrast */
.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to filter form
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Filtering...';
            submitBtn.disabled = true;
            
            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }

    // Add tooltips to action buttons
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add confirmation for sensitive actions
    const viewHistoryLinks = document.querySelectorAll('a[href*="audit.edit"]');
    viewHistoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const entityType = this.href.split('entityType=')[1]?.split('&')[0];
            if (entityType) {
                const entityName = entityType.split('\\').pop();
                if (!confirm(`View complete audit history for this ${entityName}?`)) {
                    e.preventDefault();
                }
            }
        });
    });

    // Auto-refresh functionality (optional)
    let autoRefresh = false;
    const toggleAutoRefresh = () => {
        autoRefresh = !autoRefresh;
        if (autoRefresh) {
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    location.reload();
                }
            }, 30000); // Refresh every 30 seconds
        }
    };

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + R for manual refresh
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            location.reload();
        }
        
        // Escape to clear filters
        if (e.key === 'Escape') {
            const resetLink = document.querySelector('a[href*="audit.index"]:not([href*="?"])');
            if (resetLink) {
                window.location.href = resetLink.href;
            }
        }
    });
});
</script>

@endsection
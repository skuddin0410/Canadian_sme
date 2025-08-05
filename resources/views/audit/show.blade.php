@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('header')
    <h2 class="fw-semibold h3 text-dark">
        {{ __('Audit Log Details') }}
    </h2>
@endsection

@section('content')
<div class="container">
    <!-- Navigation -->
    <div class="mb-4 mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('audit.index') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Audit Logs
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Log Details</li>
            </ol>
        </nav>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-file-text me-2"></i>Audit Log Details
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light btn-sm" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-share me-1"></i>Share
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="copyToClipboard()"><i class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i>Email</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Event and User Information Grid -->
            <div class="row g-4 mb-4">
                <!-- Event Information -->
                <div class="col-lg-6">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-activity me-2"></i>Event Information
                            </h6>
                        </div>
                        <div class="card-body mt-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">Event Type:</span>
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
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">Entity Type:</span>
                                        <span class="badge bg-info">{{ class_basename($log->auditable_type) }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">Entity ID:</span>
                                        <code class="bg-light px-2 py-1 rounded">#{{ $log->auditable_id }}</code>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">Date/Time:</span>
                                        <div class="text-end">
                                            <div class="fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $log->created_at->format('h:i:s A') }}</small>
                                            <br>
                                            <small class="badge bg-light text-dark">{{ $log->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Information -->
                <div class="col-lg-6">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="bi bi-person me-2"></i>User Information
                            </h6>
                        </div>
                        <div class="card-body mt-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">User:</span>
                                        <div class="d-flex align-items-center">
                                            @if($log->user)
                                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                    {{ substr($log->user->name, 0, 2) }}
                                                </div>
                                                <span>{{ $log->user->name }}</span>
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                                    <i class="bi bi-robot" style="font-size: 10px;"></i>
                                                </div>
                                                <span class="text-muted">System</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($log->user)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">Email:</span>
                                        <a href="mailto:{{ $log->user->email }}" class="text-decoration-none">{{ $log->user->email }}</a>
                                    </div>
                                </div>
                                @endif
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-muted">IP Address:</span>
                                        <code class="bg-light px-2 py-1 rounded">{{ $log->ip_address ?: 'N/A' }}</code>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="fw-medium text-muted">User Agent:</span>
                                        <div class="text-end" style="max-width: 200px;">
                                            <small class="text-muted" title="{{ $log->user_agent ?: 'N/A' }}">
                                                {{ Str::limit($log->user_agent ?: 'N/A', 30) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Changes Section -->
            @if($log->event == 'updated' && $log->old_values)
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0 ">
                            <i class="bi bi-arrow-left-right me-2"></i>Changed Values
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-tag me-1"></i>Field
                                        </th>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-arrow-left me-1"></i>Old Value
                                        </th>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-arrow-right me-1"></i>New Value
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->old_values as $key => $oldValue)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-secondary">{{ $key }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="bg-light p-2 rounded border-start border-danger border-3">
                                                    @if(is_array($oldValue))
                                                        <pre class="mb-0 small"><code>{{ json_encode($oldValue, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @elseif(is_bool($oldValue))
                                                        <span class="badge {{ $oldValue ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $oldValue ? 'true' : 'false' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">{{ $oldValue ?: 'NULL' }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="bg-light p-2 rounded border-start border-success border-3">
                                                    @if(isset($log->new_values[$key]))
                                                        @if(is_array($log->new_values[$key]))
                                                            <pre class="mb-0 small"><code>{{ json_encode($log->new_values[$key], JSON_PRETTY_PRINT) }}</code></pre>
                                                        @elseif(is_bool($log->new_values[$key]))
                                                            <span class="badge {{ $log->new_values[$key] ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $log->new_values[$key] ? 'true' : 'false' }}
                                                            </span>
                                                        @else
                                                            <span>{{ $log->new_values[$key] ?: 'NULL' }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($log->event == 'created' && $log->new_values)
                <div class="card border-success mb-4">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0 text-success">
                            <i class="bi bi-plus-circle me-2"></i>Created Values
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-tag me-1"></i>Field
                                        </th>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-check-circle me-1"></i>Value
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->new_values as $key => $value)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-secondary">{{ $key }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="bg-light p-2 rounded border-start border-success border-3">
                                                    @if(is_array($value))
                                                        <pre class="mb-0 small"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @elseif(is_bool($value))
                                                        <span class="badge {{ $value ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $value ? 'true' : 'false' }}
                                                        </span>
                                                    @else
                                                        <span>{{ $value ?: 'NULL' }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($log->event == 'deleted' && $log->old_values)
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger bg-opacity-10">
                        <h6 class="mb-0 text-danger">
                            <i class="bi bi-trash me-2"></i>Deleted Values
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-tag me-1"></i>Field
                                        </th>
                                        <th class="px-4 py-3">
                                            <i class="bi bi-x-circle me-1"></i>Value
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->old_values as $key => $value)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-secondary">{{ $key }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="bg-light p-2 rounded border-start border-danger border-3">
                                                    @if(is_array($value))
                                                        <pre class="mb-0 small"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                    @elseif(is_bool($value))
                                                        <span class="badge {{ $value ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $value ? 'true' : 'false' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">{{ $value ?: 'NULL' }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Additional Information -->
            @if($log->url || $log->tags)
                <div class="card border-info">
                    <div class="card-header bg-info bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Additional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @if($log->url)
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-muted">URL:</span>
                                    <div class="text-end">
                                        <a href="{{ $log->url }}" class="text-decoration-none" target="_blank" title="{{ $log->url }}">
                                            {{ Str::limit($log->url, 50) }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($log->tags)
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-muted">Tags:</span>
                                    <div>
                                        @foreach(explode(',', $log->tags) as $tag)
                                            <span class="badge bg-info me-1">{{ trim($tag) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Card Footer with Actions -->
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Last updated: {{ $log->updated_at->format('M d, Y h:i A') }}
                    </small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to List
                    </a>
                    @if($log->auditable)
                        <a href="{{ route('audit.edit', ['entityType'=>$log->auditable_type, 'entityId'=>$log->auditable_id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock-history me-1"></i>View History
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for better visual appeal */
.card {
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.border-3 {
    border-width: 3px !important;
}

pre code {
    background: transparent;
    border: none;
    padding: 0;
    font-size: 0.8rem;
    line-height: 1.4;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

/* Print styles */
@media print {
    .btn, .dropdown, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .d-flex.justify-content-between > * {
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

/* Copy animation */
.copy-success {
    animation: copySuccess 0.3s ease-in-out;
}

@keyframes copySuccess {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy to clipboard functionality
    window.copyToClipboard = function() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            // Show success message
            const btn = event.target.closest('a');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check me-2"></i>Copied!';
            btn.classList.add('copy-success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('copy-success');
            }, 2000);
        }).catch(function(err) {
            alert('Failed to copy URL to clipboard');
        });
    };

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add syntax highlighting to JSON code blocks (if highlight.js is available)
    if (typeof hljs !== 'undefined') {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    }

    // Add expandable functionality for long values
    document.querySelectorAll('pre code').forEach(function(element) {
        if (element.textContent.length > 500) {
            const wrapper = document.createElement('div');
            wrapper.className = 'expandable-content';
            
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
            toggleBtn.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Show More';
            
            element.style.maxHeight = '100px';
            element.style.overflow = 'hidden';
            
            toggleBtn.addEventListener('click', function() {
                if (element.style.maxHeight === '100px') {
                    element.style.maxHeight = 'none';
                    this.innerHTML = '<i class="bi bi-chevron-up me-1"></i>Show Less';
                } else {
                    element.style.maxHeight = '100px';
                    this.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Show More';
                }
            });
            
            element.parentNode.appendChild(toggleBtn);
        }
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        
        // Escape to go back
        if (e.key === 'Escape') {
            window.history.back();
        }
    });
});
</script>

@endsection
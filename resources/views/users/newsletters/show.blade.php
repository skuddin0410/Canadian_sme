@extends('layouts.admin')

@section('content')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-4">

    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body mt-3">
            <div class="row align-items-start">
                <div class="col-md-8">
                    <h2 class="h3 fw-bold text-dark mb-2">
                        <i class="bi bi-envelope-fill me-2 text-primary"></i>
                        {{ $newsletter->subject }}
                    </h2>
                    <div class="text-muted mb-3">
                        <i class="bi bi-calendar3 me-1"></i>
                        <small>{{ ucwords(str_replace('_', ' ', $newsletter->template_name)) }} • Created {{ $newsletter->created_at->format('M j, Y \a\t g:i A') }}</small>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge fs-6 px-3 py-2
                        @if($newsletter->status === 'sent') bg-success
                        @elseif($newsletter->status === 'sending') bg-primary
                        @elseif($newsletter->status === 'scheduled') bg-warning text-dark
                        @elseif($newsletter->status === 'failed') bg-danger
                        @else bg-secondary
                        @endif">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.6em;"></i>
                        {{ ucfirst($newsletter->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card-footer bg-light border-top">
            <div class="row g-2">
                @if($newsletter->canBeSent())
                <div class="col-auto">
                    <form action="{{ route('newsletters.send', $newsletter) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to send this newsletter now? This action cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send-fill me-1"></i>Send Now
                        </button>
                    </form>
                </div>

                <div class="col-auto">
                    <a href="{{ route('newsletters.edit', $newsletter) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                @endif

                <div class="col-auto">
                    <a href="{{ route('newsletters.preview', $newsletter) }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i>Preview
                    </a>
                </div>

                <div class="col-auto">
                    <button onclick="openPreviewPopup()" class="btn btn-info">
                        <i class="bi bi-window-stack me-1"></i>Preview in Popup
                    </button>
                </div>

                <div class="col-auto">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#testSendModal">
                        <i class="bi bi-envelope-plus me-1"></i>Test Send
                    </button>
                </div>

                <div class="col-auto ms-auto">
                    <a href="{{ route('newsletters.index') }}" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-people-fill fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ number_format($analytics['total_recipients']) }}</h3>
                    <p class="text-muted mb-0 small">Total Recipients</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ number_format($analytics['sent_count']) }}</h3>
                    <p class="text-muted mb-0 small">Successfully Sent</p>
                    @if($analytics['delivery_rate'] > 0)
                    <div class="badge bg-success bg-opacity-10 text-success mt-1">
                        {{ $analytics['delivery_rate'] }}% delivery rate
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-envelope-open-fill fs-4 text-info"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ number_format($analytics['open_count']) }}</h3>
                    <p class="text-muted mb-0 small">Email Opens</p>
                    @if($analytics['open_rate'] > 0)
                    <div class="badge bg-info bg-opacity-10 text-info mt-1">
                        {{ $analytics['open_rate'] }}% open rate
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-cursor-fill fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ number_format($analytics['click_count']) }}</h3>
                    <p class="text-muted mb-0 small">Link Clicks</p>
                    @if($analytics['click_rate'] > 0)
                    <div class="badge bg-warning bg-opacity-10 text-warning mt-1">
                        {{ $analytics['click_rate'] }}% click rate
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Send Status Breakdown -->
    @if($newsletter->status !== 'draft')
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                <h5 class="mb-0 fw-bold">Send Status Breakdown</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($analytics['sends_by_status'] as $status => $count)
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-medium text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                <span class="badge 
                                    @if($status === 'sent') bg-success
                                    @elseif($status === 'opened') bg-primary
                                    @elseif($status === 'clicked') bg-info
                                    @elseif($status === 'failed') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ number_format($count) }}
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar 
                                    @if($status === 'sent') bg-success
                                    @elseif($status === 'opened') bg-primary
                                    @elseif($status === 'clicked') bg-info
                                    @elseif($status === 'failed') bg-danger
                                    @else bg-secondary
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $analytics['total_recipients'] > 0 ? ($count / $analytics['total_recipients']) * 100 : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $analytics['total_recipients'] > 0 ? number_format(($count / $analytics['total_recipients']) * 100, 1) : 0 }}% of total
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Newsletter Content -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-text-fill me-2 text-primary"></i>
                <h5 class="mb-0 fw-bold">Newsletter Content</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="newsletter-content p-3 bg-light rounded border">
                {!! $newsletter->content !!}
            </div>
            
            @if($newsletter->template_data)
            <hr class="my-4">
            <div class="row">
                <div class="col-12">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-code-square me-1"></i>Template Data
                    </h6>
                    <div class="bg-dark text-light p-3 rounded">
                        <pre class="mb-0 text-light small" style="white-space: pre-wrap;">{{ json_encode($newsletter->template_data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Sends -->
    @if($newsletter->sends->isNotEmpty())
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    <h5 class="mb-0 fw-bold">Recent Sends</h5>
                </div>
                <span class="badge bg-primary">Last 20</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-medium">
                                <i class="bi bi-envelope me-1"></i>Email
                            </th>
                            <th class="border-0 fw-medium">
                                <i class="bi bi-info-circle me-1"></i>Status
                            </th>
                            <th class="border-0 fw-medium">
                                <i class="bi bi-send me-1"></i>Sent At
                            </th>
                            <th class="border-0 fw-medium">
                                <i class="bi bi-envelope-open me-1"></i>Opened At
                            </th>
                            <th class="border-0 fw-medium">
                                <i class="bi bi-cursor me-1"></i>Clicked At
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newsletter->sends->take(20) as $send)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-fill text-secondary"></i>
                                    </div>
                                    <span class="fw-medium">{{ $send->email }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge 
                                    @if($send->status === 'sent') bg-success
                                    @elseif($send->status === 'opened') bg-primary
                                    @elseif($send->status === 'clicked') bg-info
                                    @elseif($send->status === 'failed') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($send->status) }}
                                </span>
                            </td>
                            <td class="align-middle text-muted">
                                {{ $send->sent_at?->format('M j, Y H:i') ?? '-' }}
                            </td>
                            <td class="align-middle text-muted">
                                {{ $send->opened_at?->format('M j, Y H:i') ?? '-' }}
                            </td>
                            <td class="align-middle text-muted">
                                {{ $send->clicked_at?->format('M j, Y H:i') ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Test Send Modal -->
<div class="modal fade" id="testSendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-plus me-2"></i>Send Test Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('newsletters.test-send', $newsletter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Test Email Address</label>
                        <input type="email" name="test_email" class="form-control" placeholder="test@example.com" required>
                        <div class="form-text">A test version of this newsletter will be sent to this email address.</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-send me-1"></i>Send Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Custom styles */
.newsletter-content {
    line-height: 1.6;
}

.newsletter-content h1,
.newsletter-content h2,
.newsletter-content h3,
.newsletter-content h4,
.newsletter-content h5,
.newsletter-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.newsletter-content p {
    margin-bottom: 1rem;
}

.newsletter-content ul,
.newsletter-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.newsletter-content blockquote {
    border-left: 4px solid #dee2e6;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.badge {
    font-weight: 500;
}

.btn {
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Loading animation */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-footer .row > div {
        margin-bottom: 0.5rem;
    }
    
    .analytics-card h3 {
        font-size: 1.5rem;
    }
}

/* Dark theme for JSON display */
pre {
    background-color: #2d3748 !important;
    color: #e2e8f0 !important;
    border: none;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
    overflow-x: auto;
}

/* Animation for status badges */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.badge {
    animation: pulse 2s infinite;
}

.badge:hover {
    animation: none;
    transform: scale(1.1);
}
</style>

<script>
function openPreviewPopup() {
    const url = "{{ route('newsletters.preview-popup', $newsletter) }}";
    const popup = window.open(url, 'newsletter-preview', 'width=800,height=900,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no');
    
    if (popup) {
        popup.focus();
    } else {
        // Show Bootstrap toast notification instead of alert
        showToast('Please allow popups for this site to open the preview window.', 'warning');
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${
        type === 'success' ? 'success' : 
        type === 'error' ? 'danger' : 
        type === 'warning' ? 'warning' :
        'primary'
    } border-0`;
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Show toast using Bootstrap
    const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Add loading state to send buttons
document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn && confirm(this.getAttribute('onsubmit').match(/'([^']+)'/)[1])) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        } else {
            e.preventDefault();
        }
    });
});

// Add loading state to test send form
document.querySelector('#testSendModal form').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending Test...';
    
    // Reset button after 5 seconds as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send me-1"></i>Send Test';
    }, 5000);
});

// Initialize tooltips if any
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
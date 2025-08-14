@extends('layouts.admin')

@section('title', 'Edit Newsletter - ' . $newsletter->subject)

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="card mb-4 mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h3 mb-1">Edit Newsletter</h2>
                    <p class="text-muted mb-0">
                        Editing: <span class="fw-medium">{{ $newsletter->subject }}</span> • 
                        Created {{ $newsletter->created_at->format('M j, Y') }}
                    </p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge 
                        @if($newsletter->status === 'draft') bg-secondary
                        @elseif($newsletter->status === 'scheduled') bg-warning
                        @else bg-primary
                        @endif">
                        {{ ucfirst($newsletter->status) }}
                    </span>
                </div>
            </div>

            @if(!$newsletter->canBeSent())
            <div class="alert alert-warning d-flex align-items-start mb-3" role="alert">
                <svg class="me-2 flex-shrink-0" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                    This newsletter cannot be edited because it's currently {{ $newsletter->status }}. 
                    @if($newsletter->status === 'sent')
                        Once sent, newsletters become read-only.
                    @elseif($newsletter->status === 'sending')
                        Please wait for the sending process to complete.
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($newsletter->canBeSent())
    <form action="{{ route('newsletters.update', $newsletter) }}" method="POST" id="newsletterForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8 mb-4">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Subject Line <span class="text-danger">*</span></label>
                                <input type="text" name="subject" value="{{ old('subject', $newsletter->subject) }}" required
                                       class="form-control @error('subject') is-invalid @enderror"
                                       placeholder="Enter newsletter subject">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Template <span class="text-danger">*</span></label>
                                <select name="template_name" required onchange="updateTemplatePreview()"
                                        class="form-select @error('template_name') is-invalid @enderror">
                                    @foreach($templates as $key => $template)
                                    <option value="{{ $key }}" {{ old('template_name', $newsletter->template_name) === $key ? 'selected' : '' }}>
                                        {{ $template['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('template_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Content Editor -->
                        <div class="mb-3">
                            <label class="form-label">Newsletter Content <span class="text-danger">*</span></label>
                            <div class="border rounded">
                                <!-- Editor Toolbar -->
                                <div class="bg-light p-2 border-bottom d-flex flex-wrap gap-1">
                                    <button type="button" onclick="formatText('bold')" class="btn btn-sm btn-outline-secondary" title="Bold">
                                        <strong>B</strong>
                                    </button>
                                    <button type="button" onclick="formatText('italic')" class="btn btn-sm btn-outline-secondary" title="Italic">
                                        <em>I</em>
                                    </button>
                                    <button type="button" onclick="formatText('underline')" class="btn btn-sm btn-outline-secondary" title="Underline">
                                        <u>U</u>
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" onclick="formatText('insertUnorderedList')" class="btn btn-sm btn-outline-secondary" title="Bullet List">
                                        •
                                    </button>
                                    <button type="button" onclick="formatText('insertOrderedList')" class="btn btn-sm btn-outline-secondary" title="Numbered List">
                                        1.
                                    </button>
                                    <button type="button" onclick="insertLink()" class="btn btn-sm btn-outline-secondary" title="Insert Link">
                                        🔗
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <select onchange="formatText('formatBlock', this.value)" class="form-select form-select-sm" style="width: auto;">
                                        <option value="">Format</option>
                                        <option value="h2">Heading 2</option>
                                        <option value="h3">Heading 3</option>
                                        <option value="p">Paragraph</option>
                                    </select>
                                </div>
                                
                                <!-- Content Editor -->
                                <div id="contentEditor" contenteditable="true" 
                                     class="p-3" 
                                     style="min-height: 320px; max-height: 400px; overflow-y: auto;">
                                    {!! old('content', $newsletter->content) !!}
                                </div>
                                
                                <!-- Hidden input to store content -->
                                <input type="hidden" name="content" id="contentInput" value="{{ old('content', $newsletter->content) }}">
                            </div>
                            @error('content')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Use the toolbar above to format your content. HTML tags are supported.</div>
                        </div>

                        <!-- Sending Options -->
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Sending Options</h6>
                            
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_option" value="draft" id="draft" {{$newsletter->status == 'draft' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="draft">
                                        Save as Draft
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_option" value="send_now" id="send_now" {{$newsletter->status == 'sending' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="send_now">
                                        Send Immediately
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_option" value="schedule" id="schedule" {{$newsletter->status == 'scheduled' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="schedule">
                                        Schedule for Later
                                    </label>
                                </div>
                                @if($newsletter->scheduled_at)
                                <div class="text-muted small">
                                    Scheduled at: {{$newsletter->scheduled_at}}
                                </div>
                                @endif
                            </div>

                            <div id="scheduleFields" style="display: none;" class="mt-3">
                                <label class="form-label">Schedule Date & Time</label>
                                <input type="datetime-local" name="scheduled_at" 
                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Preview Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Preview & Actions</h6>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <button type="button" onclick="openPreview()" 
                                class="btn btn-primary">
                            Preview Newsletter
                        </button>
                        
                        <button type="button" onclick="showTestSendModal()" 
                                class="btn btn-secondary">
                            Send Test Email
                        </button>
                        
                        <hr>
                        
                        <button type="submit" class="btn btn-success">
                            Save Changes
                        </button>
                    </div>
                </div>

                <!-- Recipient Targeting -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Recipient Targeting</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Target Audience</label>
                            @php
                                $currentTypes = old('recipient_criteria.recipient_types', $newsletter->recipient_criteria['recipient_types'] ?? ['subscribers']);
                            @endphp
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recipient_criteria[recipient_types][]" value="subscribers" 
                                           {{ in_array('subscribers', $currentTypes) ? 'checked' : '' }}
                                           id="subscribers">
                                    <label class="form-check-label" for="subscribers">
                                        Newsletter Subscribers
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recipient_criteria[recipient_types][]" value="leads" 
                                           {{ in_array('leads', $currentTypes) ? 'checked' : '' }}
                                           id="leads">
                                    <label class="form-check-label" for="leads">
                                        Active Leads
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recipient_criteria[recipient_types][]" value="users" 
                                           {{ in_array('users', $currentTypes) ? 'checked' : '' }}
                                           id="users">
                                    <label class="form-check-label" for="users">
                                        Registered Users
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small">Min Lead Score</label>
                                <input type="number" name="recipient_criteria[min_lead_score]" 
                                       value="{{ old('recipient_criteria.min_lead_score', $newsletter->recipient_criteria['min_lead_score'] ?? '') }}"
                                       min="0" max="100" placeholder="0"
                                       class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Max Lead Score</label>
                                <input type="number" name="recipient_criteria[max_lead_score]" 
                                       value="{{ old('recipient_criteria.max_lead_score', $newsletter->recipient_criteria['max_lead_score'] ?? '') }}"
                                       min="0" max="100" placeholder="100"
                                       class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Active in Last (days)</label>
                            <input type="number" name="recipient_criteria[min_activity_days]" 
                                   value="{{ old('recipient_criteria.min_activity_days', $newsletter->recipient_criteria['min_activity_days'] ?? '') }}"
                                   placeholder="30"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <!-- Newsletter Statistics -->
                @if($newsletter->status !== 'draft')
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Current Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Recipients:</span>
                            <span class="fw-medium small">{{ number_format($newsletter->total_recipients) }}</span>
                        </div>
                        @if($newsletter->sent_count > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Sent:</span>
                            <span class="fw-medium small">{{ number_format($newsletter->sent_count) }}</span>
                        </div>
                        @endif
                        @if($newsletter->status === 'scheduled')
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Scheduled:</span>
                            <span class="fw-medium small">{{ $newsletter->scheduled_at->format('M j, g:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('newsletters.show', $newsletter) }}" 
                           class="btn btn-outline-secondary btn-sm">
                            View Details
                        </a>
                        
                        <button type="button" onclick="duplicateNewsletter()" 
                                class="btn btn-outline-secondary btn-sm">
                            Duplicate Newsletter
                        </button>
                        
                        <a href="{{ route('newsletters.index') }}" 
                           class="btn btn-outline-secondary btn-sm">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <!-- Read-only view for non-editable newsletters -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Newsletter Content (Read-Only)</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Subject</label>
                        <div class="p-3 bg-light border rounded">{{ $newsletter->subject }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Template</label>
                        <div class="p-3 bg-light border rounded">{{ ucwords(str_replace('_', ' ', $newsletter->template_name)) }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Content</label>
                        <div class="p-3 bg-light border rounded">
                            {!! $newsletter->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Available Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('newsletters.preview', $newsletter) }}" target="_blank"
                       class="btn btn-primary">
                        Preview Newsletter
                    </a>
                    <a href="{{ route('newsletters.show', $newsletter) }}" 
                       class="btn btn-outline-secondary">
                        View Details
                    </a>
                </div>
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
                <h5 class="modal-title">Send Test Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('newsletters.test-send', $newsletter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Test Email Address</label>
                        <input type="email" name="test_email" required
                               class="form-control"
                               placeholder="test@example.com">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Data Templates (Hidden) -->
<div id="templateDataTemplates" style="display: none;">
    <!-- Market Update Template -->
    <div data-template="market_update">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small">Call-to-Action URL</label>
                <input type="url" name="template_data[cta_url]" 
                       value="{{ old('template_data.cta_url', $newsletter->template_data['cta_url'] ?? '') }}"
                       placeholder="https://example.com/properties"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">CTA Button Text</label>
                <input type="text" name="template_data[cta_text]" 
                       value="{{ old('template_data.cta_text', $newsletter->template_data['cta_text'] ?? '') }}"
                       placeholder="View Properties"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">Market Summary</label>
                <textarea name="template_data[market_summary]" rows="3"
                          class="form-control form-control-sm"
                          placeholder="Brief market overview...">{{ old('template_data.market_summary', $newsletter->template_data['market_summary'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- New Properties Template -->
    <div data-template="new_properties">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small">Introduction Title</label>
                <input type="text" name="template_data[intro_title]" 
                       value="{{ old('template_data.intro_title', $newsletter->template_data['intro_title'] ?? '') }}"
                       placeholder="Featured Properties This Week"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">Introduction Message</label>
                <textarea name="template_data[intro_message]" rows="2"
                          class="form-control form-control-sm"
                          placeholder="Brief introduction to new properties...">{{ old('template_data.intro_message', $newsletter->template_data['intro_message'] ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label small">CTA Title</label>
                <input type="text" name="template_data[cta_title]" 
                       value="{{ old('template_data.cta_title', $newsletter->template_data['cta_title'] ?? '') }}"
                       placeholder="Ready to Invest?"
                       class="form-control form-control-sm">
            </div>
        </div>
    </div>

    <!-- Investment Tips Template -->
    <div data-template="investment_tips">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small">Issue Number</label>
                <input type="text" name="template_data[issue_number]" 
                       value="{{ old('template_data.issue_number', $newsletter->template_data['issue_number'] ?? 'Issue #' . now()->weekOfYear) }}"
                       placeholder="Issue #47"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">Featured Tip Title</label>
                <input type="text" name="template_data[featured_tip][title]" 
                       value="{{ old('template_data.featured_tip.title', $newsletter->template_data['featured_tip']['title'] ?? '') }}"
                       placeholder="Understanding Cap Rates"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">Tip Category</label>
                <select name="template_data[featured_tip][category]" 
                        class="form-select form-select-sm">
                    <option value="">Select category...</option>
                    <option value="Investment Strategy" {{ old('template_data.featured_tip.category', $newsletter->template_data['featured_tip']['category'] ?? '') === 'Investment Strategy' ? 'selected' : '' }}>Investment Strategy</option>
                    <option value="Market Analysis" {{ old('template_data.featured_tip.category', $newsletter->template_data['featured_tip']['category'] ?? '') === 'Market Analysis' ? 'selected' : '' }}>Market Analysis</option>
                    <option value="Financing" {{ old('template_data.featured_tip.category', $newsletter->template_data['featured_tip']['category'] ?? '') === 'Financing' ? 'selected' : '' }}>Financing</option>
                    <option value="Property Management" {{ old('template_data.featured_tip.category', $newsletter->template_data['featured_tip']['category'] ?? '') === 'Property Management' ? 'selected' : '' }}>Property Management</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Default/Common Template -->
    <div data-template="default">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small">Call-to-Action URL</label>
                <input type="url" name="template_data[cta_url]" 
                       value="{{ old('template_data.cta_url', $newsletter->template_data['cta_url'] ?? '') }}"
                       placeholder="https://example.com"
                       class="form-control form-control-sm">
            </div>
            <div class="col-12">
                <label class="form-label small">CTA Button Text</label>
                <input type="text" name="template_data[cta_text]" 
                       value="{{ old('template_data.cta_text', $newsletter->template_data['cta_text'] ?? '') }}"
                       placeholder="Learn More"
                       class="form-control form-control-sm">
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Content Editor Styles */
#contentEditor {
    border: none;
    outline: none;
}

#contentEditor:focus {
    box-shadow: inset 0 0 0 2px #0d6efd;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
}

.btn-outline-secondary.active {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
}

/* Content styling */
#contentEditor h2 {
    font-size: 1.5em;
    font-weight: 600;
    margin: 1em 0 0.5em 0;
    color: #212529;
}

#contentEditor h3 {
    font-size: 1.25em;
    font-weight: 600;
    margin: 1em 0 0.5em 0;
    color: #212529;
}

#contentEditor p {
    margin: 0.75em 0;
    line-height: 1.6;
}

#contentEditor ul, #contentEditor ol {
    margin: 0.75em 0;
    padding-left: 1.5em;
}

#contentEditor li {
    margin: 0.25em 0;
}

#contentEditor a {
    color: #0d6efd;
    text-decoration: underline;
}

/* Form improvements */
.form-label {
    font-weight: 500;
}

.text-danger {
    color: #dc3545 !important;
}

/* Animation for template data section */
#templateDataSection {
    transition: all 0.3s ease-in-out;
}

.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-outline-secondary {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Custom scrollbar for content editor */
#contentEditor::-webkit-scrollbar {
    width: 6px;
}

#contentEditor::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#contentEditor::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#contentEditor::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Template preview data
const templates = @json($templates);

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Update content input when editor content changes
    const contentEditor = document.getElementById('contentEditor');
    const contentInput = document.getElementById('contentInput');
    
    if (contentEditor && contentInput) {
        contentEditor.addEventListener('input', function() {
            contentInput.value = contentEditor.innerHTML;
        });
        
        // Initialize content input with current editor content
        contentInput.value = contentEditor.innerHTML;
    }
    
    // Initialize template fields
    updateTemplatePreview();
    
    // Auto-save functionality (optional)
    setInterval(autoSave, 30000); // Auto-save every 30 seconds
});

// Content editor formatting functions
function formatText(command, value = null) {
    document.execCommand(command, false, value);
    document.getElementById('contentEditor').focus();
    
    // Update the hidden input
    document.getElementById('contentInput').value = document.getElementById('contentEditor').innerHTML;
}

function insertLink() {
    const url = prompt('Enter the URL:');
    if (url) {
        formatText('createLink', url);
    }
}

// Template management functions
function updateTemplatePreview() {
    const templateSelect = document.querySelector('select[name="template_name"]');
    const templateDataSection = document.getElementById('templateDataSection');
    const templateFields = document.getElementById('templateFields');
    
    if (!templateSelect || !templateFields) return;
    
    const selectedTemplate = templateSelect.value;
    
    // Clear existing fields
    templateFields.innerHTML = '';
    
    // Find the template data template
    const templateElement = document.querySelector(`[data-template="${selectedTemplate}"]`);
    
    if (templateElement) {
        templateFields.innerHTML = templateElement.innerHTML;
        if (templateDataSection) {
            templateDataSection.classList.add('fade-in');
            
            // Remove animation class after animation completes
            setTimeout(() => {
                templateDataSection.classList.remove('fade-in');
            }, 300);
        }
    }
}

// Modal functions
function showTestSendModal() {
    const modal = new bootstrap.Modal(document.getElementById('testSendModal'));
    modal.show();
}

function hideTestSendModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('testSendModal'));
    if (modal) {
        modal.hide();
    }
}
function sendTestEmail(newsletterId, testEmail) {
    fetch(`/newsletter/${newsletterId}/send-test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_email: testEmail })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred while sending the test email.');
    });
}


// Preview function
function openPreview() {
    // Save current content before preview
    const contentEditor = document.getElementById('contentEditor');
    const contentInput = document.getElementById('contentInput');
    
    if (contentEditor && contentInput) {
        contentInput.value = contentEditor.innerHTML;
    }
    
    // Open preview in new tab
    const previewUrl = "{{ route('newsletters.preview', $newsletter) }}";
    window.open(previewUrl, 'newsletter-preview', 'width=800,height=900,scrollbars=yes,resizable=yes');
}

// Auto-save functionality
function autoSave() {
    if (!document.querySelector('form#newsletterForm')) return;
    
    const form = document.getElementById('newsletterForm');
    const formData = new FormData(form);
    
    // Update content input before saving
    const contentEditor = document.getElementById('contentEditor');
    const contentInput = document.getElementById('contentInput');
    
    if (contentEditor && contentInput) {
        contentInput.value = contentEditor.innerHTML;
        formData.set('content', contentEditor.innerHTML);
    }
    
    // Add auto-save flag
    formData.append('auto_save', '1');
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Draft auto-saved', 'success');
        }
    })
    .catch(error => {
        console.log('Auto-save failed:', error);
    });
}

// Duplicate newsletter function
function duplicateNewsletter() {
    if (confirm('Create a copy of this newsletter?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('newsletters.store') }}";
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfToken);
        
        // Add duplicate flag
        const duplicateFlag = document.createElement('input');
        duplicateFlag.type = 'hidden';
        duplicateFlag.name = 'duplicate_from';
        duplicateFlag.value = "{{ $newsletter->id }}";
        form.appendChild(duplicateFlag);
        
        // Copy current form data
        const currentForm = document.getElementById('newsletterForm');
        if (currentForm) {
            const formData = new FormData(currentForm);
            
            // Update content before duplicating
            const contentEditor = document.getElementById('contentEditor');
            if (contentEditor) {
                formData.set('content', contentEditor.innerHTML);
            }
            
            for (let [key, value] of formData.entries()) {
                if (key !== '_token' && key !== '_method') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            }
        }
        
        // Modify subject to indicate it's a copy
        const subjectInput = form.querySelector('input[name="subject"]');
        if (subjectInput) {
            subjectInput.value = 'Copy of ' + subjectInput.value;
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Form submission handler
document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
    // Update content input before submitting
    const contentEditor = document.getElementById('contentEditor');
    const contentInput = document.getElementById('contentInput');
    
    if (contentEditor && contentInput) {
        contentInput.value = contentEditor.innerHTML;
    }
    
    // Validate required fields
    const subject = document.querySelector('input[name="subject"]').value.trim();
    const template = document.querySelector('select[name="template_name"]').value;
    const content = contentInput?.value.trim() || '';
    
    if (!subject) {
        e.preventDefault();
        showToast('Please enter a subject line.', 'error');
        return false;
    }
    
    if (!template) {
        e.preventDefault();
        showToast('Please select a template.', 'error');
        return false;
    }
    
    if (!content || content === '<div><br></div>' || content === '<br>' || content === '') {
        e.preventDefault();
        showToast('Please enter newsletter content.', 'error');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
        
        // Re-enable after 10 seconds as fallback
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }, 10000);
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.getElementById('newsletterForm')?.submit();
        return false;
    }
    
    // Ctrl/Cmd + P to preview
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        openPreview();
        return false;
    }
});

// Toast notification function (Bootstrap-style)
function showToast(message, type = 'info') {
    // Remove any existing toasts
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
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
    toast.className = `toast toast-notification align-items-center text-bg-${
        type === 'success' ? 'success' : 
        type === 'error' ? 'danger' : 
        'primary'
    } border-0`;
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Show toast using Bootstrap
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Unsaved changes warning
let hasUnsavedChanges = false;

document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('change', function() {
        hasUnsavedChanges = true;
    });
});

document.getElementById('contentEditor')?.addEventListener('input', function() {
    hasUnsavedChanges = true;
});

document.getElementById('newsletterForm')?.addEventListener('submit', function() {
    hasUnsavedChanges = false;
});

window.addEventListener('beforeunload', function(e) {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});

// Rich text editor enhancements
document.getElementById('contentEditor')?.addEventListener('paste', function(e) {
    // Handle paste events to clean up formatting
    e.preventDefault();
    
    const text = (e.clipboardData || window.clipboardData).getData('text/html') || 
                 (e.clipboardData || window.clipboardData).getData('text/plain');
    
    // Clean HTML and insert
    const cleanText = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                         .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');
    
    document.execCommand('insertHTML', false, cleanText);
    
    // Update hidden input
    document.getElementById('contentInput').value = this.innerHTML;
});

// Add placeholder text to empty editor
document.getElementById('contentEditor')?.addEventListener('focus', function() {
    if (this.innerHTML.trim() === '' || this.innerHTML === '<div><br></div>' || 
        this.textContent === 'Start writing your newsletter content here...') {
        this.innerHTML = '';
        this.style.color = '';
    }
});

document.getElementById('contentEditor')?.addEventListener('blur', function() {
    if (this.innerHTML.trim() === '' || this.innerHTML === '<div><br></div>') {
        this.innerHTML = '<p class="text-muted">Start writing your newsletter content here...</p>';
    }
    
    // Update hidden input
    document.getElementById('contentInput').value = this.innerHTML;
});

// Initialize placeholder if editor is empty
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('contentEditor');
    if (editor && (editor.innerHTML.trim() === '' || editor.innerHTML === '<div><br></div>')) {
        editor.innerHTML = '<p class="text-muted">Start writing your newsletter content here...</p>';
    }
});

// Handle send option changes
document.querySelectorAll('input[name="send_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const scheduleFields = document.getElementById('scheduleFields');
        const sendNowInput = document.querySelector('input[name="send_immediately"]');
        
        if (this.value === 'schedule') {
            scheduleFields.style.display = 'block';
        } else {
            scheduleFields.style.display = 'none';
        }
        
        // Set hidden field for immediate sending
        if (sendNowInput) {
            sendNowInput.value = this.value === 'send_now' ? '1' : '0';
        } else if (this.value === 'send_now') {
            // Create hidden field if it doesn't exist
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'send_immediately';
            hiddenInput.value = '1';
            document.querySelector('form').appendChild(hiddenInput);
        }
    });
});

// Initialize send options on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedOption = document.querySelector('input[name="send_option"]:checked');
    if (checkedOption) {
        checkedOption.dispatchEvent(new Event('change'));
    }
});

// Toolbar button active states
document.querySelectorAll('.btn-outline-secondary').forEach(btn => {
    btn.addEventListener('click', function() {
        // Toggle active state for formatting buttons
        if (this.onclick && this.onclick.toString().includes('formatText')) {
            this.classList.toggle('active');
            setTimeout(() => {
                this.classList.remove('active');
            }, 200);
        }
    });
});

// Content editor focus management
document.getElementById('contentEditor')?.addEventListener('keydown', function(e) {
    // Handle tab key for better UX
    if (e.key === 'Tab') {
        e.preventDefault();
        document.execCommand('insertHTML', false, '&nbsp;&nbsp;&nbsp;&nbsp;');
    }
    
    // Handle enter key to maintain formatting
    if (e.key === 'Enter' && !e.shiftKey) {
        // Let default behavior happen, but ensure we update the hidden input
        setTimeout(() => {
            document.getElementById('contentInput').value = this.innerHTML;
        }, 10);
    }
});

// Auto-resize content editor based on content
function adjustEditorHeight() {
    const editor = document.getElementById('contentEditor');
    if (editor) {
        const minHeight = 320;
        const maxHeight = 600;
        
        editor.style.height = 'auto';
        const scrollHeight = editor.scrollHeight;
        
        if (scrollHeight > minHeight && scrollHeight < maxHeight) {
            editor.style.height = scrollHeight + 'px';
            editor.style.overflowY = 'hidden';
        } else if (scrollHeight >= maxHeight) {
            editor.style.height = maxHeight + 'px';
            editor.style.overflowY = 'auto';
        } else {
            editor.style.height = minHeight + 'px';
            editor.style.overflowY = 'hidden';
        }
    }
}

// Trigger height adjustment on input
document.getElementById('contentEditor')?.addEventListener('input', adjustEditorHeight);

// Initial height adjustment
document.addEventListener('DOMContentLoaded', adjustEditorHeight);

// Handle form validation with Bootstrap classes
function validateForm() {
    const form = document.getElementById('newsletterForm');
    let isValid = true;
    
    // Clear previous validation states
    form.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Validate subject
    const subject = form.querySelector('input[name="subject"]');
    if (!subject.value.trim()) {
        subject.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate template
    const template = form.querySelector('select[name="template_name"]');
    if (!template.value) {
        template.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate content
    const contentInput = document.getElementById('contentInput');
    const contentEditor = document.getElementById('contentEditor');
    if (contentInput) {
        contentInput.value = contentEditor.innerHTML;
        if (!contentInput.value.trim() || 
            contentInput.value === '<div><br></div>' || 
            contentInput.value.includes('Start writing your newsletter content here')) {
            contentEditor.classList.add('is-invalid');
            isValid = false;
        }
    }
    
    return isValid;
}

// Real-time validation feedback
document.querySelectorAll('#newsletterForm input, #newsletterForm select, #newsletterForm textarea').forEach(element => {
    element.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    element.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') && this.value.trim()) {
            this.classList.remove('is-invalid');
        }
    });
});

// Content editor validation
document.getElementById('contentEditor')?.addEventListener('blur', function() {
    const content = this.innerHTML.trim();
    if (!content || content === '<div><br></div>' || content.includes('Start writing your newsletter content here')) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

document.getElementById('contentEditor')?.addEventListener('input', function() {
    const content = this.innerHTML.trim();
    if (this.classList.contains('is-invalid') && content && 
        content !== '<div><br></div>' && 
        !content.includes('Start writing your newsletter content here')) {
        this.classList.remove('is-invalid');
    }
});
</script>
@endsection
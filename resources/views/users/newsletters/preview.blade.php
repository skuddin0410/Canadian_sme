@extends('layouts.preview')

@section('title', 'Newsletter Preview: ' . $newsletter->subject)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Preview Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="window.close()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">Newsletter Preview</h1>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Device Preview Toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button onclick="setPreviewMode('desktop')" 
                                class="preview-btn px-3 py-1 text-sm rounded-md active" data-mode="desktop">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Desktop
                        </button>
                        <button onclick="setPreviewMode('mobile')" 
                                class="preview-btn px-3 py-1 text-sm rounded-md" data-mode="mobile">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"></path>
                            </svg>
                            Mobile
                        </button>
                    </div>

                    <!-- Test Send Button -->
                    <button onclick="showTestSendModal()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                        Send Test Email
                    </button>
                    
                    <!-- Close Preview -->
                    <a href="{{ route('newsletters.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm">
                        Back to Newsletter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Content -->
    <div class="max-w-4xl mx-auto p-4">
        <!-- Email Client Header Simulation -->
        <div class="bg-white rounded-t-lg border border-gray-200 p-4 mb-0">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">RE</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{config('app.name')}}</div>
                        <div class="text-sm text-gray-500">{{config('mail.admin_email')}}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ now()->format('M j, Y g:i A') }}
                </div>
            </div>
            <div class="pt-3">
                <div class="text-lg font-semibold text-gray-900">{{ $newsletter->subject }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    To: {{config('mail.admin_email')}}
                </div>
            </div>
        </div>

        <!-- Email Content Preview Container -->
        <div id="preview-container" class="transition-all duration-300">
            <div id="email-preview" class="bg-white border-l border-r border-b border-gray-200 rounded-b-lg">
                <!-- Actual Email Content -->
                @include('emails.newsletters.' . ($newsletter->template_name ?: 'default'), [
                    'newsletter' => $newsletter,
                    'content' => $newsletter->content,
                    'templateData' => $newsletter->template_data ?? [],
                    'trackingPixelUrl' => route('emails.track', ['email' => $email ?? '']),
                    'unsubscribeUrl' => route('newsletter.unsubscribe'),
                    'recipientEmail' => 'preview@example.com'
                ])
            </div>
        </div>

        <!-- Preview Info Panel -->
        <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Newsletter Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Subject:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $newsletter->subject }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Template:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ ucwords(str_replace('_', ' ', $newsletter->template_name)) }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                            @if($newsletter->status === 'sent') bg-green-100 text-green-800
                            @elseif($newsletter->status === 'sending') bg-blue-100 text-blue-800
                            @elseif($newsletter->status === 'scheduled') bg-yellow-100 text-yellow-800
                            @elseif($newsletter->status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($newsletter->status) }}
                        </span>
                    </div>
                    @if($newsletter->scheduled_at)
                    <div>
                        <span class="text-sm font-medium text-gray-700">Scheduled:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $newsletter->scheduled_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Recipients:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ number_format($newsletter->total_recipients) }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Created:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $newsletter->created_at ? $newsletter->created_at->format('M j, Y g:i A') :'' }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Creator:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $newsletter->creator->name }}</span>
                    </div>
                    @if($newsletter->template_data)
                    <div>
                        <span class="text-sm font-medium text-gray-700">Template Data:</span>
                        <button onclick="toggleTemplateData()" class="text-sm text-blue-600 hover:text-blue-800 ml-2">
                            View Details
                        </button>
                    </div>
                    @endif
                </div>
            </div>

       {{--      @if($newsletter->template_data)
            <div id="template-data" class="mt-4 p-4 bg-gray-50 rounded-lg" style="display: none;">
                <h4 class="font-medium text-gray-800 mb-2">Template Configuration</h4>
                <pre class="text-xs text-gray-600 whitespace-pre-wrap">{{ json_encode($newsletter->template_data, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif --}}

            @if($newsletter->recipient_criteria)
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-2">Recipient Criteria</h4>
                <div class="text-sm text-gray-600">
                    @if(isset($newsletter->recipient_criteria['recipient_types']))
                        <div><strong>Types:</strong> {{ implode(', ', $newsletter->recipient_criteria['recipient_types']) }}</div>
                    @endif
                    @if(isset($newsletter->recipient_criteria['min_lead_score']))
                        <div><strong>Min Lead Score:</strong> {{ $newsletter->recipient_criteria['min_lead_score'] }}</div>
                    @endif
                  
                    @if(isset($newsletter->recipient_criteria['min_activity_days']))
                        <div><strong>Active in last:</strong> {{ $newsletter->recipient_criteria['min_activity_days'] }} days</div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Preview Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            @if($newsletter->canBeSent())
            <form action="{{ route('admin.newsletters.send', $newsletter) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700"
                        onclick="return confirm('Send this newsletter to all recipients now?')">
                    Send Newsletter Now
                </button>
            </form>
            
            <a href="{{ route('admin.newsletters.edit', $newsletter) }}" 
               class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Edit Newsletter
            </a>
            @endif

            <button onclick="printPreview()" 
                    class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                Print Preview
            </button>
        </div>
    </div>
</div>

<!-- Test Send Modal -->
<div id="testSendModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Send Test Email</h3>
                <button onclick="hideTestSendModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('admin.newsletters.test-send', $newsletter) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Email Address</label>
                    <input type="email" name="test_email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="test@example.com">
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                        Send Test
                    </button>
                    <button type="button" onclick="hideTestSendModal()" 
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Preview Styles */
.preview-btn.active {
    background-color: #3B82F6;
    color: white;
}

.preview-btn:not(.active) {
    color: #6B7280;
}

.preview-btn:not(.active):hover {
    background-color: #E5E7EB;
}

/* Mobile Preview Mode */
.mobile-preview #preview-container {
    max-width: 375px;
    margin: 0 auto;
}

.mobile-preview #email-preview {
    font-size: 14px;
}

.mobile-preview .container {
    padding: 10px !important;
}

/* Print Styles */
@media print {
    .bg-gray-100,
    .shadow-sm,
    .border-b,
    .preview-btn,
    button,
    .mt-6.bg-white.rounded-lg.border.border-gray-200.p-6,
    .mt-6.flex.justify-center.space-x-4 {
        display: none !important;
    }
    
    #email-preview {
        border: none !important;
        box-shadow: none !important;
    }
    
    body {
        background: white !important;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .max-w-4xl {
        max-width: 100%;
        padding: 0 1rem;
    }
    
    .flex.items-center.justify-between {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .flex.items-center.space-x-3 {
        justify-content: center;
    }
}
</style>

<script>
// Preview mode switching
function setPreviewMode(mode) {
    const body = document.body;
    const buttons = document.querySelectorAll('.preview-btn');
    
    // Remove existing mode classes
    body.classList.remove('mobile-preview', 'desktop-preview');
    
    // Add new mode class
    body.classList.add(mode + '-preview');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.mode === mode) {
            btn.classList.add('active');
        }
    });
}

// Test send modal
function showTestSendModal() {
    document.getElementById('testSendModal').classList.remove('hidden');
}

function hideTestSendModal() {
    document.getElementById('testSendModal').classList.add('hidden');
}

// Template data toggle
function toggleTemplateData() {
    const element = document.getElementById('template-data');
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

// Print preview
function printPreview() {
    window.print();
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideTestSendModal();
    }
});

// Close modal on backdrop click
document.getElementById('testSendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideTestSendModal();
    }
});

// Initialize desktop preview mode
document.addEventListener('DOMContentLoaded', function() {
    setPreviewMode('desktop');
});
</script>
@endsection
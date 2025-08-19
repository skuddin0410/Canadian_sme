@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="mb-1">Create Newsletter</h2>
            <p class="text-muted">Create and send newsletters to your investors and subscribers.</p>

            <form action="{{ route('newsletters.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Subject Line *</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Enter newsletter subject" required>
                        @error('subject')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Template *</label>
                        <select name="template_name" class="form-select" onchange="updateTemplatePreview()" required>
                            <option value="">Select template...</option>
                            @foreach($templates as $key => $template)
                                <option value="{{ $key }}" {{ old('template_name') === $key ? 'selected' : '' }}>
                                    {{ $template['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('template_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Content -->
                <div class="mt-3">
                    <label class="form-label">Content *</label>
                    <textarea name="content" rows="10" class="form-control" placeholder="Enter your newsletter content..." required>{{ old('content') }}</textarea>
                    <small class="text-muted">You can use HTML tags for formatting.</small>
                    @error('content')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Recipient Criteria -->
                <div class="card mt-4 border">
                    <div class="card-body">
                        <h5 class="card-title">Recipient Settings</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Recipient Types</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="recipient_criteria[recipient_types][]" value="subscribers" {{ in_array('subscribers', old('recipient_criteria.recipient_types', ['subscribers'])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Newsletter Subscribers</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="recipient_criteria[recipient_types][]" value="leads" {{ in_array('leads', old('recipient_criteria.recipient_types', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Leads</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="recipient_criteria[recipient_types][]" value="users" {{ in_array('users', old('recipient_criteria.recipient_types', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Registered Users</label>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <label class="form-label">Lead Filters</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="number" name="recipient_criteria[min_lead_score]" value="{{ old('recipient_criteria.min_lead_score') }}" placeholder="Min Score" min="0" max="100" class="form-control">
                                    </div>
                                    <div class="col">
                                        <input type="number" name="recipient_criteria[max_lead_score]" value="{{ old('recipient_criteria.max_lead_score') }}" placeholder="Max Score" min="0" max="100" class="form-control">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <input type="number" name="recipient_criteria[min_activity_days]" value="{{ old('recipient_criteria.min_activity_days') }}" placeholder="Active in last X days" class="form-control">
                                </div>
                            </div> --}}
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Subscriber Tags (optional)</label>
                            <input type="text" name="recipient_criteria[tags]" value="{{ old('recipient_criteria.tags') }}" placeholder="investor, premium, weekly" class="form-control">
                            <small class="text-muted">Only send to subscribers with these tags</small>
                        </div>
                    </div>
                </div>

                <!-- Template Data -->
                <div id="templateData" class="card mt-4 border d-none">
                    <div class="card-body">
                        <h5 class="card-title">Template Settings</h5>
                        <div id="templateFields"></div>
                    </div>
                </div>

                <!-- Sending Options -->
                <div class="card mt-4 border">
                    <div class="card-body">
                        <h5 class="card-title">Sending Options</h5>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="send_option" value="draft" checked>
                            <label class="form-check-label">Save as Draft</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="send_option" value="send_now">
                            <label class="form-check-label">Send Immediately</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="send_option" value="schedule">
                            <label class="form-check-label">Schedule for Later</label>
                        </div>

                        <div id="scheduleFields" class="mt-3 d-none">
                            <label class="form-label">Schedule Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" min="{{ now()->format('Y-m-d\TH:i') }}" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('newsletters.index') }}" class="btn btn-secondary">Back to List</a>
                    <button type="submit" class="btn btn-primary">Create Newsletter</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="previewNewsletter()">Preview</button>
                    <a href="{{ route('newsletters.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const templates = @json($templates);

document.querySelectorAll('input[name="send_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('scheduleFields').classList.toggle('d-none', this.value !== 'schedule');
    });
});

function updateTemplatePreview() {
    const templateSelect = document.querySelector('select[name="template_name"]');
    const templateData = document.getElementById('templateData');
    const templateFields = document.getElementById('templateFields');

    if (templateSelect.value && templates[templateSelect.value]) {
        templateData.classList.remove('d-none');
        templateFields.innerHTML = '';

        if (templateSelect.value === 'market_update') {
            templateFields.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Call-to-Action URL</label>
                        <input type="url" name="template_data[cta_url]" placeholder="https://example.com/properties" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CTA Button Text</label>
                        <input type="text" name="template_data[cta_text]" placeholder="View Properties" class="form-control">
                    </div>
                </div>
            `;
        } else if (templateSelect.value === 'new_properties') {
            templateFields.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Properties to Feature</label>
                        <select name="template_data[property_count]" class="form-select">
                            <option value="3">Top 3 Properties</option>
                            <option value="5">Top 5 Properties</option>
                            <option value="10">Top 10 Properties</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Property Filter</label>
                        <select name="template_data[property_filter]" class="form-select">
                            <option value="newest">Newest Listings</option>
                            <option value="price_low">Lowest Price</option>
                            <option value="price_high">Highest Price</option>
                            <option value="roi">Best ROI</option>
                        </select>
                    </div>
                </div>
            `;
        }
    } else {
        templateData.classList.add('d-none');
    }
}

function previewNewsletter() {
    alert('Preview functionality will show a live preview here.');
}
</script>
@endsection

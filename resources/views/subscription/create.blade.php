@extends('layouts.admin')
@section('content')

<style>
    :root {
        --ink: #0d0d12;
        --surface: #f5f4f0;
        --card-bg: #ffffff;
        --border: #e2e1dc;
        --accent: #696cff;
        --accent-hover: #5254d4;
        --success: #16a34a;
        --success-bg: #dcfce7;
        --danger: #dc2626;
        --danger-bg: #fee2e2;
        --muted: #6b7280;
        --muted-light: #9ca3af;
        --input-bg: #fafaf8;
        --input-focus: #f0f4ff;
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
        --radius: 14px;
        --radius-sm: 8px;
        --radius-xs: 6px;
    }

    .create-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 2.5rem 2rem;
        background: var(--surface);
        min-height: 100vh;
    }

    /* ── Header ── */
    .create-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;

    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--card-bg);
        border: 1px solid var(--border);
        color: var(--muted);
        text-decoration: none;
        transition: background .15s, color .15s, transform .12s;
        flex-shrink: 0;
    }

    .back-btn:hover {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
        transform: translateX(-2px);
        text-decoration: none;
    }

    .create-header-text h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--ink);
        margin: 0 0 .2rem;
        letter-spacing: -.03em;
    }

    .create-header-text p {
        font-size: .875rem;
        color: var(--muted);
        margin: 0;
    }

    /* ── Card ── */
    .create-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .create-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--border);
        background: #fafaf8;
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .create-card-header span {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .8rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .section-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        flex-shrink: 0;
    }

    .create-card-body {
        padding: 2rem 1.75rem;
    }

    /* ── Error alert ── */
    .error-alert {
        background: var(--danger-bg);
        border: 1px solid #fecaca;
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
    }

    .error-alert-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .82rem;
        color: var(--danger);
        margin-bottom: .5rem;
    }

    .error-alert ul {
        margin: 0;
        padding-left: 1.2rem;
        font-size: .82rem;
        color: var(--danger);
    }

    .error-alert li {
        margin-bottom: .2rem;
    }

    .error-alert li:last-child {
        margin-bottom: 0;
    }

    /* ── Form grid ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem 1.5rem;
    }

    @media (max-width: 680px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .create-wrapper {
            padding: 1.5rem 1rem;
        }
    }

    /* ── Field ── */
    .field {
        display: flex;
        flex-direction: column;
        gap: .45rem;
    }

    .field label {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .72rem;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .field-input,
    .field-select {
        width: 100%;
        background: var(--input-bg);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-xs);
        padding: .65rem .9rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .9rem;
        color: var(--ink);
        transition: border-color .15s, background .15s, box-shadow .15s;
        appearance: none;
        -webkit-appearance: none;
        outline: none;
    }

    .field-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8' fill='none'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%236b7280' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .9rem center;
        padding-right: 2.25rem;
        cursor: pointer;
    }

    .field-input:focus,
    .field-select:focus {
        border-color: var(--accent);
        background: var(--input-focus);
        box-shadow: 0 0 0 3px rgba(45, 91, 227, .12);
    }

    .field-input::placeholder {
        color: var(--muted-light);
    }

    /* number spinner hide */
    .field-input[type="number"]::-webkit-inner-spin-button,
    .field-input[type="number"]::-webkit-outer-spin-button {
        opacity: 1;
    }

    /* ── Input with icon wrapper ── */
    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap svg {
        position: absolute;
        left: .9rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted-light);
        pointer-events: none;
    }

    .input-icon-wrap .field-input {
        padding-left: 2.4rem;
    }

    /* ── Helper text ── */
    .field-hint {
        font-size: .75rem;
        color: var(--muted-light);
        margin-top: .1rem;
    }

    /* ── Divider ── */
    .form-divider {
        grid-column: 1 / -1;
        height: 1px;
        background: var(--border);
        margin: .25rem 0;
    }

    /* ── Footer actions ── */
    .form-actions {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--accent);
        color: #fff;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .875rem;
        padding: .7rem 1.5rem;
        border-radius: var(--radius-sm);
        border: none;
        cursor: pointer;
        transition: background .18s, transform .15s, box-shadow .18s;
        box-shadow: 0 2px 8px rgba(105, 108, 255, .3);
        letter-spacing: .01em;
    }

    .btn-submit:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(105, 108, 255, .4);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: transparent;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        font-size: .875rem;
        padding: .7rem 1.25rem;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        text-decoration: none;
        transition: background .15s, color .15s, border-color .15s;
    }

    .btn-cancel:hover {
        background: #f1f0ec;
        color: var(--ink);
        border-color: #ccc;
        text-decoration: none;
    }

    /* ── Animate in ── */
    .create-card {
        animation: cardIn .35s ease both;
    }


    @keyframes cardIn {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="create-wrapper">

    {{-- Header --}}
    <div class="create-header">
        <a href="{{ route('subscription.index') }}" class="back-btn" title="Back to list">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
        <div class="create-header-text">
            <h2>New Subscription</h2>
            <p>Fill in the details below to create a subscription</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="create-card">
        <div class="create-card-header">
            <div class="section-dot"></div>
            <span>Subscription Details</span>
        </div>
        <div class="create-card-body">

            <form action="{{ route('subscription.store') }}" method="POST" id="subscriptionForm">
                @csrf
                <div class="form-grid">

                    {{-- User --}}
                    <div class="field">
                        <label for="user_id">User<span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="field-select searchable" required>
                            <option value="">Select a user…</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->lastname }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pricing --}}
                    <div class="field">
                        <label for="price_id">Pricing Plan<span class="text-danger">*</span></label>
                        <select name="price_id" id="price_id" class="field-select searchable" required>
                            <option value="">Select a plan…</option>
                            @foreach($pricings as $pricing)
                            <option value="{{ $pricing->id }}" {{ old('price_id') == $pricing->id ? 'selected' : '' }}>
                                {{ $pricing->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-divider"></div>

                    {{-- Attendee Count --}}
                    <div class="field">
                        <label for="attendee_count">Attendee Count<span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="5.5" cy="4" r="2.5" stroke="currentColor" stroke-width="1.4" />
                                <path d="M1 12c0-2.21 2.015-4 4.5-4s4.5 1.79 4.5 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                <path d="M10.5 6.5v4M8.5 8.5h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                            <input type="number" name="attendee_count" id="attendee_count"
                                class="field-input" min="1" value="{{ old('attendee_count', 1) }}" required>
                        </div>
                    </div>

                    {{-- Event Count --}}
                    <div class="field">
                        <label for="event_count">Event Count<span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <rect x="1.5" y="2.5" width="11" height="10" rx="2" stroke="currentColor" stroke-width="1.4" />
                                <path d="M4.5 1v3M9.5 1v3M1.5 6h11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                            <input type="number" name="event_count" id="event_count"
                                class="field-input" min="1" value="{{ old('event_count', 1) }}" required>
                        </div>
                    </div>

                    {{-- Expiry (Days) --}}
                    <div class="field">
                        <label for="expired_at">Expiry Duration (Days)<span class="text-danger">*</span></label>
                        {{-- 
                        Original label:
                        <label for="expired_at">Expiry Duration<span class="text-danger">*</span></label>
                        --}}
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.4" />
                                <path d="M7 4v3.5l2 1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <input type="number" name="expired_at" id="expired_at"
                                class="field-input" min="1"
                                placeholder="e.g. 30, 90, 365"
                                value="{{ old('expired_at') }}">
                        </div>
                        <span class="field-hint">Specify the duration in days.</span>
                        {{-- 
                        Original hint:
                        <span class="field-hint">Leave blank for no expiry. Value is in months.</span> 
                        --}}

                        {{-- Date Preview --}}
                        <div id="date-preview-section" style="display: none; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 0.75rem; border-radius: 8px; margin-top: 0.5rem;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;">Start Date</small>
                                    <span id="preview-start-date" class="text-dark fw-bold" style="font-size: 0.85rem;"></span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;">Expiry Date</small>
                                    <span id="preview-end-date" class="text-primary fw-bold" style="font-size: 0.85rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="field">
                        <label for="status">Status<span class="text-danger">*</span></label>
                        <select name="status" id="status" class="field-select" required>
                            <option value="">Select status…</option>
                            <option value="active" {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M2 7.5l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Create
                    </button>
                    <a href="{{ route('subscription.index') }}" class="btn-cancel">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.searchable').select2({
            placeholder: "Search...",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        // Date calculation logic
        const expiredInput = document.getElementById('expired_at');
        const previewSection = document.getElementById('date-preview-section');
        const startSpan = document.getElementById('preview-start-date');
        const endSpan = document.getElementById('preview-end-date');

        function updatePreview() {
            const days = parseInt(expiredInput.value);
            if (!isNaN(days) && days > 0) {
                const startDate = new Date();
                const endDate = new Date();
                endDate.setDate(startDate.getDate() + days);

                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                startSpan.textContent = startDate.toLocaleDateString('en-US', options);
                endSpan.textContent = endDate.toLocaleDateString('en-US', options);
                previewSection.style.display = 'block';
            } else {
                previewSection.style.display = 'none';
            }
        }

        expiredInput.addEventListener('input', updatePreview);
        
        // Run on load if old value exists
        if (expiredInput.value) {
            updatePreview();
        }

        // SWAL Validation
        $('#subscriptionForm').on('submit', function(e) {
            const form = this;
            const $form = $(form);
            const user = $('#user_id').val();
            const pricing = $('#price_id').val();
            const attendee = $('#attendee_count').val();
            const eventCount = $('#event_count').val();
            const expired = $('#expired_at').val();
            const status = $('#status').val();

            let errors = [];

            if (!user) errors.push("Admin user is required.");
            if (!pricing) errors.push("Pricing plan is required.");
            if (!attendee || attendee < 1) errors.push("Attendee count must be at least 1.");
            if (!eventCount || eventCount < 1) errors.push("Event count must be at least 1.");
            if (!expired || expired < 1) errors.push("Expiry duration is required.");
            if (!status) errors.push("Status is required.");

            if (errors.length > 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Validation Error',
                    html: '<ul class="text-start">' + errors.map(err => `<li>${err}</li>`).join('') + '</ul>',
                    icon: 'error',
                    confirmButtonColor: '#696cff'
                });
                return false;
            }

            // If local validation is passed, check for existing active subscription
            e.preventDefault();
            
            // If already forced, just submit
            if ($('#force_create').length > 0) {
                form.submit();
                return;
            }

            $.ajax({
                url: "{{ route('subscription.check-active') }}",
                type: 'GET',
                data: { user_id: user },
                success: function(resp) {
                    if (resp.exists) {
                        Swal.fire({
                            title: 'Active Subscription Found',
                            html: `User already has an active <b>${resp.plan_name}</b> plan (Expires: ${resp.expired_at}).<br><br>Do you still want to create a new one?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#696cff',
                            cancelButtonColor: '#8592a3',
                            confirmButtonText: 'Yes, create it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $form.append('<input type="hidden" name="force_create" id="force_create" value="1">');
                                form.submit();
                            }
                        });
                    } else {
                        // No active sub, proceed normally
                        form.submit();
                    }
                },
                error: function() {
                    // Fallback to normal submission if AJAX fails
                    form.submit();
                }
            });
        });

        // Handle server-side errors
        @if($errors->any())
            Swal.fire({
                title: 'Submission Error',
                html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                icon: 'error',
                confirmButtonColor: '#696cff'
            });
        @endif
    });
</script>
@endsection
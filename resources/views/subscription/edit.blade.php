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
        --accent-soft: #ededff;
        --accent-mid: #c5c6ff;
        --success: #16a34a;
        --success-bg: #dcfce7;
        --danger: #dc2626;
        --danger-bg: #fee2e2;
        --muted: #6b7280;
        --muted-light: #9ca3af;
        --input-bg: #fafaf8;
        --input-focus: #f0f0ff;
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
        --radius: 16px;
        --radius-sm: 10px;
        --radius-xs: 6px;
    }


    .edit-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 2.5rem 2rem;
        background: var(--surface);
        min-height: 100vh;
    }

    /* ── Header ── */
    .edit-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: var(--radius-sm);
        background: var(--card-bg);
        border: 1px solid var(--border);
        color: var(--muted);
        text-decoration: none;
        transition: background .15s, color .15s, transform .12s, border-color .15s;
        flex-shrink: 0;
    }

    .back-btn:hover {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
        transform: translateX(-2px);
        text-decoration: none;
    }

    .edit-header-text h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--ink);
        margin: 0 0 .2rem;
        letter-spacing: -.03em;
    }

    .edit-header-text p {
        font-size: .875rem;
        color: var(--muted);
        margin: 0;
    }

    /* ── Card ── */
    .edit-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: cardIn .35s ease both;
        max-width: 100%;
    }

    @keyframes cardIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Card hero strip ── */
    .edit-hero {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        padding: 1.4rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }

    .edit-hero::before {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .07);
        pointer-events: none;
    }

    .edit-hero::after {
        content: '';
        position: absolute;
        right: 70px;
        bottom: -55px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .05);
        pointer-events: none;
    }

    .hero-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        background: rgba(255, 255, 255, .18);
        border: 1.5px solid rgba(255, 255, 255, .3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #000000;
        backdrop-filter: blur(4px);
    }

    .hero-text {
        flex: 1;
    }

    .hero-text strong {
        display: block;
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        color: #444141;
        letter-spacing: -.01em;
    }

    .hero-text span {
        font-size: .8rem;
        color: rgba(12, 12, 12, 0.75);
    }

    .hero-id {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .72rem;
        letter-spacing: .07em;
        background: rgba(255, 255, 255, .18);
        color: #000000;
        padding: .25rem .7rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .1);
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }

    /* ── Card header ── */
    .edit-card-header {
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--border);
        background: #fafaf8;
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .section-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        flex-shrink: 0;
    }

    .edit-card-header span {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .7rem;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ── Card body ── */
    .edit-card-body {
        padding: 2rem;
    }

    /* ── Form grid ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem 1.5rem;
    }

    @media (max-width: 620px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .edit-wrapper {
            padding: 1.5rem 1rem;
        }

        .edit-card-body {
            padding: 1.5rem 1.25rem;
        }

        .edit-hero {
            padding: 1.25rem 1.5rem;
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
        font-size: .7rem;
        letter-spacing: .08em;
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
        box-shadow: 0 0 0 3px rgba(105, 108, 255, .13);
    }

    .field-input::placeholder {
        color: var(--muted-light);
    }

    /* icon input wrapper */
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

    /* hint */
    .field-hint {
        font-size: .73rem;
        color: var(--muted-light);
    }

    /* warn chip on extend field */
    .field-warn {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .72rem;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 5px;
        padding: .2rem .55rem;
        margin-top: .1rem;
        width: fit-content;
    }

    /* divider */
    .form-divider {
        grid-column: 1 / -1;
        height: 1px;
        background: var(--border);
        margin: .15rem 0;
    }

    /* ── Footer ── */
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
        box-shadow: 0 4px 18px rgba(105, 108, 255, .4);
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
</style>

<div class="edit-wrapper">

    {{-- Page header --}}
    <div class="edit-header">
        <a href="{{ route('subscription.index') }}" class="back-btn" title="Back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
        <div class="edit-header-text">
            <h2>Edit Subscription</h2>
            <p>Update the details for this subscription record</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="edit-card">

        {{-- Hero --}}
        <div class="edit-hero">
            <div class="hero-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4 6h12M4 10h8M4 14h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <path d="M14 13l1.5 1.5L18 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="hero-text">
                <strong>{{ $subscription->user->name ?? 'Subscription' }}</strong>
                <span>Editing subscription record</span>
            </div>
            <div class="hero-id">{{ $subscription->id }}</div>
        </div>

        {{-- Section header --}}
        <div class="edit-card-header">
            <div class="section-dot"></div>
            <span>Subscription Details</span>
        </div>

        {{-- Form --}}
        <div class="edit-card-body">
            <form action="{{ route('subscription.update', $subscription->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">

                    {{-- User --}}
                    <div class="field">
                        <label for="user_id">User</label>
                        <select name="user_id" id="user_id" class="field-select searchable">
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $subscription->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->lastname }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pricing --}}
                    <div class="field">
                        <label for="price_id">Pricing Plan</label>
                        <select name="price_id" id="price_id" class="field-select searchable">
                            @foreach($pricings as $pricing)
                            <option value="{{ $pricing->id }}" {{ $subscription->price_id == $pricing->id ? 'selected' : '' }}>
                                {{ $pricing->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-divider"></div>

                    {{-- Attendee Count --}}
                    <div class="field">
                        <label for="attendee_count">Attendee Count</label>
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="4.5" cy="3.5" r="2.5" stroke="currentColor" stroke-width="1.4" />
                                <path d="M1 12c0-2.21 1.791-4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                <path d="M10.5 7v4M8.5 9h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                            <input type="number" name="attendee_count" id="attendee_count"
                                class="field-input" min="1"
                                value="{{ $subscription->attendee_count }}">
                        </div>
                    </div>

                    {{-- Event Count --}}
                    <div class="field">
                        <label for="event_count">Event Count</label>
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <rect x="1.5" y="2.5" width="11" height="10" rx="2" stroke="currentColor" stroke-width="1.4" />
                                <path d="M4.5 1v3M9.5 1v3M1.5 6h11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                            <input type="number" name="event_count" id="event_count"
                                class="field-input" min="1"
                                value="{{ $subscription->event_count }}">
                        </div>
                    </div>

                    {{-- Extend months --}}
                    <div class="field">
                        <label for="expired_at">Extend Expiry</label>
                        <div class="input-icon-wrap">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.4" />
                                <path d="M7 4v3.5l2 1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <input type="number" name="expired_at" id="expired_at"
                                class="field-input" min="1"
                                value="{{ $monthsRemaining }}"
                                placeholder="e.g. 1, 3, 6, 12">
                        </div>
                        <span class="field-warn">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                <path d="M5 1L9 9H1L5 1Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" />
                                <path d="M5 4.5v2M5 7.5h.01" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                            Adds months to current expiry. Leave blank to keep unchanged.
                        </span>
                    </div>

                    {{-- Status --}}
                    <div class="field">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="field-select">
                            <option value="active" {{ $subscription->status == 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $subscription->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M2 7.5l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Save Changes
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
            width: '100%'
        });
    });
</script>
@endsection
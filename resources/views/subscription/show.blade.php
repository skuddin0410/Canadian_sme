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
        --shadow-md: 0 4px 16px rgba(0,0,0,.08);
        --shadow-lg: 0 12px 40px rgba(105,108,255,.12);
        --radius: 16px;
        --radius-sm: 10px;
        --radius-xs: 6px;
    }

    .show-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 2.5rem 2rem;
        background: var(--surface);
        min-height: 100vh;
    }

    /* ── Header ── */
    .show-header {
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

    .show-header-text h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--ink);
        margin: 0 0 .2rem;
        letter-spacing: -.03em;
    }

    .show-header-text p {
        font-size: .875rem;
        color: var(--muted);
        margin: 0;
    }

    /* ── Main card ── */
    .show-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: cardIn .35s ease both;
        max-width: 100%;
        max-height: fit-content;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Hero strip ── */
    .show-hero {
        background: linear-gradient(135deg, #696cff 0%, #9b9dff 100%);
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        overflow: hidden;
    }

    .show-hero::before {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }

    .show-hero::after {
        content: '';
        position: absolute;
        right: 60px;
        bottom: -60px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
        pointer-events: none;
    }

    .hero-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(255,255,255,.2);
        border: 2px solid rgba(255,255,255,.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1.3rem;
        color: #fff;
        flex-shrink: 0;
        backdrop-filter: blur(4px);
    }

    .hero-info { flex: 1; min-width: 0; }

    .hero-name {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1.2rem;
        color: #fff;
        margin: 0 0 .3rem;
        letter-spacing: -.02em;
    }

    .hero-meta {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        background: rgba(255,255,255,.18);
        color: #fff;
        font-size: .75rem;
        font-weight: 500;
        padding: .22rem .65rem;
        border-radius: 999px;
        backdrop-filter: blur(4px);
    }

    .status-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-weight: 700;
        font-size: .75rem;
        padding: .25rem .7rem;
        border-radius: 999px;
    }

    .status-hero-badge.active {
        background: rgba(255,255,255,.95);
        color: var(--success);
    }

    .status-hero-badge.inactive {
        background: rgba(255,255,255,.95);
        color: var(--danger);
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-hero-badge.active .status-dot {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .35; }
    }

    /* ── Detail grid ── */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    @media (max-width: 560px) {
        .detail-grid { grid-template-columns: 1fr; }
        .show-wrapper { padding: 1.5rem 1rem; }
    }

    .detail-item {
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--border);
        border-right: 1px solid var(--border);
        transition: background .12s;
    }

    .detail-item:nth-child(even) { border-right: none; }

    /* last row — remove bottom border */
    .detail-item:nth-last-child(-n+2) { border-bottom: none; }

    /* if odd total, last item spans */
    .detail-item.span-full {
        grid-column: 1 / -1;
        border-right: none;
        border-bottom: none;
    }

    .detail-item:hover { background: #fafaf8; }

    .detail-label {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .68rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted-light);
        margin-bottom: .45rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .detail-label svg { color: var(--accent); }

    .detail-value {
        font-size: .95rem;
        font-weight: 500;
        color: var(--ink);
    }

    /* count badge */
    .count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 28px;
        background: var(--accent-soft);
        color: var(--accent);
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: .9rem;
        border-radius: var(--radius-xs);
        padding: 0 .55rem;
    }

    /* pricing tag */
    .pricing-tag {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: var(--accent-soft);
        color: var(--accent);
        font-weight: 600;
        font-size: .82rem;
        padding: .3rem .75rem;
        border-radius: 999px;
        border: 1px solid var(--accent-mid);
    }

    /* expiry */
    .expiry-none { color: var(--muted-light); font-style: italic; font-size: .88rem; }

    /* ── Footer ── */
    .show-footer {
        padding: 1.25rem 2rem;
        background: #fafaf8;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .75rem;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: transparent;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        font-size: .875rem;
        padding: .65rem 1.25rem;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        text-decoration: none;
        transition: background .15s, color .15s, border-color .15s;
    }

    .btn-back:hover {
        background: var(--ink);
        color: #fff;
        border-color: var(--ink);
        text-decoration: none;
    }
</style>

<div class="show-wrapper">

    {{-- Page header --}}
    <div class="show-header">
        <a href="{{ route('subscription.index') }}" class="back-btn" title="Back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <div class="show-header-text">
            <h2>Subscription Details</h2>
            <p>Viewing full record for subscription #{{ $subscription->id }}</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="show-card">

        {{-- Hero --}}
        <div class="show-hero">
            <div class="hero-avatar">
                {{ strtoupper(substr($subscription->user->name ?? 'N', 0, 1)) }}
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $subscription->user->name ?? 'N/A' }} {{ $subscription->user->lastname ?? 'N/A' }}</div>
                <div class="hero-meta">
                    <span class="hero-tag">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <rect x="1" y="1.5" width="8" height="7" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                            <path d="M3 1v1.5M7 1v1.5M1 4h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        #{{ $subscription->id }}
                    </span>
                    <span class="status-hero-badge {{ $subscription->status == 'active' ? 'active' : 'inactive' }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detail grid --}}
        <div class="detail-grid">

            {{-- Pricing --}}
            <div class="detail-item">
                <div class="detail-label">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M1 6.5L4 9.5 11 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pricing Plan
                </div>
                <div class="detail-value">
                    @if($subscription->pricing->name ?? false)
                        <span class="pricing-tag">{{ $subscription->pricing->name }}</span>
                    @else
                        <span style="color:var(--muted-light)">N/A</span>
                    @endif
                </div>
            </div>

            {{-- Status --}}
            <div class="detail-item">
                <div class="detail-label">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M6 3.5V6.5L8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Status
                </div>
                <div class="detail-value">
                    <span class="status-hero-badge {{ $subscription->status == 'active' ? 'active' : 'inactive' }}"
                        style="background:{{ $subscription->status == 'active' ? 'var(--success-bg)' : 'var(--danger-bg)' }}; color:{{ $subscription->status == 'active' ? 'var(--success)' : 'var(--danger)' }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
            </div>

            {{-- Attendee Count --}}
            <div class="detail-item">
                <div class="detail-label">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="4.5" cy="3.5" r="2" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M1 10c0-1.933 1.567-3.5 3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                        <path d="M8.5 6v4M6.5 8h4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                    Attendees
                </div>
                <div class="detail-value">
                    <span class="count-pill">{{ $subscription->attendee_count }}</span>
                </div>
            </div>

            {{-- Event Count --}}
            <div class="detail-item">
                <div class="detail-label">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <rect x="1" y="2" width="10" height="9" rx="2" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M4 1v2M8 1v2M1 5.5h10" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                    Event Count
                </div>
                <div class="detail-value">
                    <span class="count-pill">{{ $subscription->event_count }}</span>
                </div>
            </div>

            {{-- Expiry --}}
            <div class="detail-item span-full">
                <div class="detail-label">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M6 3.5V6L7.5 7.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Expires At
                </div>
                <div class="detail-value">
                    @if($subscription->expired_at)
                        {{ $subscription->expired_at->format('d M Y, h:i A') }}
                    @else
                        <span class="expiry-none">No expiry set</span>
                    @endif
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="show-footer">
            <a href="{{ route('subscription.index') }}" class="btn-back">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M9 11L5 7L9 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to List
            </a>
        </div>

    </div>
</div>

@endsection
@extends('layouts.admin')
@section('content')

<style>
    :root {

        --surface: #f5f4f0;
        --card-bg: #ffffff;
        --border: #e2e1dc;
        --accent: #696cff;
        --accent-hover: #5254d4;
        --success: #16a34a;
        --success-bg: #dcfce7;
        --danger: #dc2626;
        --danger-bg: #fee2e2;
        --warning: #d97706;
        --warning-bg: #fef3c7;
        --info: #696cff;
        --info-bg: #e0f2fe;
        --muted: #6b7280;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
        --shadow-lg: 0 12px 40px rgba(0, 0, 0, .12);
        --radius: 14px;
        --radius-sm: 8px;
    }

    .subs-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 2.5rem 2rem;
        background: var(--surface);
        min-height: 100vh;
    }

    /* ── Header ── */
    .subs-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2rem;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .subs-header-left h2 {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: var(--ink);
        margin: 0 0 .25rem;
        letter-spacing: -.03em;
    }

    .subs-header-left p {
        font-size: .875rem;
        color: var(--muted);
        margin: 0;
    }

    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--accent);
        color: #fff;
        font-family: 'Syne', sans-serif;
        font-weight: 600;
        font-size: .875rem;
        letter-spacing: .01em;
        padding: .65rem 1.25rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background .18s, transform .15s, box-shadow .18s;
        box-shadow: 0 2px 8px rgba(105, 108, 255, .3);
        white-space: nowrap;
    }

    .btn-create:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(105, 108, 255, .4);
        color: #fff;
        text-decoration: none;
    }

    .btn-create svg {
        flex-shrink: 0;
    }

    /* ── Card ── */
    .subs-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    /* ── Table ── */
    .subs-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .subs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }

    .subs-table thead {
        background: #f9f8f6;
        border-bottom: 2px solid var(--border);
    }

    .subs-table thead th {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .7rem;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: var(--muted);
        padding: .9rem 1.1rem;
        white-space: nowrap;
        text-align: left;
    }

    .subs-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .12s;
    }

    .subs-table tbody tr:last-child {
        border-bottom: none;
    }

    .subs-table tbody tr:hover {
        background: #fafaf8;
    }

    .subs-table td {
        padding: .9rem 1.1rem;
        color: var(--ink);
        vertical-align: middle;
        white-space: nowrap;
    }

    /* ── ID chip ── */
    .id-chip {
        display: inline-block;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .72rem;
        background: var(--ink);
        color: #000000;
        padding: .18rem .55rem;
        border-radius: 5px;
        letter-spacing: .04em;
    }

    /* ── User / Event cells ── */
    .cell-user {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #696cff 0%, #6c93f5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .7rem;
        color: #fff;
        flex-shrink: 0;
    }

    .cell-label {
        font-weight: 500;
        color: var(--ink);
    }

    .cell-sub {
        font-size: .75rem;
        color: var(--muted);
    }

    /* ── Pricing badge ── */
    .pricing-tag {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        background: var(--info-bg);
        color: var(--info);
        font-weight: 600;
        font-size: .75rem;
        padding: .25rem .65rem;
        border-radius: 999px;
    }

    /* ── Count pills ── */
    .count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 24px;
        background: #f1f0ec;
        color: var(--ink);
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .78rem;
        border-radius: 6px;
        padding: 0 .4rem;
    }

    /* ── Status badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-weight: 600;
        font-size: .75rem;
        padding: .28rem .75rem;
        border-radius: 999px;
        letter-spacing: .01em;
    }

    .status-badge.active {
        background: var(--success-bg);
        color: var(--success);
    }

    .status-badge.inactive {
        background: var(--danger-bg);
        color: var(--danger);
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-badge.active .status-dot {
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .4;
        }
    }

    /* ── Expiry ── */
    .expiry-text {
        color: var(--muted);
        font-size: .82rem;
    }

    .expiry-none {
        font-style: italic;
        color: #bbb;
        font-size: .8rem;
    }

    /* ── Date ── */
    .date-text {
        color: var(--muted);
        font-size: .82rem;
    }

    /* ── Action buttons ── */
    .action-group {
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        border: 1px solid transparent;
        font-size: .8rem;
        transition: background .15s, color .15s, transform .12s, border-color .15s;
        text-decoration: none;
        cursor: pointer;
        background: transparent;
        line-height: 1;
    }

    .act-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .act-btn.view {
        color: var(--info);
        background: var(--info-bg);
        border-color: #bae6fd;
    }

    .act-btn.view:hover {
        background: #bae6fd;
        color: var(--info);
    }

    .act-btn.edit {
        color: var(--warning);
        background: var(--warning-bg);
        border-color: #fde68a;
    }

    .act-btn.edit:hover {
        background: #fde68a;
        color: var(--warning);
    }

    .act-btn.delete {
        color: var(--danger);
        background: var(--danger-bg);
        border-color: #fecaca;
    }

    .act-btn.delete:hover {
        background: #fecaca;
        color: var(--danger);
    }

    /* ── Empty state ── */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state svg {
        opacity: .25;
        margin-bottom: 1rem;
    }

    .empty-state p {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--muted);
        margin: 0;
    }

    /* ── Row enter animation ── */
    .subs-table tbody tr {
        animation: rowIn .3s ease both;
    }



    @keyframes rowIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="subs-wrapper">

    {{-- Header --}}
    <div class="subs-header">
        <div class="subs-header-left">
            <h2>Subscriptions</h2>
            <p>Manage all active and past subscription records</p>
        </div>
        <a href="{{ route('subscription.create') }}" class="btn-create">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            New Subscription
        </a>
    </div>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif

    {{-- Table Card --}}
    <div class="subs-card">
        <div class="subs-table-wrap">
            <table class="subs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <!-- <th>Event</th> -->
                        <th>Pricing</th>
                        <th>Attendees</th>
                        <th>Event Count</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        {{-- ID --}}
                        <td>
                            <span class="id-chip">{{ $loop->iteration }}</span>
                        </td>

                        {{-- User --}}
                        <td>
                            <div class="cell-user">
                                <div class="avatar">
                                    {{ strtoupper(substr($subscription->user->name ?? 'N', 0, 1)) }}
                                </div>
                                <span class="cell-label">{{ $subscription->user->name ?? 'N/A' }} {{ $subscription->user->lastname ?? 'N/A' }}</span>
                            </div>
                        </td>

                        {{-- Event --}}
                        <!-- <td>
                            <span class="cell-label">{{ $subscription->event->title ?? 'N/A' }}</span>
                        </td> -->

                        {{-- Pricing --}}
                        <td>
                            @if($subscription->pricing->name ?? false)
                            <span class="pricing-tag">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <path d="M1 5.5L3.5 8 9 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ $subscription->pricing->name }}
                            </span>
                            @else
                            <span style="color:#bbb;font-size:.8rem;">—</span>
                            @endif
                        </td>

                        {{-- Attendees --}}
                        <td><span class="count-pill">{{ $subscription->attendee_count }}</span></td>

                        {{-- Event Count --}}
                        <td><span class="count-pill">{{ $subscription->event_count }}</span></td>

                        {{-- Status --}}
                        <td>
                            @if($subscription->status == 'active' && $subscription->expired_at && $subscription->expired_at->isPast())
                                <span class="status-badge inactive" style="background: var(--danger-bg); color: var(--danger); border-color: #fecaca;">
                                    <span class="status-dot"></span>
                                    Expired
                                </span>
                            @else
                                <span class="status-badge {{ $subscription->status == 'active' ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @endif
                        </td>

                        {{-- Expires At --}}
                        <td>
                            @if($subscription->expired_at)
                            <span class="expiry-text">
                                {{ $subscription->expired_at->format('d M Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ getPreciseRemainingTime($subscription->expired_at) }}
                                </small>
                            </span>
                            @else
                            <span class="expiry-none">No Expiry</span>
                            @endif
                        </td>

                        {{-- Created --}}
                        <td>
                            <span class="date-text">{{ $subscription->created_at->format('d M Y') }}</span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-group">
                                <a href="{{ route('subscription.show', $subscription->id) }}" class="act-btn view" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('subscription.edit', $subscription->id) }}" class="act-btn edit" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('subscription.destroy', $subscription->id) }}" method="POST" class="d-inline delete-form m-0 p-0">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="act-btn delete btn-delete" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <svg width="48" height="48" fill="none" viewBox="0 0 48 48">
                                    <rect x="8" y="10" width="32" height="34" rx="4" stroke="#6b7280" stroke-width="2" />
                                    <path d="M16 10V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2" stroke="#6b7280" stroke-width="2" />
                                    <path d="M17 22h14M17 29h10" stroke="#6b7280" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <p>No subscriptions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script id="swal-delete-script">
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {

                let form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This subscription will be deleted permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });
        });

    });
</script>
@extends('layouts.admin')

@section('title', 'Email Analytics')


<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap');

    .analytics-wrap {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Page header ── */
    .page-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.07);
    }
    .page-header-bar .breadcrumb-label {
        font-size: .72rem;
        font-weight: 500;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: .25rem;
    }
    .page-header-bar h2 {
        font-size: 1.45rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        letter-spacing: -.02em;
    }
    .page-header-bar .live-dot {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .75rem;
        font-weight: 500;
        color: #6b7280;
        background: #f3f4f6;
        padding: .35rem .8rem;
        border-radius: 99px;
    }
    .page-header-bar .live-dot::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34,197,94,.25);
        animation: pulse-green 1.8s ease-in-out infinite;
    }
    @keyframes pulse-green {
        0%,100% { box-shadow: 0 0 0 2px rgba(34,197,94,.25); }
        50%      { box-shadow: 0 0 0 5px rgba(34,197,94,.08); }
    }

    /* ── Stat cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 768px) { .stat-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,.08);
        transform: translateY(-2px);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card.sent::before   { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .stat-card.opens::before  { background: linear-gradient(90deg, #22c55e, #86efac); }
    .stat-card.clicks::before { background: linear-gradient(90deg, #f59e0b, #fcd34d); }

    .stat-card .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    .stat-card.sent   .stat-icon { background: #eef2ff; color: #6366f1; }
    .stat-card.opens  .stat-icon { background: #f0fdf4; color: #22c55e; }
    .stat-card.clicks .stat-icon { background: #fffbeb; color: #f59e0b; }

    .stat-card .stat-label {
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: .5rem;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        letter-spacing: -.03em;
        line-height: 1;
        margin-bottom: .5rem;
        font-family: 'DM Mono', monospace;
    }
    .stat-card .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .72rem;
        font-weight: 600;
        padding: .2rem .6rem;
        border-radius: 99px;
    }
    .stat-card.opens  .stat-badge { background: #f0fdf4; color: #16a34a; }
    .stat-card.clicks .stat-badge { background: #fffbeb; color: #d97706; }

    /* ── Table card ── */
    .log-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
    }
    .log-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .log-card-header h5 {
        font-size: .95rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        letter-spacing: -.01em;
    }
    .log-card-header .record-count {
        font-size: .72rem;
        font-weight: 500;
        color: #9ca3af;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: .25rem .7rem;
        border-radius: 99px;
    }

    /* ── Table ── */
    .analytics-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .845rem;
    }
    .analytics-table thead th {
        padding: .75rem 1rem;
        font-size: .68rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #9ca3af;
        background: #f9fafb;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
    }
    .analytics-table thead th:first-child { padding-left: 1.75rem; }
    .analytics-table thead th:last-child  { padding-right: 1.75rem; }

    .analytics-table tbody tr {
        border-bottom: 1px solid #f9fafb;
        transition: background .15s;
    }
    .analytics-table tbody tr:last-child { border-bottom: none; }
    .analytics-table tbody tr:hover { background: #fafafa; }

    .analytics-table td {
        padding: .9rem 1rem;
        color: #374151;
        vertical-align: middle;
    }
    .analytics-table td:first-child { padding-left: 1.75rem; color: #9ca3af; font-family: 'DM Mono', monospace; font-size: .78rem; }
    .analytics-table td:last-child  { padding-right: 1.75rem; }

    .td-email {
        font-family: 'DM Mono', monospace;
        font-size: .8rem;
        color: #4b5563 !important;
    }
    .td-subject {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #111827 !important;
        font-weight: 500;
    }
    .td-date {
        font-size: .78rem;
        color: #6b7280 !important;
        white-space: nowrap;
        line-height: 1.6;
    }

    /* ── Badges ── */
    .badge-opened {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #f0fdf4; color: #16a34a;
        font-size: .7rem; font-weight: 600;
        padding: .3rem .75rem; border-radius: 99px;
        letter-spacing: .03em;
    }
    .badge-opened::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: #22c55e;
    }
    .badge-not-opened {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #f9fafb; color: #9ca3af;
        font-size: .7rem; font-weight: 600;
        padding: .3rem .75rem; border-radius: 99px;
        letter-spacing: .03em;
    }
    .badge-not-opened::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: #d1d5db;
    }
    .badge-clicks {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 28px; height: 24px;
        background: #eff6ff; color: #2563eb;
        font-size: .72rem; font-weight: 700;
        padding: 0 .6rem; border-radius: 99px;
        font-family: 'DM Mono', monospace;
    }
    .badge-clicks-zero {
        background: #f9fafb; color: #9ca3af;
    }

    /* ── User avatar chip ── */
    .user-chip {
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .user-chip .avatar {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
        display: flex; align-items: center; justify-content: center;
        font-size: .65rem; font-weight: 700; color: #4338ca;
        flex-shrink: 0;
    }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #9ca3af;
    }
    .empty-state .empty-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: .4;
    }
    .empty-state p { font-size: .875rem; margin: 0; }

    /* ── Pagination wrapper ── */
    .pagination-wrap {
        padding: 1rem 1.75rem;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: flex-end;
    }
    .pagination-wrap .pagination { margin: 0; }
</style>


@section('content')
<div class="container-xxl flex-grow-1 container-p-y analytics-wrap">

    {{-- ── Page Header ── --}}
    <div class="page-header-bar">
        <div>
            <div class="breadcrumb-label">Analytics</div>
            <h2>Email Tracking</h2>
        </div>
        <span class="live-dot">Live data</span>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-grid">

        <div class="stat-card sent">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
            </div>
            <div class="stat-label">Emails Sent</div>
            <div class="stat-value">{{ number_format($totalSent) }}</div>
        </div>

        <div class="stat-card opens">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
            </div>
            <div class="stat-label">Total Opens</div>
            <div class="stat-value">{{ number_format($totalOpened) }}</div>
            <span class="stat-badge">↑ {{ $openRate }}% open rate</span>
        </div>

        <div class="stat-card clicks">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </div>
            <div class="stat-label">Total Clicks</div>
            <div class="stat-value">{{ number_format($totalClicks) }}</div>
            <span class="stat-badge">↑ {{ $clickRate }}% click rate</span>
        </div>

    </div>

    {{-- ── Log Table ── --}}
    <div class="log-card">

        <div class="log-card-header">
            <h5>Email Tracking Logs</h5>
            <span class="record-count">{{ $emailLogs->total() }} records</span>
        </div>

        <div class="table-responsive">
            <table class="analytics-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Recipient</th>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Sent At</th>
                        <th>Status</th>
                        <th>Opened At</th>
                        <th>Clicks</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($emailLogs as $index => $log)
                    <tr>
                        <td>{{ $emailLogs->firstItem() + $index }}</td>

                        <td class="td-email">{{ $log->email }}</td>

                        <td>
                            <div class="user-chip">
                                <div class="avatar">{{ strtoupper(substr($log->user->name ?? 'N', 0, 1)) }}</div>
                                <span>{{ $log->user->name ?? 'N/A' }}</span>
                            </div>
                        </td>

                        <td class="td-subject" title="{{ $log->subject }}">{{ $log->subject }}</td>

                        <td class="td-date">
                            {{ $log->created_at->format('d M Y') }}<br>
                            <span style="color:#9ca3af">{{ $log->created_at->format('H:i') }}</span>
                        </td>

                        <td>
                            @if($log->opened)
                                <span class="badge-opened">Opened</span>
                            @else
                                <span class="badge-not-opened">Unopened</span>
                            @endif
                        </td>

                        <td class="td-date">
                            @if($log->opened_at)
                                {{ $log->opened_at->format('d M Y') }}<br>
                                <span style="color:#9ca3af">{{ $log->opened_at->format('H:i') }}</span>
                            @else
                                <span style="color:#d1d5db">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge-clicks {{ $log->click_count == 0 ? 'badge-clicks-zero' : '' }}">
                                {{ $log->click_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>No email tracking data available yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if($emailLogs->hasPages())
        <div class="pagination-wrap">
            {{ $emailLogs->links() }}
        </div>
        @endif

    </div>

</div>
@endsection
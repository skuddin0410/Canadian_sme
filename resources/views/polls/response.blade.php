@extends('layouts.admin')

@section('content')

<style>
    /* ── Variables ── */
    :root {
        --pr-bg:        #f0f2f5;
        --pr-surface:   #ffffff;
        --pr-border:    #e2e6ea;
        --pr-accent:    #4f46e5;
        --pr-accent-lt: #eef2ff;
        --pr-text:      #1a1d23;
        --pr-muted:     #6b7280;
        --pr-yes-bg:    #ecfdf5;
        --pr-yes-fg:    #059669;
        --pr-no-bg:     #fff1f2;
        --pr-no-fg:     #e11d48;
        --pr-star-bg:   #fffbeb;
        --pr-star-fg:   #d97706;
    }

    /* ── Page wrapper ── */
    .pr-page {
        background: var(--pr-bg);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
    }

    .pr-inner {
        max-width: 1100px;
        margin: 0 auto;
    }

    /* ── Top bar ── */
    .pr-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .pr-breadcrumb {
        font-size: .75rem;
        color: var(--pr-muted);
        margin-bottom: .3rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .pr-breadcrumb svg { opacity: .5; }

    .pr-heading {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--pr-text);
        margin: 0;
        line-height: 1.25;
    }

    .pr-heading em {
        font-style: normal;
        color: var(--pr-accent);
    }

    /* ── Stat pills ── */
    .pr-stats {
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
    }

    .pr-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .85rem;
        border-radius: 999px;
        background: var(--pr-surface);
        border: 1px solid var(--pr-border);
        font-size: .75rem;
        color: var(--pr-muted);
        font-weight: 500;
        white-space: nowrap;
    }

    .pr-pill strong {
        color: var(--pr-text);
        font-weight: 700;
    }

    /* ── Card ── */
    .pr-card {
        background: var(--pr-surface);
        border: 1px solid var(--pr-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    }

    /* ── Table ── */
    .pr-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .pr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }

    .pr-table thead {
        background: #f8f9fb;
        border-bottom: 1px solid var(--pr-border);
    }

    .pr-table thead th {
        padding: .85rem 1.25rem;
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--pr-muted);
        white-space: nowrap;
        text-align: left;
    }

    .pr-table thead th:first-child {
        width: 60px;
        text-align: center;
    }

    .pr-table tbody tr {
        border-bottom: 1px solid var(--pr-border);
        transition: background .1s;
    }

    .pr-table tbody tr:last-child {
        border-bottom: none;
    }

    .pr-table tbody tr:hover {
        background: #fafbfc;
    }

    .pr-table td {
        padding: 1rem 1.25rem;
        color: var(--pr-text);
        vertical-align: middle;
    }

    /* ── Index cell ── */
    .pr-idx {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--pr-bg);
        font-size: .72rem;
        font-weight: 600;
        color: var(--pr-muted);
    }

    /* ── User cell ── */
    .pr-user {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
    }

    .pr-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--pr-accent);
        color: #fff;
        font-size: .68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .pr-avatar.is-guest {
        background: #e5e7eb;
        color: var(--pr-muted);
    }

    .pr-uname {
        font-weight: 600;
        font-size: .85rem;
        color: var(--pr-text);
    }

    /* ── Question cell ── */
    .pr-q {
        color: var(--pr-muted);
        font-size: .83rem;
        line-height: 1.45;
        max-width: 280px;
    }

    /* ── Answer badges ── */
    .pr-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .75rem;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }

    .pr-badge.yes  { background: var(--pr-yes-bg); color: var(--pr-yes-fg); }
    .pr-badge.no   { background: var(--pr-no-bg);  color: var(--pr-no-fg);  }
    .pr-badge.star { background: var(--pr-star-bg); color: var(--pr-star-fg); }

    .pr-badge.txt {
        background: var(--pr-accent-lt);
        color: var(--pr-accent);
        white-space: normal;
        max-width: 220px;
        border-radius: 8px;
        line-height: 1.4;
        font-weight: 400;
    }

    .pr-dash {
        color: #d1d5db;
        font-size: 1rem;
    }

    /* ── Date cell ── */
    .pr-date {
        font-size: .78rem;
        color: var(--pr-muted);
        white-space: nowrap;
        line-height: 1.5;
    }

    .pr-date b {
        display: block;
        color: var(--pr-text);
        font-weight: 600;
        font-size: .82rem;
    }

    /* ── Empty state ── */
    .pr-empty {
        padding: 4.5rem 2rem;
        text-align: center;
    }

    .pr-empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1.1rem;
        border-radius: 16px;
        background: var(--pr-bg);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pr-empty-icon svg { opacity: .4; }

    .pr-empty-title {
        font-weight: 700;
        color: var(--pr-text);
        margin-bottom: .35rem;
    }

    .pr-empty-sub {
        font-size: .85rem;
        color: var(--pr-muted);
    }

    /* ── Pagination footer ── */
    .pr-pager {
        padding: .9rem 1.25rem;
        border-top: 1px solid var(--pr-border);
        background: #f8f9fb;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    /* ── Row fade-in ── */
    @keyframes prFade {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: none; }
    }

    .pr-table tbody tr { animation: prFade .2s ease both; }
    .pr-table tbody tr:nth-child(1)  { animation-delay:   0ms; }
    .pr-table tbody tr:nth-child(2)  { animation-delay:  25ms; }
    .pr-table tbody tr:nth-child(3)  { animation-delay:  50ms; }
    .pr-table tbody tr:nth-child(4)  { animation-delay:  75ms; }
    .pr-table tbody tr:nth-child(5)  { animation-delay: 100ms; }
    .pr-table tbody tr:nth-child(6)  { animation-delay: 125ms; }
    .pr-table tbody tr:nth-child(7)  { animation-delay: 150ms; }
    .pr-table tbody tr:nth-child(8)  { animation-delay: 175ms; }
    .pr-table tbody tr:nth-child(9)  { animation-delay: 200ms; }
    .pr-table tbody tr:nth-child(10) { animation-delay: 225ms; }
</style>

<div class="pr-page">
<div class="pr-inner">

    {{-- Top bar --}}
    <div class="pr-topbar">
        <div>
            <div class="pr-breadcrumb">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Polls &rsaquo; Responses
            </div>
            <h1 class="pr-heading">
                Responses for: <em>{{ $poll->title }}</em>
            </h1>
        </div>

        <div class="pr-stats">
            <span class="pr-pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Total: <strong>{{ $answers->total() }}</strong>
            </span>
            <span class="pr-pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M3 9h18M9 21V9"/>
                </svg>
                Page <strong>{{ $answers->currentPage() }}</strong> of <strong>{{ $answers->lastPage() }}</strong>
            </span>
        </div>
    </div>

    {{-- Card --}}
    <div class="pr-card">
        <div class="pr-scroll">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($answers as $answer)
                        @php
                            $name     = $answer->user->name ?? 'Guest';
                            $isGuest  = !$answer->user;
                            $initials = $isGuest
                                ? '?'
                                : collect(explode(' ', $name))
                                    ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                        @endphp
                        <tr>
                            {{-- Index --}}
                            <td style="text-align:center">
                                <span class="pr-idx">{{ $loop->iteration }}</span>
                            </td>

                            {{-- User --}}
                            <td>
                                <div class="pr-user">
                                    <div class="pr-avatar {{ $isGuest ? 'is-guest' : '' }}">{{ $initials }}</div>
                                    <span class="pr-uname">{{ $name }}</span>
                                </div>
                            </td>

                            {{-- Question --}}
                            <td>
                                <div class="pr-q">{{ $answer->question->question }}</div>
                            </td>

                            {{-- Answer --}}
                            <td>
                                @if($answer->text_answer)
                                    <span class="pr-badge txt">{{ $answer->text_answer }}</span>
                                @elseif(!is_null($answer->yes_no_answer))
                                    @if($answer->yes_no_answer)
                                        <span class="pr-badge yes">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                            Yes
                                        </span>
                                    @else
                                        <span class="pr-badge no">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                            No
                                        </span>
                                    @endif
                                @elseif($answer->rating_answer)
                                    <span class="pr-badge star">⭐ {{ $answer->rating_answer }} / 5</span>
                                @else
                                    <span class="pr-dash">—</span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td>
                                <div class="pr-date">
                                    <b>{{ $answer->created_at->format('d M Y') }}</b>
                                    {{ $answer->created_at->format('H:i') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="pr-empty">
                                    <div class="pr-empty-icon">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="pr-empty-title">No responses yet</div>
                                    <div class="pr-empty-sub">Responses to this poll will appear here once submitted.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($answers->hasPages())
            <div class="pr-pager">
                {{ $answers->links() }}
            </div>
        @endif
    </div>

</div>
</div>

@endsection
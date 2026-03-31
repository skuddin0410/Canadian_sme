@extends('layouts.admin')

@section('content')

<style>
    :root {
        --bg: #f0f2f5;
        --surface: #ffffff;
        --border: #e2e6ea;
        --accent: #4f46e5;
        --accent-lt: #eef2ff;
        --text: #1a1d23;
        --muted: #6b7280;
        --green-bg: #ecfdf5;
        --green-fg: #059669;
        --amber-bg: #fffbeb;
        --amber-fg: #d97706;
        --subtle: #f8f9fb;
        --red-bg: #fff1f2;
        --red-fg: #e11d48;
        --purple-bg: #f5f3ff;
        --purple-fg: #7c3aed;
    }

    /* ── Layout ── */
    .ap-page {
        background: var(--bg);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
    }

    .ap-inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* ── Page header ── */
    .ap-header {
        margin-bottom: 1.75rem;
    }

    .ap-eyebrow {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: .3rem;
    }

    .ap-heading {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 1.1rem;
        line-height: 1.2;
    }

    /* ── Toolbar ── */
    .ap-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .65rem;
        margin-bottom: 1.1rem;
    }

    .ap-select {
        height: 36px;
        padding: 0 .85rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
        color: var(--text);
        font-size: .82rem;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        min-width: 200px;
    }

    .ap-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
    }

    .ap-toolbar-btn {
        height: 36px;
        padding: 0 1rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: .8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: background .15s, box-shadow .15s;
        text-decoration: none;
    }

    .ap-toolbar-btn.primary {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 1px 3px rgba(79, 70, 229, .25);
    }

    .ap-toolbar-btn.primary:hover {
        background: #4338ca;
    }

    .ap-toolbar-btn.secondary {
        background: var(--surface);
        color: var(--muted);
        border: 1px solid var(--border);
    }

    .ap-toolbar-btn.secondary:hover {
        background: var(--bg);
        color: var(--text);
    }

    .ap-toolbar-btn.export {
        background: #f0fdf4;
        color: var(--green-fg);
        border: 1px solid #bbf7d0;
    }

    .ap-toolbar-btn.export:hover {
        background: #dcfce7;
    }

    /* ── Stat pills ── */
    .ap-stats {
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
    }

    .ap-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .85rem;
        border-radius: 999px;
        background: var(--surface);
        border: 1px solid var(--border);
        font-size: .74rem;
        color: var(--muted);
        font-weight: 500;
    }

    .ap-pill strong {
        color: var(--text);
        font-weight: 700;
    }

    /* ── Card ── */
    .ap-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .04);
    }

    /* ── Table ── */
    .ap-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .ap-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .855rem;
    }

    .ap-table thead {
        background: var(--subtle);
        border-bottom: 1px solid var(--border);
    }

    .ap-table thead th {
        padding: .85rem 1.15rem;
        font-size: .67rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
        text-align: left;
    }

    .ap-table thead th:first-child {
        width: 56px;
        text-align: center;
    }

    .ap-table thead th:last-child {
        text-align: center;
        width: 88px;
    }

    .ap-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .1s;
        animation: rowIn .2s ease both;
    }

    .ap-table tbody tr:last-child {
        border-bottom: none;
    }

    .ap-table tbody tr:hover {
        background: #fafbfc;
    }

    .ap-table td {
        padding: .9rem 1.15rem;
        color: var(--text);
        vertical-align: middle;
    }

    .ap-table td:last-child {
        text-align: center;
    }

    .ap-table tbody tr:nth-child(1) {
        animation-delay: 0ms;
    }

    .ap-table tbody tr:nth-child(2) {
        animation-delay: 20ms;
    }

    .ap-table tbody tr:nth-child(3) {
        animation-delay: 40ms;
    }

    .ap-table tbody tr:nth-child(4) {
        animation-delay: 60ms;
    }

    .ap-table tbody tr:nth-child(5) {
        animation-delay: 80ms;
    }

    .ap-table tbody tr:nth-child(6) {
        animation-delay: 100ms;
    }

    .ap-table tbody tr:nth-child(7) {
        animation-delay: 120ms;
    }

    .ap-table tbody tr:nth-child(8) {
        animation-delay: 140ms;
    }

    .ap-table tbody tr:nth-child(9) {
        animation-delay: 160ms;
    }

    .ap-table tbody tr:nth-child(10) {
        animation-delay: 180ms;
    }

    @keyframes rowIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    /* ── Index badge ── */
    .ap-idx {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--bg);
        font-size: .72rem;
        font-weight: 600;
        color: var(--muted);
    }

    /* ── Poll name ── */
    .ap-poll-name {
        font-weight: 600;
        color: var(--text);
        font-size: .86rem;
    }

    /* ── Event ── */
    .ap-event {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        color: var(--muted);
    }

    /* ── Question text ── */
    .ap-q-text {
        font-size: .8rem;
        color: var(--muted);
        max-width: 220px;
        line-height: 1.4;
    }

    /* ── Answer badge ── */
    .ap-ans-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .65rem;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.4;
    }

    .ap-ans-badge.yes {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .ap-ans-badge.no {
        background: var(--red-bg);
        color: var(--red-fg);
    }

    .ap-ans-badge.star {
        background: var(--amber-bg);
        color: var(--amber-fg);
    }

    .ap-ans-badge.txt {
        background: var(--accent-lt);
        color: var(--accent);
        font-weight: 400;
        max-width: 180px;
        white-space: normal;
        line-height: 1.35;
    }

    .ap-ans-badge.opt {
        background: var(--purple-bg);
        color: var(--purple-fg);
    }

    /* ── User ── */
    .ap-user {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }

    .ap-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        font-size: .62rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .ap-avatar.guest {
        background: #e5e7eb;
        color: var(--muted);
    }

    .ap-uname {
        font-size: .83rem;
        font-weight: 500;
        color: var(--text);
    }

    /* ── Date ── */
    .ap-date {
        font-size: .77rem;
        color: var(--muted);
        line-height: 1.5;
        white-space: nowrap;
    }

    .ap-date b {
        display: block;
        color: var(--text);
        font-weight: 600;
        font-size: .81rem;
    }

    /* ── View btn ── */
    .ap-btn {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .85rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        background: var(--accent);
        color: #fff;
        font-size: .76rem;
        font-weight: 600;
        transition: background .15s, box-shadow .15s, transform .1s;
        box-shadow: 0 1px 3px rgba(79, 70, 229, .25);
    }

    .ap-btn:hover {
        background: #4338ca;
        box-shadow: 0 3px 10px rgba(79, 70, 229, .3);
    }

    .ap-btn:active {
        transform: scale(.97);
    }

    /* ── Empty ── */
    .ap-empty {
        padding: 4.5rem 2rem;
        text-align: center;
    }

    .ap-empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: var(--bg);
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ap-empty-icon svg {
        opacity: .4;
    }

    .ap-empty-title {
        font-weight: 700;
        color: var(--text);
        margin-bottom: .35rem;
    }

    .ap-empty-sub {
        font-size: .85rem;
        color: var(--muted);
    }

    /* ══════════════════════════════════════════
       MODAL — redesigned
    ══════════════════════════════════════════ */
    .ap-modal .modal-content {
        border: none;
        border-radius: 18px;
        box-shadow: 0 24px 72px rgba(0, 0, 0, .18);
        overflow: hidden;
        background: var(--surface);
    }

    /* Header */
    .ap-modal .modal-header {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 1.15rem 1.5rem;
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .am-header-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--accent-lt);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ap-modal .modal-title {
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
        flex: 1;
        margin: 0;
        line-height: 1.3;
    }

    .am-title-sub {
        font-size: .72rem;
        color: var(--muted);
        font-weight: 400;
        margin-top: 1px;
    }

    .ap-modal .btn-close {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--bg);
        border: 1px solid var(--border);
        opacity: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        flex-shrink: 0;
        font-size: .65rem;
    }

    .ap-modal .btn-close:hover {
        background: var(--border);
    }

    /* Body */
    .ap-modal .modal-body {
        padding: 0;
        background: var(--bg);
        max-height: 68vh;
        overflow-y: auto;
    }

    /* Poll info strip */
    .am-poll-strip {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 1rem 1.5rem;
    }

    .am-poll-strip-title {
        font-size: .92rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: .25rem;
    }

    .am-poll-strip-event {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .77rem;
        color: var(--muted);
    }

    /* Stats bar */
    .am-stats-bar {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .7rem 1.5rem;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .am-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .7rem;
        border-radius: 7px;
        font-size: .72rem;
        font-weight: 600;
    }

    .am-stat-chip.total {
        background: var(--accent-lt);
        color: var(--accent);
    }

    .am-stat-chip.qcount {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    /* Questions list */
    .am-body-wrap {
        padding: 1rem 1.25rem;
    }

    /* Question block */
    .am-question-block {
        margin-bottom: 1.25rem;
    }

    .am-question-block:last-child {
        margin-bottom: 0;
    }

    .am-q-head {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        padding: .8rem 1rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px 10px 0 0;
        border-bottom: none;
    }

    .am-q-num {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--accent);
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .am-q-text {
        font-size: .84rem;
        font-weight: 600;
        color: var(--text);
        line-height: 1.45;
        flex: 1;
    }

    .am-q-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .18rem .55rem;
        border-radius: 5px;
        font-size: .68rem;
        font-weight: 600;
        background: var(--purple-bg);
        color: var(--purple-fg);
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Answer rows under question */
    .am-answers-list {
        border: 1px solid var(--border);
        border-top: none;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
        background: var(--surface);
    }

    .am-answer {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .8rem 1rem;
        border-bottom: 1px solid var(--border);
        transition: background .1s;
    }

    .am-answer:last-child {
        border-bottom: none;
    }

    .am-answer:hover {
        background: var(--subtle);
    }

    /* Avatar */
    .am-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: #fff;
        font-size: .66rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .am-avatar.guest {
        background: #e5e7eb;
        color: var(--muted);
    }

    .am-avatar.av-1 {
        background: #6366f1;
    }

    .am-avatar.av-2 {
        background: #8b5cf6;
    }

    .am-avatar.av-3 {
        background: #ec4899;
    }

    .am-avatar.av-4 {
        background: #14b8a6;
    }

    .am-avatar.av-5 {
        background: #f59e0b;
    }

    .am-avatar.av-6 {
        background: #10b981;
    }

    .am-answer-body {
        flex: 1;
        min-width: 0;
    }

    .am-user {
        font-size: .8rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: .25rem;
    }

    .am-val {
        margin-bottom: .25rem;
    }

    .am-time {
        font-size: .7rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    /* Modal answer badges */
    .am-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .65rem;
        border-radius: 7px;
        font-size: .76rem;
        font-weight: 600;
        line-height: 1.4;
    }

    .am-badge.yes {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .am-badge.no {
        background: var(--red-bg);
        color: var(--red-fg);
    }

    .am-badge.star {
        background: var(--amber-bg);
        color: var(--amber-fg);
        gap: .2rem;
    }

    .am-badge.txt {
        background: var(--accent-lt);
        color: var(--accent);
        font-weight: 400;
        word-break: break-word;
        white-space: normal;
        font-size: .8rem;
    }

    .am-badge.opt {
        background: var(--purple-bg);
        color: var(--purple-fg);
    }

    /* No-answers inside question block */
    .am-no-answers {
        padding: 1.5rem;
        text-align: center;
        font-size: .8rem;
        color: var(--muted);
    }

    /* Loading */
    .am-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .75rem;
        padding: 4rem 1rem;
        color: var(--muted);
        font-size: .85rem;
    }

    .am-spinner {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2.5px solid var(--border);
        border-top-color: var(--accent);
        animation: am-spin .7s linear infinite;
    }

    @keyframes am-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Full empty state */
    .am-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 3.5rem 1.5rem;
        text-align: center;
    }

    .am-empty-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--bg);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .9rem;
    }

    .am-empty-icon svg {
        opacity: .35;
    }

    .am-empty-title {
        font-weight: 700;
        color: var(--text);
        margin-bottom: .3rem;
        font-size: .92rem;
    }

    .am-empty-sub {
        font-size: .8rem;
        color: var(--muted);
    }

    /* Row entrance */
    @keyframes amIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .am-answer {
        animation: amIn .16s ease both;
    }

    @media (max-width: 600px) {
        .ap-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .ap-select {
            width: 100%;
            min-width: unset;
        }

        .am-body-wrap {
            padding: .75rem;
        }
    }
</style>

<div class="ap-page">
    <div class="ap-inner">

        {{-- Header --}}
        <div class="ap-header">
            <div class="ap-eyebrow">Analytics</div>
            <h1 class="ap-heading">All Poll Responses</h1>

            {{-- Filter toolbar --}}
            <form method="GET" action="{{ route('polls.responses.index') }}">
                <div class="ap-toolbar">
                    <select name="event_id" class="ap-select">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit" class="ap-toolbar-btn primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                        Filter
                    </button>

                    @if(request('event_id'))
                    <a href="{{ route('polls.responses.index') }}" class="ap-toolbar-btn secondary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                        Reset
                    </a>
                    @endif

                    <a href="{{ route('response.export', request()->query()) }}" class="ap-toolbar-btn export">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        Export
                    </a>
                </div>
            </form>

            {{-- Stat pills --}}
            <div class="ap-stats">
                <span class="ap-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M3 9h18" />
                    </svg>
                    <strong>{{ $polls->count() }}</strong> polls listed
                </span>
            </div>
        </div>

        {{-- Table card --}}
        <div class="ap-card">
            <div class="ap-scroll">
                <table class="ap-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Poll</th>
                            <th>Event</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>User</th>
                            <th>Submitted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polls as $poll)
                        @foreach($poll->questions as $question)
                        @foreach($question->answers as $answer)
                        @php
                        $name = $answer->user->name ?? 'Guest';
                        $isGuest = !$answer->user;
                        $initials = $isGuest
                        ? '?'
                        : collect(explode(' ', $name))
                        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                        ->take(2)
                        ->implode('');
                        $type = $question->type;
                        $ansLabel = match($type) {
                        'text' => $answer->text_answer,
                        'yes_no' => ($answer->yes_no_answer ? 'Yes' : 'No'),
                        'rating' => $answer->rating_answer . ' / ' . $question->rating_scale,
                        'option' => $question->options->firstWhere('id', $answer->option_id)->option_text ?? 'N/A',
                        default => null,
                        };
                        $badgeClass = match($type) {
                        'text' => 'txt',
                        'yes_no' => ($answer->yes_no_answer ? 'yes' : 'no'),
                        'rating' => 'star',
                        'option' => 'opt',
                        default => '',
                        };
                        @endphp
                        <tr>
                            <td style="text-align:center">
                                <span class="ap-idx">{{ $loop->parent->parent->iteration }}</span>
                            </td>

                            <td>
                                <div class="ap-poll-name">{{ $poll->title }}</div>
                            </td>

                            <td>
                                <span class="ap-event">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.5;flex-shrink:0">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                    {{ $poll->event->title ?? '—' }}
                                </span>
                            </td>

                            <td>
                                <div class="ap-q-text">{{ $question->question }}</div>
                            </td>

                            <td>
                                @if($ansLabel !== null && $badgeClass !== '')
                                <span class="ap-ans-badge {{ $badgeClass }}">
                                    @if($type === 'rating')
                                    ⭐
                                    @endif
                                    {{ $ansLabel }}
                                </span>
                                @else
                                <span style="color:#d1d5db">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="ap-user">
                                    <div class="ap-avatar {{ $isGuest ? 'guest' : '' }}">{{ $initials }}</div>
                                    <span class="ap-uname">{{ $name }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="ap-date">
                                    <b>{{ $answer->created_at->format('d M Y') }}</b>
                                    {{ $answer->created_at->format('H:i') }}
                                </div>
                            </td>

                            <td>
                                <button class="ap-btn view-responses-btn" data-id="{{ $poll->id }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="ap-empty">
                                    <div class="ap-empty-icon">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ap-empty-title">No poll responses found</div>
                                    <div class="ap-empty-sub">Once polls receive submissions, they will appear here.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal --}}
<div class="modal fade ap-modal" id="responsesModal" tabindex="-1" aria-labelledby="responsesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <div class="am-header-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                    </svg>
                </div>
                <div style="flex:1;">
                    <h5 class="modal-title" id="responsesModalLabel">Poll Responses</h5>
                    <div class="am-title-sub" id="am-modal-sub">Loading…</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="10" height="10" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M1 1l12 12M13 1L1 13" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body" id="modalContent">
                <div class="am-loading">
                    <div class="am-spinner"></div>
                    Loading responses…
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const avatarColors = ['av-1', 'av-2', 'av-3', 'av-4', 'av-5', 'av-6'];

        function initials(name) {
            return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
        }

        function avatarClass(name) {
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            return avatarColors[Math.abs(hash) % avatarColors.length];
        }

        function formatDate(str) {
            const d = new Date(str);
            return d.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) +
                ' · ' +
                d.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        function answerBadge(answer, question) {

            if (question.type === 'text' && answer.text_answer) {
                return `<span class="am-badge txt">${answer.text_answer}</span>`;
            }

            if (question.type === 'yes_no' && answer.yes_no_answer !== null) {
                return answer.yes_no_answer ?
                    `<span class="am-badge yes">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                    Yes</span>` :
                    `<span class="am-badge no">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    No</span>`;
            }

            if (question.type === 'rating' && answer.rating_answer) {
                const scale = question.rating_scale ?? 5;
                const val = answer.rating_answer;
                let stars = '';
                for (let i = 1; i <= scale; i++) {
                    stars += `<svg width="11" height="11" viewBox="0 0 24 24" fill="${i <= val ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>`;
                }
                return `<span class="am-badge star">${stars}<span style="margin-left:3px;">${val} / ${scale}</span></span>`;
            }

            if (question.type === 'option') {
                const opt = question.options ? question.options.find(o => o.id === answer.option_id) : null;
                return `<span class="am-badge opt">${opt?.option_text ?? 'N/A'}</span>`;
            }

            return `<span style="color:#d1d5db;font-size:.8rem;">—</span>`;
        }

        document.querySelectorAll('.view-responses-btn').forEach(button => {
            button.addEventListener('click', function() {

                const pollId = this.getAttribute('data-id');
                const modalContent = document.getElementById('modalContent');
                const modalSubtitle = document.getElementById('am-modal-sub');

                modalSubtitle.textContent = 'Loading…';
                modalContent.innerHTML = `
                <div class="am-loading">
                    <div class="am-spinner"></div>
                    Loading responses…
                </div>`;

                const modal = new bootstrap.Modal(document.getElementById('responsesModal'));
                modal.show();

                fetch(`/admin/poll/${pollId}/show`)
                    .then(r => r.json())
                    .then(data => {

                        if (!data.success) {
                            modalSubtitle.textContent = 'Error';
                            modalContent.innerHTML = `
                            <div class="am-empty">
                                <div class="am-empty-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                </div>
                                <div class="am-empty-title">Could not load responses</div>
                                <div class="am-empty-sub">Please try again or refresh the page.</div>
                            </div>`;
                            return;
                        }

                        const poll = data.poll;

                        // Count total answers
                        let totalAnswers = 0;
                        let answeredQuestions = 0;
                        poll.questions.forEach(q => {
                            if (q.answers && q.answers.length) {
                                totalAnswers += q.answers.length;
                                answeredQuestions++;
                            }
                        });

                        modalSubtitle.textContent = poll.event?.title ?? 'No event';

                        let html = `
                        <!-- Poll strip -->
                        <div class="am-poll-strip">
                            <div class="am-poll-strip-title">${poll.title}</div>
                            <span class="am-poll-strip-event">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.55;flex-shrink:0;">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                                ${poll.event?.title ?? 'No event'}
                            </span>
                        </div>

                        <!-- Stats bar -->
                        <div class="am-stats-bar">
                            <span class="am-stat-chip total">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                ${totalAnswers} ${totalAnswers === 1 ? 'Response' : 'Responses'}
                            </span>
                            <span class="am-stat-chip qcount">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                ${poll.questions.length} ${poll.questions.length === 1 ? 'Question' : 'Questions'}
                            </span>
                        </div>

                        <!-- Questions -->
                        <div class="am-body-wrap">`;

                        let hasAny = false;

                        poll.questions.forEach((question, qi) => {
                            hasAny = true;
                            const qType = question.type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                            html += `
                            <div class="am-question-block">
                                <div class="am-q-head">
                                    <div class="am-q-num">${qi + 1}</div>
                                    <div class="am-q-text">${question.question}</div>
                                    <div class="am-q-badge">${qType}</div>
                                </div>
                                <div class="am-answers-list">`;

                            if (!question.answers || !question.answers.length) {
                                html += `<div class="am-no-answers">No responses for this question yet.</div>`;
                            } else {
                                question.answers.forEach((answer, ai) => {
                                    const name = answer.user?.name ?? 'Guest';
                                    const isGuest = !answer.user;
                                    const ini = isGuest ? '?' : initials(name);
                                    const avClass = isGuest ? 'guest' : avatarClass(name);
                                    const dateStr = formatDate(answer.created_at);

                                    html += `
                                    <div class="am-answer" style="animation-delay:${ai * 25}ms;">
                                        <div class="am-avatar ${avClass}">${ini}</div>
                                        <div class="am-answer-body">
                                            <div class="am-user">${name}</div>
                                            <div class="am-val">${answerBadge(answer, question)}</div>
                                            <div class="am-time">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.55;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                ${dateStr}
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            }

                            html += `</div></div>`; // close am-answers-list + am-question-block
                        });

                        if (!hasAny) {
                            html += `
                            <div class="am-empty">
                                <div class="am-empty-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="am-empty-title">No responses yet</div>
                                <div class="am-empty-sub">This poll hasn't received any answers.</div>
                            </div>`;
                        }

                        html += `</div>`; // close am-body-wrap

                        modalContent.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        modalSubtitle.textContent = 'Error';
                        modalContent.innerHTML = `
                        <div class="am-empty">
                            <div class="am-empty-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                            </div>
                            <div class="am-empty-title">Something went wrong</div>
                            <div class="am-empty-sub">Unable to fetch responses. Please try again.</div>
                        </div>`;
                    });
            });
        });
    });
</script>
@endsection
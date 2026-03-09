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
        padding: .22rem .65rem;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 600;
    }

    .ap-ans-badge.yes {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .ap-ans-badge.no {
        background: #fff1f2;
        color: #e11d48;
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
        border-radius: 7px;
        line-height: 1.35;
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

    /* ══ MODAL ══ */
    .ap-modal .modal-content {
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .13);
        overflow: hidden;
    }

    .ap-modal .modal-header {
        background: var(--subtle);
        border-bottom: 1px solid var(--border);
        padding: 1.1rem 1.4rem;
    }

    .ap-modal .modal-title {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
    }

    .ap-modal .modal-body {
        padding: 1.4rem;
    }

    .modal-poll-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: .25rem;
    }

    .modal-poll-event {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .78rem;
        color: var(--muted);
    }

    .modal-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 1rem 0;
    }

    .modal-question {
        background: var(--bg);
        border-radius: 10px;
        padding: .65rem 1rem;
        margin-bottom: .6rem;
        font-size: .82rem;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: flex-start;
        gap: .5rem;
    }

    .modal-question-num {
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--accent);
        color: #fff;
        font-size: .64rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-answer {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .75rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--border);
        margin-bottom: .5rem;
    }

    .modal-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        font-size: .64rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .modal-avatar.guest {
        background: #e5e7eb;
        color: var(--muted);
    }

    .modal-answer-user {
        font-size: .78rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: .2rem;
    }

    .modal-answer-val {
        font-size: .81rem;
    }

    .modal-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .2rem .6rem;
        border-radius: 6px;
        font-size: .74rem;
        font-weight: 600;
    }

    .modal-badge.yes {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .modal-badge.no {
        background: #fff1f2;
        color: #e11d48;
    }

    .modal-badge.star {
        background: var(--amber-bg);
        color: var(--amber-fg);
    }

    .modal-badge.txt {
        background: var(--accent-lt);
        color: var(--accent);
        font-weight: 400;
    }

    .modal-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        padding: 3rem;
        color: var(--muted);
        font-size: .88rem;
    }

    .modal-spinner {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid var(--border);
        border-top-color: var(--accent);
        animation: spin .7s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
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
                        $initials = $isGuest ? '?' : collect(explode(' ', $name))->map(fn($w) => mb_strtoupper(mb_substr($w,0,1)))->take(2)->implode('');

                        if ($answer->text_answer) {
                        $ansType = 'txt';
                        $ansLabel = $answer->text_answer;
                        } elseif (!is_null($answer->yes_no_answer)) {
                        $ansType = $answer->yes_no_answer ? 'yes' : 'no';
                        $ansLabel = $answer->yes_no_answer ? 'Yes' : 'No';
                        } elseif ($answer->rating_answer) {
                        $ansType = 'star';
                        $ansLabel = '⭐ ' . $answer->rating_answer;
                        } else {
                        $ansType = '';
                        $ansLabel = '—';
                        }
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
                                @if($ansType)
                                <span class="ap-ans-badge {{ $ansType }}">{{ $ansLabel }}</span>
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
<div class="modal fade ap-modal" id="responsesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Poll Responses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="modal-loading">
                    <div class="modal-spinner"></div>
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

        document.querySelectorAll('.view-responses-btn').forEach(button => {
            button.addEventListener('click', function() {

                const pollId = this.getAttribute('data-id');
                const modalContent = document.getElementById('modalContent');

                modalContent.innerHTML = `
                <div class="modal-loading">
                    <div class="modal-spinner"></div>
                    Loading responses…
                </div>`;

                const modal = new bootstrap.Modal(document.getElementById('responsesModal'));
                modal.show();

                fetch(`/admin/poll/${pollId}/show`)
                    .then(r => r.json())
                    .then(data => {

                        if (!data.success) {
                            modalContent.innerHTML = `<p class="text-center text-muted py-4">Could not load responses.</p>`;
                            return;
                        }

                        const poll = data.poll;

                        function initials(name) {
                            return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                        }

                        function answerBadge(answer) {
                            if (answer.text_answer)
                                return `<span class="modal-badge txt">${answer.text_answer}</span>`;
                            if (answer.yes_no_answer !== null && answer.yes_no_answer !== undefined)
                                return answer.yes_no_answer ?
                                    `<span class="modal-badge yes">✓ Yes</span>` :
                                    `<span class="modal-badge no">✗ No</span>`;
                            if (answer.rating_answer)
                                return `<span class="modal-badge star">⭐ ${answer.rating_answer} / 5</span>`;
                            return `<span style="color:#d1d5db">—</span>`;
                        }

                        let html = `
                        <div class="modal-poll-title">${poll.title}</div>
                        <span class="modal-poll-event">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            ${poll.event?.title ?? 'No event'}
                        </span>
                        <hr class="modal-divider">
                    `;

                        let hasAny = false;

                        poll.questions.forEach((question, qi) => {
                            if (!question.answers.length) return;
                            hasAny = true;

                            html += `
                            <div class="modal-question">
                                <div class="modal-question-num">${qi + 1}</div>
                                <div>${question.question}</div>
                            </div>
                        `;

                            question.answers.forEach(answer => {
                                const name = answer.user?.name ?? 'Guest';
                                const isGuest = !answer.user;
                                const ini = isGuest ? '?' : initials(name);

                                html += `
                                <div class="modal-answer">
                                    <div class="modal-avatar ${isGuest ? 'guest' : ''}">${ini}</div>
                                    <div>
                                        <div class="modal-answer-user">${name}</div>
                                        <div class="modal-answer-val">${answerBadge(answer)}</div>
                                    </div>
                                </div>
                            `;
                            });
                        });

                        if (!hasAny) {
                            html += `
                            <div class="ap-empty">
                                <div class="ap-empty-icon" style="margin:0 auto 1rem">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="ap-empty-title">No answers yet</div>
                                <div class="ap-empty-sub">This poll hasn't received any responses.</div>
                            </div>`;
                        }

                        modalContent.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        modalContent.innerHTML = `<p class="text-center text-muted py-4">Something went wrong.</p>`;
                    });
            });
        });
    });
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Poll Details')

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
        --subtle: #f8f9fb;
        --green-bg: #ecfdf5;
        --green-fg: #059669;
        --amber-bg: #fffbeb;
        --amber-fg: #d97706;
        --purple-bg: #f5f3ff;
        --purple-fg: #7c3aed;
        --red-bg: #fff1f2;
        --red-fg: #e11d48;
        --yellow-bg: #fefce8;
        --yellow-fg: #a16207;
    }

    /* ── Wrapper ── */
    .pd-wrap {
        background: var(--bg);
        min-height: 100vh;
        padding: 2rem 1.5rem 4rem;
    }

    .pd-inner {
        width: 100%;
        margin: 0 auto;
    }

    /* ── Page header ── */
    .pd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .pd-header-left {
        display: flex;
        align-items: center;
        gap: .875rem;
    }

    .pd-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--muted);
        text-decoration: none;
        transition: background .15s, color .15s;
        flex-shrink: 0;
    }

    .pd-back:hover {
        background: var(--border);
        color: var(--text);
    }

    .pd-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        letter-spacing: -.01em;
        line-height: 1.2;
    }

    .pd-subtitle {
        font-size: .76rem;
        color: var(--muted);
        margin: .1rem 0 0;
    }

    /* ── Card ── */
    .pd-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .05), 0 4px 16px rgba(0, 0, 0, .04);
    }

    .pd-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.4rem;
        border-bottom: 1px solid var(--border);
        background: var(--subtle);
    }

    .pd-card-head-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-weight: 700;
        font-size: .9rem;
        color: var(--text);
        margin: 0;
    }

    .pd-card-head-title svg {
        color: var(--muted);
        flex-shrink: 0;
    }

    .pd-card-head-badge {
        font-size: .74rem;
        color: var(--muted);
        font-weight: 500;
    }

    .pd-card-body {
        padding: 1.4rem;
        width: 100%;
    }

    /* ── Info grid ── */
    .pd-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .pd-item {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: .85rem 1.1rem;
    }

    .pd-item.span2 {
        grid-column: 1 / -1;
    }

    .pd-label {
        font-size: .68rem;
        font-weight: 600;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: .3rem;
    }

    .pd-value {
        font-size: .88rem;
        font-weight: 500;
        color: var(--text);
    }

    /* ── Status badges ── */
    .st-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .75rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
    }

    .st-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .st-active {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .st-active::before {
        background: var(--green-fg);
    }

    .st-inactive {
        background: var(--border);
        color: var(--muted);
    }

    .st-inactive::before {
        background: var(--muted);
    }

    .st-upcoming {
        background: var(--yellow-bg);
        color: var(--yellow-fg);
    }

    .st-upcoming::before {
        background: var(--yellow-fg);
    }

    .st-expired {
        background: var(--red-bg);
        color: var(--red-fg);
    }

    .st-expired::before {
        background: var(--red-fg);
    }

    /* ── Question items ── */
    .pd-question {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: box-shadow .15s, border-color .15s;
    }

    .pd-question:last-child {
        margin-bottom: 0;
    }

    .pd-question:hover {
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        border-color: #c7d2fe;
    }

    .pd-q-head {
        display: flex;
        align-items: flex-start;
        gap: .875rem;
        padding: 1rem 1.25rem;
        background: var(--subtle);
        border-bottom: 1px solid var(--border);
    }

    .pd-q-num {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: var(--accent);
        color: #fff;
        font-size: .7rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .pd-q-text {
        font-weight: 600;
        color: var(--text);
        font-size: .88rem;
        line-height: 1.45;
        flex: 1;
    }

    .pd-q-arrow {
        color: var(--muted);
        margin-top: 2px;
        flex-shrink: 0;
    }

    .pd-q-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
        padding: .75rem 1.25rem;
    }

    .pd-pill {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .22rem .65rem;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 600;
    }

    .pd-pill.type {
        background: var(--purple-bg);
        color: var(--purple-fg);
    }

    .pd-pill.rating {
        background: var(--amber-bg);
        color: var(--amber-fg);
    }

    .pd-pill.count {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    /* ── Empty ── */
    .pd-empty {
        text-align: center;
        padding: 3.5rem 1rem;
    }

    .pd-empty-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--bg);
        margin: 0 auto .9rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pd-empty-icon svg {
        opacity: .4;
    }

    .pd-empty-title {
        font-weight: 700;
        color: var(--text);
        margin-bottom: .3rem;
    }

    .pd-empty-sub {
        font-size: .83rem;
        color: var(--muted);
    }

    /* ── Modal ── */
    .pd-modal .modal-content {
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .13);
        overflow: hidden;
    }

    .pd-modal .modal-header {
        background: var(--subtle);
        border-bottom: 1px solid var(--border);
        padding: 1.1rem 1.4rem;
    }

    .pd-modal .modal-title {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
    }

    .pd-modal .modal-body {
        padding: 1.4rem;
    }

    .pm-q-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: .25rem;
    }

    .pm-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 1rem 0;
    }

    /* answer card */
    .pm-answer {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .8rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--border);
        margin-bottom: .5rem;
        transition: background .1s;
    }

    .pm-answer:hover {
        background: var(--subtle);
    }

    .pm-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        font-size: .67rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .pm-avatar.guest {
        background: #e5e7eb;
        color: var(--muted);
    }

    .pm-user {
        font-size: .8rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: .2rem;
    }

    .pm-time {
        font-size: .72rem;
        color: var(--muted);
        margin-top: .2rem;
    }

    .pm-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .22rem .65rem;
        border-radius: 6px;
        font-size: .76rem;
        font-weight: 600;
    }

    .pm-badge.yes {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .pm-badge.no {
        background: var(--red-bg);
        color: var(--red-fg);
    }

    .pm-badge.star {
        background: var(--amber-bg);
        color: var(--amber-fg);
    }

    .pm-badge.txt {
        background: var(--accent-lt);
        color: var(--accent);
        font-weight: 400;
    }

    /* spinner */
    .pm-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        padding: 3rem;
        color: var(--muted);
        font-size: .88rem;
    }

    .pm-spinner {
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

    /* row entrance */
    @keyframes pdIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .pd-question {
        animation: pdIn .22s ease both;
    }

    .pd-question:nth-child(1) {
        animation-delay: 0ms;
    }

    .pd-question:nth-child(2) {
        animation-delay: 35ms;
    }

    .pd-question:nth-child(3) {
        animation-delay: 70ms;
    }

    .pd-question:nth-child(4) {
        animation-delay: 105ms;
    }

    .pd-question:nth-child(5) {
        animation-delay: 140ms;
    }

    .pd-question:nth-child(6) {
        animation-delay: 175ms;
    }

    @media (max-width: 576px) {
        .pd-wrap {
            padding: 1rem 1rem 3rem;
        }

        .pd-grid {
            grid-template-columns: 1fr;
        }

        .pd-item.span2 {
            grid-column: auto;
        }
    }
</style>

<div class="pd-wrap">
    <div class="pd-inner">

        {{-- Page header --}}
        <div class="pd-header">
            <div class="pd-header-left">
                <a href="{{ route('polls.index') }}" class="pd-back" title="Back to Polls">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="pd-title">Poll Details</h1>
                    <p class="pd-subtitle">Viewing poll #{{ $poll->id }}</p>
                </div>
            </div>
        </div>

        {{-- Info card --}}
        <div class="pd-card">
            <div class="pd-card-head">
                <h5 class="pd-card-head-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4M12 8h.01" />
                    </svg>
                    Poll Information
                </h5>
            </div>
            <div class="pd-card-body">
                <div class="pd-grid">

                    <div class="pd-item span2">
                        <div class="pd-label">Title</div>
                        <div class="pd-value">{{ $poll->title }}</div>
                    </div>

                    <div class="pd-item">
                        <div class="pd-label">Event</div>
                        <div class="pd-value">{{ $poll->event->title ?? '—' }}</div>
                    </div>

                    <div class="pd-item">
                        <div class="pd-label">Session</div>
                        <div class="pd-value">{{ $poll->eventSession->name ?? 'All Sessions' }}</div>
                    </div>

                    <div class="pd-item">
                        <div class="pd-label">Start Date</div>
                        <div class="pd-value">
                            {{ $poll->start_date ? $poll->start_date->format('d M Y, H:i') : '—' }}
                        </div>
                    </div>

                    <div class="pd-item">
                        <div class="pd-label">End Date</div>
                        <div class="pd-value">
                            {{ $poll->end_date ? $poll->end_date->format('d M Y, H:i') : '—' }}
                        </div>
                    </div>

                    <div class="pd-item span2">
                        <div class="pd-label">Status</div>
                        <div class="pd-value" style="margin-top:.15rem">
                            @php $now = now(); @endphp
                            @if(!$poll->is_active)
                            <span class="st-badge st-inactive">Inactive</span>
                            @elseif($poll->start_date && $now->lt($poll->start_date))
                            <span class="st-badge st-upcoming">Upcoming</span>
                            @elseif($poll->end_date && $now->gt($poll->end_date))
                            <span class="st-badge st-expired">Expired</span>
                            @else
                            <span class="st-badge st-active">Active</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Questions card --}}
        <div class="pd-card">
            <div class="pd-card-head">
                <h5 class="pd-card-head-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    Questions & Responses
                </h5>
                <span class="pd-card-head-badge">
                    {{ $poll->questions->count() }} {{ Str::plural('question', $poll->questions->count()) }}
                </span>
            </div>
            <div class="pd-card-body">

                @forelse($poll->questions as $question)
                <div class="pd-question question-click" data-id="{{ $question->id }}">
                    <div class="pd-q-head">
                        <div class="pd-q-num">{{ $loop->iteration }}</div>
                        <div class="pd-q-text">{{ $question->question }}</div>
                        <div class="pd-q-arrow">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </div>
                    </div>
                    <div class="pd-q-meta">
                        <span class="pd-pill type">
                            <i class="fa-solid fa-tag" style="font-size:.6rem;"></i>
                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                        </span>

                        @if($question->type == 'rating')
                        <span class="pd-pill rating">
                            <i class="fa-solid fa-star" style="font-size:.6rem;"></i>
                            Scale: {{ $question->rating_scale }}
                        </span>
                        @endif

                        <span class="pd-pill count">
                            <i class="fa-solid fa-reply" style="font-size:.6rem;"></i>
                            {{ $question->answers->count() }} {{ Str::plural('response', $question->answers->count()) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="pd-empty">
                    <div class="pd-empty-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="pd-empty-title">No questions yet</div>
                    <div class="pd-empty-sub">No questions have been added to this poll.</div>
                </div>
                @endforelse

            </div>
        </div>

    </div>
</div>

{{-- Modal --}}
<div class="modal fade pd-modal" id="questionAnswersModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Responses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="questionModalContent">
                <div class="pm-loading">
                    <div class="pm-spinner"></div>
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

        document.querySelectorAll('.question-click').forEach(item => {
            item.addEventListener('click', function() {

                const qId = this.getAttribute('data-id');
                const modalContent = document.getElementById('questionModalContent');

                modalContent.innerHTML = `
                <div class="pm-loading">
                    <div class="pm-spinner"></div>
                    Loading responses…
                </div>`;

                const modal = new bootstrap.Modal(document.getElementById('questionAnswersModal'));
                modal.show();

                fetch(`/admin/question/${qId}/answers`)
                    .then(r => r.json())
                    .then(data => {

                        if (!data.success) {
                            modalContent.innerHTML = `<p class="text-center text-muted py-4">Could not load responses.</p>`;
                            return;
                        }

                        const question = data.question;

                        function initials(name) {
                            return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                        }

                        function answerBadge(answer) {
                            if (answer.text_answer)
                                return `<span class="pm-badge txt">${answer.text_answer}</span>`;
                            if (answer.yes_no_answer !== null && answer.yes_no_answer !== undefined)
                                return answer.yes_no_answer ?
                                    `<span class="pm-badge yes"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg> Yes</span>` :
                                    `<span class="pm-badge no"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg> No</span>`;
                            if (answer.rating_answer)
                                return `<span class="pm-badge star">⭐ ${answer.rating_answer} / 5</span>`;
                            return `<span style="color:#d1d5db">—</span>`;
                        }

                        let html = `
                        <div class="pm-q-title">${question.question}</div>
                        <hr class="pm-divider">
                    `;

                        if (!question.answers.length) {
                            html += `
                            <div style="text-align:center;padding:2.5rem 1rem;">
                                <div style="width:48px;height:48px;border-radius:12px;background:var(--bg);display:flex;align-items:center;justify-content:center;margin:0 auto .8rem;opacity:.5;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                </div>
                                <div style="font-weight:700;color:var(--text);margin-bottom:.3rem">No responses yet</div>
                                <div style="font-size:.82rem;color:var(--muted)">This question hasn't been answered.</div>
                            </div>`;
                        } else {
                            question.answers.forEach(answer => {
                                const name = answer.user?.name ?? 'Guest';
                                const isGuest = !answer.user;
                                const ini = isGuest ? '?' : initials(name);
                                const date = new Date(answer.created_at).toLocaleString();

                                html += `
                                <div class="pm-answer">
                                    <div class="pm-avatar ${isGuest ? 'guest' : ''}">${ini}</div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="pm-user">${name}</div>
                                        <div>${answerBadge(answer)}</div>
                                        <div class="pm-time">${date}</div>
                                    </div>
                                </div>`;
                            });
                        }

                        modalContent.innerHTML = html;
                    })
                    .catch(err => {
                        console.error(err);
                        modalContent.innerHTML = `<p class="text-center text-muted py-4">Something went wrong.</p>`;
                    });
            });
        });

    });
</script>
@endsection
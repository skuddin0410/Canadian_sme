@extends('layouts.admin')

@section('content')

<style>
    :root {
        --bg:         #f0f2f5;
        --surface:    #ffffff;
        --border:     #e2e6ea;
        --accent:     #4f46e5;
        --accent-lt:  #eef2ff;
        --text:       #1a1d23;
        --muted:      #6b7280;
        --green-bg:   #ecfdf5;
        --green-fg:   #059669;
        --amber-bg:   #fffbeb;
        --amber-fg:   #d97706;
    }

    /* ── Layout ── */
    .ap-page  { background: var(--bg); min-height: 100vh; padding: 2rem 1.5rem 4rem; }
    .ap-inner { max-width: 1100px; margin: 0 auto; }

    /* ── Header ── */
    .ap-header { margin-bottom: 1.75rem; }
    .ap-eyebrow {
        font-size: .72rem; font-weight: 600; letter-spacing: .1em;
        text-transform: uppercase; color: var(--accent); margin-bottom: .35rem;
    }
    .ap-heading {
        font-size: 1.5rem; font-weight: 700; color: var(--text);
        margin: 0; line-height: 1.2;
    }

    /* ── Stat pills ── */
    .ap-stats { display: flex; gap: .6rem; flex-wrap: wrap; margin-top: .85rem; }
    .ap-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .35rem .85rem; border-radius: 999px;
        background: var(--surface); border: 1px solid var(--border);
        font-size: .74rem; color: var(--muted); font-weight: 500;
    }
    .ap-pill strong { color: var(--text); font-weight: 700; }

    /* ── Card ── */
    .ap-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 14px; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    }

    /* ── Table ── */
    .ap-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .ap-table  { width: 100%; border-collapse: collapse; font-size: .875rem; }

    .ap-table thead {
        background: #f8f9fb;
        border-bottom: 1px solid var(--border);
    }
    .ap-table thead th {
        padding: .85rem 1.2rem; font-size: .68rem; font-weight: 600;
        letter-spacing: .08em; text-transform: uppercase;
        color: var(--muted); white-space: nowrap; text-align: left;
    }
    .ap-table thead th:first-child { width: 56px; text-align: center; }
    .ap-table thead th:last-child  { text-align: center; width: 90px; }

    .ap-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .1s;
        animation: rowIn .2s ease both;
    }
    .ap-table tbody tr:last-child  { border-bottom: none; }
    .ap-table tbody tr:hover       { background: #fafbfc; }

    .ap-table td {
        padding: .95rem 1.2rem;
        color: var(--text); vertical-align: middle;
    }
    .ap-table td:last-child { text-align: center; }

    /* stagger */
    .ap-table tbody tr:nth-child(1)  { animation-delay:   0ms; }
    .ap-table tbody tr:nth-child(2)  { animation-delay:  25ms; }
    .ap-table tbody tr:nth-child(3)  { animation-delay:  50ms; }
    .ap-table tbody tr:nth-child(4)  { animation-delay:  75ms; }
    .ap-table tbody tr:nth-child(5)  { animation-delay: 100ms; }
    .ap-table tbody tr:nth-child(6)  { animation-delay: 125ms; }
    .ap-table tbody tr:nth-child(7)  { animation-delay: 150ms; }
    .ap-table tbody tr:nth-child(8)  { animation-delay: 175ms; }
    .ap-table tbody tr:nth-child(9)  { animation-delay: 200ms; }
    .ap-table tbody tr:nth-child(10) { animation-delay: 225ms; }

    @keyframes rowIn {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: none; }
    }

    /* ── Index badge ── */
    .ap-idx {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 8px;
        background: var(--bg); font-size: .72rem; font-weight: 600; color: var(--muted);
    }

    /* ── Poll title ── */
    .ap-poll-name { font-weight: 600; color: var(--text); font-size: .88rem; }
    .ap-poll-sub  { font-size: .76rem; color: var(--muted); margin-top: .15rem; }

    /* ── Event chip ── */
    .ap-event {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .78rem; color: var(--muted);
    }
    .ap-event svg { flex-shrink: 0; opacity: .55; }

    /* ── Count badges ── */
    .ap-count {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .25rem .65rem; border-radius: 8px;
        font-size: .75rem; font-weight: 600;
    }
    .ap-count.q { background: var(--accent-lt); color: var(--accent); }
    .ap-count.a { background: var(--green-bg);  color: var(--green-fg); }

    /* ── Date ── */
    .ap-date { font-size: .78rem; color: var(--muted); line-height: 1.5; white-space: nowrap; }
    .ap-date b { display: block; color: var(--text); font-weight: 600; font-size: .82rem; }

    /* ── View button ── */
    .ap-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .4rem .9rem; border-radius: 8px; border: none; cursor: pointer;
        background: var(--accent); color: #fff;
        font-size: .78rem; font-weight: 600; letter-spacing: .02em;
        transition: background .15s, transform .1s, box-shadow .15s;
        box-shadow: 0 1px 3px rgba(79,70,229,.3);
    }
    .ap-btn:hover  { background: #4338ca; box-shadow: 0 3px 10px rgba(79,70,229,.35); }
    .ap-btn:active { transform: scale(.97); }

    /* ── Empty state ── */
    .ap-empty { padding: 4.5rem 2rem; text-align: center; }
    .ap-empty-icon {
        width: 56px; height: 56px; border-radius: 16px;
        background: var(--bg); margin: 0 auto 1rem;
        display: flex; align-items: center; justify-content: center;
    }
    .ap-empty-icon svg { opacity: .4; }
    .ap-empty-title { font-weight: 700; color: var(--text); margin-bottom: .35rem; }
    .ap-empty-sub   { font-size: .85rem; color: var(--muted); }

    /* ══════════════════════════════════════
       MODAL
    ══════════════════════════════════════ */
    .ap-modal .modal-content {
        border: 1px solid var(--border); border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,.14); overflow: hidden;
    }
    .ap-modal .modal-header {
        background: #f8f9fb; border-bottom: 1px solid var(--border);
        padding: 1.1rem 1.4rem;
    }
    .ap-modal .modal-title { font-weight: 700; font-size: 1rem; color: var(--text); }
    .ap-modal .modal-body  { padding: 1.4rem; background: var(--surface); }

    /* modal poll meta */
    .modal-poll-title { font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: .3rem; }
    .modal-poll-event {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .8rem; color: var(--muted);
    }
    .modal-divider { border: none; border-top: 1px solid var(--border); margin: 1rem 0; }

    /* question block */
    .modal-question {
        background: var(--bg); border-radius: 10px;
        padding: .7rem 1rem; margin-bottom: .65rem;
        font-size: .82rem; font-weight: 700; color: var(--text);
        display: flex; align-items: flex-start; gap: .5rem;
    }
    .modal-question-num {
        flex-shrink: 0; width: 22px; height: 22px; border-radius: 6px;
        background: var(--accent); color: #fff;
        font-size: .65rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }

    /* answer card */
    .modal-answer {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .75rem 1rem; border-radius: 10px;
        border: 1px solid var(--border); margin-bottom: .5rem;
        background: var(--surface);
    }
    .modal-avatar {
        width: 30px; height: 30px; border-radius: 50%;
        background: var(--accent); color: #fff;
        font-size: .65rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; text-transform: uppercase;
    }
    .modal-avatar.guest { background: #e5e7eb; color: var(--muted); }
    .modal-answer-user  { font-size: .78rem; font-weight: 600; color: var(--text); margin-bottom: .25rem; }
    .modal-answer-val   { font-size: .82rem; color: var(--muted); }

    .modal-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .2rem .6rem; border-radius: 6px;
        font-size: .75rem; font-weight: 600;
    }
    .modal-badge.yes  { background: var(--green-bg); color: var(--green-fg); }
    .modal-badge.no   { background: #fff1f2; color: #e11d48; }
    .modal-badge.star { background: var(--amber-bg); color: var(--amber-fg); }
    .modal-badge.txt  { background: var(--accent-lt); color: var(--accent); font-weight: 400; }

    /* modal loading */
    .modal-loading {
        display: flex; align-items: center; justify-content: center;
        gap: .6rem; padding: 3rem 1rem; color: var(--muted); font-size: .88rem;
    }
    .modal-spinner {
        width: 18px; height: 18px; border: 2px solid var(--border);
        border-top-color: var(--accent); border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="ap-page">
<div class="ap-inner">

    {{-- Header --}}
    <div class="ap-header">
        <div class="ap-eyebrow">Analytics</div>
        <h1 class="ap-heading">All Poll Responses</h1>
        <div class="ap-stats">
            <span class="ap-pill">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg>
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
                        <th>Questions</th>
                        <th>Answers</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($polls as $poll)
                    <tr>
                        <td style="text-align:center">
                            <span class="ap-idx">{{ $loop->iteration }}</span>
                        </td>

                        <td>
                            <div class="ap-poll-name">{{ $poll->title }}</div>
                        </td>

                        <td>
                            <span class="ap-event">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                {{ $poll->event->title ?? '—' }}
                            </span>
                        </td>

                        <td>
                            <span class="ap-count q">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                                {{ $poll->questions_count }}
                            </span>
                        </td>

                        <td>
                            <span class="ap-count a">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                {{ $poll->answers_count }}
                            </span>
                        </td>

                        <td>
                            <div class="ap-date">
                                <b>{{ $poll->created_at->format('d M Y') }}</b>
                                {{ $poll->created_at->format('H:i') }}
                            </div>
                        </td>

                        <td>
                            <button class="ap-btn view-responses-btn" data-id="{{ $poll->id }}">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="ap-empty">
                                <div class="ap-empty-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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

<!-- Modal -->
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
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.view-responses-btn').forEach(button => {
        button.addEventListener('click', function () {

            const pollId = this.getAttribute('data-id');
            const modalContent = document.getElementById('modalContent');

            // Show spinner
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

                    // Helper: answer badge HTML
                    function answerBadge(answer) {
                        if (answer.text_answer)
                            return `<span class="modal-badge txt">${answer.text_answer}</span>`;
                        if (answer.yes_no_answer !== null && answer.yes_no_answer !== undefined)
                            return answer.yes_no_answer
                                ? `<span class="modal-badge yes">✓ Yes</span>`
                                : `<span class="modal-badge no">✗ No</span>`;
                        if (answer.rating_answer)
                            return `<span class="modal-badge star">⭐ ${answer.rating_answer} / 5</span>`;
                        return `<span style="color:#d1d5db">—</span>`;
                    }

                    // Helper: initials
                    function initials(name) {
                        return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
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
                            const name    = answer.user?.name ?? 'Guest';
                            const isGuest = !answer.user;
                            const ini     = isGuest ? '?' : initials(name);

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
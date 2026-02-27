@extends('layouts.admin')

@section('title', 'Poll Details')

@section('content')

<style>
    .poll-detail-wrapper {
        padding: 2rem;
        width:100%;
    }

    /* ── Page Header ── */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
    }

    .page-header-left {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .back-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .page-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .page-subtitle {
        font-size: 0.8rem;
        color: #94a3b8;
        margin: 0.1rem 0 0;
    }

    /* ── Card Base ── */
    .detail-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf4;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .detail-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }

    .detail-card-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .detail-card-body {
        padding: 1.5rem;
    }

    /* ── Poll Info Grid ── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .info-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 0.9rem 1.1rem;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 0.3rem;
    }

    .info-value {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1e293b;
    }

    /* ── Status Badge ── */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.65rem;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 600;
    }

    .badge-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .badge-active    { background: #dcfce7; color: #16a34a; }
    .badge-active::before    { background: #16a34a; }
    .badge-inactive  { background: #f1f5f9; color: #64748b; }
    .badge-inactive::before  { background: #94a3b8; }
    .badge-upcoming  { background: #fef9c3; color: #a16207; }
    .badge-upcoming::before  { background: #eab308; }
    .badge-expired   { background: #fee2e2; color: #dc2626; }
    .badge-expired::before   { background: #dc2626; }

    /* ── Questions ── */
    .question-item {
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: box-shadow 0.15s ease;
    }

    .question-item:hover {
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .question-item:last-child {
        margin-bottom: 0;
    }

    .question-header {
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .question-number {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #1e293b;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .question-text {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
        line-height: 1.45;
        flex: 1;
    }

    .question-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.6rem;
        padding: 0.75rem 1.25rem;
    }

    .q-type-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #ede9fe;
        color: #6d28d9;
        border-radius: 6px;
        padding: 0.2rem 0.6rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .q-response-count {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f0fdf4;
        color: #16a34a;
        border-radius: 6px;
        padding: 0.2rem 0.6rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .q-rating-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #fff7ed;
        color: #c2410c;
        border-radius: 6px;
        padding: 0.2rem 0.6rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    /* ── Empty State ── */
    .empty-questions {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-questions i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.75rem;
        color: #cbd5e1;
    }

    .empty-questions p {
        margin: 0;
        font-size: 0.875rem;
    }

    /* ── Responsive ── */
    @media (max-width: 576px) {
        .poll-detail-wrapper { padding: 1rem; }
        .info-grid { grid-template-columns: 1fr; }
        .info-item.full-width { grid-column: auto; }
    }
</style>

<div class="poll-detail-wrapper">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <a href="{{ route('polls.index') }}" class="back-btn" title="Back to Polls">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title">Poll Details</h1>
                <p class="page-subtitle">Viewing poll #{{ $poll->id }}</p>
            </div>
        </div>
    </div>

    {{-- Poll Info Card --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <h5>
                <i class="fa-solid fa-circle-info me-2 text-muted" style="font-size:0.85rem;"></i>
                Poll Information
            </h5>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">

                <div class="info-item full-width">
                    <div class="info-label">Title</div>
                    <div class="info-value">{{ $poll->title }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Event</div>
                    <div class="info-value">{{ $poll->event->title ?? '—' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Session</div>
                    <div class="info-value">{{ $poll->eventSession->name ?? 'All Sessions' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Start Date</div>
                    <div class="info-value">
                        {{ $poll->start_date ? $poll->start_date->format('d M Y, H:i') : '—' }}
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">End Date</div>
                    <div class="info-value">
                        {{ $poll->end_date ? $poll->end_date->format('d M Y, H:i') : '—' }}
                    </div>
                </div>

                <div class="info-item full-width">
                    <div class="info-label">Status</div>
                    <div class="info-value" style="margin-top:0.15rem;">
                        @php $now = now(); @endphp

                        @if(!$poll->is_active)
                            <span class="badge-status badge-inactive">Inactive</span>
                        @elseif($poll->start_date && $now->lt($poll->start_date))
                            <span class="badge-status badge-upcoming">Upcoming</span>
                        @elseif($poll->end_date && $now->gt($poll->end_date))
                            <span class="badge-status badge-expired">Expired</span>
                        @else
                            <span class="badge-status badge-active">Active</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Questions Card --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <h5>
                <i class="fa-solid fa-list-ul me-2 text-muted" style="font-size:0.85rem;"></i>
                Questions & Responses
            </h5>
            <span style="font-size:0.8rem; color:#94a3b8; font-weight:500;">
                {{ $poll->questions->count() }} {{ Str::plural('question', $poll->questions->count()) }}
            </span>
        </div>
        <div class="detail-card-body">

            @forelse($poll->questions as $question)
            <div class="question-item">
                <div class="question-header">
                    <div class="question-number">{{ $loop->iteration }}</div>
                    <div class="question-text">{{ $question->question }}</div>
                </div>
                <div class="question-meta">
                    <span class="q-type-pill">
                        <i class="fa-solid fa-tag" style="font-size:0.65rem;"></i>
                        {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                    </span>

                    @if($question->type == 'rating')
                    <span class="q-rating-pill">
                        <i class="fa-solid fa-star" style="font-size:0.65rem;"></i>
                        Scale: {{ $question->rating_scale }}
                    </span>
                    @endif

                    <span class="q-response-count">
                        <i class="fa-solid fa-reply" style="font-size:0.65rem;"></i>
                        {{ $question->answers->count() }} {{ Str::plural('response', $question->answers->count()) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="empty-questions">
                <i class="fa-regular fa-rectangle-list"></i>
                <p>No questions have been added to this poll yet.</p>
            </div>
            @endforelse

        </div>
    </div>

</div>

@endsection
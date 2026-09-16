@extends('layouts.admin')

@section('title', 'Activity Details')

@section('header')
    <h2 class="fw-semibold h3 text-dark">Activity Details</h2>
@endsection

@section('content')
<style>
    .al-hero {
        border: 0;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }
    .al-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: #4e54c8;
        flex-shrink: 0;
    }
    .al-kv dt {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6c757d;
        margin-bottom: .15rem;
    }
    .al-kv dd {
        margin-bottom: 1rem;
        word-break: break-word;
    }
    .al-kv dd:last-child { margin-bottom: 0; }
    @media print {
        .no-print { display: none !important; }
        .al-hero { background: #fff !important; }
    }
</style>

@php
    $grouped = collect($log->grouped_detail_rows)
        ->only(['Submitted Data', 'Related Record'])
        ->filter(fn ($rows) => !empty($rows));
@endphp

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('admin.activity-logs.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Activity Logs
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>

    <div class="card shadow-sm mb-4 al-hero">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3 flex-wrap">
                <div class="al-avatar bg-{{ $log->action_badge_class }}">
                    <i class="bi {{ $log->action_icon }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-{{ $log->action_badge_class }}">{{ $log->action_label }}</span>
                        <span class="badge bg-white text-dark border">{{ $log->module_label }}</span>
                        <span class="badge bg-{{ $log->status_badge_class }}">{{ $log->status_label }}</span>
                    </div>
                    <h3 class="h4 mb-2 fw-semibold">{{ $log->summary }}</h3>
                    <div class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $log->created_at?->format('l, M d, Y · h:i A') }}
                        <span class="mx-1">·</span>
                        {{ $log->created_at?->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>Who did this</h5>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="al-avatar">{{ $log->actor_initials }}</div>
                        <div>
                            <div class="fw-semibold">{{ $log->actor_label }}</div>
                            <div class="text-muted small">{{ $log->actor_email ?: 'No email recorded' }}</div>
                        </div>
                    </div>
                    <dl class="al-kv mb-0">
                        <dt>From IP</dt>
                        <dd>{{ $log->ip_address ?: 'Not available' }}</dd>
                        <dt>Device</dt>
                        <dd>{{ $log->browser_label }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>What happened</h5>
                </div>
                <div class="card-body pt-2">
                    <dl class="al-kv mb-0">
                        <dt>Action</dt>
                        <dd>{{ $log->action_label }}</dd>
                        <dt>Section</dt>
                        <dd>{{ $log->module_label }}</dd>
                        <dt>Result</dt>
                        <dd>
                            <span class="badge bg-{{ $log->status_badge_class }}">{{ $log->status_label }}</span>
                        </dd>
                        @if($log->event_id)
                            <dt>Related Event</dt>
                            <dd>Event #{{ $log->event_id }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @forelse($grouped as $group => $rows)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    @if($group === 'Submitted Data')
                        <i class="bi bi-input-cursor-text me-2 text-success"></i>Details submitted
                    @else
                        <i class="bi bi-link-45deg me-2 text-primary"></i>Related items
                    @endif
                </h5>
                <small class="text-muted">
                    @if($group === 'Submitted Data')
                        Information entered or changed in this action
                    @else
                        Records linked to this action
                    @endif
                </small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3" style="width: 30%;">Field</th>
                                <th class="px-4 py-3">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="px-4 py-3 fw-medium">{{ $row['label'] }}</td>
                                    <td class="px-4 py-3">{{ $row['value'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        @if($log->module === 'auth')
            <div class="card shadow-sm mb-4">
                <div class="card-body text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    This is a sign-in / sign-out record. No form details were involved.
                </div>
            </div>
        @endif
    @endforelse

    <div class="d-flex gap-2 flex-wrap no-print mb-4">
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
        @if($log->module === 'auth')
            <a href="{{ route('admin.activity-logs.index', ['module' => 'auth']) }}" class="btn btn-outline-primary">
                All login activity
            </a>
        @elseif($log->module)
            <a href="{{ route('admin.activity-logs.index', ['module' => $log->module]) }}" class="btn btn-outline-primary">
                Same section
            </a>
        @endif
        @if($log->actor_id && isSuperAdmin())
            <a href="{{ route('admin.activity-logs.index', ['actor_id' => $log->actor_id]) }}" class="btn btn-outline-primary">
                Same user
            </a>
        @endif
    </div>
</div>
@endsection

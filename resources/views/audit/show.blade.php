@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('header')
    <h2 class="fw-semibold h3 text-dark">Audit Log Details</h2>
@endsection

@section('content')
<div class="container mt-4">
    <div class="mb-3">
        <a href="{{ route('audit.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Audit Logs
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-{{ $log->event_badge_class }}">
                            <i class="bi {{ $log->event_icon }} me-1"></i>{{ $log->event_label }}
                        </span>
                        <span class="badge bg-light text-dark">{{ $log->entity_label }}</span>
                        <span class="badge bg-light text-dark">#{{ $log->auditable_id }}</span>
                    </div>
                    <h4 class="mb-1">{{ $log->summary }}</h4>
                    <div class="text-muted small">
                        {{ $log->created_at->format('M d, Y h:i A') }} • {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Who Did It</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            {{ $log->actor_initials }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $log->actor_label }}</div>
                            <div class="text-muted small">{{ $log->user?->email ?? 'Automated process' }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">IP Address</div>
                    <div class="mb-3"><code>{{ $log->ip_address ?: 'N/A' }}</code></div>
                    <div class="small text-muted">User Agent</div>
                    <div>{{ $log->user_agent ?: 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">What Changed</h5>
                </div>
                <div class="card-body">
                    <div class="small text-muted">Entity</div>
                    <div class="mb-3">{{ $log->entity_label }} #{{ $log->auditable_id }}</div>
                    <div class="small text-muted">Fields Recorded</div>
                    <div class="mb-3">{{ $log->change_count }}</div>
                    <div class="small text-muted">Field Summary</div>
                    <div>{{ $log->changed_fields_summary }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Field Details</h5>
        </div>
        <div class="card-body p-0">
            @if($log->change_count > 0)
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Field</th>
                                @if($log->event === 'updated')
                                    <th class="px-4 py-3">Old Value</th>
                                @endif
                                <th class="px-4 py-3">{{ $log->event === 'deleted' ? 'Deleted Value' : 'Value' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($log->change_rows as $row)
                                <tr>
                                    <td class="px-4 py-3 fw-medium">{{ $row['field_label'] }}</td>
                                    @if($log->event === 'updated')
                                        <td class="px-4 py-3"><pre class="mb-0 small">{{ $log->displayValue($row['old']) }}</pre></td>
                                    @endif
                                    <td class="px-4 py-3"><pre class="mb-0 small">{{ $log->displayValue($row['new']) }}</pre></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-muted">No field details were stored for this record.</div>
            @endif
        </div>
    </div>

    @if($log->url || $log->tags)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Technical Details</h5>
            </div>
            <div class="card-body">
                @if($log->url)
                    <div class="small text-muted">URL</div>
                    <div class="mb-3">{{ $log->url }}</div>
                @endif
                @if($log->tags)
                    <div class="small text-muted">Tags</div>
                    <div>
                        @foreach(explode(',', $log->tags) as $tag)
                            <span class="badge bg-info me-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
        @if($log->auditable)
            <a href="{{ route('audit.edit', ['entityType' => $log->auditable_type, 'entityId' => $log->auditable_id]) }}" class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-1"></i>View Full History
            </a>
        @endif
    </div>
</div>
@endsection

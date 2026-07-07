@extends('layouts.admin')

@section('title', 'Entity Audit Logs')

@section('header')
    <h2 class="fw-semibold h3 text-dark">
        History for <span class="badge bg-primary">{{ class_basename($entityType) }}</span>
        <code class="bg-light text-dark px-2 py-1 rounded">#{{ $entityId }}</code>
    </h2>
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
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="small text-muted">Entity</div>
                    <div class="fw-semibold">{{ class_basename($entityType) }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">ID</div>
                    <div class="fw-semibold">#{{ $entityId }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Total Entries</div>
                    <div class="fw-semibold">{{ $logs->total() }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Latest Activity</div>
                    <div class="fw-semibold">{{ $logs->count() ? $logs->first()->created_at->diffForHumans() : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Timeline</h5>
        </div>
        <div class="card-body p-0">
            @if($logs->count())
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Event</th>
                                <th class="px-4 py-3">What Changed</th>
                                <th class="px-4 py-3">By</th>
                                <th class="px-4 py-3">When</th>
                                <th class="px-4 py-3 text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $log->event_badge_class }}">
                                            <i class="bi {{ $log->event_icon }} me-1"></i>{{ $log->event_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-medium">{{ $log->changed_fields_summary }}</div>
                                        <small class="text-muted">{{ $log->change_count }} field{{ $log->change_count === 1 ? '' : 's' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-medium">{{ $log->actor_label }}</div>
                                        <small class="text-muted">{{ $log->user?->email ?? 'Automated process' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-medium">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('audit.show', $log) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Open
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-muted">This record has no audit history yet.</div>
            @endif
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div>{{ $logs->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>
</div>
@endsection

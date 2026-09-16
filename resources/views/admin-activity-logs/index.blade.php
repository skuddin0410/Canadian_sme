@extends('layouts.admin')

@section('title', 'Activity Log')

@section('header')
    <h2 class="fw-semibold h3 text-dark">
        {{ __('Activity Log') }}
    </h2>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4 mt-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filter Activity Logs
            </h5>
            <small class="text-muted">
                {{ isSuperAdmin() ? 'Showing all admin panel actions' : 'Showing only your actions' }}
            </small>
        </div>
        <div class="card-body mt-1">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="action" class="form-label fw-medium">Action</label>
                        <select name="action" id="action" class="form-select">
                            <option value="">All Actions</option>
                            @foreach($actionOptions as $actionOption)
                                <option value="{{ $actionOption }}" {{ request('action') == $actionOption ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $actionOption)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="module" class="form-label fw-medium">Module</label>
                        <select name="module" id="module" class="form-select">
                            <option value="">All Modules</option>
                            @foreach($moduleOptions as $moduleOption)
                                <option value="{{ $moduleOption }}" {{ request('module') == $moduleOption ? 'selected' : '' }}>
                                    {{ ucwords(str_replace(['_', '-'], ' ', $moduleOption)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="from" class="form-label fw-medium">From Date</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="to" class="form-label fw-medium">To Date</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
                    </div>

                    @if(isSuperAdmin())
                    <div class="col-md-3">
                        <label for="actor_id" class="form-label fw-medium">Admin User</label>
                        <select name="actor_id" id="actor_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($actors as $actor)
                                <option value="{{ $actor->id }}" {{ (string) request('actor_id') === (string) $actor->id ? 'selected' : '' }}>
                                    {{ $actor->full_name ?? $actor->name }} ({{ $actor->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-{{ isSuperAdmin() ? '9' : '12' }}">
                        <label for="q" class="form-label fw-medium">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Description, user, module, route...">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter Results
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>Activity Logs
            </h5>
            <span class="badge bg-secondary">Total: {{ $logs->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Module</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Date/Time</th>
                            <th class="px-4 py-3 text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $log->action_badge_class }}">
                                        {{ $log->action_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $log->module_label }}</td>
                                <td class="px-4 py-3">
                                    <div class="fw-medium">{{ $log->summary }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $log->actor_label }}</div>
                                    <small class="text-muted">{{ $log->actor_email }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $log->created_at?->format('M d, Y h:i A') }}
                                    <div><small class="text-muted">{{ $log->created_at?->diffForHumans() }}</small></div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No activity logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

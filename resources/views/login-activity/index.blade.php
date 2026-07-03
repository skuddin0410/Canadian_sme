@extends('layouts.admin')

@section('title', 'Login Activity')

@section('content')
<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Activity
                </h5>
                <small class="text-muted">
                    {{ isSuperAdmin() ? 'All admin login records' : 'Your recent login records' }}
                </small>
            </div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Login Time</th>
                            <th class="px-4 py-3">IP Address</th>
                            <th class="px-4 py-3">User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            @php
                                $loginTime = $log->logged_in_at ?? $log->created_at;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">{{ $log->user?->full_name ?? 'System' }}</td>
                                <td class="px-4 py-3">
                                    {{ optional($loginTime)->format('M d, Y h:i A') ?? 'N/A' }}
                                    @if($loginTime)
                                        <div><small class="text-muted">{{ $loginTime->diffForHumans() }}</small></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $log->ip_address ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-break">{{ $log->user_agent ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No login activity found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($loginLogs->hasPages())
            <div class="card-footer bg-white">
                {{ $loginLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

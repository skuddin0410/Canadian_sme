@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Connections / Leads </h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Total Connections</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($connections as $index => $connection)
                        <tr>
                            
                            <td>{{ $index + 1 }}</td>
                            <td>  <div class="fw-semibold">{{ $connection->full_name ?? 'N/A' }}</div>
                            <div class="text-muted small">
                                {{ $connection->company ?? 'N/A' }}<br>
                                {{ $connection->designation ?? 'N/A' }}
                            </div></td>
                            <td>{{ $connection->email ?? 'N/A' }}</td>
                            <td>{{ $connection->total_connections }}</td>
                            <td>
                                <a href="{{ route('user-connections.show', $connection) }}" class="btn btn-sm btn-outline-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">
                                No connections found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         @if ($connections->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $connections->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

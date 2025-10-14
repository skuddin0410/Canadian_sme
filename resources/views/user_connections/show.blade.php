@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Connections for {{ $user->name }}</h3>
            <p class="text-muted mb-0">
                {{ $user->company ?? 'N/A' }} — {{ $user->designation ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('user-connections.export', $user->id) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Friend Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Designation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($connections as $index => $connection)
                        <tr> 
                            <td>{{  $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $connection->connection->name ?? 'N/A' }}</div>
                                <div class="text-muted small">
                                    ID: {{ $connection->connection->id ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $connection->connection->email ?? 'N/A' }}</td>
                            <td>{{ $connection->connection->company ?? 'N/A' }}</td>
                            <td>{{ $connection->connection->designation ?? 'N/A' }}</td>
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
    </div>
</div>
@endsection

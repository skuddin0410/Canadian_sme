@extends('layouts.admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
@endpush

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-4 mt-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Connections / Leads</h1>
            <p class="text-muted mb-0">Track and manage attendee networking connections</p>
        </div>
    </div>

    <!-- Filters + Advanced Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user-connections.index') }}" class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="form-control">
                    </div>
                </div>

                <!-- Event Filter -->
                <div class="col-md-4">
                    <select name="event_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter & Clear -->
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fa fa-filter me-2"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','event_id']))
                        <a href="{{ route('user-connections.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Connections Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th class="text-center">Total Connections</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($connections as $connection)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $connection->full_name ?? ($connection->name . ' ' . $connection->lastname) }}</div>
                                <div class="text-muted small">
                                    {{ $connection->company ?? 'N/A' }} | {{ $connection->designation ?? 'N/A' }}
                                </div>
                            </td>
                            <td>{{ $connection->email ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                    {{ $connection->total_connections }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('user-connections.show', ['user_connection' => $connection->connection_id, 'event_id' => request('event_id')]) }}" class="btn btn-sm btn-outline-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa fa-users-slash fa-3x mb-3 d-block opacity-25"></i>
                                No connections found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($connections->hasPages())
            <div class="card-footer d-flex justify-content-center border-top-0 bg-transparent pb-4">
                {{ $connections->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

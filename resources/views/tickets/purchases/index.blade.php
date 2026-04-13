@extends('layouts.admin')

@section('title', 'Ticket Purchases')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-1">Ticket Purchases</h1>
                    <p class="text-muted mb-0">Review all purchased tickets, payment status, and buyer details.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="event_id" class="form-label">Event</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="ticket_type_id" class="form-label">Ticket Type</label>
                            <select class="form-select" id="ticket_type_id" name="ticket_type_id">
                                <option value="">All Ticket Types</option>
                                @foreach($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->id }}" {{ request('ticket_type_id') == $ticketType->id ? 'selected' : '' }}>
                                        {{ $ticketType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Ref, user, email">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.ticket-purchases.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Buyer</th>
                                    <th>Event</th>
                                    <th>Ticket Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                    <th>Purchased At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketPurchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ trim(($purchase->user->name ?? '') . ' ' . ($purchase->user->lastname ?? '')) ?: 'N/A' }}</div>
                                            <small class="text-muted">{{ $purchase->user->email ?? 'No email' }}</small>
                                        </td>
                                        <td>{{ $purchase->event->title ?? 'N/A' }}</td>
                                        <td>{{ $purchase->ticketType->name ?? 'N/A' }}</td>
                                        <td>${{ number_format((float) $purchase->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($purchase->status) {
                                                    'completed' => 'success',
                                                    'pending_payment' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucwords(str_replace('_', ' ', $purchase->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $purchase->payment_reference ?: 'N/A' }}</small>
                                        </td>
                                        <td>{{ optional($purchase->created_at)->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No ticket purchases found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $ticketPurchases->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

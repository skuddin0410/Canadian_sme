@extends('layouts.admin')

@section('title', 'Event Waitlist')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-1">Event Waitlist</h1>
                    <p class="text-muted mb-0">People who tried to register after ticket capacity was full.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="event_id">Event</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, email, company">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.waitlists.index') }}" class="btn btn-outline-secondary">Clear</a>
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Event</th>
                                    <th>Ticket</th>
                                    <th>Mode</th>
                                    <th>Attendees</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($waitlists as $waitlist)
                                    <tr>
                                        <td>
                                            <strong>{{ trim($waitlist->first_name . ' ' . $waitlist->last_name) }}</strong>
                                            @if($waitlist->company)
                                                <small class="d-block text-muted">{{ $waitlist->company }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $waitlist->email }}
                                            @if($waitlist->mobile)
                                                <small class="d-block text-muted">{{ $waitlist->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $waitlist->event->title ?? 'N/A' }}</td>
                                        <td>{{ $waitlist->ticketType->name ?? 'Any available ticket' }}</td>
                                        <td>{{ ucfirst($waitlist->registration_mode) }}</td>
                                        <td>{{ $waitlist->attendee_count }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $waitlist->status === 'waiting' ? 'warning' : ($waitlist->status === 'converted' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($waitlist->status) }}
                                            </span>
                                        </td>
                                        <td>{{ optional($waitlist->joined_at)->format('M d, Y h:i A') ?? $waitlist->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No waitlist entries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $waitlists->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

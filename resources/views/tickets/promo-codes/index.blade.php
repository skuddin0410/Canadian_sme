@extends('layouts.admin')

@section('title', 'Promo Codes')

@section('content')
@php($currencySymbol = config('tickets.defaults.currency_symbol', '$'))
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-1">Promo Codes</h1>
                    <p class="text-muted mb-0">Create discounts, track usage, and monitor redemptions.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.promo-codes.bulk-create') }}" class="btn btn-outline-primary">Bulk Generate</a>
                    <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-primary">Add Promo Code</a>
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
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Code or notes">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('admin.promo-codes.redemptions') }}" class="btn btn-outline-secondary btn-sm">View Redemptions</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Event</th>
                                    <th>Ticket Scope</th>
                                    <th>Discount</th>
                                    <th>Window</th>
                                    <th>Usage</th>
                                    <th>Discount Given</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($promoCodes as $promoCode)
                                    <tr>
                                        <td>
                                            <strong>{{ $promoCode->code }}</strong>
                                            @if($promoCode->notes)
                                                <small class="d-block text-muted">{{ \Illuminate\Support\Str::limit($promoCode->notes, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $promoCode->event->title ?? 'N/A' }}</td>
                                        <td>{{ $promoCode->ticketType->name ?? 'All Tickets' }}</td>
                                        <td>
                                            {{ $promoCode->discount_type === 'percentage' ? rtrim(rtrim(number_format((float) $promoCode->discount_value, 2), '0'), '.') . '%' : $currencySymbol . number_format((float) $promoCode->discount_value, 2) }}
                                        </td>
                                        <td>
                                            @if($promoCode->starts_at || $promoCode->ends_at)
                                                <small>
                                                    {{ optional($promoCode->starts_at)->format('M d, Y h:i A') ?? 'Any time' }}<br>
                                                    to {{ optional($promoCode->ends_at)->format('M d, Y h:i A') ?? 'No end' }}
                                                </small>
                                            @else
                                                <small class="text-muted">Always valid</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ (int) ($promoCode->completed_redemptions_count ?? 0) }} {{ $promoCode->usage_limit_total ? '/ ' . $promoCode->usage_limit_total : '' }}</div>
                                            <small class="text-muted">Per user: {{ $promoCode->usage_limit_per_user ?? 'Unlimited' }}</small>
                                        </td>
                                        <td>{{ $currencySymbol }}{{ number_format((float) ($promoCode->completed_discount_amount ?? 0), 2) }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $promoCode->is_active ? 'success' : 'secondary' }}">
                                                {{ $promoCode->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.promo-codes.edit', $promoCode) }}" class="btn btn-outline-primary">Edit</a>
                                                <form method="POST" action="{{ route('admin.promo-codes.destroy', $promoCode) }}" onsubmit="return confirm('Delete this promo code?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No promo codes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $promoCodes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

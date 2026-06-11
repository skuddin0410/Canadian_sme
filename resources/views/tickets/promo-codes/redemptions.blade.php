@extends('layouts.admin')

@section('title', 'Promo Code Redemptions')

@section('content')
@php($currencySymbol = config('tickets.defaults.currency_symbol', '$'))
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-1">Promo Code Redemptions</h1>
                    <p class="text-muted mb-0">Track code usage, discounts, and refunds.</p>
                </div>
                <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary">Back to Promo Codes</a>
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
                        <div class="col-md-4">
                            <label class="form-label" for="promo_code_id">Promo Code</label>
                            <select class="form-select" id="promo_code_id" name="promo_code_id">
                                <option value="">All Promo Codes</option>
                                @foreach($promoCodes as $promoCode)
                                    <option value="{{ $promoCode->id }}" {{ request('promo_code_id') == $promoCode->id ? 'selected' : '' }}>{{ $promoCode->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                @foreach(['pending', 'completed', 'refunded'] as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.promo-codes.redemptions') }}" class="btn btn-outline-secondary">Clear</a>
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
                                    <th>Code</th>
                                    <th>Event</th>
                                    <th>Email</th>
                                    <th>Ticket</th>
                                    <th>Attendees</th>
                                    <th>Discount</th>
                                    <th>Final Total</th>
                                    <th>Status</th>
                                    <th>Used At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($redemptions as $redemption)
                                    <tr>
                                        <td>{{ $redemption->code }}</td>
                                        <td>{{ $redemption->event->title ?? 'N/A' }}</td>
                                        <td>{{ $redemption->email ?? $redemption->user->email ?? 'N/A' }}</td>
                                        <td>{{ $redemption->ticketType->name ?? 'All Tickets' }}</td>
                                        <td>{{ $redemption->attendee_count }}</td>
                                        <td>{{ $currencySymbol }}{{ number_format((float) $redemption->discount_amount, 2) }}</td>
                                        <td>{{ $currencySymbol }}{{ number_format((float) $redemption->final_total, 2) }}</td>
                                        <td><span class="badge bg-label-{{ $redemption->status === 'completed' ? 'success' : ($redemption->status === 'refunded' ? 'danger' : 'warning') }}">{{ ucfirst($redemption->status) }}</span></td>
                                        <td>{{ optional($redemption->used_at)->format('M d, Y h:i A') ?? 'Pending' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No redemptions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $redemptions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

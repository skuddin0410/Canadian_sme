@extends('layouts.admin')

@section('title', 'Bulk Generate Promo Codes')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h1 class="h3 mb-0">Bulk Generate Promo Codes</h1>
                <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.promo-codes.bulk-store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="event_id">Event</label>
                                <select name="event_id" id="event_id" class="form-select" required>
                                    <option value="">Select Event</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ticket_type_id">Ticket Type</label>
                                <select name="ticket_type_id" id="ticket_type_id" class="form-select">
                                    <option value="">All Ticket Types</option>
                                    @foreach($ticketTypes as $ticketType)
                                        <option value="{{ $ticketType->id }}" data-event-id="{{ $ticketType->event_id }}" {{ old('ticket_type_id') == $ticketType->id ? 'selected' : '' }}>{{ $ticketType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="prefix">Code Prefix</label>
                                <input type="text" name="prefix" id="prefix" class="form-control" value="{{ old('prefix', 'PROMO') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input type="number" min="1" max="500" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', 10) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="discount_type">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-select" required>
                                    <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="discount_value">Discount Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="usage_limit_total">Total Usage Limit</label>
                                <input type="number" min="1" name="usage_limit_total" id="usage_limit_total" class="form-control" value="{{ old('usage_limit_total') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="usage_limit_per_user">Per User Limit</label>
                                <input type="number" min="1" name="usage_limit_per_user" id="usage_limit_per_user" class="form-control" value="{{ old('usage_limit_per_user') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="min_attendee_count">Min Attendees</label>
                                <input type="number" min="1" name="min_attendee_count" id="min_attendee_count" class="form-control" value="{{ old('min_attendee_count') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="max_attendee_count">Max Attendees</label>
                                <input type="number" min="1" name="max_attendee_count" id="max_attendee_count" class="form-control" value="{{ old('max_attendee_count') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="starts_at">Starts At</label>
                                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="ends_at">Ends At</label>
                                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Generate Codes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventSelect = document.getElementById('event_id');
        const ticketSelect = document.getElementById('ticket_type_id');

        function syncTicketTypeOptions() {
            const selectedEvent = eventSelect.value;
            Array.from(ticketSelect.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedEvent !== '' && option.dataset.eventId !== selectedEvent;
                if (option.hidden && option.selected) {
                    ticketSelect.value = '';
                }
            });
        }

        eventSelect.addEventListener('change', syncTicketTypeOptions);
        syncTicketTypeOptions();
    });
</script>
@endsection

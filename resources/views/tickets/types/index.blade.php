@extends('layouts.admin')

@section('title', 'Ticket Types')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h1 class="h3 mb-0">Ticket Types</h1>
                <a href="{{ route('admin.ticket-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Ticket Type
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="{{ route('admin.ticket-types.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Event</th>
                                    <th>Category</th>
                                    <th>Base Price</th>
                                    <th>Inventory</th>
                                    <th>Sale Period</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketTypes as $ticketType)
                                    <tr>
                                        <td>
                                            <strong>{{ $ticketType->name }}</strong>
                                            <small class="text-muted d-block">{{ $ticketType->slug }}</small>
                                        </td>
                                        <td>{{ $ticketType->event->name }}</td>
                                        <td>
                                            @if($ticketType->category)
                                                <span class="badge" style="background-color: {{ $ticketType->category->color }}">
                                                    {{ $ticketType->category->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($ticketType->base_price, 2) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $ticketType->available_quantity }}/{{ $ticketType->total_quantity }}</span>
                                                @php
                                                    $percentage = $ticketType->total_quantity > 0 ? ($ticketType->available_quantity / $ticketType->total_quantity) * 100 : 0;
                                                    $colorClass = $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger');
                                                @endphp
                                                <div class="progress" style="width: 80px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $colorClass }}" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($ticketType->sale_start_date && $ticketType->sale_end_date)
                                                <small>
                                                    {{ $ticketType->sale_start_date->format('M j') }} - 
                                                    {{ $ticketType->sale_end_date->format('M j, Y') }}
                                                </small>
                                            @elseif($ticketType->sale_start_date)
                                                <small>From {{ $ticketType->sale_start_date->format('M j, Y') }}</small>
                                            @elseif($ticketType->sale_end_date)
                                                <small>Until {{ $ticketType->sale_end_date->format('M j, Y') }}</small>
                                            @else
                                                <small class="text-muted">Always Available</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $ticketType->is_active ? 'success' : 'secondary' }}">
                                                {{ $ticketType->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if($ticketType->available_quantity == 0)
                                                <span class="badge badge-danger">Sold Out</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.ticket-types.show', $ticketType) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.ticket-types.edit', $ticketType) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $ticketType->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No ticket types found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $ticketTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
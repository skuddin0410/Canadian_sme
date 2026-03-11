@extends('layouts.admin')

@section('title')
Admin | Pricing Setup
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="py-3 mb-0"><span class="text-muted fw-light">Pricing /</span> Setup</h4>
        <a href="{{ route('admin.pricing.setup.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Add New Pricing
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Pricing Plans</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Timespan</th>
                        <th>Most Popular</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($pricings as $pricing)
                        <tr>
                            <td>{{ $pricing->order_by }}</td>
                            <td><strong>{{ $pricing->name }}</strong></td>
                            <td>{{ $pricing->amount }}</td>
                            <td>{{ $pricing->timespan }}</td>
                            <td>
                                @if($pricing->mostpopular)
                                    <span class="badge bg-label-success me-1">Yes</span>
                                @else
                                    <span class="badge bg-label-secondary me-1">No</span>
                                @endif
                            </td>
                            <td>
                                @if($pricing->status)
                                    <span class="badge bg-label-primary me-1">Active</span>
                                @else
                                    <span class="badge bg-label-danger me-1">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.pricing.setup.edit', $pricing->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.pricing.setup.destroy', $pricing->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pricing plan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No pricing plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

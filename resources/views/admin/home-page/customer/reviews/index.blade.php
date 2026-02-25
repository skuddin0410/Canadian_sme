@extends('layouts.admin')

@section('title')
Admin | Landing Page Home Reviews
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Customer /</span> Home Reviews</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Home Review List</h5>
                    <a href="{{ route('admin.home-page.customer.reviews.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Add New Review
                    </a>
                </div>
                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success mt-3">
                            {{ Session::get('success') }}
                        </div>
                    @endif

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Customer Name</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>
                                            @if($review->profileImage)
                                                <img src="{{ $review->profileImage->file_path }}" alt="{{ $review->customer_name }}" class="rounded-circle" style="height: 40px; width: 40px; object-fit: cover;">
                                            @else
                                                <span class="badge bg-label-secondary">No Image</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $review->customer_name }}</strong></td>
                                        <td>
                                            @if($review->status)
                                                <span class="badge bg-label-success me-1">Active</span>
                                            @else
                                                <span class="badge bg-label-danger me-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->order_by }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('admin.home-page.customer.reviews.edit', $review->id) }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.home-page.customer.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No reviews found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

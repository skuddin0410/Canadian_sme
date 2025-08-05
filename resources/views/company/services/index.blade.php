@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Services</h4>
            <a href="{{ route('services.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Service
            </a>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search services..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Services Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            {{-- <th>Image</th> --}}
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                {{-- <td>
                                    @if($service->image_url)
                                        <img src="{{ asset($service->image_url) }}" alt="{{ $service->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                                    @else
                                        <div class="bg-light text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bx bx-image text-muted"></i>
                                        </div>
                                    @endif
                                </td> --}}
                                <td>
                                    <strong>{{ $service->name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                </td>
                                <td>{{ $service->category->name ?? 'Uncategorized' }}</td>
                                <td>
                                    <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $service->sort_order }}</td>
                                <td>
                                    {{ $service->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">by {{ $service->creator->name ?? 'System' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- View -->
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-icon btn-primary" title="View">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $services->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

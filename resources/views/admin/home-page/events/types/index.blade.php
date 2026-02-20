@extends('layouts.admin')

@section('title')
Admin | Landing Page Event Types
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Page / Events /</span> Event Types</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Event Type List</h5>
                    <a href="{{ route('admin.home-page.events.types.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Add New Event Type
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
                                    <th>Heading</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($types as $type)
                                    <tr>
                                        <td>
                                            @if($type->typeImage)
                                                <img src="{{ $type->typeImage->file_path }}" alt="{{ $type->heading }}" class="rounded" style="height: 40px; width: auto;">
                                            @else
                                                <span class="badge bg-label-secondary">No Image</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $type->heading }}</strong></td>
                                        <td>
                                            @if($type->status)
                                                <span class="badge bg-label-success me-1">Active</span>
                                            @else
                                                <span class="badge bg-label-danger me-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $type->order }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('admin.home-page.events.types.edit', $type->id) }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.home-page.events.types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event type?')">
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
                                        <td colspan="5" class="text-center">No event types found.</td>
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

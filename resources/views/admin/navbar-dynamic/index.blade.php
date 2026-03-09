@extends('layouts.admin')

@section('title')
Admin | Dynamic Nav
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Dynamic Nav</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Navbar Item List</h5>
                    <a href="{{ route('admin.navbar-dynamic.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Add New Item
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
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($navbars as $navbar)
                                    <tr>
                                        <td><strong>{{ $navbar->title }}</strong></td>
                                        <td>{{ $navbar->slug }}</td>
                                        <td>
                                            @if($navbar->status == 'active')
                                                <span class="badge bg-label-success me-1">Published</span>
                                            @else
                                                <span class="badge bg-label-danger me-1">Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $navbar->order_by }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-menu-item btn" href="{{ route('admin.navbar-dynamic.edit', $navbar->id) }}">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.navbar-dynamic.destroy', $navbar->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-menu-item btn text-danger">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No items found.</td>
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

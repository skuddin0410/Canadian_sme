@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Products</h3>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
                
                <div class="card-body">
                    {{-- <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
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
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search products..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">Filter</button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form> --}}

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                                @forelse($products as $product)
                                    <tr>
                                        {{-- <td>
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td> --}}
                                        <td>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        </td>
                                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $product->sort_order }}</td>
                                        <td>
                                            {{ $product->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">by {{ $product->creator->name ?? 'System' }}</small>
                                        </td>
                                        {{-- <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $product) }}" 
                                                   class="btn btn-sm btn-outline-info">View</a>
                                                <a href="{{ route('products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form method="POST" action="{{ route('products.destroy', $product) }}" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td> --}}
                                        <td>
    <div class="d-flex gap-2">
        {{-- View --}}
        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-icon btn-primary" title="View">
            <i class="bx bx-show"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
            <i class="bx bx-edit-alt"></i>
        </a>

        {{-- Delete --}}
        <form method="POST" action="{{ route('products.destroy', $product) }}" 
              onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                                        <td colspan="7" class="text-center">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

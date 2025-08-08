<table class="table table-bordered">
    <thead>
        <tr>
           
            <th>Name</th>
            <th>Slug</th>
            {{-- <th>Parent Category</th> --}}
            {{-- <th>Active</th>
            <th>Sort Order</th> --}}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
            <tr>
                
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                {{-- <td>{{ optional($category->parent)->name ?? '-' }}</td> --}}
                {{-- <td>{{ $category->is_active ? 'Yes' : 'No' }}</td>
                <td>{{ $category->sort_order }}</td> --}}
                <td>
                    <div class="d-flex gap-2">
                        {{-- View --}}
                        <a href="{{ route('product-categories.show', ['product_category' => $category->id]) }}" 
                           class="btn btn-sm btn-icon btn-primary" title="View">
                            <i class="bx bx-show"></i>
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('product-categories.edit', ['product_category' => $category->id]) }}" 
                           class="btn btn-sm btn-icon item-edit" title="Edit">
                            <i class="bx bx-edit-alt"></i>
                        </a>

                        {{-- Delete --}}
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                <td colspan="7">No Product Categories found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $categories->links() }}

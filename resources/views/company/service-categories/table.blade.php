<table class="table table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
            <tr>
                <td>
                    @if($category->image_url)
                        <img src="{{ asset('storage/'.$category->image_url) }}" alt="{{ $category->name }}" style="max-width: 80px; max-height: 50px;">
                    @else
                        <span>No Image</span>
                    @endif
                </td>
               
                <td>{{ $category->name }}</td>
                <td>{{ Str::limit($category->description, 50) }}</td>
                
                <td>
                    <a href="{{ route('service-categories.show', ['service_category' => $category->id]) }}" class="btn btn-sm btn-primary" title="View">
                        <i class="bx bx-show"></i>
                    </a>
                    <a href="{{ route('service-categories.edit', ['service_category' => $category->id]) }}" class="btn btn-icon item-edit" title="Edit">
                        <i class="bx bx-edit-alt"></i>
                    </a>
                    <form action="{{ route('service-categories.destroy', ['service_category' => $category->id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No service categories found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}
<div class="custom_pagination mt-3">
    {!! $categories->links() !!}
</div>

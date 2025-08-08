<style>
    .custom_pagination {
        /* display: inline-block; */
        float: right;
        margin: 10px;
    }

    .custom_pagination a {
        color: #78818b;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid transparent;

    }

    .custom_pagination a.pagination-link:hover {
        background: linear-gradient(90deg, #F5286E 0%, #FC6D43 100%);
        color: #FFF;

    }

    .custom_pagination a {
        color: #78818b;
        border-radius: 7px;

    }

    .custom_pagination a.pagination-link {
        box-shadow: 0 5px 15px rgb(0 0 0 / 10%);
        margin: 10px 2px 10px 2px;
        font-size: 12px;
        font-weight: 300;
    }

    .page-count a {
        border: none;
        margin: 10px 0 10px 0;
    }
</style>

<table id="products-table"
    class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
    <thead>
        <tr>
            <th>Image</th>
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
            <td>
                @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-thumbnail"
                    style="width: 50px; height: 50px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <i class="fas fa-image text-muted"></i>
                </div>
                @endif
            </td>
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

            <td width="5%">
                <div class="d-flex gap-2">
                    {{-- View --}}
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-icon btn-primary"
                        title="View">
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
<div class="text-xs-center">
    @if ($products->hasPages())
    <div class="custom_pagination">
        @if (!$products->onFirstpage())
        <a href="{{ $products->appends(request()->input())->url(1) }}" class="pagination-link">
            <i class="bx bx-chevron-left"></i>
            <i class="bx bx-chevron-left"></i>
        </a>
        <a href="{{ $products->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
            <i class="bx bx-chevron-left"></i>
        </a>
        @endif

        <span class="page-count"> <a href="#"> Page {{ number_format($products->currentPage()) }} of
                {{ number_format($products->lastPage()) }} </a></span>
        @if (!$products->onLastpage())
        <a href="{{ $products->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
            <i class="bx bx-chevron-right"></i>

        </a>
        <a href="{{ $products->appends(request()->input())->url($products->lastPage()) }}" class="pagination-link">
            <i class="bx bx-chevron-right"></i>
            <i class="bx bx-chevron-right"></i>
        </a>
        @endif
    </div>
    @endif
</div>
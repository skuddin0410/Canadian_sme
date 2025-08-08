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

<table class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
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
        @forelse($services as $service)
            <tr>
             <td>
                    @if($service->image_url)
                        <img src="{{ asset($service->image_url) }}" alt="{{ $service->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                    @else
                        <div class="bg-light text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bx bx-image text-muted"></i>
                        </div>
                    @endif
                </td>
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
<div class="text-xs-center">
    @if ($services->hasPages())
    <div class="custom_pagination">
        @if (!$services->onFirstpage())
        <a href="{{ $services->appends(request()->input())->url(1) }}" class="pagination-link">
            <i class="bx bx-chevron-left"></i>
            <i class="bx bx-chevron-left"></i>
        </a>
        <a href="{{ $services->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
            <i class="bx bx-chevron-left"></i>
        </a>
        @endif

        <span class="page-count"> <a href="#"> Page {{ number_format($services->currentPage()) }} of
                {{ number_format($services->lastPage()) }} </a></span>
        @if (!$services->onLastpage())
        <a href="{{ $services->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
            <i class="bx bx-chevron-right"></i>

        </a>
        <a href="{{ $services->appends(request()->input())->url($services->lastPage()) }}" class="pagination-link">
            <i class="bx bx-chevron-right"></i>
            <i class="bx bx-chevron-right"></i>
        </a>
        @endif
    </div>
    @endif
</div>
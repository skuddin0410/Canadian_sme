@extends('layouts.admin')

@section('title', 'Ticket Categories')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Ticket Categories</h1>
                <a href="{{ route('admin.ticket-categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Category
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Color</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Ticket Types</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="color-indicator" style="background-color: {{ $category->color }}; width: 20px; height: 20px; border-radius: 50%;"></div>
                                        </td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                            <small class="text-muted d-block">{{ $category->slug }}</small>
                                        </td>
                                        <td>{!! Str::words(strip_tags($category->description), 10, '...') !!}</td>

                                        {{-- <td>{{ Str::limit($category->description, 50) }}</td> --}}
                                        <td>
                                            <span class="badge badge-info">{{ $category->ticketTypes->count() }}</span>
                                        </td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.ticket-categories.show', $category) }}" class="btn btn-sm btn-icon btn-primary">
                                                    <i class="bx bxs-show"></i>
                                                </a>
                                                <a href="{{ route('admin.ticket-categories.edit', $category) }}" class="btn btn-sm btn-icon item-edit">
                                                   <i class="bx bxs-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No ticket categories found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this ticket category?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('deleteForm').action = `/admin/ticket-types/${id}`;
    modal.show();
}
</script>
@endpush
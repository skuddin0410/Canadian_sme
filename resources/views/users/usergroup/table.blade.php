<style>
.custom_pagination {
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

<table id="roles-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
    <thead>
        <tr>
            <th>Name</th>
            <th>Guard Name</th>
            <th>Created At</th>
            <th width="12%">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
            @php
                $restrictedRoles = ["Admin", "Exhibitor", "Attendee","Sponsors","Representative", "Speaker", "Support Staff Or Helpdesk", "Registration Desk"];
                $isRestricted = in_array($role->name, $restrictedRoles);
            @endphp

            <tr>
                <td>{{ $role->name ?? '' }}</td>
                <td>{{ $role->guard_name ?? '' }}</td>
                <td>{{ $role->created_at ? $role->created_at->format('Y-m-d') : '' }}</td>
                <td>
                    <div class="d-flex gap-2">
                        {{-- View --}}
                        <a href="{{ route('usergroup.show', $role->id) }}" 
                           class="btn btn-sm btn-icon btn-primary" 
                           title="View">
                            <i class="bx bx-show"></i>
                        </a>

                        {{-- Edit --}}
                        <a href="{{ $isRestricted ? 'javascript:void(0)' : route('usergroup.edit', $role->id) }}" 
                           class="btn btn-sm btn-icon item-edit {{ $isRestricted ? 'disabled' : '' }}" 
                           title="Edit"
                           @if($isRestricted) aria-disabled="true" @endif>
                            <i class="bx bx-edit-alt"></i>
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('usergroup.destroy', $role->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this role?');"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon" type="submit" title="Delete"
                                    {{ $isRestricted ? 'disabled' : '' }}>
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach

        @if($roles->count() <= 0)
            <tr>
                <td colspan="4" class="text-center">No roles available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="text-xs-center">
    @if ($roles->hasPages())
        <div class="custom_pagination">
            {{-- First Page --}}
            @if (!$roles->onFirstPage())
                <a href="{{ $roles->appends(request()->input())->url(1) }}" class="pagination-link">
                    <i class="bx bx-chevron-left"></i>
                    <i class="bx bx-chevron-left"></i>
                </a>
                <a href="{{ $roles->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
                    <i class="bx bx-chevron-left"></i>
                </a>
            @endif

            {{-- Page Count --}}
            <span class="page-count">
                <a href="#"> Page {{ number_format($roles->currentPage()) }} of {{ number_format($roles->lastPage()) }} </a>
            </span>

            {{-- Next Page --}}
            @if ($roles->hasMorePages())
                <a href="{{ $roles->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
                    <i class="bx bx-chevron-right"></i>
                </a>
                <a href="{{ $roles->appends(request()->input())->url($roles->lastPage()) }}" class="pagination-link">
                    <i class="bx bx-chevron-right"></i>
                    <i class="bx bx-chevron-right"></i>
                </a>
            @endif
        </div>
    @endif
</div>

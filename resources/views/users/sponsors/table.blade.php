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
  border-radius: 7px;
}
.custom_pagination a.pagination-link {
  box-shadow: 0 5px 15px rgb(0 0 0 / 10%);
  margin: 10px 2px;
  font-size: 12px;
  font-weight: 300;
}
.custom_pagination a.pagination-link:hover {
  background: linear-gradient(90deg, #F5286E 0%, #FC6D43 100%);
  color: #FFF;
}
.page-count a {
  border: none;
  margin: 10px 0;
}
</style>

<table id="post-manager" class="table table-striped table-bordered dt-responsive nowrap">
  <thead>
    <tr>
      <th>Logo</th>
      <th>Name</th>
      <th>QR</th>
      <th width="20%">Actions</th>
    </tr>
  </thead>
 <tbody>
  @forelse($companies as $company)
    <tr>
      <td>
        @if($company->logo)
          <img src="{{ $company->logo->file_path }}"
               alt="Logo" width="40" height="40" class="rounded">
        @else
          <span class="text-muted">-</span>
        @endif
      </td>
      <td>{{ $company->name ?? '-' }}</td>
      <td>
		
      @if($company->user && $company->user->qr_code)
        <a href="{{ route('sponsors.qr.download', $company->user->id) }}" 
           class="btn btn-sm btn-primary" 
           title="Download QR">
             Download QR
        </a>
        @else
          <span class="text-muted">No QRCode Generated Yet</span>
        @endif
     </td>

      <td>
        <div class="d-flex gap-2">
          <a href="{{ route('sponsors.show', $company->id) }}" class="btn btn-sm btn-icon btn-primary" title="View">
            <i class="bx bx-show"></i>
          </a>
          <a href="{{ route('sponsors.edit', $company->id) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
            <i class="bx bx-edit-alt"></i>
          </a>
          <form action="{{ route('sponsors.destroy', $company->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this sponsor?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger btn-icon" type="submit" title="Delete">
              <i class="bx bx-trash"></i>
            </button>
          </form>
        </div>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="4" class="text-center">No data available</td>
    </tr>
  @endforelse
</tbody>

</table>

<div class="text-xs-center">
  @if ($companies->hasPages())
  <div class="custom_pagination">
    {{-- Previous --}}
    @if (!$companies->onFirstPage())
      <a href="{{ $companies->appends(request()->input())->url(1) }}" class="pagination-link">
        <i class="bx bx-chevron-left"></i><i class="bx bx-chevron-left"></i>
      </a>
      <a href="{{ $companies->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
        <i class="bx bx-chevron-left"></i>
      </a>
    @endif

    <span class="page-count">
      <a href="#"> Page {{ number_format($companies->currentPage()) }} of {{ number_format($companies->lastPage()) }} </a>
    </span>

    {{-- Next --}}
    @if ($companies->hasMorePages())
      <a href="{{ $companies->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
        <i class="bx bx-chevron-right"></i>
      </a>
      <a href="{{ $companies->appends(request()->input())->url($companies->lastPage()) }}" class="pagination-link">
        <i class="bx bx-chevron-right"></i><i class="bx bx-chevron-right"></i>
      </a>
    @endif
  </div>
@endif

</div>

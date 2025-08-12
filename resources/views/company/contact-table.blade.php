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
<table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Actions</th>
        </tr>
      </thead>
<tbody>	
   @foreach($contacts as $contact)
          <tr>
            <td>{{ $contact->name ?? '-' }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ $contact->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-flex gap-2">
                <form action="{{ route('company.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach

	@if(count($contacts) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>
<div class="text-xs-center">
	    @if ($contacts->hasPages())
	        <div class="custom_pagination">
	            @if (!$contacts->onFirstpage())
	                <a href="{{ $contacts->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $contacts->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($contacts->currentPage()) }} of
	                    {{ number_format($contacts->lastPage()) }} </a></span>
	            @if (!$contacts->onLastpage())
	                <a href="{{ $contacts->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $contacts->appends(request()->input())->url($contacts->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
</div>
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
  border-radius: 7px;
}
.custom_pagination a.pagination-link {
  box-shadow: 0 5px 15px rgb(0 0 0 / 10%);
  margin: 10px 2px;
  font-size: 12px;
  font-weight: 300;
}
.page-count a {
  border: none;
  margin: 10px 0;
}
</style>

<table class="table table-striped table-bordered dt-responsive display nowrap">
<thead>
	<tr>
		<th>Category</th>
		<th>Title</th>
		<th>Type</th>
		{{-- <th>Weblink</th>
		<th>Document</th>
		<th>Created At</th> --}}
		<th width="12%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($guides as $guide)
    <tr> 
    	<td>{{ $guide->category }}</td>
    	<td>{{ $guide->title }}</td>
    	<td>{{ $guide->type }}</td>
    	
		<td>
			<div class="row">
				<div class="col-4 p-1">
					<a href="{{route('event-guides.show',$guide->id)}}" class="btn btn-sm btn-icon item-show"><i class="bx bx-show"></i></a>
        
				</div>
				<div class="col-4 p-1">
					<a href="{{ route('event-guides.edit', $guide->id) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit" title="Edit"></i></a>
				</div>
				<div class="col-4 p-1">
					<form action="{{ route('event-guides.destroy', $guide->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-sm btn-icon btn-danger"><i class="bx bxs-trash"></i></button>
					</form>
				</div>
			</div>    
		</td>
	</tr>
	@endforeach

	@if(count($guides) <= 0)
	    <tr>
          <td colspan="7" class="text-center">No data available</td>
        </tr>
	@endif
</tbody>
</table>

<div class="text-xs-center">
    @if ($guides->hasPages())
        <div class="custom_pagination">
            {{-- First page --}}
            @if (!$guides->onFirstPage())
                <a href="{{ $guides->appends(request()->input())->url(1) }}" class="pagination-link">
                    <i class="bx bx-chevron-left"></i><i class="bx bx-chevron-left"></i>
                </a>
                <a href="{{ $guides->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
                    <i class="bx bx-chevron-left"></i>
                </a>
            @endif

            <span class="page-count">
                <a href="#">Page {{ number_format($guides->currentPage()) }} of {{ number_format($guides->lastPage()) }}</a>
            </span>

            {{-- Next page --}}
            @if ($guides->hasMorePages())
                <a href="{{ $guides->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
                    <i class="bx bx-chevron-right"></i>
                </a>
                <a href="{{ $guides->appends(request()->input())->url($guides->lastPage()) }}" class="pagination-link">
                    <i class="bx bx-chevron-right"></i><i class="bx bx-chevron-right"></i>
                </a>
            @endif
        </div>
    @endif
</div>

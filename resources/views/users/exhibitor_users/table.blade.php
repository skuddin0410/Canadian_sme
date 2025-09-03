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

<table id="post-manager" 
       class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
	<tr>
		<th>Content Icon</th>
		<th>Name</th>
		<th>email</th>
		<th>Booth ID</th>
		<th width="20%">Actions</th>
	</tr>
</thead>

<tbody>	
@foreach($users as $user)
<tr>
	{{-- Content Icon --}}
	<td>
		@if($user && $user->contentIconFile)
			<img src="{{$user->contentIconFile->file_path}}" 
			     alt="Content Icon" 
			     width="40" height="40" 
			     class="rounded">
		@else
			<span class="text-muted">-</span>
		@endif
	</td>

	{{-- Company Name --}}
	<td>{{ $user->name ?? '-' }}</td>
    <td>{{ $user->email ?? '-' }}</td>
	{{-- Booth ID --}}
	<td>
     {{$user->booth?? ''}}
    </td>



	{{-- Actions --}}
	<td>
		<div class="d-flex flex-wrap gap-1">
			<a href="{{ route('exhibitor-users.show', ['exhibitor_user' => $user->id]) }}" 
			   class="btn btn-sm btn-icon item-show" title="Show">
				<i class="bx bxs-show"></i>
			</a>
			<a href="{{ route('exhibitor-users.edit', ['exhibitor_user' => $user->id]) }}" 
			   class="btn btn-sm btn-icon item-edit" title="Edit">
				<i class="bx bxs-edit"></i>
			</a>

			{{-- Delete button --}}
			<form action="{{ route('exhibitor-users.destroy', $user->id) }}" 
			      method="POST" 
			      onsubmit="return confirm('Are you sure you want to delete this Exhibitor?');">
				@csrf
				@method('DELETE')
				<button type="submit" class="btn btn-sm btn-icon btn-danger" title="Delete">
					<i class="bx bxs-trash"></i>
				</button>
			</form>
		</div>
	</td>
</tr>
@endforeach

@if($users->count() <= 0)
<tr>
	<td colspan="5" class="text-center">No data available</td>
</tr>
@endif
</tbody>
</table>


	<div class="text-xs-center">
	   @if ($users->hasPages())
    <div class="custom_pagination">
        @if (!$users->onFirstPage())
            <a href="{{ $users->appends(request()->input())->url(1) }}" class="pagination-link">
                <i class="bx bx-chevron-left"></i><i class="bx bx-chevron-left"></i>
            </a>
            <a href="{{ $users->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
                <i class="bx bx-chevron-left"></i>
            </a>
        @endif

        <span class="page-count">
            <a href="#"> Page {{ $users->currentPage() }} of {{ $users->lastPage() }} </a>
        </span>

        @if (!$users->onLastPage())
            <a href="{{ $users->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
                <i class="bx bx-chevron-right"></i>
            </a>
            <a href="{{ $users->appends(request()->input())->url($users->lastPage()) }}" class="pagination-link">
                <i class="bx bx-chevron-right"></i><i class="bx bx-chevron-right"></i>
            </a>
        @endif
    </div>
@endif
</div>
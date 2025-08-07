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
		<th>Role</th>
		<th>User name</th>
		<th>Email</th>
		<th>Mobile</th>
		{{-- <th>Referral coupon</th> --}}
		<th>Created At</th>
		<th width="8%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($users as $user)
    <tr>
    	<th>{{$user->name ?? ''}} {{$user->lastname ?? ''}}</th>
		<th>{{!empty($user->roles) ? $user->roles[0]->name : ''}}</th>
		<th style="text-transform:none">{{$user->username ?? ''}}</th>
		<th style="text-transform:none">{{$user->email ?? ''}}</th>
		<th>{{$user->mobile ?? ''}}</th>
		{{-- <th style="text-transform:none">{{$user->referral_coupon}}</th> --}}
		<th>{{dateFormat($user->created_at) ?? '' }}</th>
		
         <th>
  <div class="d-flex gap-2">
    {{-- View --}}
    <a href="{{ route('attendee-users.show', $user->id) }}" class="btn btn-sm btn-icon btn-primary" title="View">
      <i class="bx bx-show"></i>
    </a>

    {{-- Edit --}}
    <a href="{{ route('attendee-users.edit', $user->id) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
      <i class="bx bx-edit-alt"></i>
    </a>

    {{-- Delete --}}
    <form action="{{ route('attendee-users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this representative user?');">
      @csrf
      @method('DELETE')
      <button class="btn btn-sm btn-danger btn-icon" type="submit" title="Delete">
        <i class="bx bx-trash"></i>
      </button>
    </form>
  </div>
</th>
	</tr>
	@endforeach
	@if(count($users) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>
	<div class="text-xs-center">
	    @if ($users->hasPages())
	        <div class="custom_pagination">
	            @if (!$users->onFirstpage())
	                <a href="{{ $users->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $users->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($users->currentPage()) }} of
	                    {{ number_format($users->lastPage()) }} </a></span>
	            @if (!$users->onLastpage())
	                <a href="{{ $users->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $users->appends(request()->input())->url($users->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
	</div>
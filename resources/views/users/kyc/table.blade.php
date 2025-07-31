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
		<th>User name</th>
		<th>User Role</th>
		<th>Email</th>
		<th>Mobile</th>
		<th>DOB</th>
		<th>Gender</th>
		<th width="4%" align="text-center">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($users as $user)
    <tr>
    	<th>{{$user->name ?? ''}} {{$user->lastname ?? ''}}</th>
		<th style="text-transform:none">{{$user->username ?? ''}}</th>
		<th>{{!empty($user->roles) ? $user->roles[0]->name : ''}}</th>
		<th style="text-transform:none">{{$user->email ?? ''}}</th>
		<th>{{$user->mobile ?? ''}}</th>
		<th>{{$user->dob ? dateFormat($user->dob) : '' }}</th>
		<th>{{$user->gender ?? '' }}</th>
		{{-- <th>{{$user->country ?? ''}}</th> --}}
		<th>
			<div class="row">
			<div class="col-4 p-1">	
				<a href="{{ route("users.show",["user"=> $user->id,'page'=>'kyc' ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a>
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
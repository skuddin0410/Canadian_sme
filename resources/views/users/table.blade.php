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
		
		<th>Email</th>
		<th>Mobile</th>
		<th>DOB</th>
		<th>Gender</th>
		<th>State</th>
		<th>Country</th>
		<th width="10%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($users as $user)
    <tr>
    	<th>{{$user->name ?? ''}} {{$user->lastname ?? ''}}</th>
		<th style="text-transform:none">{{$user->username ?? ''}}</th>
		{{-- <th>{{!empty($user->roles) ? $user->roles[0]->name : ''}}</th> --}}
		<th style="text-transform:none">{{$user->email ?? ''}}</th>
		<th>{{$user->mobile ?? ''}}</th>
		<th>{{$user->dob ? dateFormat($user->dob) : '' }}</th>
		<th>{{$user->gender ?? '' }}</th>
		<th>{{$user->state ?? ''}}</th> 
		<th>{{$user->country ?? ''}}</th> 
		<th class="text-center align-middle">
    <div class="d-flex justify-content-center align-items-center gap-1">
        <!-- Show -->
        <a href="{{ route('users.show', ['user' => $user->id]) }}" 
           class="btn btn-sm btn-icon p-1" 
           title="View">
            <i class="bx bxs-show" style="font-size: 1rem;"></i>
        </a>

        @if(Auth::user()->hasRole('Admin'))
            <!-- Edit -->
            <a href="{{ route('users.edit', ['user' => $user->id]) }}" 
               class="btn btn-sm btn-icon p-1" 
               title="Edit">
                <i class="bx bxs-edit" style="font-size: 1rem;"></i>
            </a>

            <!-- Delete -->
            <form action="{{ route('users.destroy', $user->id) }}" method="post" class="m-0 p-0">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-sm btn-icon btn-danger p-1 delete" 
                        onclick="return confirm('Are you sure you want to delete?')" 
                        title="Delete">
                    <i class="bx bxs-trash" style="font-size: 1rem;"></i>
                </button>
            </form>
        @endif
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

<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="sendMailModalLabel" aria-hidden="true">
<form action="{{route('sendmail_to_user')}}" method="POST" enctype="multipart/form-data">	
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendMailModalLabel">
          <i class="bi bi-envelope me-2"></i>Send Email
          <br><small id="fullname"></small>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
          @csrf
          <input type="hidden" name="user_id" id="user_id" value="{{old('user_id')}}" />
          <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" value="{{old('subject')}}" id="subject">
          </div>

          <div class="mb-3">
                <label for="body" class="form-label">Body</label>
                <textarea name="body" class="form-control" value="{{old('body')}}" required></textarea>
          </div>

          <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-envelope me-2"></i>Send Email
                </button>
          </div>
      </div>
    </div>
  </div>
</form>  
</div>

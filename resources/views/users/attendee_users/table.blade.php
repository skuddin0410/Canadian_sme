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
		{{-- <th>Email</th>
		<th>Mobile</th> --}}
    <th>Added on</th>
    <th>QR</th>
		{{-- <th>Referral coupon</th> --}}
		
		<th width="28%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($users as $user)
    <tr>
    <th>{{$user->name ?? ''}} {{$user->lastname ?? ''}}</th>
		{{-- <th style="text-transform:none">{{$user->email ?? ''}}</th>
		<th>{{$user->mobile ?? ''}}</th> --}}
    <th>{{dateFormat($user->created_at) ?? '' }}</th>
    <th>
		
		@if($user->qr_code)
        <a href="{{ route('speaker.qr.download', $user->id) }}" 
           class="btn btn-sm btn-primary" 
           title="Download QR">
             Download QR
        </a>
        @else
          <span class="text-muted">No QRCode Generated Yet</span>
        @endif
		
	</th>
		
		
         <th>
  <div class="d-flex gap-2">
        <form action="{{ route('attendee-users.allow-access', $user->id) }}" method="POST" style="display:inline;">
    @csrf
    @if($user->is_approve)
        <button type="submit" class="btn btn-sm btn-success" title="App access approved">
            ✔ Approved
        </button>
    @else
        <button type="submit" class="btn btn-sm btn-primary" title="Allow app access">
            Allow app access
        </button>
    @endif
</form>
      <button type="button" 
        class="btn btn-sm btn-primary" 
        data-bs-toggle="modal" 
        data-bs-target="#sendMailModal{{ $user->id }}">
         Send Mail
        </button>

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
@foreach($users as $user)
<!-- Send Mail Modal -->
<div class="modal fade" id="sendMailModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('attendee-users.sendMail', $user->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Send Mail to {{ $user->name }} {{ $user->lastname }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Subject -->
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ getKeyValue('email_subject')->value ?? '' }}" required>
          </div>
          <!-- Message -->
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required>{{getKeyValue('email_content')->value ?? ''}}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Mail</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
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
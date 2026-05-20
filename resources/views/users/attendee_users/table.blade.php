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
<div class="row">
<div class="col-6 text-end p-3"></div>
<div class="col-3 text-end p-3">
        <button type="button" class="btn btn-outline-info filterAppUsers">
               Total App Users: {{ $totalAppUsers }}
        </button>
 
</div>
<div class="col-3 text-end p-3">
 {{$range}} out of {{$totalRecords}} users
</div>
</div>
<table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
	<tr>
    <th><input type="checkbox" id="select-all"></th>
		<th>Name</th>
		<th>Email</th>
		<th>Mobile</th>
        <th>App Use</th>
        <th>Added on</th>
        <th>QR</th>
        <th>Badge</th>
		
		<th width="25%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($users as $user)
    <tr>
      <td>
            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox">
      </td>
    <th>
      {{$user->name ?? ''}} {{$user->lastname ?? ''}}

{!! $user->access_speaker_ids 
    ? '<a href="' . route("speaker.edit", $user->access_speaker_ids) . '" class="text-decoration-none" target="_blank">'
        . '<span class="badge border border-primary text-primary rounded-pill badge-sm">As Speaker</span>'
      . '</a>' 
    : '' 
!!}

{!! $user->access_exhibitor_ids 
    ? '<a href="' . route("exhibitor-users.edit", $user->access_exhibitor_ids) . '" class="text-decoration-none me-1" target="_blank">'
        . '<span class="badge border border-primary text-primary rounded-pill badge-sm">As Exhibitor</span>'
      . '</a>' 
    : '' 
!!}

{!! $user->access_sponsor_ids 
    ? '<a href="' . route("sponsors.edit", $user->access_sponsor_ids) . '" class="text-decoration-none me-1" target="_blank">'
        . '<span class="badge border border-primary text-primary rounded-pill badge-sm">As Sponsor</span>'
      . '</a>' 
    : '' 
!!} 

  @if($user->hasRole('Admin'))
  {!! 
      '<span class="badge border border-danger text-danger rounded-pill badge-sm">As Admin</span>' 
  !!}
  @endif


    </th>
		<th style="text-transform: lowercase;">{{$user->email ?? ''}}</th>
		<th>{{$user->mobile ?? ''}}</th>
        <th>{{$user->onesignal_userid ? 'Yes' : ''}}</th>
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
        <a href="" 
           class="btn btn-sm btn-primary" 
           title="Download QR" data-id="{{$user->id ?? ''}}" onclick="submitBadgeActionNew()">
             Download Badge
        </a>
	</th>
		
		
         <th>
  <div class="d-flex gap-2">
    <form action="{{ route('attendee-users.allow-access', $user->id) }}" method="POST" style="display:inline;">
    @csrf
    @if($user->is_approve ==0)
        <button type="submit" class="btn btn-sm btn-primary" title="Allow app access">
          Allow App Access
        </button>
    @else
        <button type="submit" class="btn btn-sm btn-secondary" title="Reject app access">
           Reject App Access
        </button>
    @endif
   </form>
   <!--    <button type="button" 
        class="btn btn-sm btn-primary" 
        data-bs-toggle="modal" 
        data-bs-target="#sendMailModal{{ $user->id }}">
         Send Mail
        </button> -->

    {{-- View --}}
    <a href="{{ route('attendee-users.show', $user->id) }}" class="btn btn-sm btn-icon btn-primary" title="View">
      <i class="bx bx-show"></i>
    </a>

    {{-- Edit --}}
    <a href="{{ route('attendee-users.edit', $user->id) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
      <i class="bx bx-edit-alt"></i>
    </a>

    {{-- Delete --}}
    <form action="{{ route('attendee-users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
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

<script>
function updateCounts() {
    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);

    if (typeof bulkSelectedIds !== 'undefined') {
        bulkSelectedIds = selectedIds;
    }

    if (typeof bulkSelectionMode !== 'undefined' && bulkSelectionMode !== 'selected' && selectedIds.length > 0) {
        bulkSelectionMode = 'selected';
    }

    if (typeof window.syncBulkUiState === 'function') {
        window.syncBulkUiState();
    }

    if(selectedIds.length){
       document.getElementById('sendAllEmailCheckboxId').style.display = 'none';
       document.getElementById('sendAllotificationCheckboxId').style.display = 'none';
       
   }else{
       document.getElementById('sendAllEmailCheckboxId').style.display = 'block';
       document.getElementById('sendAllotificationCheckboxId').style.display = 'block';
   }
    
}

// Select All checkbox
document.getElementById('select-all').addEventListener('change', function() {
    let checked = this.checked;
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = checked);
    updateCounts();
});

// Individual checkboxes
document.querySelectorAll('.user-checkbox').forEach(cb => {
    cb.addEventListener('change', updateCounts);
});

updateCounts();



function submitBadgeAction() {
    if (typeof window.ensureFiltersApplied === 'function' && !window.ensureFiltersApplied()) {
        return;
    }

    const selected = typeof window.getBulkSelectedIds === 'function' ? window.getBulkSelectedIds() : [];
    const selectionMode = typeof window.getBulkSelectionMode === 'function' ? window.getBulkSelectionMode() : 'selected';
    const selectedCount = selectionMode === 'selected'
        ? selected.length
        : (typeof bulkSelectionMode !== 'undefined' && bulkSelectionMode === 'all_except_event' ? bulkAllExceptEventCount : bulkFilteredCount);

    if (selectionMode === 'selected' && selected.length === 0) {
        Swal.fire('Select Users First', 'Please select users or use Select All Filtered.', 'warning');
        return;
    }

    if (selectedCount > 50) {
        Swal.fire('Limit Reached', `You can generate badges for up to 50 users at a time. Current selection: ${selectedCount}.`, 'warning');
        return;
    }

    const selectedBadgeId = $('#badge_id').val();
    if (!selectedBadgeId) {
        Swal.fire('Badge Required', 'Please select a badge template first.', 'warning');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('attendee-users.generateBadge') }}";
    form.target = "_blank";

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const badgeInput = document.createElement('input');
    badgeInput.type = 'hidden';
    badgeInput.name = 'badge_id';
    badgeInput.value = selectedBadgeId;
    form.appendChild(badgeInput);

    const userIdsInput = document.createElement('input');
    userIdsInput.type = 'hidden';
    userIdsInput.name = 'user_ids';
    userIdsInput.value = JSON.stringify(selected);
    form.appendChild(userIdsInput);

    const selectionModeInput = document.createElement('input');
    selectionModeInput.type = 'hidden';
    selectionModeInput.name = 'selection_mode';
    selectionModeInput.value = selectionMode;
    form.appendChild(selectionModeInput);

    const eventInput = document.createElement('input');
    eventInput.type = 'hidden';
    eventInput.name = 'event_id';
    eventInput.value = document.getElementById('event_id').value || '';
    form.appendChild(eventInput);

    const searchInput = document.createElement('input');
    searchInput.type = 'hidden';
    searchInput.name = 'search';
    searchInput.value = document.getElementById('search').value || '';
    form.appendChild(searchInput);

    document.body.appendChild(form);
    form.submit();
}

function submitBadgeActionNew() {
    let selectedBadgeId = $('#badge_id').val();
    if (!selectedBadgeId) {
        selectedBadgeId = $('#badge_id option:eq(1)').val();
        if (selectedBadgeId) {
            $('#badge_id').val(selectedBadgeId);
        }
    }

    if (!selectedBadgeId) {
        Swal.fire('Badge Required', 'Please select a badge template first.', 'warning');
        return;
    }

    const selectedUserId = event.currentTarget.getAttribute('data-id');
    if (!selectedUserId) {
        Swal.fire('Select User First', 'Unable to find the selected user for badge generation.', 'warning');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('attendee-users.generateBadge') }}";
    form.target = "_blank";

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const badgeInput = document.createElement('input');
    badgeInput.type = 'hidden';
    badgeInput.name = 'badge_id';
    badgeInput.value = selectedBadgeId;
    form.appendChild(badgeInput);

    const userIdsInput = document.createElement('input');
    userIdsInput.type = 'hidden';
    userIdsInput.name = 'user_ids';
    userIdsInput.value = JSON.stringify([selectedUserId]);
    form.appendChild(userIdsInput);

    const selectionModeInput = document.createElement('input');
    selectionModeInput.type = 'hidden';
    selectionModeInput.name = 'selection_mode';
    selectionModeInput.value = 'selected';
    form.appendChild(selectionModeInput);

    document.body.appendChild(form);
    form.submit();
}

</script>
<style>
  .badge-sm {
    font-size: 0.5rem;     /* smaller text */
    padding: 0.22em 0.5em; /* smaller padding */
}

</style>

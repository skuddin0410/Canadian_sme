@extends('layouts.admin')

@section('title')
    Admin | Attendee
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Attendee/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Attendee List</h5>
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                            <a href="{{route('attendee-users.export')}}" class="btn btn-outline-primary btn-pill">Export</a>
                            <a href="{{ route('attendee-users.create') }}" class="btn btn-primary dt-button create-new">
                                <i class="bx bx-plus me-sm-1"></i> Add Attendee
                            </a>
                    
                    </div>
                </div>

                <!-- Search Section -->
                <div class="row p-3 align-items-center">
                    <!-- Search / Filter -->
                    <div class="col-md-6 text-end">
                        <form action="#" method="GET" id="users-search-filter-form">
                            <div class="row g-2 align-items-center">
                                <!-- Search -->
                                <div class="col-auto">
                                    <input type="text" class="form-control form-control-md" name="search" id="search"
                                        value="{{ request('search') }}" placeholder="Search by Name, Email">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-md btn-primary" id="search-btn">Search</button>
                                </div>
                                <!-- Reset -->
                                <div class="col-auto">
                                    <button type="button" class="btn btn-md btn-secondary reset-filter">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="col-md-6 text-end">
                        <form id="bulkActionForm" action="#" method="POST">
                            @csrf
                            <input type="hidden" name="user_ids" id="selectedUserIds">

                            <div class="d-inline-block">
                                <button type="button" class="btn btn-primary" onclick="openModal('email')">
                                    Send Email (<span id="emailCount">0</span>)
                                </button>
                                <button type="button" class="btn btn-success" onclick="openModal('notification')">
                                    Send Notification (<span id="notifCount">0</span>)
                                </button>

                                 <button type="button" class="btn btn-secondary" onclick="openModal('notification')">
                                    Generate Badge (<span id="badgeCount">0</span>)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Card Body -->
                <div class="card-body pt-0">
                    <!-- Loading Spinner -->
                    <div class="text-center mb-3">
                        <div class="spinner-border spinner-border-sm" style="display:none;"></div>
                    </div>

                    <!-- User Table -->
                    <div class="table-responsive" id="user-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkActionModalEmail" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Select Email Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="actionType">Choose Template</label>
                <select id="emailTemplate" class="form-control" name="template_name">
                    <option value="">Please Select Template</option>
                    @if(!empty(fetchEmailTemplates()))
                        @foreach(fetchEmailTemplates() as $emailtemplate)
                          <option value="{{$emailtemplate->template_name}}">{{$emailtemplate->template_name}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkAction()">Submit</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="bulkActionModalNotifications" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Select Select Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="actionType">Choose Notification Template</label>
                <select id="notificationTemplate" class="form-control" name="template_name">
                    <option value="">Please Select Template</option>
                    @if(!empty(fetchNotificationTemplates()))
                        @foreach(fetchNotificationTemplates() as $emailtemplate)
                          <option value="{{$emailtemplate->template_name}}">{{$emailtemplate->template_name}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkAction()">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // Function to load users via AJAX
    function loadUsers(params = {}) {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('attendee-users.index') }}",
            type: 'GET',
            headers: { 'X-CSRF-Token': $('meta[name="_token"]').attr('content') },
            data: Object.assign({ ajax_request: true }, params),
            dataType: "json",
            success: function(data) {
                $("#user-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function() {
                $(".spinner-border").fadeOut(300);
            }
        });
    }

    // Initial load
    loadUsers();

    // Search button click
    $('#search-btn').click(function() {
        const searchVal = $('#search').val().trim();
        loadUsers({ search: searchVal });
    });

    // Filter button click
    $('#filter-btn').click(function() {
        const kycVal = $('#kyc').val().trim();
        loadUsers({ kyc: kycVal });
    });

    // Reset function
    $('.reset-filter').click(function() {
        $('#search').val('');
        $('#kyc').val('');
        loadUsers();
    });

    // Pagination click
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");
        if(!url) return;
        $(".spinner-border").fadeIn(300);
        $.get(url + '&ajax_request=true', function(data) {
            $("#user-table").html(data.html);
        }).done(function() {
            $(".spinner-border").fadeOut(300);
        });
    });
});


function openModal(actionType) {
    window.selectedActionType = actionType;
    if (actionType === 'email') {
        $('#bulkActionModalEmail').modal('show');
    } 

    if (actionType === 'notification') {
        $('#bulkActionModalNotifications').modal('show');
    }

    document.getElementById('emailTemplate').value=''
    document.getElementById('notificationTemplate').value=''
}

function closeModal(){
    $('#bulkActionModalEmail').modal('hide');
    $('#bulkActionModalNotifications').modal('hide');
    document.getElementById('emailTemplate').value=''
    document.getElementById('notificationTemplate').value=''

}
</script>


@endsection

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
                            <a href="{{route('attendee-users.export')}}" class="btn btn-outline-primary btn-pill">Export Users</a>
                            <a href="#" class="btn btn-outline-primary btn-pill" id="importusers" onclick="openImportModal()">Import Users</a>
                            <a href="{{ route('attendee-users.create') }}" class="btn btn-primary dt-button create-new">
                                <i class="bx bx-plus me-sm-1"></i> Add Attendee
                            </a>

                             <a href="{{ route('attendee-users.generateQrCodeManually') }}" class="btn btn-secondary dt-button create-new">
                                 Generate Qrcode
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

                                 <button type="button" class="btn btn-secondary" onclick="submitBadgeAction()">
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

<!---<div class="modal fade" id="bulkBadgeModal" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Select Badge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="actionType">Choose Badge Template</label>
                <select id="badge" class="form-control" name="template_name">
                    <option value="">Please Select Badge Template</option>
                    @if(!empty(fetchBadgeTemplates()))
                        @foreach(fetchBadgeTemplates() as $emailtemplate)
                          <option value="{{$emailtemplate->badge_name}}">{{ $emailtemplate->badge_name ?? 'Badge #' . $emailtemplate->id}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitBadgeAction()">Submit</button>
            </div>
        </div>
    </div>
</div> --->

<div class="modal fade" id="openImportModal" tabindex="-1" role="dialog" aria-labelledby="ImportModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('user_import')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mt-3">
                        <label for="userImportFile">Import Users (CSV, XLSX)</label>
                        <input type="file" id="userImportFile" class="form-control" name="file" accept=".csv" required>
                        <!-- <small class="form-text text-muted">After the import, an email will be sent to each user.</small> -->
                    </div>
                    <div id="rowCount"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeImportModal()">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display:none;">Submit</button>
                </div>
            </form>

            
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
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

    if (actionType === 'badge') {
        $('#bulkBadgeModal').modal('show');
    }

    document.getElementById('emailTemplate').value=''
    document.getElementById('notificationTemplate').value=''
    document.getElementById('badge').value=''
}

function closeModal(){
    $('#bulkActionModalEmail').modal('hide');
    $('#bulkActionModalNotifications').modal('hide');
    $('#bulkBadgeModal').modal('hide');
    document.getElementById('emailTemplate').value=''
    document.getElementById('notificationTemplate').value=''
    document.getElementById('badge').value=''

}

function openImportModal(actionType) {
    $('#openImportModal').modal('show');
}

function closeImportModal(){
    $('#openImportModal').modal('hide'); 
}
</script>
<script>
  // Event listener for file input change
  document.getElementById('userImportFile').addEventListener('change', function (event) {
    const file = event.target.files[0];

    if (!file) {
      alert('Please select a CSV file');
      return;
    }

    // Parse the selected CSV file
    Papa.parse(file, {
      complete: function(results) {
        // Remove blank rows (rows that don't have any meaningful data)
        const filteredData = results.data.filter(row => {
          // Check if there's at least one non-empty cell in the row
          return Object.values(row).some(cell => cell.trim() !== '');
        });

        // Get the number of rows after filtering out blank ones
        const numRows = filteredData.length;

        // Show the row count
        document.getElementById('rowCount').innerText = `Number of rows in CSV: ${numRows}`;

        // Enable the submit button if there are rows to process
        if (numRows > 0) {
          document.getElementById('submitBtn').style.display = 'inline-block';
        } else {
          document.getElementById('submitBtn').style.display = 'none';
        }
      },
      header: true, // Assuming the first row is a header
      skipEmptyLines: true // Skip any empty lines in the CSV
    });
  });
</script>
@endsection

@extends('layouts.admin')

@section('title')
    Admin | Attendee
@endsection

@section('content')
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
    <div class="container flex-grow-1 container-p-y pt-0">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Attendee/</span>Lists</h4>
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Attendee List</h5>
                        <div class="dt-action-buttons text-end pt-3 pt-md-0">
                            <button type="button" class="btn btn-outline-info btn-pill" onclick="openScheduledEmailsModal()">
                                <i class="bx bx-time me-1"></i>Scheduled Emails
                            </button>
                            <button type="button" class="btn btn-outline-info btn-pill" onclick="openScheduledNotificationsModal()">
                                <i class="bx bx-bell me-1"></i>Scheduled Notifications
                            </button>
                            <a href="{{ route('attendee-users.export') }}" class="btn btn-outline-primary btn-pill">Export
                                Users</a>
                            <a href="#" class="btn btn-outline-primary btn-pill" id="importusers"
                                onclick="openImportModal()">Import Users</a>
                            <a href="{{ route('attendee-users.create') }}" class="btn btn-primary dt-button create-new">
                                <i class="bx bx-plus me-sm-1"></i> Add Attendee
                            </a>

                            <a href="{{ route('attendee-users.generateQrCodeManually') }}"
                                class="btn btn-secondary dt-button create-new">
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
                                        <input type="text" class="form-control form-control-md" name="search"
                                            id="search" value="{{ request('search') }}"
                                            placeholder="Search by Name, Email">
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select select2" name="event_id"
                                                data-placeholder="Select event" data-allow-clear="true" id="event_id">
                                                <option value="">Please select</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">
                                            {{ $event->title }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-md btn-primary"
                                            id="search-btn">Search</button>
                                    </div>
                                    <!-- Reset -->
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-md btn-secondary reset-filter">Reset</button>
                                    </div>
                                    @if(isSuperAdmin())
                                    <!-- <div class="col-auto">
                                        <button type="button" class="btn btn-md btn-danger search-admins" onclick="filterAdmins()">Event Admins</button>
                                    </div> -->
                                    @endif
                                </div>
                            </form>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="col-md-12 text-end">
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
                                    <!-- <button type="button" class="btn btn-warning" onclick="openModal('both')">
                                        Send Email/Notification (<span id="bothCount">0</span>)
                                    </button> -->

                                    <button type="button" class="btn btn-secondary" onclick="openBadgeModal()">
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

    <div class="modal fade" id="bulkActionModalEmail" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel"
        aria-hidden="true">
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
                        @if (!empty(fetchEmailTemplates()))
                            @foreach (fetchEmailTemplates() as $emailtemplate)
                                <option value="{{ $emailtemplate->template_name }}">{{ $emailtemplate->template_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <div class="form-check mt-2" id="sendAllEmailCheckboxId">
                       <div class="form-check">
                      
                        <div class="alert alert-warning d-flex align-items-center mt-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>No users was selected.</strong>The email will be sent to all users.
                        </div>
                    </div>
           
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-start">
                            <label for="schedule_time_email" class="form-label">Schedule Time (Optional)</label>
                            <input type="datetime-local" id="schedule_time_email" name="schedule_time" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitBulkAction()">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="bulkActionModalNotifications" tabindex="-1" role="dialog"
        aria-labelledby="bulkActionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionModalLabel">Select Select Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="actionType">Choose Notification Template</label>
                    <select id="notificationTemplate" class="form-control" name="template_name">
                        <option value="">Please Select Template</option>
                        @if (!empty(fetchNotificationTemplates()))
                            @foreach (fetchNotificationTemplates() as $emailtemplate)
                                <option value="{{ $emailtemplate->template_name }}">{{ $emailtemplate->template_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <div class="form-check" id="sendAllotificationCheckboxId">
                      
                        <div class="alert alert-warning d-flex align-items-center mt-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>No users was selected.</strong>The notification will be sent to all users.
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-start">
                            <label for="schedule_time_notif" class="form-label">Schedule Time (Optional)</label>
                            <input type="datetime-local" id="schedule_time_notif" name="schedule_time" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitBulkAction()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkActionModalBoth" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Email & Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="emailTemplateBoth">Email Template</label>
                        <select id="emailTemplateBoth" class="form-control">
                            <option value="">Please select email template</option>
                            @if (!empty(fetchEmailTemplates()))
                                @foreach (fetchEmailTemplates() as $template)
                                    <option value="{{ $template->template_name }}">{{ $template->template_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notificationTemplateBoth">Notification Template</label>
                        <select id="notificationTemplateBoth" class="form-control">
                            <option value="">Please select notification template</option>
                            @if (!empty(fetchNotificationTemplates()))
                                @foreach (fetchNotificationTemplates() as $template)
                                    <option value="{{ $template->template_name }}">{{ $template->template_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-start">
                            <label for="schedule_time_both" class="form-label">Schedule Time (Optional)</label>
                            <input type="datetime-local" id="schedule_time_both" name="schedule_time" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitBulkActionBoth()">Send</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Scheduled Emails Modal --}}
    <div class="modal fade" id="scheduledEmailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-time me-1"></i> Scheduled Emails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3" id="scheduledEmailsLoading">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading scheduled emails...</p>
                    </div>
                    <div id="scheduledEmailsContent" style="display:none;">
                        <table class="table table-striped table-bordered" id="scheduledEmailsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Template</th>
                                    <th>Scheduled For</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="noScheduledEmails" class="text-center text-muted py-4" style="display:none;">
                            <i class="bx bx-envelope" style="font-size: 2rem;"></i>
                            <p class="mt-2">No scheduled emails found.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scheduled Notifications Modal --}}
    <div class="modal fade" id="scheduledNotificationsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-bell me-1"></i> Scheduled Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3" id="scheduledNotifsLoading">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading scheduled notifications...</p>
                    </div>
                    <div id="scheduledNotifsContent" style="display:none;">
                        <table class="table table-striped table-bordered" id="scheduledNotifsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Scheduled For</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="noScheduledNotifs" class="text-center text-muted py-4" style="display:none;">
                            <i class="bx bx-bell-off" style="font-size: 2rem;"></i>
                            <p class="mt-2">No scheduled notifications found.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="bulkBadgeModal" tabindex="-1" role="dialog" aria-labelledby="bulkBadgeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkBadgeModalLabel">Select Badge Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label for="badge_id">Choose Badge Template</label>
                <select id="badge_id" class="form-control" name="badge_id">
                    <option value="">Please Select Badge Template</option>
                    @if (!empty(fetchNewBadgeTemplates()))
                        @foreach (fetchNewBadgeTemplates() as $badge)
                            <option value="{{ $badge->id }}">
                                {{ $badge->badge_name ?? 'Badge #' . $badge->id }}
                            </option>
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
    </div>


    <div class="modal fade" id="openImportModal" tabindex="-1" role="dialog" aria-labelledby="ImportModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('user_import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mt-3">
                            <label for="userImportFile">Import Users (Max 100 rows allowed, Allowed file CSV, XLSX) </label>
                            <input type="file" id="userImportFile" class="form-control" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <!-- <small class="form-text text-muted">After the import, an email will be sent to each user.</small> -->
                        </div>
                        <div id="rowCount"></div>
                    </div>
                    <div class="modal-footer">
                        Sample File: <a href="{{ asset('/Application Test Leads Import.xlsx') }}" download>Download</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onclick="closeImportModal()">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn"
                            style="display:none;">Submit</button>
                    </div>
                </form>


            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        let savedParams = JSON.parse(localStorage.getItem('attendeeParams')) || {};
        // 🔹 Restore search input
        if (savedParams.search) {
            $('#search').val(savedParams.search);
        }

        // 🔹 Restore event dropdown
        if (savedParams.event_id) {
            $('#event_id').val(savedParams.event_id).trigger('change');
        }
        loadUsers(savedParams);

        function loadUsers(params = {}) {
            localStorage.setItem('attendeeParams', JSON.stringify(params));
            $(".spinner-border").fadeIn(300);

            $.ajax({
                url: "{{ route('attendee-users.index') }}",
                type: 'GET',
                headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                },
                data: Object.assign({
                    ajax_request: true
                }, params),
                dataType: "json",
                success: function(data) {
                    $("#user-table").html(data.html);
                },
                error: function(xhr) {
                    console.error('Error loading users:', xhr.responseText);
                },
                complete: function() {
                    $(".spinner-border").fadeOut(300);
                }
            });
        }
            
        // Function to handle Admin filter
        function filterAdmins() {
            // Create a new search parameter object with show_admins as true
            let params = {
                show_admins: 'true',
                search: $('#search').val().trim(),  // Retain any existing search values
                page: 1
            };

            // Reload users with the new filter applied
            loadUsers(params);
        }

        $(document).ready(function() {


            // let savedParams = JSON.parse(localStorage.getItem('attendeeParams')) || {};
            // loadUsers(savedParams);

            // function loadUsers(params = {}) {
            //     localStorage.setItem('attendeeParams', JSON.stringify(params));
            //     $(".spinner-border").fadeIn(300);

            //     $.ajax({
            //         url: "{{ route('attendee-users.index') }}",
            //         type: 'GET',
            //         headers: {
            //             'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            //         },
            //         data: Object.assign({
            //             ajax_request: true
            //         }, params),
            //         dataType: "json",
            //         success: function(data) {
            //             $("#user-table").html(data.html);
            //         },
            //         error: function(xhr) {
            //             console.error('Error loading users:', xhr.responseText);
            //         },
            //         complete: function() {
            //             $(".spinner-border").fadeOut(300);
            //         }
            //     });
            // }


            $('#search-btn, #filter-btn').click(function() {
                const searchVal = $('#search').val().trim();
                const eventId   = $('#event_id').val();

                loadUsers({
                    search: searchVal,
                    event_id: eventId,
                    page: 1
                });
            });

            $('.reset-filter').click(function() {
                $('#search').val('');
                $('#event_id').val('').trigger('change');
                localStorage.removeItem('attendeeParams');
                loadUsers();
            });

            $(document).on("click", ".custom_pagination .pagination-link", function(e) {
                e.preventDefault();
                let url = $(this).attr("href");
                if (!url) return;

                let pageMatch = url.match(/page=(\d+)/);
                let page = pageMatch ? pageMatch[1] : 1;

                let params = JSON.parse(localStorage.getItem('attendeeParams')) || {};
                params.page = page;
                loadUsers(params);
            });

            $(document).on("click", ".filterAppUsers", function() {
                const searchVal = $('#search').val().trim();
                loadUsers({
                    search: searchVal,
                    page: 1,
                    onsignal: 1
                });
            });


            window.selectedActionType = null;

            function resetTemplateSelections() {
                $('#emailTemplate, #notificationTemplate, #emailTemplateBoth, #notificationTemplateBoth').val('');
                $('#schedule_time_email, #schedule_time_notif, #schedule_time_both').val('');
            }

            window.openModal = function(actionType) {
                window.selectedActionType = actionType;
                closeModal();

                if (actionType === 'email') $('#bulkActionModalEmail').modal('show');
                else if (actionType === 'notification') $('#bulkActionModalNotifications').modal('show');
                else if (actionType === 'both') $('#bulkActionModalBoth').modal('show');

                resetTemplateSelections();
            }

            window.closeModal = function() {
                $('#bulkActionModalEmail, #bulkActionModalNotifications, #bulkActionModalBoth, #openImportModal, #bulkBadgeModal')
                    .modal('hide');
                resetTemplateSelections();
                window.selectedActionType = null;
            }

            window.openImportModal = function() {
                $('#openImportModal').modal('show');
            }
            window.closeImportModal = function() {
                $('#openImportModal').modal('hide');
            }


            window.submitBulkActionBoth = function() {
                const emailTemplate = $('#emailTemplateBoth').val();
                const notificationTemplate = $('#notificationTemplateBoth').val();

                if (!emailTemplate && !notificationTemplate) {
                    Swal.fire('Error', 'Please select at least one template (email or notification).', 'error');
                    return;
                }

                const scheduleTime = $('#schedule_time_both').val();
                const confirmTitle = scheduleTime ? 'Schedule Both?' : 'Send Both Now?';
                const confirmText = scheduleTime 
                    ? 'This will schedule both email and notification for all users.' 
                    : 'This will send both email and notification to all users immediately.';

                Swal.fire({
                    title: confirmTitle,
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: scheduleTime ? 'Schedule' : 'Send Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let data = {
                            user_ids: 'all',
                            _token: '{{ csrf_token() }}'
                        };

                        if (emailTemplate) data.email_template = emailTemplate;
                        if (notificationTemplate) data.notification_template = notificationTemplate;
                        if (scheduleTime) {
                            data.schedule_time = scheduleTime;
                            data.timezone = userTimezone;
                        }

                        // Determine the correct route based on whether it is scheduled or immediate
                        // Actually the user wants to use schedule endpoint if time is set
                        let url = "{{ route('attendee-users.send-both') }}";
                        
                        Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                        $.ajax({
                            url: url,
                            method: "POST",
                            data: data,
                            success: function(resp) {
                                Swal.fire('Success!', resp.message || 'Action completed successfully!', 'success');
                                closeModal();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Unexpected error occurred.', 'error');
                            }
                        });
                    }
                });
            }

            // window.submitBulkAction = function() {
            //     const emailTemplate = $('#emailTemplate').val();
            //     const notificationTemplate = $('#notificationTemplate').val();

            //     if (!emailTemplate && !notificationTemplate) {
            //         alert("Please select at least one template (email or notification).");
            //         return;
            //     }

            //     // Always send to all users
            //     let data = {
            //         user_ids: 'all',
            //         _token: '{{ csrf_token() }}'
            //     };

            //     if (emailTemplate) data.email_template = emailTemplate;
            //     if (notificationTemplate) data.notification_template = notificationTemplate;

            //     $.ajax({
            //         url: "{{ route('attendee-users.bulkAction') }}",
            //         method: "POST",
            //         data: data,
            //         beforeSend: function() {
            //             $('.btn-primary').prop('disabled', true).text('Sending...');
            //         },
            //         success: function(resp) {
            //             alert(resp.message || 'Emails & Notifications sent successfully!');
            //             closeModal();
            //         },
            //         error: function(xhr) {
            //             console.error(xhr.responseText);
            //             alert(xhr.responseJSON?.message || 'Unexpected error occurred.');
            //         },
            //         complete: function() {
            //             $('.btn-primary').prop('disabled', false).text('Send');
            //         }
            //     });
            // }

        });
    </script>

    <script>
        // =============================================
        // Scheduled Emails Modal
        // =============================================
        window.openScheduledEmailsModal = function() {
            $('#scheduledEmailsModal').modal('show');
            $('#scheduledEmailsLoading').show();
            $('#scheduledEmailsContent').hide();

            $.ajax({
                url: "{{ route('attendee-users.scheduledEmails') }}",
                method: 'GET',
                success: function(data) {
                    $('#scheduledEmailsLoading').hide();
                    $('#scheduledEmailsContent').show();

                    const tbody = $('#scheduledEmailsTable tbody');
                    tbody.empty();

                    if (data.length === 0) {
                        $('#scheduledEmailsTable').hide();
                        $('#noScheduledEmails').show();
                    } else {
                        $('#scheduledEmailsTable').show();
                        $('#noScheduledEmails').hide();
                        data.forEach(function(item, index) {
                            const localScheduledAt = item.scheduled_at_iso 
                                ? new Date(item.scheduled_at_iso).toLocaleString() 
                                : item.scheduled_at;
                            const localCreatedAt = item.created_at_iso 
                                ? new Date(item.created_at_iso).toLocaleString() 
                                : item.created_at;

                            tbody.append(`
                                <tr id="scheduled-email-row-${item.id}">
                                    <td>${index + 1}</td>
                                    <td>${item.template_name}</td>
                                    <td>${localScheduledAt}</td>
                                    <td>${localCreatedAt}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cancelScheduledEmail(${item.id})">
                                            <i class="bx bx-trash"></i> Cancel
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                },
                error: function() {
                    $('#scheduledEmailsLoading').hide();
                    $('#scheduledEmailsContent').show();
                    $('#scheduledEmailsTable').hide();
                    $('#noScheduledEmails').show();
                }
            });
        };

        window.cancelScheduledEmail = function(jobId) {
            Swal.fire({
                title: 'Cancel Scheduled Email?',
                text: 'This scheduled email will be permanently cancelled.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('attendee-users.cancelScheduledEmail') }}",
                        method: 'POST',
                        data: {
                            job_id: jobId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            Swal.fire('Cancelled!', resp.message, 'success');
                            $('#scheduled-email-row-' + jobId).fadeOut(300, function() {
                                $(this).remove();
                                if ($('#scheduledEmailsTable tbody tr').length === 0) {
                                    $('#scheduledEmailsTable').hide();
                                    $('#noScheduledEmails').show();
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to cancel.', 'error');
                        }
                    });
                }
            });
        };

        // =============================================
        // Scheduled Notifications Modal
        // =============================================
        window.openScheduledNotificationsModal = function() {
            $('#scheduledNotificationsModal').modal('show');
            $('#scheduledNotifsLoading').show();
            $('#scheduledNotifsContent').hide();

            $.ajax({
                url: "{{ route('attendee-users.scheduledNotifications') }}",
                method: 'GET',
                success: function(data) {
                    $('#scheduledNotifsLoading').hide();
                    $('#scheduledNotifsContent').show();

                    const tbody = $('#scheduledNotifsTable tbody');
                    tbody.empty();

                    if (data.length === 0) {
                        $('#scheduledNotifsTable').hide();
                        $('#noScheduledNotifs').show();
                    } else {
                        $('#scheduledNotifsTable').show();
                        $('#noScheduledNotifs').hide();
                        data.forEach(function(item, index) {
                            const shortMsg = item.message.length > 60 ? item.message.substring(0, 60) + '...' : item.message;
                            const localScheduledAt = item.scheduled_at_iso 
                                ? new Date(item.scheduled_at_iso).toLocaleString() 
                                : item.scheduled_at;

                            tbody.append(`
                                <tr id="scheduled-notif-row-${item.id}">
                                    <td>${index + 1}</td>
                                    <td>${item.title}</td>
                                    <td title="${item.message}">${shortMsg}</td>
                                    <td>${localScheduledAt}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cancelScheduledNotification('${item.id}')">
                                            <i class="bx bx-trash"></i> Cancel
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                },
                error: function() {
                    $('#scheduledNotifsLoading').hide();
                    $('#scheduledNotifsContent').show();
                    $('#scheduledNotifsTable').hide();
                    $('#noScheduledNotifs').show();
                }
            });
        };

        window.cancelScheduledNotification = function(notificationId) {
            Swal.fire({
                title: 'Cancel Scheduled Notification?',
                text: 'This scheduled notification will be permanently cancelled on OneSignal.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('attendee-users.cancelScheduledNotification') }}",
                        method: 'POST',
                        data: {
                            notification_id: notificationId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            Swal.fire('Cancelled!', resp.message, 'success');
                            $(`#scheduled-notif-row-${notificationId}`).fadeOut(300, function() {
                                $(this).remove();
                                if ($('#scheduledNotifsTable tbody tr').length === 0) {
                                    $('#scheduledNotifsTable').hide();
                                    $('#noScheduledNotifs').show();
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to cancel.', 'error');
                        }
                    });
                }
            });
        };
    </script>

    <script>
        window.submitBadgeAction = function() {
    const selectedBadgeId = $('#badge_id').val();

    if (!selectedBadgeId) {
        alert('⚠️ Please select a badge template before submitting.');
        return;
    }

    // Collect selected user IDs (from checkboxes)
    const selectedUserIds = [];
    $('input[name="user_checkbox[]"]:checked').each(function() {
        selectedUserIds.push($(this).val());
    });

    if (selectedUserIds.length === 0) {
        alert('⚠️ Please select at least one user to generate badges for.');
        return;
    }

    // Create a form dynamically
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('attendee-users.generateBadge') }}";

    // CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    // Badge ID
    const badgeInput = document.createElement('input');
    badgeInput.type = 'hidden';
    badgeInput.name = 'badge_id';
    badgeInput.value = selectedBadgeId;
    form.appendChild(badgeInput);

    // User IDs (as JSON)
    const userIdsInput = document.createElement('input');
    userIdsInput.type = 'hidden';
    userIdsInput.name = 'user_ids';
    userIdsInput.value = JSON.stringify(selectedUserIds);
    form.appendChild(userIdsInput);

    document.body.appendChild(form);
    form.submit();
};
        window.openBadgeModal = function() {
            closeModal(); // closes other modals
            $('#bulkBadgeModal').modal('show'); // opens badge modal
        };
    </script>

    <script>
        // Event listener for file input change
        document.getElementById('userImportFile').addEventListener('change', function(event) {
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

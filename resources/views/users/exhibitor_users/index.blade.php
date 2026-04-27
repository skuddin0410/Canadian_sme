@extends('layouts.admin')

@section('title')
    Admin | Exhibitor
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Exhibitor/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0"> Exhibitor List</h5>
					<div class="dt-action-buttons text-end pt-3 pt-md-0">
                         @if(Auth::user()->hasRole('Admin')  ||  Auth::user()->hasRole('Admin') )


						<div class="dt-buttons"> 
                            <a href="{{route('exhibitors.export')}}" class="btn btn-outline-primary btn-pill">Export</a>
							<a href="{{route('exhibitor-users.create')}}" class="dt-button create-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button">
								<span><i class="bx bx-plus me-sm-1"></i> 
									<span class="d-none d-sm-inline-block">Add Exhibitor</span>
								</span>
							</a> 
						</div>
                        @endif
					</div>
				</div>
                <div class="col-12 text-right">
                <form action="#" method="GET" id="users-filter">        
                    <div class="row padding-none">
                        <div class="col-4">  
                        </div>
                        <div class="col-3">
                        <div class="">
                            <select class="form-select select2" name="event_id"
                                    data-placeholder="Select event" data-allow-clear="true" id="event_id">
                                    <option value="">Please select event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">
                                {{ $event->title }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        </div>          
                        <div class="col-3">  
                        <div class="mb-3">
                            <input
                            type="text"
                            class="form-control"
                            name="search"
                            value="{{ request('search') }}"
                            id="search"
                            placeholder="Search"/>  
                        </div>
                        </div>
                        <div class="col-2 text-center">
                           <button type="button" class="btn btn-outline-primary btn-pill reset-filter">Reset</button>
                           <button type="button" class="btn btn-primary filter">Filter</button>
                        </div>  
                    </div>
                </form>
                 </div>
				<div class="card-body pt-0">
                    <div class="col text-center">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>
					<div class="table-responsive" id="user-table">

                    </div> 
		        </div>
			</div>
		</div>
    </div>
</div>

<!-- Team Modal -->
<div class="modal fade" id="teamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exhibitor Team: <span id="modalExhibitorName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Email</th>
                                <th>Mobile</th>
                            </tr>
                        </thead>
                        <tbody id="teamTableBody">
                            <!-- Team members will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="teamLoader" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="noTeamMessage" class="text-center d-none p-3">
                    <p class="text-muted mb-0">No team members found for this exhibitor.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
function GetUserList() {
    $(".spinner-border").fadeIn(300);
    var params = $("#users-filter").serialize();

    $.ajax({
        url: "{{route('exhibitor-users.index')}}?" + params,
        type: 'get',
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        },
        data: { ajax_request: true },
        dataType: "json",
        success: function (data) {
           $(document).find("#user-table").html(data.html);
           $(".spinner-border").fadeOut(300);
        },
        error:function(data){
            $(".spinner-border").fadeOut(300);
        }
    });
}


    $(document).ready(function() {
        GetUserList();

        // Handle View Team Button Click
        $(document).on('click', '.view-team-btn', function() {
            const exhibitorId = $(this).data('id');
            const exhibitorName = $(this).data('name');
            
            $('#modalExhibitorName').text(exhibitorName);
            $('#teamTableBody').empty();
            $('#teamLoader').removeClass('d-none');
            $('#noTeamMessage').addClass('d-none');
            $('#teamModal').modal('show');

            $.ajax({
                url: `/admin/exhibitor-users/${exhibitorId}/team`,
                type: 'GET',
                success: function(response) {
                    $('#teamLoader').addClass('d-none');
                    if (response.success && response.team.length > 0) {
                        response.team.forEach(member => {
                            $('#teamTableBody').append(`
                                <tr>
                                    <td>
                                        <img src="${member.image}" alt="${member.name}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td class="fw-semibold">${member.name}</td>
                                    <td>${member.designation || '-'}</td>
                                    <td>${member.email || '-'}</td>
                                    <td>${member.mobile || '-'}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#noTeamMessage').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#teamLoader').addClass('d-none');
                    alert('Failed to fetch team members. Please try again.');
                }
            });
        });
    });

    jQuery(function($) {
            $(document).on("click", ".custom_pagination .pagination-link", function(e) {
                e.preventDefault();
                $(document).ajaxSend(function() {
                   $(".spinner-border").fadeIn(300);
                });
                $(this).parent().siblings('.page-item').removeAttr('aria-current');
                $(this).parent().siblings('.page-item').removeClass('active');
                var url = $(this).attr("href");
                finalURL = url;
                $(this).parent().addClass('active');
                $(this).parent().attr('aria-current', 'page');
                $.get(finalURL, function(data) {
                    $(document).find("#user-table").html(data.html);
                }).done(function() {
                   $(".spinner-border").fadeOut(300);
            });
            return false;
        })

  });
   $(document).on("click", ".filter", function(e) {
        var search = $('#search').val();
        // if( search.trim() == ''){
        //    return ;
        // }
        if( search.trim() == ''){
           search = '';
        }
       $(".spinner-border").fadeIn(300);  
       $.ajax({
            url: "{{route('exhibitor-users.index')}}" + '?' + $("#users-filter").serialize(),
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: {
                ajax_request: true
            },
            dataType: "json",
            success: function(data) {
                $(document).find("#user-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function(data) {
                $(".spinner-border").fadeOut(300);
            }
        });
    })
    
    $('.reset-filter').on('click', function() {
      window.location.href = "{{route('exhibitor-users.index')}}";
    });   	
</script>	
@endsection

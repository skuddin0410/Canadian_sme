@extends('layouts.admin')

@section('title')
    Admin | User List
@endsection
@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> User/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0"> User List</h5>

                    <div class="row justify-content-end" style="width:345px">
                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin')) 
                        <div class="col-6 text-end"> 
                            <a href="{{route('user_export')}}" class="dt-button create-new btn btn-primary"><span class="d-none d-sm-inline-block">Export User</span></a> 
                        </div> 
                        <div class="col-6 text-end"> 
                        <a href="#" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                          <i class="bi bi-upload me-2"></i>Bulk Import
                        </a>
                        </div>
                        @endif
                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
                        <div class="col-12 text-end ml-5 mt-2" style="padding: none!important"> 
							<a href="{{route('users.create')}}" class="dt-button create-new btn btn-primary">
								<span>
									<span class="d-none d-sm-inline-block">Add User</span>
								</span>
							</a> 
                    </div>
                        @endif
                    </div>
				</div>
                <div class="col-12 text-right mb-3">
                <form action="#" method="GET" id="users-filter">        
                    <div class="row padding-none">
                        <div class="col-2"> </div>
                        <div class="col-2"> 
                           
                        </div>
                        <div class="col-2"> 
                            <div class="mb-3">
                            
                            </div>
                        </div>
                        <div class="col-4">
                            <input
                              type="text"
                              class="form-control"
                              name="search"
                              value=""
                              id="search"
                              placeholder="Search"/>  
                        </div>          
                        <div class="col-2 text-center">
                           <button type="button" class="btn btn-outline-primary btn-pill reset-filter">Reset</button>
                           <button type="button" class="btn btn-primary filter">Filter</button>
                        </div>  
                    </div>
                </form>
                 </div>
				<div class="card-body pt-0">
					@if(Session::has('success'))
		              <div class="alert alert-success">
		              {{ Session::get('success') }}
		              </div>
		          @endif
		          @if(Session::has('error'))
		              <div class="alert alert-danger">
		              {{ Session::get('error') }}
		              </div>
		          @endif
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

<!-- Bulk Import Modal -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkImportModalLabel">
          <i class="bi bi-upload me-2"></i>Bulk Import Data
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('user_import')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="importType" class="form-label">Import Type</label>
            <select class="form-select" id="importType" name="role" required>
              <option value="">Select role...</option>
              <option value="Event Admin" >Event Admin</option> 

                  <option value="Admin">Admin</option> 

                  <option value="Representative">Representative</option> 

                  <option value="Attendee">Attendee</option> 

                  <option value="Speaker">Speaker</option> 

                  <option value="Support Staff Or Helpdesk">Support Staff Or Helpdesk</option> 

                  <option value="Registration Desk">Registration Desk</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="importFile" class="form-label">Choose File</label>
            <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.xlsx" required>
            <div class="form-text">Supported formats: CSV, Excel (.xlsx, .xls)</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-upload me-2"></i>Import Data
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
@php
   $url = route('users.index');
@endphp

 function GetUserList() {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{$url}}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
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
        var kyc = $('#kyc').val();
        var start_at = $('#start_at').val();
        var end_at = $('#end_at').val();
        var startDate = new Date(start_at);
        var endDate = new Date(end_at);
        if(startDate > endDate){
            alert("Start date should be less than end date");
            return ;

        }
        if( search.trim() == '' && kyc.trim()== '' && start_at == '' && end_at==''){
           return ;
        }
       $(".spinner-border").fadeIn(300);  
       $.ajax({
            url: "{{ $url }}" + '?' + $("#users-filter").serialize(),
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
      window.location.href = "{{$url}}";
    });   	
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    window.openModal = function(el) {
      document.getElementById('user_id').value = el.getAttribute('data-id');
      document.getElementById('fullname').innerHTML = '<i class="bi bi-person me-2"> '+el.getAttribute('data-email');
      new bootstrap.Modal(document.getElementById('sendMailModal')).show();
    }
  });
</script>
@endsection

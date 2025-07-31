@extends('layouts.admin')

@section('title')
    Admin | User List
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{ (!empty($kyc) && $kyc == "done") ? "KYC " : ''}} {{ (!empty($kyc) && $kyc == "required") ? "KYC Required " : '' }} User/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">{{ (!empty($kyc) && $kyc == "done") ? "KYC " : ''}} {{ (!empty($kyc) && $kyc == "required") ? "KYC Required " : '' }} User List</h5>

				</div>
                <div class="col-12 text-right">
                <form action="#" method="GET" id="users-filter">        
                    <div class="row padding-none">
                        <div class="col-4">  
                        </div>
                        <div class="col-3">
                            <input
                              type="text"
                              class="form-control"
                              name="search"
                              value=""
                              id="search"
                              placeholder="Search"/>  
                        </div>          
                        <div class="col-3">  
                         <div class="mb-3">
                          <div class="input-group input-group-merge padding-none">
                            <span id="title-icon" class="input-group-text"><i class="bx bx-chevron-down"></i></span>
                            <select class="form-control" name="user_type" id="user_type">
                              <option value="">Please select user type</option>    
                              {{-- <option value="Affiliate">Affiliate</option> --}}
                              <option value="User">User</option>
                            </select>
                          </div>
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
@endsection

@section('scripts')
<script type="text/javascript">
@php
   $url = route('users.index');
   if($kyc=='done'){
      $url = route('kyc-users');
   }
   if($kyc=='required'){
      $url = route('kyc-required-users');
   }
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
        var user_type = $('#user_type').val();
        if( search.trim() == '' && user_type.trim()== ''){
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
@endsection

@extends('layouts.admin')

@section('title')
    Admin | Contact List
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4">Contact</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Contact List</h5>
          <div class="dt-action-buttons text-end pt-3 pt-md-0">
            <div class="dt-buttons"> 
              <a href="{{ route('company.contacts.create') }}" class="dt-button create-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                <span><i class="bx bx-plus me-sm-1"></i> 
                  <span class="d-none d-sm-inline-block">Add Contact</span>
                </span>
              </a> 
            </div>
          </div>
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

          <div class="table-responsive" id="contact-table"></div> 
        </div>
      </div>
    </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
   function GetBlogList() {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{route('company.contacts.index')}}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
            dataType: "json",
            success: function (data) {
               $(document).find("#contact-table").html(data.html);
               $(".spinner-border").fadeOut(300);
            },
            error:function(data){
               $(".spinner-border").fadeOut(300);
            }
        });
    }

    $(document).ready(function() {
        GetBlogList();
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
                    $(document).find("#contact-table").html(data.html);
                }).done(function() {
                   $(".spinner-border").fadeOut(300);
            });
            return false;
        })

  });

    $(document).on("click", ".filter", function(e) {
        var search = $('#search').val();
        var category = $('#category').val();
        if( search.trim() == '' && category.trim()== ''){
           return ;
        } 
       $(".spinner-border").fadeIn(300); 
       $.ajax({
            url: "{{route('company.contacts.index')}}" + '?' + $("#blog-filter").serialize(),
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: {
                ajax_request: true
            },
            dataType: "json",
            success: function(data) {
                $(document).find("#contact-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function(data) {
                $(".spinner-border").fadeOut(300);
            }
        });
    })
    
    $('.reset-filter').on('click', function() {
      window.location.href = "{{route('company.contacts.index')}}";
    });       
</script> 
@endsection


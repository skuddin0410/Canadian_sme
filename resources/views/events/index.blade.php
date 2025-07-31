@extends('layouts.admin')

@section('title')
    Admin | Event List
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Event/</span>List</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Event List</h5>
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        @if(Auth::user()->hasRole('Admin'))
                        <div class="dt-buttons"> 
                            <a href="{{route('events.create')}}" class="dt-button create-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                <span><i class="bx bx-plus me-sm-1"></i> 
                                    <span class="d-none d-sm-inline-block">Add Event</span>
                                </span>
                            </a> 
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-12 text-right">
                    <form action="#" method="GET" id="blog-filter">        
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
                            <select class="form-control" name="category" id="category">
                                <option value="">Please select category</option>
                              @if(!empty($catgories))
                                @foreach($catgories as $category)
                                 <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach 
                              @endif
                            </select>
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

                    <div class="table-responsive" id="blog-table">

                    </div> 
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
            url: "{{route('events.index')}}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
            dataType: "json",
            success: function (data) {
               $(document).find("#blog-table").html(data.html);
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
                    $(document).find("#blog-table").html(data.html);
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
            url: "{{route('events.index')}}" + '?' + $("#blog-filter").serialize(),
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: {
                ajax_request: true
            },
            dataType: "json",
            success: function(data) {
                $(document).find("#blog-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function(data) {
                $(".spinner-border").fadeOut(300);
            }
        });
    })
    
    $('.reset-filter').on('click', function() {
      window.location.href = "{{route('events.index')}}";
    });         
</script>   
@endsection
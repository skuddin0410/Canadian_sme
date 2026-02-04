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
                        @if(Auth::user()->hasRole('Admin') )
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
<!-- Loader (hidden by default) -->
<div id="cloning-loader" class="loader-overlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <p>Cloning event, please wait...</p>
</div>
<style>
    /* Loader overlay */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999; /* Makes sure it stays on top */
        color: white; /* Make text color white */
        font-size: 18px; /* Adjust font size for better visibility */
    }

    /* Spinner styling */
    .loader-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.4em;
    }

    /* Optional: You can also add some styling to the text */
    .loader-overlay p {
        margin-top: 10px; /* Add some spacing between the spinner and text */
        font-weight: bold; /* Make the text bolder */
        font-size: 16px; /* Optional: Adjust the text size */
    }
</style>
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
    
    $(document).on("click", ".clone-btn", function() {
        var eventId = $(this).data('id');
        var eventTitle = $(this).closest('tr').find('td').eq(1).text(); // Assuming the title is in the second column

        // Show confirmation alert
        if (confirm("Are you sure you want to clone this event: " + eventTitle + "?")) {
            // Show the loader before making the AJAX request
            $('#cloning-loader').show();

            // Trigger the clone operation via AJAX
            $.ajax({
                url: "/admin/events/clone/" + eventId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert("Event cloned successfully!");
                        // Reload the event list or update the table dynamically
                        GetBlogList(); // You can use your existing function to reload the events table
                    } else {
                        alert("Error cloning the event.");
                    }
                },
                error: function(xhr) {
                    alert("An error occurred while cloning the event.");
                },
                complete: function() {
                // Hide the loader once the AJAX request is complete (success or failure)
                $('#cloning-loader').hide();
                }
            });
        }
    });

</script>   
@endsection
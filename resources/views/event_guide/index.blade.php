@extends('layouts.admin')

@section('title')
    Admin | Event Guide
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Event Guide</span></h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">Event Guide Lists</h5>
					<div class="dt-action-buttons text-end pt-3 pt-md-0">
						<div class="dt-buttons"> 
							<a href="{{ route('event-guides.create') }}" class="dt-button create-new btn btn-primary">
								<span><i class="bx bx-plus me-sm-1"></i> 
									<span class="d-none d-sm-inline-block">Add Event Guide</span>
								</span>
							</a> 
						</div>
					</div>
				</div>

                {{-- Filter --}}
                <div class="col-12 text-right">
                    <form action="#" class="" method="GET" id="event-guide-filter">        
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-3"></div>          
                            <div class="col-3 text-center">  
                                <div class="mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="search"
                                        id="search"
                                        placeholder="Search by title"/>
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
		              <div class="alert alert-success">{{ Session::get('success') }}</div>
		          @endif
		          @if(Session::has('error'))
		              <div class="alert alert-danger">{{ Session::get('error') }}</div>
		          @endif

                    <div class="col text-center">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>

					<div class="table-responsive" id="event-guide-table">
                    </div> 
		        </div>
			</div>
		</div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	// Load list
	 function GetEventGuideList() {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('event-guides.index') }}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
            dataType: "json",
            success: function (data) {
               $("#event-guide-table").html(data.html);
               $(".spinner-border").fadeOut(300);
            },
            error:function(data){
               $(".spinner-border").fadeOut(300);
            }
        });
    }

    $(document).ready(function() {
        GetEventGuideList();
    });

    // Pagination
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        $(".spinner-border").fadeIn(300);
        var url = $(this).attr("href");
        $.get(url, function(data) {
            $("#event-guide-table").html(data.html);
        }).done(function() {
            $(".spinner-border").fadeOut(300);
        });
        return false;
    });

    // Filter
    $(document).on("click", ".filter", function(e) {
       var search = $('#search').val();
       $(".spinner-border").fadeIn(300);
       $.ajax({
            url: "{{ route('event-guides.index') }}" + '?' + $("#event-guide-filter").serialize(),
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: { ajax_request: true },
            dataType: "json",
            success: function(data) {
                $("#event-guide-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function(data) {
                $(".spinner-border").fadeOut(300);
            }
        });
    });

    // Reset filter
    $('.reset-filter').on('click', function() {
        window.location.href = "{{ route('event-guides.index') }}";
    });    	
</script>	
@endsection

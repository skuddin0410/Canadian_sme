@extends('layouts.admin')

@section('title')
    Admin | Support
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Support</span></h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">Support Tickets</h5>
					<div class="dt-action-buttons text-end pt-3 pt-md-0">
						<!-- <div class="dt-buttons"> 
							<a href="" class="dt-button create-new btn btn-primary">
								<span><i class="bx bx-plus me-sm-1"></i> 
									<span class="d-none d-sm-inline-block">Add Support Ticket</span>
								</span>
							</a> 
						</div> -->
					</div>
				</div>

                
                <div class="col-12 text-right">
                    <form action="#" method="GET" id="support-filter">        
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
                                        placeholder="Search by name"/>
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

					<div class="table-responsive" id="support-table">
                    </div> 
		        </div>
			</div>
		</div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	
	function GetSupportList() {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('supports.index') }}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
            dataType: "json",
            success: function (data) {
               $("#support-table").html(data.html);
               $(".spinner-border").fadeOut(300);
            },
            error:function(data){
               $(".spinner-border").fadeOut(300);
            }
        });
    }

    $(document).ready(function() {
        GetSupportList();
    });

   
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        $(".spinner-border").fadeIn(300);
        var url = $(this).attr("href");
        $.get(url, function(data) {
            $("#support-table").html(data.html);
        }).done(function() {
            $(".spinner-border").fadeOut(300);
        });
        return false;
    });

   
    $(document).on("click", ".filter", function(e) {
       var search = $('#search').val();
       $(".spinner-border").fadeIn(300);
       $.ajax({
            url: "{{ route('supports.index') }}" + '?' + $("#support-filter").serialize(),
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data: { ajax_request: true },
            dataType: "json",
            success: function(data) {
                $("#support-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function(data) {
                $(".spinner-border").fadeOut(300);
            }
        });
    });

    
    $('.reset-filter').on('click', function() {
        window.location.href = "{{ route('supports.index') }}";
    });    	
</script>	
@endsection

@extends('layouts.admin')

@section('title')
    Admin | FAQ
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">FAQ</span></h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">Faq Lists</h5>
					<div class="dt-action-buttons text-end pt-3 pt-md-0">
						<div class="dt-buttons"> 
                            @if(Auth::user()->hasRole('Admin') )	
							<a href="{{route('faqs.create')}}" class="dt-button create-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button">
								<span><i class="bx bx-plus me-sm-1"></i> 
									<span class="d-none d-sm-inline-block">Add faq</span>
								</span>
							</a> 
                            @endif
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
					<div class="table-responsive" id="faq-table">

                    </div> 
		        </div>
			</div>
		</div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	const isNumeric = (string) => /^[+-]?\d+(\.\d+)?$/.test(string)
	$(document).on("focusout", ".editOrder", function(e) {
      var id = $(this).attr("data-id");
      var orderOld = $(this).attr("data-order");
      var order = $('#'+id).text();
        order = order.trim();
        if(isNumeric(order) && order >= 0 && (orderOld != order)){

		let link = '/faqs/'+id+'/order/'+ order
		$.ajax({
		    url: link,
		}).done(function(data) {
		    GetFaqList()
		});
      }      
  });
</script>
<script type="text/javascript">

 function GetFaqList() {
        $.ajax({
            url: "{{route('faqs.index')}}",
            type: 'get',
            headers: {
                'X-CSRF-Token': $('meta[name="_token"]').attr('content')
            },
            data:{ajax_request:true},
            dataType: "json",
            success: function (data) {
               $(document).find("#faq-table").html(data.html);
            },
            error:function(data){

            }
        });
    }

    $(document).ready(function() {
        GetFaqList();
    });

    jQuery(function($) {
            $(document).on("click", ".custom_pagination .pagination-link", function(e) {
                e.preventDefault();
                $(document).ajaxSend(function() {
                   // $(".card-loader").fadeIn(300);
                });
                $(this).parent().siblings('.page-item').removeAttr('aria-current');
                $(this).parent().siblings('.page-item').removeClass('active');
                var url = $(this).attr("href");
                finalURL = url;
                $(this).parent().addClass('active');
                $(this).parent().attr('aria-current', 'page');
                $.get(finalURL, function(data) {
                    $(document).find("#faq-table").html(data.html);
                }).done(function() {
                   // $(".card-loader").fadeOut(300);
            });
            return false;
        })

  });   	
</script>
@endsection
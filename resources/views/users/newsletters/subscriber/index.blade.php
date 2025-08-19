@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4">Newsletter Subscribers</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">

                <!-- Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subscribers List</h5>
                    
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        
                        <div class="dt-buttons"> 
                             <a href="{{ route('newsletters.index') }}" 
                             class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">
                             <i class="bx bx-arrow-back me-1"></i> Back
                             </a>
                            <a href="{{ route('newsletter-subscribers.create') }}" class="dt-button create-new btn btn-primary">
                                <span><i class="bx bx-plus me-sm-1"></i> 
                                    <span class="d-none d-sm-inline-block">Add Subscriber</span>
                                </span>
                            </a> 
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                {{-- <div class="col-12 text-right">
                    <form id="subscriber-filter" method="GET">        
                        <div class="row padding-none mt-2 mb-2">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search by email or name">
                            </div>
                            <div class="col-4 text-center">
                               <button type="button" class="btn btn-outline-primary btn-pill reset-filter">Reset</button>
                               <button type="button" class="btn btn-primary filter">Filter</button>
                            </div>  
                        </div>
                    </form>
                </div>     --}}
                <div class="col-12 d-flex justify-content-end">
                    <form id="subscriber-filter" method="GET" class="w-auto">        
                    <div class="row g-2 align-items-center mt-2 mb-2">
                    <div class="col-auto">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by email or name">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary btn-pill reset-filter">Reset</button>
                        <button type="button" class="btn btn-primary filter">Filter</button>
                    </div>  
                    </div>
                    </form>
                </div>


                <!-- Card Body -->
                <div class="card-body pt-0">
                    <div class="col text-center mb-2">
                        <div class="spinner-border spinner-border-sm" style="display:none;"></div>
                    </div>

                    <div class="table-responsive" id="subscriber-table">
                        <!-- Table will be loaded here via AJAX -->
                    </div> 
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
function fetchSubscribers(page = 1) {
    $(".spinner-border").fadeIn(300);
    $.ajax({
        url: "{{ route('newsletter-subscribers.index') }}",
        type: 'GET',
        data: {
            ajax_request: true,
            page: page,
            search: $('#search').val()
        },
        dataType: "json",
        success: function(data) {
            $("#subscriber-table").html(data.html);
            $(".spinner-border").fadeOut(300);
        },
        error: function() {
            $(".spinner-border").fadeOut(300);
        }
    });
}

$(document).ready(function() {
    fetchSubscribers();

    // Pagination click
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        let page = $(this).attr("href").split('page=')[1];
        fetchSubscribers(page);
    });

    // Filter
    $(document).on("click", ".filter", function() {
        fetchSubscribers(1);
    });

    // Reset
    $(document).on("click", ".reset-filter", function() {
        $('#search').val('');
        fetchSubscribers(1);
    });
});
</script>
@endsection

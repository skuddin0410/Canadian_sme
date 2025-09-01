@extends('layouts.admin')

@section('title')
    Admin | Sponsors
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Sponsors/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sponsors List</h5>
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                       <a href="{{ route('sponsors.export') }}" class="btn btn-outline-primary btn-pill">Export</a>
                        <a href="{{ route('sponsors.index') }}" class="btn btn-outline-primary btn-pill">Back</a>

                        <a href="{{ route('sponsors.create') }}" class="btn btn-primary dt-button create-new">
                                <i class="bx bx-plus me-sm-1"></i> Add New
                            </a>
                  
                    </div>
                </div>

      
<div class="col-12 text-end p-3">
    <form action="#" method="GET" id="users-search-filter-form">
        <div class="row justify-content-end g-2 align-items-center">
            <!-- Search -->
            <div class="col-auto">
                <input type="text" class="form-control form-control-md" name="search" id="search" 
                    value="{{ request('search') }}" placeholder="Search by Name, Email">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-md btn-primary" id="search-btn">Search</button>
            </div>

            <!-- Filter -->
            <div class="col-auto">
                <input type="text" class="form-control form-control-md" name="kyc" id="kyc" 
                    placeholder="Filter KYC Status">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-md btn-primary" id="filter-btn">Filter</button>
            </div>

            <!-- Reset Button -->
            <div class="col-auto">
                <button type="button" class="btn btn-md btn-primary reset-filter">Reset</button>
            </div>
        </div>
    </form>
</div>

                <!-- Card Body -->
                <div class="card-body pt-0">
                    @if(Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif

                    <!-- Loading Spinner -->
                    <div class="text-center mb-3">
                        <div class="spinner-border spinner-border-sm" style="display:none;"></div>
                    </div>

                    <!-- User Table -->
                    <div class="table-responsive" id="user-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // Function to load users via AJAX
    function loadUsers(params = {}) {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('sponsors.index') }}",
            type: 'GET',
            headers: { 'X-CSRF-Token': $('meta[name="_token"]').attr('content') },
            data: Object.assign({ ajax_request: true }, params),
            dataType: "json",
            success: function(data) {
                $("#user-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function() {
                $(".spinner-border").fadeOut(300);
            }
        });
    }

    // Initial load
    loadUsers();

    // Search button click
    $('#search-btn').click(function() {
        const searchVal = $('#search').val().trim();
        loadUsers({ search: searchVal });
    });

    // Filter button click
    $('#filter-btn').click(function() {
        const kycVal = $('#kyc').val().trim();
        loadUsers({ kyc: kycVal });
    });

    // Reset function
    $('.reset-filter').click(function() {
        $('#search').val('');
        $('#kyc').val('');
        loadUsers();
    });

    // Pagination click
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");
        if(!url) return;
        $(".spinner-border").fadeIn(300);
        $.get(url + '&ajax_request=true', function(data) {
            $("#user-table").html(data.html);
        }).done(function() {
            $(".spinner-border").fadeOut(300);
        });
    });
});
</script>
@endsection

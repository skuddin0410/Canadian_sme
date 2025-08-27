@extends('layouts.admin')

@section('title')
    Admin | UserGroup
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> UserGroup/</span>Lists</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">UserGroup List</h5>
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        @if(auth()->user()->hasRole('Admin'))
                            <a href="{{ route('usergroup.create') }}" class="btn btn-primary dt-button create-new">
                                <i class="bx bx-plus me-sm-1"></i> Add UserGroup
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Search Section -->
                <div class="col-12 text-end p-3">
                    <form action="#" method="GET" id="roles-search-filter-form">
                        <div class="row justify-content-end g-2 align-items-center">
                            <!-- Search -->
                            <div class="col-auto">
                                <input type="text" class="form-control form-control-md" name="search" id="search" 
                                    value="{{ request('search') }}" placeholder="Search by Role Name">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-md btn-primary" id="search-btn">Search</button>
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

                    <!-- Roles Table -->
                    <div class="table-responsive" id="roles-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // Function to load roles via AJAX
    function loadRoles(params = {}) {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('usergroup.index') }}",
            type: 'GET',
            headers: { 'X-CSRF-Token': $('meta[name="_token"]').attr('content') },
            data: Object.assign({ ajax_request: true }, params),
            dataType: "json",
            success: function(data) {
                $("#roles-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function() {
                $(".spinner-border").fadeOut(300);
            }
        });
    }

    // Initial load
    loadRoles();

    // Search button click
    $('#search-btn').click(function() {
        const searchVal = $('#search').val().trim();
        loadRoles({ search: searchVal });
    });

    // Reset function
    $('.reset-filter').click(function() {
        $('#search').val('');
        loadRoles();
    });

    // Pagination click
    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        var url = $(this).attr("href");
        if(!url) return;
        $(".spinner-border").fadeIn(300);
        $.get(url + '&ajax_request=true', function(data) {
            $("#roles-table").html(data.html);
        }).done(function() {
            $(".spinner-border").fadeOut(300);
        });
    });
});
</script>
@endsection

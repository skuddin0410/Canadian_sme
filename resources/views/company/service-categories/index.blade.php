@extends('layouts.admin')

@section('title')
    Admin | Service Category List
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4">Service Categories</h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Service Category List</h5>
                    <div class="dt-action-buttons text-end pt-3 pt-md-0">
                        <div class="dt-buttons">
                            <a href="{{ route('service-categories.create') }}" class="dt-button create-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                <span><i class="bx bx-plus me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">Add Service Category</span>
                                </span>
                            </a>
                        </div>
                    </div>
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

                    <div class="table-responsive" id="service-category-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function GetServiceCategoryList() {
        $(".spinner-border").fadeIn(300);
        $.ajax({
            url: "{{ route('service-categories.index') }}",
            type: 'get',
            data: { ajax_request: true },
            dataType: "json",
            success: function (data) {
                $("#service-category-table").html(data.html);
                $(".spinner-border").fadeOut(300);
            },
            error: function () {
                $(".spinner-border").fadeOut(300);
            }
        });
    }

    $(document).ready(function () {
        GetServiceCategoryList();
    });

    $(document).on("click", ".custom_pagination .pagination-link", function (e) {
        e.preventDefault();
        $(".spinner-border").fadeIn(300);

        var url = $(this).attr("href");
        $.get(url, { ajax_request: true }, function (data) {
            $("#service-category-table").html(data.html);
            $(".spinner-border").fadeOut(300);
        });

        return false;
    });
</script>
@endsection

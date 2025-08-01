@extends('layouts.admin')
{{-- @section('title')
    Exhibitor Admin | Company Details
@endsection --}}

@section('content')

 <div class="container">
        <h2>Company Info</h2>
        <a href="{{ route('company.create') }}" class="btn btn-primary mb-3">Add Company</a>
        <div id="company-list">
            Loading...
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(function() {
    fetchCompanies();

    function fetchCompanies(page = 1) {
        $.ajax({
            url: "{{ route('company.index') }}?ajax_request=true&page=" + page,
            method: "GET",
            success: function(res) {
                $('#company-list').html(res.html);
            }
        });
    }
});
</script>
{{-- <script>
    fetch("{{ route('company.index') }}?ajax_request=true")
        .then(res => res.text())
        .then(html => {
            document.getElementById('company-list').innerHTML = html;
        });
</script> --}}
@endpush
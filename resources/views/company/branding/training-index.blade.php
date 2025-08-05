@extends('layouts.admin')

@section('content')
<div class="container-xxl container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><span class="text-muted fw-light">Marketing /</span> Collateral Uploads</h4>
    <a href="{{ route('trainings.create') }}" class="btn btn-primary">+ Add Material</a>
  </div>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <div class="card">
    <div class="card-header">Uploaded Materials</div>
    <div class="card-body" id="trainingTable">
      @include('company.branding.partials.training-table')
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    $(document).on('click', '.pagination a', function (e) {
      e.preventDefault();
      let url = $(this).attr('href');
      fetchTrainings(url);
    });

    function fetchTrainings(url) {
      $.ajax({
        url: url,
        type: "GET",
        success: function (data) {
          $('#trainingTable').html(data);
        },
        error: function () {
          alert('Failed to load training materials.');
        }
      });
    }
  });
</script>
@endpush

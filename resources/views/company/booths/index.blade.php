@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="mb-4">Booth Listings</h4>
  <div class="mb-3">
   <a href="{{ route('booths.create') }}" class="btn btn-primary">+ Add Booth</a>
  </div>

  <div id="booth-table-container">
    @include('company.booths.partials.booth-table')
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    let url = $(this).attr('href');
    fetchBoothPage(url);
  });

  function fetchBoothPage(url) {
    $.ajax({
      url: url,
      type: 'GET',
      success: function(response) {
        $('#booth-table-container').html(response);
      },
      error: function() {
        alert('Failed to fetch data');
      }
    });
  }
</script>
@endpush

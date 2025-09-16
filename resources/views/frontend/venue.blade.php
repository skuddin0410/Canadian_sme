@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')
<div class="container py-4 py-lg-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-sm-4">
          <div class="d-flex align-items-center mb-4">
            <div class="text-left mb-2" style="width:100%">

              <div class="venue-location my-4">
                <h4>Venue Location</h4>

                @if(!empty($location))
                  <p>{{ $location }}</p>

                  <!-- Use the mapUrl returned by the controller in an iframe -->
                @if(!empty($mapUrl))
  <div class="ratio ratio-16x9" style="height:400px;">
    <iframe
      src="{{ $mapUrl }}"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
@else
  <p class="text-muted">Map not available.</p>
@endif


                @else
                  <p>Location not available.</p>
                @endif
              </div>

            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
@endsection

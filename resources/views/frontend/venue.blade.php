@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-4">
            <div class="text-left mb-2" style="width:100%">

              <div class="venue-location my-4">
                <h4>Venue Location</h4>
                @if(!empty($location))
                  <p>{{ $location }}</p>

                 
                  <iframe 
                      src="{{ $mapUrl }}" 
                      width="100%" 
                      height="400" 
                      style="border:0;" 
                      allowfullscreen="" 
                      loading="lazy" 
                      referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
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

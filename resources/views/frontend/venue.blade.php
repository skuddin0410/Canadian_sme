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

                  <!-- Map container -->
                  <div id="map" style="width:100%; height:400px;"></div>
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

@if(!empty($location))
  <!-- Load Google Maps API -->
  <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleApiKey }}"></script>
  <script>
    function initMap() {
        let geocoder = new google.maps.Geocoder();
        let address = @json($location);

        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status === 'OK') {
                // Create the map
                let map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: results[0].geometry.location
                });

                // Drop the pin
                let marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });

                // Force center on marker after map loads
                google.maps.event.addListenerOnce(map, 'idle', function() {
                    map.setCenter(marker.getPosition());
                });
            } else {
                document.getElementById('map').innerHTML =
                  "<p style='color:red'>Could not load map: " + status + "</p>";
            }
        });
    }

    // Load map after page is ready
    window.onload = initMap;
  </script>
@endif
@endsection

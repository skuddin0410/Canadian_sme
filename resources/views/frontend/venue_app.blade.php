<head>
    <title>Map with Zones</title>
    <style>
        #map {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<div class="container py-4 py-lg-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-sm-4">
          <div class="d-flex align-items-center mb-4">
            <div class="text-left mb-2" style="width:100%">

              <div class="venue-location my-4">
                @if(!empty($location))
                  <p class="mb-2" style="font-size:34px">{{ $location }}</p>

                  <!-- Use the mapUrl returned by the controller in an iframe -->
                @if(!empty($mapUrl))
                <div class="ratio ratio-16x9">
                 <!--  <iframe
                    src="{{ $mapUrl }}"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe> -->

                      <div id="map"></div>
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

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    <script>
        function initMap() {
            // Default map center (fallback)
            const mapCenter = { lat: 43.6445, lng: -79.3951 }; // Toronto

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: mapCenter,
            });

            // Geocode the address and place a marker
            @if($location)
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: "{{ $location }}" }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        map.setCenter(results[0].geometry.location);
                        new google.maps.Marker({
                            position: results[0].geometry.location,
                            map: map,
                            title: "{{ $location }}"
                        });
                    } else {
                        console.error("Geocode was not successful for the following reason: " + status);
                    }
                });
            @endif

            // Draw zones
            const zones = @json($zones);
            zones.forEach(zone => {
                const polygon = new google.maps.Polygon({
                    paths: zone.paths,
                    strokeColor: zone.color,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: zone.color,
                    fillOpacity: 0.35,
                });
                polygon.setMap(map);

                // Info window
                const infoWindow = new google.maps.InfoWindow({ content: zone.name });
                polygon.addListener("click", (e) => {
                    infoWindow.setPosition(e.latLng);
                    infoWindow.open(map);
                });
            });
        }

        window.onload = initMap;
    </script>
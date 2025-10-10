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
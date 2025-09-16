<!-- Blade view -->
<section class="maps">
    <div class="map-container mt-3" style="width:100%; height:400px;">
        @if($mapUrl)
            <iframe
                src="{{ $mapUrl }}"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        @else
            <p class="text-muted">Map not available.</p>
        @endif
    </div>
</section>

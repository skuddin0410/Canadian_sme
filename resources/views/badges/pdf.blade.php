<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Badges</title>
</head>
<body style="margin:0; padding:0; background:white;">
  <div style="width:100%; max-width:1300px; margin:0 auto; padding:20px; box-sizing:border-box;">
      <a 
      href="{{ route('attendee-users.index') }}" 
      style="margin-bottom:20px; padding:10px 20px; background:#555; color:#fff; border:none; cursor:pointer; text-decoration:none; display:inline-block; border-radius:4px;"
    >
       Back
    </a>
    <button
      onclick="this.style.display='none'; window.print(); setTimeout(()=>{ this.style.display='inline-block'; }, 0)"
      style="margin-bottom:20px; padding:10px 20px; background:#4CAF50; color:#fff; border:none; cursor:pointer; display:inline-block;"
    >
      Print Badges
    </button>

    <!-- Grid: 2 per row, with gaps; works for screen & print -->
    <div
      style="
        display:grid;
        grid-template-columns: repeat(2, 1fr);
        gap:8mm;
        box-sizing:border-box;
      "
    >
      @foreach($badges as $badge)
        <!-- Card wrapper: spacing + avoid splitting across pages -->
          @php
             $width_mm  = isset($badge['width'])  ? $badge['width'] * 10  : 85.6;
             $height_mm = isset($badge['height']) ? $badge['height'] * 10 : 54;
          @endphp
        <div
          style="
            box-sizing:border-box;
            width:auto;
            margin:5mm;
            break-inside:avoid;
            page-break-inside:avoid;
            -webkit-region-break-inside:avoid;
            -webkit-column-break-inside:avoid;
          "
        >
          <!-- Badge: fixed CR80 size, no split -->
          <div
            style="
              width:{{$width_mm}}mm;
              height:{{$height_mm}}mm;
              border:2px solid #333;
              border-radius:10px;
              padding:15px;
              box-sizing:border-box;
              background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
              display:flex;
              justify-content:space-between;
              align-items:center;
              page-break-inside:avoid;
              break-inside:avoid;
            "
          >
            <!-- Left section -->
            <div
              style="
                display:flex;
                flex-direction:column;
                width:60%;
                text-align:left;
                box-sizing:border-box;
              "
            > 
             @if (isset($badge['logo']) && !empty($badge['logo']))
              <img
                src="{{ asset('sme-logo.png') }}"
                style="max-height:80px; max-width:80px; object-fit:contain; border-radius:8px; margin-bottom:3px; display:block;"
              >
              @endif

              @if (isset($badge['name']) && !empty($badge['name']))
              <p style="font-weight:bold; font-size:1rem; margin:0;">{{$badge['name']}}</p>
              @endif
              
              @if (isset($badge['company_name']) && !empty($badge['company_name']))
              <p style="font-size:0.9rem; color:#555; margin:0;">{{$badge['company_name']}}</p>
              @endif

               @if (isset($badge['designation']) && !empty($badge['designation']))
              <p style="font-size:0.8rem; color:#888; margin:0; font-style:italic;">{{$badge['designation']}}</p>
              @endif

            </div>

            <!-- QR section -->
            <div
              style="
                width:35%;
                display:flex;
                justify-content:center;
                align-items:center;
                box-sizing:border-box;
              "
            > 
             @if (isset($badge['qr_code']) && !empty($badge['qr_code']))
              <img
                src="{{ $badge['qr_code'] }}"
                style="width:100px; height:100px; object-fit:contain; display:block;"
              >
              @endif

            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</body>
</html>



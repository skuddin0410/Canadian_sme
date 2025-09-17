<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Badges</title>
</head>
<body style="margin:0; padding:0; background:white;">
  <div style="width:100%; max-width:1300px; margin:0 auto; padding:20px; box-sizing:border-box;">
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
              width:85.6mm;
              height:54mm;
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
              <img
                src="{{asset('sme-logo.png')}}"
                style="max-height:80px; max-width:80px; object-fit:contain; border-radius:8px; margin-bottom:3px; display:block;"
              >
              <p style="font-weight:bold; font-size:1rem; margin:0;">Name</p>
              <p style="font-size:0.9rem; color:#555; margin:0;">company_name</p>
              <p style="font-size:0.8rem; color:#888; margin:0; font-style:italic;">Designation</p>
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
              <img
                src="https://api.qrserver.com/v1/create-qr-code/?data=Sample-QR-Code"
                style="width:100px; height:100px; object-fit:contain; display:block;"
              >
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</body>
</html>



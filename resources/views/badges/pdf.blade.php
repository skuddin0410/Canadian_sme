<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Badges</title>
  <style>
    @page {
      size: 3in 2.2in;
      margin: 0;
    }

    body {
      margin: 0;
      padding: 0;
      background: white;
    }

    .container {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-sizing: border-box;
    }

    .badge-wrapper {
      page-break-inside: avoid;
      page-break-after: always;
      width: 3in;
      height: 2.2in;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }

    .badge {
      width: 2.95in;
      height: 2in;
      border-radius: 8px;
      padding: 0.25in; /* Slightly reduced padding */
      box-sizing: border-box;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1rem; /* Reduced font size */
    }

    .left {
      display: flex;
      flex-direction: column;
      width: 55%;
      text-align: left;
    }

    .left img {
      max-height: 100px; /* Slightly reduced image size */
      max-width: 100px;
      object-fit: contain;
      border-radius: 4px;
      margin-bottom: 4px; /* Reduced margin */
    }

    .left p {
      margin: 0;
      line-height: 1.4; /* Slightly reduced line height */
      font-size: 22px; /* Reduced font size */
    }

    .qr {
      width: 40%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .qr img {
      width: 120px; /* Slightly reduced QR code size */
      height: 120px;
      object-fit: contain;
      display: block;
    }

    a, button {
      margin-bottom: 12px;
      padding: 8px 16px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 16px;
    }

    a {
      background: #555;
      color: #fff;
    }

    button {
      background: #4CAF50;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      @page {
        size: 3in 2.2in;
        margin: 0;
      }

      html, body {
        width: 3in;
        height: 2.2in;
        margin: 0;
        padding: 0;
        background: none;
      }

      a, button {
        display: none !important;
      }

      .container {
        padding: 0 !important;
      }

      .badge-wrapper {
        page-break-inside: avoid;
        page-break-after: always;
        width: 3in;
        height: 2.2in;
        margin: 0 auto;
      }

      .badge {
        width: 2.95in;
        height: 2in;
        padding: 0.25in;
        margin: 0 auto;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="{{ route('attendee-users.index') }}">Back</a>
    <button onclick="this.style.display='none'; window.print(); setTimeout(()=>{ this.style.display='inline-block'; }, 0)">
      Print Badges
    </button>

    @foreach($badges as $badge)
      <div class="badge-wrapper">
        <div class="badge">
          <div class="left">
            @if (isset($badge['logo']) && !empty($badge['logo']))
              <img src="{{ asset('sme-logo.png') }}">
            @endif

            @if (!empty($badge['name']))
              <p style="font-weight:bold;">
                {{ strlen($badge['name']) > 16 ? substr($badge['name'], 0, 14) . '..' : $badge['name'] }}
              </p>
            @endif

            @if (!empty($badge['company_name']))
              <p style="color:#555;">
                {{ strlen($badge['company_name']) > 16 ? substr($badge['company_name'], 0, 12) . '..' : $badge['company_name'] }}
              </p>
            @endif

            @if (!empty($badge['designation']))
              <p style="color:#888; font-style:italic;">{{ $badge['designation'] }}</p>
            @endif
          </div>
          <div class="qr">
            @if (!empty($badge['qr_code']))
              <img src="{{ $badge['qr_code'] }}">
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
</body>
</html>

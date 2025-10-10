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
      width: 2.99in;
      height: 2in;
      border-radius: 8px;
      padding: 0.12in;
      box-sizing: border-box;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1rem;
    }

    .left {
      display: flex;
      flex-direction: column;
      width: 50%;
      text-align: left;
    }

    .left img {
      max-height: 100px;
      max-width: 100px;
      object-fit: contain;
      border-radius: 4px;
      margin-bottom: 4px;
    }

    .left p {
      margin: 0;
      line-height: 1.4;
      font-size: 22px; /* Name font size */
    }

    .designation {
      color: #888;
      font-style: italic;
      font-size: 17px !important; /* Added !important for the font size */
    }

    .qr {
      width: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .qr img {
      width: 1.4in; /* QR code size */
      height: 1.4in; 
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
        width: 2.99in;
        height: 2in;
        padding: 0.12in;
        margin: 0 auto;
      }

      .left p {
        font-size: 22px !important; /* Name font size for print */
      }

      .designation {
        font-size: 17px !important; /* Designation font size for print */
      }

      .qr img {
        width: 1.4in !important; /* QR code size for print */
        height: 1.4in !important;
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
        <div class="badge"  data-company-name="{{ $badge['company_name'] ?? '' }}" data-badge-user-id="{{ $badge['user_id'] ?? '' }}" data-badge-name="{{ $badge['name'] ?? '' }}">
          <div class="left">
            @if (isset($badge['logo']) && !empty($badge['logo']))
              <img src="{{ asset('sme-logo.png') }}">
            @endif

            @if (!empty($badge['name']))
              <p style="font-weight:bold;">
                {{ $badge['name'] }}
              </p>
            @endif

            @if (!empty($badge['company_name']))
              <p style="color:#555;font-size:17px !important">
                {{$badge['company_name'] }} 
              </p>
            @endif

            @if (!empty($badge['designation']))
              <p class="designation">{{ $badge['designation'] }}</p>
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

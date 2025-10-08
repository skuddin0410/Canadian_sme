<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Badges</title>
  <style>
  @page {
  size: 3in 2.2in;   /* slightly bigger sheet */
  margin: 0.05in;    /* safe space on all sides */
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
      align-items: center; /* center badges horizontally */
      justify-content: center;
      padding: 10px;
      box-sizing: border-box;
    }
 .badge-wrapper {
  page-break-inside: avoid;
  margin: 0 auto; /* center horizontally */
}
.badge {
  width: 2.70in;
  height: 1.7in;
  border-radius: 8px;
  padding: 10px;       /* inner padding for text/QR */
  box-sizing: border-box;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  margin: 0 auto;      /* ensure centered inside page */
}
    .left {
      display: flex;
      flex-direction: column;
      width: 60%;
      text-align: left;
    }
    .left img {
      max-height: 90px;
      max-width: 90px;
      object-fit: contain;
      border-radius: 4px;
      margin-bottom: 2px;
    }
    .left p {
      margin: 0;
      line-height: 1.2;
      font-size: 18px;
    }
    .qr {
      width: 40%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .qr img {
      width: 95px;
      height: 95px;
      object-fit: contain;
      display: block;
    }
    @media print {
      a, button { display: none !important; }
      body { background: none; }
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="{{ route('attendee-users.index') }}" 
       style="margin-bottom:10px; padding:6px 12px; background:#555; color:#fff; border-radius:4px; text-decoration:none;">
       Back
    </a>
    <button onclick="this.style.display='none'; window.print(); setTimeout(()=>{ this.style.display='inline-block'; }, 0)"
            style="margin-bottom:10px; padding:6px 12px; background:#4CAF50; color:#fff; border:none; border-radius:4px; cursor:pointer;">
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
              <p style="font-weight:bold;">{{ strlen($badge['name']) > 16 ? substr($badge['name'], 0, 14) . '..' : $badge['name'] }}</p>
            @endif
            @if (!empty($badge['company_name']))
              <p style="color:#555;">{{ strlen($badge['company_name']) > 16 ? substr($badge['company_name'], 0, 12) . '..' : $badge['company_name'] }}</p>
            @endif
            @if (!empty($badge['designation']))
              <p style="color:#888; font-style:italic;">{{$badge['designation']}}</p>
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    /* Reset for Apple Mail, Gmail App */
    img {
      border: 0;
      outline: none;
      text-decoration: none;
      max-width: 100%;
      height: auto;
      display: block;
    }
    table {
      border-collapse: collapse !important;
    }
    p {
      margin: 0;
      /* padding: 0; */
    }

    /* Mobile Styles */
    @media only screen and (max-width:600px) {
      .container {
        width: 100% !important;
        border-radius: 0 !important;
      }
      .content {
        padding: 20px !important;
        font-size: 14px !important;
      }
      .content h1 {
        font-size: 18px !important;
      }
      .btn a {
        font-size: 14px !important;
        padding: 10px 16px !important;
      }
      .footer-links,
      .footer-links tbody,
      .footer-links tr,
      .footer-link-cell {
        display: block !important;
        width: 100% !important;
      }
      .footer-link-cell {
        padding: 0 0 12px 0 !important;
      }
      .footer-link-cell:last-child {
        padding-bottom: 0 !important;
      }
      .store-badge,
      .website-button {
        margin: 0 auto !important;
      }
      .website-button {
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, sans-serif;">
  <!-- @php
    $platformBranding = !empty($platformBranding);
    $mailEvent = $platformBranding ? null : ($event ?? null);
    $mailSponsor = $platformBranding ? null : ($sponsor ?? null);

    // Site emails (Contact Us, etc.) must show Eventzen — not an event / company_name title
    if ($platformBranding || !$mailEvent) {
      $headerTitle = $headerBrandTitle
        ?? config('app.name')
        ?: 'Eventzen';
      $headerImage = $headerBrandImage
        ?? asset('images/footer-logo.png');
    } else {
      $headerImage = $mailEvent?->eventLogo?->file_path
        ?: $mailEvent?->photo?->file_path
        ?: asset('images/footer-logo.png');
      $headerTitle = $mailEvent?->title
        ?: (config('app.name') ?: 'Eventzen');
    }

    $partnerLogo = $mailEvent?->emailBrandingLogo?->file_path ?: $mailSponsor?->logo?->file_path ?: $mailEvent?->sponsorBanner?->file_path;
    $partnerName = $mailEvent?->email_branding_name ?: $mailSponsor?->name;
    $partnerLabel = $mailEvent?->email_branding_type === 'powered_by'
      ? 'Powered by'
      : ($mailEvent?->email_branding_type === 'sponsored_by'
        ? 'Sponsored by'
        : ($mailSponsor
          ? (str_contains(strtolower((string) $mailSponsor->type), 'power') ? 'Powered by' : 'Sponsored by')
          : null));
  @endphp -->

  @php
    $mailEvent = $event ?? null;
    // \Log::info('EVENT EMAIL BRANDING', [
    //     'event_id' => $mailEvent?->id,
    //     'branding_type' => $mailEvent?->email_branding_type,
    //     'branding_name' => $mailEvent?->email_branding_name,
    // ]);
    $mailSponsor = $sponsor ?? null;

    $headerImage = $mailEvent?->eventLogo?->file_path
        ?: $mailEvent?->photo?->file_path
        ?: asset('images/footer-logo.png');

    $headerTitle = $mailEvent?->title
        ?: (config('app.name') ?: 'Eventzen');

    $partnerLogo = $mailEvent?->emailBrandingLogo?->file_path
        ?: $mailSponsor?->logo?->file_path
        ?: $mailEvent?->sponsorBanner?->file_path;

    $partnerName = $mailEvent?->email_branding_name
        ?: $mailSponsor?->name;

    $partnerLabel = $mailEvent?->email_branding_type === 'powered_by'
        ? 'Powered by'
        : ($mailEvent?->email_branding_type === 'sponsored_by'
            ? 'Sponsored by'
            : ($mailEvent?->email_branding_type === 'presented_by'
                ? 'Presented by'
                : ($mailSponsor
                    ? (str_contains(
                        strtolower((string) $mailSponsor->type),
                        'power'
                    ) ? 'Powered by' : 'Sponsored by')
                    : null
                )
            )
        );
      
@endphp

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f6f8">
    <tr>
      <td align="center" style="padding:20px 10px;">
        
        <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden;">
          
          <!-- Header -->
          <!-- <tr>
            <td bgcolor="#004fb8" style="padding:24px 28px 22px 28px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td width="235" valign="middle" style="width:235px; padding-right:20px;">
                    <img src="{{ $headerImage }}" alt="{{ $headerTitle }}" width="215"
                      style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;width:215px !important;height:auto !important;max-width:100%;max-height:88px;">
                  </td>
                  <td valign="middle" style="color:white; padding-left:0;">
                    @if($mailEvent && $partnerLabel && ($partnerLogo || $partnerName))
                      @if($partnerLogo)
                        <img src="{{ $partnerLogo }}" alt="{{ $partnerName ?: $partnerLabel }}" width="100"
                          style="display:block;border:0;outline:none;text-decoration:none;width:100px !important;height:auto !important;max-width:100%;max-height:48px;margin:0 0 8px 0;">
                      @endif
                      <p style="font-size:14px; line-height:1.35; margin:0 0 2px 0; opacity:0.85;">{{ $partnerLabel }}</p>
                      @if($partnerName)
                        <p style="font-size:20px; line-height:1.25; margin:0; font-weight:700;">{{ $partnerName }}</p>
                      @endif
                    @else
                      <p style="font-size:23px; line-height:1.28; margin:0; font-weight:700;">{{ $headerTitle }}</p>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr> -->

          <!-- Header -->
          <tr>
              <td
                  bgcolor="#004fb8"
                  style="padding:24px 28px 22px 28px;"
              >
                  <table
                      role="presentation"
                      width="100%"
                      cellspacing="0"
                      cellpadding="0"
                      border="0"
                      style="width:100%; border-collapse:collapse;"
                  >
                      <tr>

                          <!-- LEFT LOGO -->
                          <td
                              width="215"
                              valign="middle"
                              style="
                                  width:215px;
                                  padding:0;
                                  vertical-align:middle;
                                  text-align:left;
                              "
                          >
                              <img
                                  src="{{ $headerImage }}"
                                  alt="{{ $headerTitle }}"
                                  width="215"
                                  style="
                                      display:block;
                                      width:215px;
                                      height:auto;
                                      max-width:215px;
                                      max-height:88px;
                                      margin:0;
                                      padding:0;
                                      border:0;
                                      outline:none;
                                  "
                              >
                          </td>


                          <!-- FLEXIBLE SPACE -->
                          <td
                              style="
                                  width:auto;
                                  padding:0;
                                  font-size:0;
                                  line-height:0;
                              "
                          >
                              &nbsp;
                          </td>


                          <!-- RIGHT BLOCK -->
                          <td
                              width="180"
                              valign="middle"
                              style="
                                  width:180px;
                                  padding:0;
                                  vertical-align:middle;
                                  text-align:right;
                              "
                          >
                              <table
                                  role="presentation"
                                  cellspacing="0"
                                  cellpadding="0"
                                  border="0"
                                  align="right"
                                  style="
                                      border-collapse:collapse;
                                      margin:0 0 0 auto;
                                  "
                              >
                                  <tr>
                                      <td
                                          style="
                                              padding:0;
                                              vertical-align:middle;
                                              text-align:right;
                                          "
                                      >

                                          @if($mailEvent && $partnerLabel && ($partnerLogo || $partnerName))

                                              <!-- BIG SPONSOR LOGO -->
                                              @if($partnerLogo)
                                                  <img
                                                      src="{{ $partnerLogo }}"
                                                      alt="{{ $partnerName ?: $partnerLabel }}"
                                                      width="170"
                                                      style="
                                                          display:block;
                                                          width:170px;
                                                          max-width:170px;
                                                          height:auto;
                                                          max-height:70px;
                                                          margin:0 0 5px auto;
                                                          padding:0;
                                                          border:0;
                                                          outline:none;
                                                      "
                                                  >
                                              @endif

                                              <!-- LABEL -->
                                              <p
                                                  style="
                                                      margin:0;
                                                      padding:0;
                                                      font-size:10px;
                                                      line-height:14px;
                                                      color:#ffffff;
                                                      text-align:right;
                                                  "
                                              >
                                                  {{ $partnerLabel }}
                                              </p>

                                              <!-- SMALL NAME -->
                                              @if($partnerName)
                                                  <p
                                                      style="
                                                          margin:0;
                                                          padding:0;
                                                          font-size:14px;
                                                          line-height:18px;
                                                          font-weight:600;
                                                          color:#ffffff;
                                                          text-align:right;
                                                      "
                                                  >
                                                      {{ $partnerName }}
                                                  </p>
                                              @endif

                                          @else

                                              <!-- HEADER TITLE -->
                                              <p
                                                  style="
                                                      margin:0;
                                                      padding:0;
                                                      font-size:23px;
                                                      line-height:29px;
                                                      font-weight:700;
                                                      color:#ffffff;
                                                      text-align:right;
                                                  "
                                              >
                                                  {{ $headerTitle }}
                                              </p>

                                          @endif

                                      </td>
                                  </tr>
                              </table>
                          </td>

                      </tr>
                  </table>
              </td>
          </tr>

          <!-- Content -->
          <tr>
            <td class="content" style="padding:30px 25px; font-size:15px; line-height:1.6; color:#444;">

  

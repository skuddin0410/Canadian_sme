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
  @php
    $headerImage = $event?->eventLogo?->file_path ?: $event?->photo?->file_path ?: asset('images/footer-logo.png');
    $headerTitle = $event?->title ?: (getKeyValue('company_name')->value ?? config('app.name'));
  @endphp

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f6f8">
    <tr>
      <td align="center" style="padding:20px 10px;">
        
        <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden;">
          
          <!-- Header -->
          <tr>
            <td bgcolor="#0e3e97ff" style="padding:20px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td width="190" valign="middle">
                    <img src="{{ $headerImage }}" alt="{{ $headerTitle }}" width="180"
                      style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;width:180px !important;height:auto !important;max-width:100%;">
                  </td>
                  <td valign="middle" style="color:white; padding-left:15px;">
                    <p style="font-size:22px; line-height:1.3; margin:0; font-weight:700;">{{ $headerTitle }}</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td class="content" style="padding:30px 25px; font-size:15px; line-height:1.6; color:#444;">

  

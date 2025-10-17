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
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, sans-serif;">

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f6f8">
    <tr>
      <td align="center" style="padding:20px 10px;">
        
        <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden;">
          
          <!-- Header -->
          <tr>
           <td align="left" bgcolor="#002364" style="padding:20px; display: flex; align-items: center; color: white;">
    <img src="{{asset('images/footer-logo.png')}}" alt="Company Logo" width="180" height="61"
  style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;width:180px !important;height:61px !important;max-width:100%;margin-right:15px;margin-top:23px;">
    <div style="color:white;text-decoration: none;">
      <p style="font-size: 15px;">{{ getKeyValue('company_name')->value }}</p>
      {{-- <p style="font-size: 15px;">{{ getKeyValue('company_address')->value }}</p> --}}
    </div>
  </td>
</tr>

          <!-- Content -->
          <tr>
            <td class="content" style="padding:30px 25px; font-size:15px; line-height:1.6; color:#444;">

  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $appName }} verification code</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f6f8">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden;">
          <tr>
            <td bgcolor="#004fb8" style="padding:20px 24px; color:#ffffff;">
              <p style="margin:0; font-size:20px; font-weight:700; letter-spacing:0.02em;">{{ $appName }}</p>
              @if(!empty($eventTitle))
                <p style="margin:6px 0 0 0; font-size:13px; opacity:0.9;">{{ $eventTitle }}</p>
              @endif
            </td>
          </tr>

          <tr>
            <td style="padding:28px 24px; color:#444;">
              <p style="margin:0 0 8px 0; font-size:13px; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; color:#6b7a99;">Login verification</p>
              <h1 style="margin:0 0 16px 0; font-size:22px; line-height:1.3; color:#0f1530;">Your verification code</h1>

              <p style="margin:0 0 20px 0; font-size:15px; line-height:1.7; color:#4a5068;">
                Use this one-time code to sign in
                @if(!empty($eventTitle))
                  to <strong style="color:#0f1530;">{{ $eventTitle }}</strong>
                @endif.
                It expires in <strong>{{ $expiresMinutes }} minutes</strong>.
              </p>

              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 22px 0;">
                <tr>
                  <td align="center" style="background:#f5f7ff; border:1px solid #dde3f5; border-radius:10px; padding:20px 12px;">
                    <p style="margin:0 0 8px 0; font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#8b91b5;">Code</p>
                    <p style="margin:0; font-size:34px; font-weight:700; letter-spacing:0.25em; color:#004fb8;">{{ $otp }}</p>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 18px 0; font-size:14px; line-height:1.7; color:#6b7a99;">
                If you did not request this code, you can ignore this email. {{ $appName }} will never ask you to share this code.
              </p>

              <p style="margin:0; font-size:14px; line-height:1.7; color:#4a5068;">
                Thanks,<br>
                <strong style="color:#0f1530;">{{ $appName }}</strong><br>
                <a href="{{ $brandUrl }}" style="color:#004fb8; text-decoration:none;">{{ str_replace(['https://','http://'], '', $brandUrl) }}</a>
              </p>
            </td>
          </tr>

          <tr>
            <td align="center" bgcolor="#004fb8" style="padding:14px; color:#ffffff; font-size:12px;">
              © {{ date('Y') }} {{ $appName }}. All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

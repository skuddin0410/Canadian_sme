@include('emails.layout.header')

<!--[if mso]><style>td, th { font-family: Arial, sans-serif !important; }</style><![endif]-->

<!-- Greeting -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td>
            <p style="margin:0 0 6px 0; font-size:13px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:#6b7a99;">Support Request Received</p>
            <h2 style="margin:0; font-size:22px; font-weight:700; color:#0f1530; line-height:1.3;">Hi {{ $support->name }}, we've got you! 🙌</h2>
        </td>
    </tr>
</table>

<!-- Intro -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td style="font-size:15px; color:#4a5068; line-height:1.7;">
            Thank you for reaching out to us. We've successfully received your support request and our team is already on it. You'll hear back from us as soon as possible.
        </td>
    </tr>
</table>

<!-- Ticket Summary Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#f5f7ff; border-radius:12px; border:1px solid #dde3f5; margin-bottom:24px;">
    <tr>
        <td style="padding:18px 20px 10px 20px;">
            <p style="margin:0 0 3px 0; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#8b91b5;">Your Query</p>
            <p style="margin:0 0 16px 0; font-size:16px; font-weight:600; color:#0f1530;">{{ $support->subject }}</p>
        </td>
    </tr>
    <tr>
        <td style="padding:0 20px 18px 20px;">
            <!-- Status Badge -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background:#fff8e1; border:1px solid #f5cc5a; border-radius:20px; padding:7px 18px;">
                        <span style="font-size:13px; font-weight:700; color:#9a6c00;">⏳&nbsp;&nbsp;Pending Review</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- What Happens Next -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td style="padding-bottom:12px;">
            <p style="margin:0; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#8b91b5;">What happens next?</p>
        </td>
    </tr>

    <!-- Step 1 -->
    <tr>
        <td style="padding-bottom:10px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#ffffff; border:1px solid #e8eaf5; border-radius:10px;">
                <tr>
                    <td width="48" style="padding:14px 0 14px 16px; vertical-align:top;">
                        <div style="width:32px; height:32px; background:#e8f0ff; border-radius:50%; text-align:center; line-height:32px; font-size:15px;">📋</div>
                    </td>
                    <td style="padding:14px 16px 14px 10px; vertical-align:top;">
                        <p style="margin:0 0 2px 0; font-size:13px; font-weight:700; color:#0f1530;">Request Logged</p>
                        <p style="margin:0; font-size:13px; color:#6b7a99;">Your request has been recorded in our system.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Step 2 -->
    <tr>
        <td style="padding-bottom:10px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#ffffff; border:1px solid #e8eaf5; border-radius:10px;">
                <tr>
                    <td width="48" style="padding:14px 0 14px 16px; vertical-align:top;">
                        <div style="width:32px; height:32px; background:#e8f0ff; border-radius:50%; text-align:center; line-height:32px; font-size:15px;">🔍</div>
                    </td>
                    <td style="padding:14px 16px 14px 10px; vertical-align:top;">
                        <p style="margin:0 0 2px 0; font-size:13px; font-weight:700; color:#0f1530;">Team Review</p>
                        <p style="margin:0; font-size:13px; color:#6b7a99;">Our support team will review your query carefully.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Step 3 -->
    <tr>
        <td>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                style="background:#ffffff; border:1px solid #e8eaf5; border-radius:10px;">
                <tr>
                    <td width="48" style="padding:14px 0 14px 16px; vertical-align:top;">
                        <div style="width:32px; height:32px; background:#e8f0ff; border-radius:50%; text-align:center; line-height:32px; font-size:15px;">💬</div>
                    </td>
                    <td style="padding:14px 16px 14px 10px; vertical-align:top;">
                        <p style="margin:0 0 2px 0; font-size:13px; font-weight:700; color:#0f1530;">We'll Be in Touch</p>
                        <p style="margin:0; font-size:13px; color:#6b7a99;">A team member will reach out to you directly via email.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Assurance Note -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#ffffff; border-left:4px solid #4f6ef7; border-radius:0 8px 8px 0; margin-bottom:28px;">
    <tr>
        <td style="padding:14px 18px; font-size:14px; color:#4a5068; line-height:1.7;">
            In the meantime, if you have anything to add, simply reply to this email and we'll include it with your request.
        </td>
    </tr>
</table>

<!-- Sign off -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td style="font-size:14px; color:#4a5068; line-height:1.8;">
            Warm regards,<br>
            <strong style="color:#0f1530; font-weight:700;">Eventzen.io</strong>
        </td>
    </tr>
</table>

@include('emails.layout.footer')
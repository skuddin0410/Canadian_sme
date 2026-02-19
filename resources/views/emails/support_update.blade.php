@include('emails.layout.header')

<!--[if mso]><style>td, th { font-family: Arial, sans-serif !important; }</style><![endif]-->

<!-- Greeting -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td>
            <p style="margin:0 0 6px 0; font-size:13px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:#6b7a99;">Support Update</p>
            <h2 style="margin:0; font-size:22px; font-weight:700; color:#0f1530; line-height:1.3;">Hello, {{ $support->name }} 👋</h2>
        </td>
    </tr>
</table>

<!-- Intro -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td style="font-size:15px; color:#4a5068; line-height:1.7;">
            We wanted to let you know that your support request has been updated. Here's a quick summary of where things stand right now.
        </td>
    </tr>
</table>

<!-- Ticket Info Card -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#f5f7ff; border-radius:12px; border:1px solid #dde3f5; margin-bottom:24px; overflow:hidden;">

    <!-- Subject Row -->
    <tr>
        <td style="padding:16px 20px; border-bottom:1px solid #dde3f5;">
            <p style="margin:0 0 3px 0; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#8b91b5;">Subject</p>
            <p style="margin:0; font-size:15px; font-weight:600; color:#0f1530;">{{ $support->subject }}</p>
        </td>
    </tr>

    <!-- Status Row -->
    <tr>
        <td style="padding:16px 20px;">
            <p style="margin:0 0 10px 0; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#8b91b5;">Current Status</p>

            @if($support->status == 'pending')
            <!-- Pending -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background:#fff8e1; border:1px solid #f5cc5a; border-radius:20px; padding:7px 18px;">
                        <span style="font-size:14px; font-weight:700; color:#9a6c00;">⏳&nbsp;&nbsp;Pending</span>
                    </td>
                </tr>
            </table>
            <p style="margin:10px 0 0 0; font-size:13px; color:#6b7a99;">Your request is in our queue and will be picked up shortly.</p>

            @elseif($support->status == 'inprogress')
            <!-- In Progress -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background:#e8f0ff; border:1px solid #92aef7; border-radius:20px; padding:7px 18px;">
                        <span style="font-size:14px; font-weight:700; color:#2b4db5;">🔄&nbsp;&nbsp;In Progress</span>
                    </td>
                </tr>
            </table>
            <p style="margin:10px 0 0 0; font-size:13px; color:#6b7a99;">Our team is actively working on your request.</p>

            @else
            <!-- Completed -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background:#e6faf2; border:1px solid #62d4a0; border-radius:20px; padding:7px 18px;">
                        <span style="font-size:14px; font-weight:700; color:#0f6e42;">✅&nbsp;&nbsp;Completed</span>
                    </td>
                </tr>
            </table>
            <p style="margin:10px 0 0 0; font-size:13px; color:#6b7a99;">Your request has been resolved. We hope everything is sorted!</p>
            @endif
        </td>
    </tr>
</table>

<!-- Assurance Note -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#ffffff; border-left:4px solid #4f6ef7; border-radius:0 8px 8px 0; margin-bottom:28px;">
    <tr>
        <td style="padding:14px 18px; font-size:14px; color:#4a5068; line-height:1.7;">
            We're committed to assisting you every step of the way. If you have any additional questions or concerns, feel free to reply to this email.
        </td>
    </tr>
</table>

<!-- Sign off -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td style="font-size:14px; color:#4a5068; line-height:1.8;">
            Warm regards,<br>
            <strong style="color:#0f1530; font-weight:700;">Eventzen Support Team</strong>
        </td>
    </tr>
</table>

@include('emails.layout.footer')
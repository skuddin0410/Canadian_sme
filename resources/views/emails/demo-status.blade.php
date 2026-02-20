@include('emails.layout.header')
<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi,{{ $demo->name }}</h1>
<p>Your demo booking has been updated.</p>

<p><strong>Status:</strong> {{ ucfirst($demo->status) }}</p>

@if($demo->note)
<p><strong>Reason:</strong></p>
<p>{{ $demo->note }}</p>
@endif

<p>Thank you.</p>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td style="font-size:14px; color:#4a5068; line-height:1.8;">
            Warm regards,<br>
            <strong style="color:#0f1530; font-weight:700;">Eventzen.io</strong>
        </td>
    </tr>
</table>
@include('emails.layout.footer')
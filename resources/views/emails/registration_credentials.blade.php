@include('emails.layout.header')

<h3 style="margin-bottom:15px; color: #002364;">
    Registration Successful, {{ $recipientName }}!
</h3>

@if($isTeamRegistration)
<p style="margin:0 0 15px 0;">
    Your team registration for <strong>{{ $event?->title ?? 'the event' }}</strong> has been completed successfully.
    Below are the registered attendee details.
</p>
@else
<p style="margin:0 0 15px 0;">
    Your registration for <strong>{{ $event?->title ?? 'the event' }}</strong> has been completed successfully.
    Your attendee account has been created successfully.
</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
    <thead>
        <tr>
            <th align="left" style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;">Name</th>
            <th align="left" style="border:1px solid #dbe3f0; padding:10px; background:#f7f9fc;">Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendees as $attendee)
        <tr>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $attendee['name'] ?? 'N/A' }}</td>
            <td style="border:1px solid #dbe3f0; padding:10px;">{{ $attendee['email'] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="margin:0 0 20px 0;">
    Login here to continue:
    <a href="{{ $loginUrl }}" style="color:#002364; font-weight:600;">{{ $loginUrl }}</a>
</p>

<p style="margin:0; font-size:13px; color:#777;">
    Use the OTP-based login flow on the event login page to access the account.
</p>

@include('emails.layout.footer')

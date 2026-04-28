@include('emails.layout.header')

<h3 style="margin-bottom:15px; color: #002364;">
    Congratulations, {{ $user->name }} {{ $user->lastname }}!
</h3>

<p style="margin:0 0 15px 0;">
    We are pleased to inform you that your admin account for <strong>{{ getKeyValue('company_name')->value ?? 'our platform' }}</strong> has been successfully created.
</p>

<p style="margin:0 0 20px 0;">
    You can now access your dashboard and start managing your events. Please use your registered email ({{ $user->email }}) to log in.
</p>

<div style="text-align: center; margin-bottom: 30px;">
    <a href="https://eventzen.io/admin/login" style="background-color: #002364; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
        Login to Dashboard
    </a>
</div>

<p style="margin:0; font-size: 13px; color: #777;">
    If you have any questions or need assistance getting started, please don't hesitate to contact our support team.
</p>

@include('emails.layout.footer')

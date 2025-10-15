@include('emails.layout.header')


<p>Hello {{ $user->name }},</p>

<p>Please find attached the CSV file containing all your connections.</p>

<p>Thank you</p>

@include('emails.layout.footer')
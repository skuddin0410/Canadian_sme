@include('emails.layout.header')
<h2 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi {{ $support->name }}</h2>

<p>Thank you for contacting us.</p>

<p>We have received your query regarding:</p>

<b>{{ $support->subject }}</b>

<p>Our team will contact you soon.</p>

<br>
Thanks,<br>
Eventzen Support Team
@include('emails.layout.footer')

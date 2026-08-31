@include('emails.layout.header')

<h3 style="margin-bottom:15px; color:#002364;">New inquiry received</h3>

<p style="margin:0 0 12px 0;"><strong>Type:</strong> {{ $subjectLine }}</p>

@if(!empty($payload['event']))
<p style="margin:0 0 12px 0;"><strong>Event:</strong> {{ $payload['event'] }}</p>
@endif

<p style="margin:0 0 12px 0;"><strong>Name:</strong> {{ $payload['name'] ?? '-' }}</p>
<p style="margin:0 0 12px 0;"><strong>Email:</strong> {{ $payload['email'] ?? '-' }}</p>
<p style="margin:0 0 12px 0;"><strong>Phone:</strong> {{ $payload['phone'] ?? '-' }}</p>

@if(!empty($payload['location']))
<p style="margin:0 0 12px 0;"><strong>Location:</strong> {{ $payload['location'] }}</p>
@endif

<p style="margin:0 0 12px 0;"><strong>Message:</strong></p>
<p style="margin:0 0 20px 0;">{{ $payload['message'] ?? '-' }}</p>

@include('emails.layout.footer')

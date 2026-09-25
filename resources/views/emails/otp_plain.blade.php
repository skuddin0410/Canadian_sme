{{ $appName }} verification code

Code: {{ $otp }}
@if(!empty($eventTitle))
Event: {{ $eventTitle }}
@endif

This code expires in {{ $expiresMinutes }} minutes.

If you did not request this code, ignore this email. {{ $appName }} will never ask you to share this code.

{{ $brandUrl }}

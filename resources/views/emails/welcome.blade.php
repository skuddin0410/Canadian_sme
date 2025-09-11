
@include('emails.layout.header')
<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi,</h1>
<p style="margin:0 0 15px 0;">
   @if($bodyText)
     {{$bodyText}}
   @else
    Thank you for registering with {{config('app.name')}}. Your account has been created successfully.
   @endif
</p>
@include('emails.layout.footer')
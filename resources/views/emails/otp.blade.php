@include('emails.layout.header')
<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi,</h1>
<p style="margin:0 0 15px 0;">
  {{ $otp }} is your login OTP. Please ensure this as confidential. {{config('app.name')}} will never call you to verify your OTP. Good Luck,
</p>
@include('emails.layout.footer')
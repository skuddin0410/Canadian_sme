@include('emails.layout.header')
<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi, {{!empty($user) ? $user->full_name : ''}}</h1>
<p style="margin:0 0 15px 0;">
    <p>{!! nl2br(e($messageContent)) !!}</p>
</p>
@include('emails.layout.footer')
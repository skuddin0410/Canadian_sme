<!-- @include('emails.layout.header')
<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">Hi, {{!empty($user) ? $user->full_name : ''}}</h1>
<p style="margin:0 0 15px 0;">
    <p>{!! nl2br(e($messageContent)) !!}</p>
</p>
@include('emails.layout.footer') -->
@include('emails.layout.header')

<h1 style="font-size:20px; color:#004fb8; margin:0 0 15px 0; font-weight:bold;">
    Hi, {{ !empty($user) ? $user->full_name : '' }}
</h1>

<p style="margin:0 0 15px 0;">
    {!! nl2br(e($messageContent)) !!}
</p>

<p style="color:red;font-size:12px;">
    Pixel URL: {{ url('/email/open/' . $mailLogId) }}
</p>

{{-- Example tracked link --}}
<p>
    <a href="{{ url('/email/click/'.$mailLogId.'?url='.urlencode(config('app.url'))) }}"
       style="color:#004fb8;">
        Visit Website
    </a>
</p>

{{-- Email Open Tracking Pixel --}}
<img src="{{ url('/email/open/'.$mailLogId) }}"
     width="1"
     height="1"
     style="opacity:0;" />

@include('emails.layout.footer')
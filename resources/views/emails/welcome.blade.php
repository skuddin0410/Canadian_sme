@include('emails.layout.header')

<h3 style="margin-bottom:15px;">
    Hi {{ $user->full_name ?? '' }},
</h3>

@if(!empty($bodyText))
<p style="margin:0 0 15px 0;">
    {!! $bodyText !!}
</p>
@endif
@if(!empty($mailLogId))
<p style="color:red;font-size:12px;">
    Pixel URL: {{ url('/email/open/' . $mailLogId) }}
</p>
@endif
{{-- Email Open Tracking Pixel --}}
@if(!empty($mailLogId))
<img src="{{ url('/email/open/' . $mailLogId) }}"
     width="1"
     height="1"
     alt=""
     style="opacity:0;">
@endif

@include('emails.layout.footer')
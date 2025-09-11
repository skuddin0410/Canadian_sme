@include('emails.layout.header')
<p style="margin:0 0 15px 0;">
   @if($bodyText)
     {!! $bodyText !!}
   @endif
</p>
@include('emails.layout.footer')
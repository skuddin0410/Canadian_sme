<!DOCTYPE html>
<html>
<body>
    <h2>{{ $emailRecord->subject }}</h2>
    <p>{!! nl2br(e($emailRecord->body)) !!}</p>

    <!-- Tracking Pixel -->
    <img src="{{ url('/email/open/' . $emailRecord->id) }}" width="1" height="1" alt="" style="display:none;">
</body>
</html>

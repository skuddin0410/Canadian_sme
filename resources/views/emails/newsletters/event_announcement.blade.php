
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #EC4899 0%, #BE185D 100%); color: white; padding: 30px 20px; text-align: center; }
        .content { padding: 30px 20px; }
        .event-card { border: 1px solid #E5E7EB; border-radius: 8px; margin: 20px 0; overflow: hidden; background: white; }
        .event-header { background: #FDF2F8; padding: 15px 20px; border-bottom: 1px solid #F3E8FF; }
        .event-title { margin: 0; color: #831843; font-size: 18px; }
        .event-date { color: #BE185D; font-size: 14px; font-weight: bold; }
        .event-details { padding: 20px; }
        .event-meta { display: flex; align-items: center; margin: 10px 0; color: #6B7280; font-size: 14px; }
        .event-meta svg { width: 16px; height: 16px; margin-right: 8px; }
        .register-btn { display: inline-block; background: linear-gradient(135deg, #EC4899 0%, #BE185D 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 15px 0; }
        .footer { background-color: #1F2937; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .footer a { color: #EC4899; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Upcoming Events</h1>
            <p>Join us for exclusive real estate events and networking opportunities</p>
        </div>

        <div class="content">
            @if(isset($templateData['featured_event']))
            <div style="background: linear-gradient(135deg, #FEF7FF 0%, #FAF5FF 100%); padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                <h2 style="color: #831843; margin-top: 0;">🌟 Featured Event</h2>
                <h3 style="color: #BE185D;">{{ $templateData['featured_event']['title'] }}</h3>
                <p style="color: #6B7280;">{{ $templateData['featured_event']['description'] }}</p>
                <a href="{{ $templateData['featured_event']['register_url'] }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   class="register-btn">Register Now - FREE</a>
            </div>
            @endif

            {!! $content !!}

            @if(isset($templateData['upcoming_events']))
            <h3 style="color: #1F2937;">All Upcoming Events</h3>
            @foreach($templateData['upcoming_events'] as $event)
            <div class="event-card">
                <div class="event-header">
                    <div class="event-title">{{ $event['title'] }}</div>
                    <div class="event-date">📅 {{ \Carbon\Carbon::parse($event['date'])->format('M j, Y • g:i A') }}</div>
                </div>
                <div class="event-details">
                    <p style="margin: 0 0 15px 0; color: #4B5563;">{{ $event['description'] }}</p>
                    
                    <div class="event-meta">
                        <span>📍</span>
                        <span>{{ $event['location'] ?? 'Online Event' }}</span>
                    </div>
                    
                    @if(isset($event['speaker']))
                    <div class="event-meta">
                        <span>🎤</span>
                        <span>Speaker: {{ $event['speaker'] }}</span>
                    </div>
                    @endif
                    
                    @if(isset($event['capacity']))
                    <div class="event-meta">
                        <span>👥</span>
                        <span>Limited to {{ $event['capacity'] }} attendees</span>
                    </div>
                    @endif
                    
                    <a href="{{ $event['register_url'] }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                       class="register-btn">
                       {{ $event['is_free'] ?? true ? 'Register FREE' : 'Register Now' }}
                    </a>
                </div>
            </div>
            @endforeach
            @endif

            @if(isset($templateData['past_events_recap']))
            <div style="background: #F3F4F6; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3 style="color: #1F2937; margin-top: 0;">📹 Missed Our Last Event?</h3>
                <p style="color: #4B5563;">{{ $templateData['past_events_recap'] }}</p>
                @if(isset($templateData['recording_url']))
                <a href="{{ $templateData['recording_url'] }}" style="color: #EC4899; font-weight: bold; text-decoration: none;">
                    Watch the Recording →
                </a>
                @endif
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0; padding: 20px; background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%); border-radius: 8px;">
                <h3 style="color: #1F2937; margin-top: 0;">Stay Connected</h3>
                <p style="color: #4B5563; margin-bottom: 20px;">Never miss an event! Follow us for the latest updates.</p>
                <div style="margin: 20px 0;">
                    <a href="#" style="display: inline-block; margin: 0 10px; color: #EC4899; text-decoration: none;">Facebook</a>
                    <a href="#" style="display: inline-block; margin: 0 10px; color: #EC4899; text-decoration: none;">LinkedIn</a>
                    <a href="#" style="display: inline-block; margin: 0 10px; color: #EC4899; text-decoration: none;">Twitter</a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #9CA3AF;">
                 © {{ date('Y') }} {{config('app.name')}}. All rights reserved.
            </p>
        </div>
    </div>

    <img src="{{ $trackingPixelUrl }}" width="1" height="1" style="display: none;" alt="">
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; }
        .content { padding: 30px 20px; }
        .tip-highlight { background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10B981; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 20px 0; }
        .footer { background-color: #1F2937; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .footer a { color: #10B981; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 New Investment Opportunities</h1>
            <p>Fresh properties just added to our portfolio</p>
        </div>

        <div class="content">
            <h2 style="color: #1F2937; text-align: center;">Featured Properties This Week</h2>
            
            @if(isset($templateData['new_properties']))
            @foreach($templateData['new_properties'] as $property)
            <div class="property-card">
                <div class="property-image">
                    🏠
                </div>
                <div class="property-details">
                    <h3 style="margin: 0 0 10px 0; color: #1F2937; font-size: 20px;">{{ $property['title'] }}</h3>
                    <p style="margin: 5px 0; color: #6B7280; font-size: 14px;">📍 {{ $property['location'] }}</p>
                    <div class="property-price">${{ number_format($property['price']) }}</div>
                    @if(isset($property['roi']))
                    <span class="property-roi">{{ $property['roi'] }} ROI</span>
                    @endif
                    <p style="margin: 15px 0; color: #4B5563;">{{ $property['description'] }}</p>
                    <a href="{{ $property['url'] }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                       class="cta-button">View Property Details</a>
                </div>
            </div>
            @endforeach
            @endif

            {!! $content !!}

            @if(isset($templateData['market_summary']))
            <div style="background: #F3F4F6; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3 style="color: #1F2937; margin-top: 0;">This Week's Market Summary</h3>
                <p style="color: #4B5563;">{{ $templateData['market_summary'] }}</p>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0; padding: 20px; background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%); border-radius: 8px;">
                <h3 style="color: #1F2937; margin-top: 0;">Don't Miss Out!</h3>
                <p style="color: #4B5563; margin-bottom: 20px;">Get personalized property recommendations based on your investment profile.</p>
                <a href="{{ $templateData['cta_url'] ?? '#' }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   class="cta-button">View All New Properties</a>
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
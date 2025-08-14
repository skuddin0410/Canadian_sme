<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        /* Include the same styles as default template */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .market-stats { display: flex; justify-content: space-around; margin: 20px 0; padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .stat-label { font-size: 12px; color: #7f8c8d; text-transform: uppercase; }
        .trend-up { color: #27ae60; }
        .trend-down { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📈 Weekly Market Update</h1>
            <p>{{ now()->format('F j, Y') }}</p>
        </div>

        <div class="content" style="padding: 30px 20px;">
            <h2>Market Highlights</h2>
            
            @if(isset($templateData['market_stats']))
            <div class="market-stats">
                @foreach($templateData['market_stats'] as $stat)
                <div class="stat-item">
                    <div class="stat-number {{ $stat['trend'] ?? '' }}">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {!! $content !!}

            @if(isset($templateData['featured_properties']))
            <h3>Featured Investment Opportunities</h3>
            @foreach($templateData['featured_properties'] as $property)
            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin: 15px 0; background-color: #fafafa;">
                <h4 style="margin: 0 0 10px 0; color: #2c3e50;">{{ $property['title'] }}</h4>
                <p style="margin: 5px 0; color: #7f8c8d;">{{ $property['location'] }}</p>
                <p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #27ae60;">${{ number_format($property['price']) }}</p>
                <p style="margin: 10px 0;">{{ $property['description'] }}</p>
                <a href="{{ $property['url'] }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   style="color: #3498db; text-decoration: none; font-weight: bold;">View Details →</a>
            </div>
            @endforeach
            @endif

            <div style="text-align: center; margin: 30px 0;">
              {{--   <a href="{{ route('properties.matches') }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;">
                    View All Opportunities
                </a> --}}
                <a href="" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;">
                    View All Opportunities
                </a>
            </div>
        </div>

        <div style="background-color: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px;">
            <p>
                <a href="{{ $unsubscribeUrl }}" style="color: #3498db;">Unsubscribe</a> 
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #9CA3AF;">
                 © {{ date('Y') }} {{config('app.name')}}. All rights reserved.
            </p>
        </div>
    </div>

    <img src="{{ $trackingPixelUrl }}" width="1" height="1" style="display: none;" alt="">
</body>
</html>
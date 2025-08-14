<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 30px 20px; text-align: center; }
        .content { padding: 30px 20px; }
        .portfolio-stats { display: flex; justify-content: space-around; margin: 20px 0; padding: 20px; background: #FEF3C7; border-radius: 8px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #92400E; }
        .stat-label { font-size: 12px; color: #78350F; text-transform: uppercase; }
        .performance-item { background: white; border: 1px solid #E5E7EB; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .performance-positive { border-left: 4px solid #10B981; }
        .performance-negative { border-left: 4px solid #EF4444; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 20px 0; }
        .footer { background-color: #1F2937; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .footer a { color: #F59E0B; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Portfolio Performance Update</h1>
            <p>Your investment summary for {{ now()->format('F Y') }}</p>
        </div>

        <div class="content">
            <h2 style="color: #1F2937; text-align: center;">Portfolio Overview</h2>
            
            @if(isset($templateData['portfolio_stats']))
            <div class="portfolio-stats">
                @foreach($templateData['portfolio_stats'] as $stat)
                <div class="stat-item">
                    <div class="stat-number">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {!! $content !!}

            @if(isset($templateData['property_performance']))
            <h3 style="color: #1F2937;">Property Performance</h3>
            @foreach($templateData['property_performance'] as $property)
            <div class="performance-item {{ $property['performance'] >= 0 ? 'performance-positive' : 'performance-negative' }}">
                <h4 style="margin: 0 0 10px 0; color: #1F2937;">{{ $property['name'] }}</h4>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #6B7280;">{{ $property['location'] }}</span>
                    <span style="font-weight: bold; color: {{ $property['performance'] >= 0 ? '#10B981' : '#EF4444' }};">
                        {{ $property['performance'] >= 0 ? '+' : '' }}{{ $property['performance'] }}%
                    </span>
                </div>
                @if(isset($property['notes']))
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #4B5563;">{{ $property['notes'] }}</p>
                @endif
            </div>
            @endforeach
            @endif

            @if(isset($templateData['recommendations']))
            <div style="background: #EBF8FF; padding: 20px; border-radius: 8px; margin: 30px 0; border-left: 4px solid #3B82F6;">
                <h3 style="color: #1E40AF; margin-top: 0;">💡 This Month's Recommendations</h3>
                <ul style="color: #1E3A8A; margin: 0; padding-left: 20px;">
                    @foreach($templateData['recommendations'] as $recommendation)
                    <li style="margin-bottom: 8px;">{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $templateData['dashboard_url'] ?? '#' }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   class="cta-button">View Full Dashboard</a>
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
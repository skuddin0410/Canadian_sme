<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 30px 20px;
        }
        .content h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #3498db;
            text-decoration: none;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Real Estate Investment Platform</h1>
            <p>Your trusted partner in real estate investments</p>
        </div>

        <!-- Content -->
        <div class="content">
            {!! $content !!}
            
            @if(isset($templateData['cta_url']) && isset($templateData['cta_text']))
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $templateData['cta_url'] }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   class="cta-button">{{ $templateData['cta_text'] }}</a>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Twitter</a>
                <a href="#">LinkedIn</a>
                <a href="#">Instagram</a>
            </div>
            
            <p>
                You're receiving this email because you subscribed to our newsletter.<br>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> | 
                <a href="#">Update Preferences</a> | 
                <a href="#">View in Browser</a>
            </p>
            
            <p style="margin-top: 20px; font-size: 12px; color: #bdc3c7;">
                Real Estate Investment Platform<br>
                123 Business Street, City, State 12345<br>
                © {{ date('Y') }} All rights reserved.
            </p>
        </div>
    </div>

    <!-- Tracking Pixel -->
    <img src="{{ $trackingPixelUrl }}" width="1" height="1" style="display: none;" alt="">
</body>
</html>
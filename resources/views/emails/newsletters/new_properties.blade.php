<!-- resources/views/emails/newsletters/new-properties.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
    <style>
        /* Base Styles */
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
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header .subtitle {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Content Styles */
        .content {
            padding: 30px 20px;
        }
        
        .intro-section {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #F8FAFF 0%, #F1F5F9 100%);
            border-radius: 12px;
            border: 1px solid #E2E8F0;
        }
        
        .intro-section h2 {
            color: #1E293B;
            margin: 0 0 15px 0;
            font-size: 24px;
        }
        
        .intro-section p {
            color: #64748B;
            margin: 0;
            font-size: 16px;
        }
        
        /* Property Card Styles */
        .property-grid {
            margin: 30px 0;
        }
        
        .property-card {
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            margin: 25px 0;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .property-image {
            height: 200px;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            position: relative;
            overflow: hidden;
        }
        
        .property-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px, 20px 20px;
        }
        
        .property-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            color: #7C3AED;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            backdrop-filter: blur(10px);
        }
        
        .property-details {
            padding: 25px;
        }
        
        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .property-title {
            flex: 1;
        }
        
        .property-title h3 {
            margin: 0 0 5px 0;
            color: #1E293B;
            font-size: 20px;
            font-weight: 600;
        }
        
        .property-location {
            margin: 0;
            color: #64748B;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .property-location::before {
            content: '📍';
            margin-right: 5px;
        }
        
        .property-price {
            text-align: right;
            margin-left: 15px;
        }
        
        .property-price .amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin: 0;
            line-height: 1;
        }
        
        .property-price .type {
            font-size: 12px;
            color: #6B7280;
            margin: 2px 0 0 0;
        }
        
        .property-features {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 8px;
        }
        
        .feature-item {
            text-align: center;
            flex: 1;
        }
        
        .feature-item .icon {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        
        .feature-item .value {
            font-weight: bold;
            color: #1E293B;
            font-size: 16px;
            display: block;
        }
        
        .feature-item .label {
            font-size: 11px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .property-metrics {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        
        .metric-badge {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        
        .metric-badge.roi {
            background: #DCFCE7;
            color: #166534;
        }
        
        .metric-badge.cap-rate {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .metric-badge.cash-flow {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .property-description {
            margin: 15px 0;
            color: #4B5563;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .property-highlights {
            margin: 15px 0;
        }
        
        .property-highlights h4 {
            margin: 0 0 10px 0;
            color: #1E293B;
            font-size: 14px;
            font-weight: 600;
        }
        
        .highlights-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .highlights-list li {
            color: #4B5563;
            font-size: 13px;
            margin: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        
        .highlights-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #059669;
            font-weight: bold;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            transition: all 0.2s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        
        .btn-secondary {
            background: #F1F5F9;
            color: #475569;
            flex: 1;
            border: 1px solid #E2E8F0;
        }
        
        .btn-secondary:hover {
            background: #E2E8F0;
        }
        
        /* Market Summary Section */
        .market-summary {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            border-left: 4px solid #0EA5E9;
        }
        
        .market-summary h3 {
            color: #0C4A6E;
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        
        .market-summary p {
            color: #0369A1;
            margin: 0;
            line-height: 1.6;
        }
        
        /* Call to Action Section */
        .cta-section {
            text-align: center;
            margin: 40px 0;
            padding: 30px 20px;
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border-radius: 12px;
            border: 2px dashed #D1D5DB;
        }
        
        .cta-section h3 {
            color: #1E293B;
            margin: 0 0 15px 0;
            font-size: 22px;
        }
        
        .cta-section p {
            color: #4B5563;
            margin: 0 0 25px 0;
            font-size: 16px;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }
        
        /* Stats Section */
        .stats-section {
            background: #1E293B;
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 12px;
        }
        
        .stats-grid {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #8B5CF6;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Footer Styles */
        .footer {
            background-color: #1F2937;
            color: white;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer-content {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .footer h4 {
            color: #8B5CF6;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        
        .footer p {
            margin: 10px 0;
            line-height: 1.6;
        }
        
        .footer a {
            color: #8B5CF6;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .footer-links {
            border-top: 1px solid #374151;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .social-links {
            margin: 15px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #8B5CF6;
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Responsive Styles */
        @media (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .property-details {
                padding: 20px;
            }
            
            .property-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .property-price {
                margin-left: 0;
                margin-top: 10px;
                text-align: left;
            }
            
            .property-features {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .feature-item {
                flex: 1;
                min-width: calc(50% - 5px);
            }
            
            .property-actions {
                flex-direction: column;
            }
            
            .stats-grid {
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .stat-item {
                flex: 1;
                min-width: calc(50% - 10px);
            }
        }
        
        /* Print Styles */
        @media print {
            .footer, .cta-section {
                display: none !important;
            }
            
            .container {
                box-shadow: none !important;
            }
            
            .property-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <h1>🏢 New Investment Opportunities</h1>
                <p class="subtitle">Fresh properties just added to our exclusive portfolio</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Introduction Section -->
            <div class="intro-section">
                <h2>{{ $templateData['intro_title'] ?? 'Handpicked Properties This Week' }}</h2>
                <p>{{ $templateData['intro_message'] ?? 'Discover carefully selected investment opportunities that match your criteria and offer exceptional potential returns.' }}</p>
            </div>

            <!-- Custom Content -->
            {!! $content !!}

            <!-- Featured Properties Grid -->
            @if(isset($templateData['new_properties']) && !empty($templateData['new_properties']))
            <div class="property-grid">
                @foreach($templateData['new_properties'] as $property)
                <div class="property-card">
                    <!-- Property Image/Placeholder -->
                    <div class="property-image">
                        @if(isset($property['featured']))
                        <div class="property-badge">{{ $property['badge'] ?? 'FEATURED' }}</div>
                        @endif
                        
                        @if(isset($property['property_type']))
                            @switch($property['property_type'])
                                @case('residential')
                                    🏠
                                    @break
                                @case('commercial')
                                    🏢
                                    @break
                                @case('industrial')
                                    🏭
                                    @break
                                @case('mixed-use')
                                    🏘️
                                    @break
                                @default
                                    🏡
                            @endswitch
                        @else
                            🏡
                        @endif
                    </div>

                    <!-- Property Details -->
                    <div class="property-details">
                        <!-- Header with Title and Price -->
                        <div class="property-header">
                            <div class="property-title">
                                <h3>{{ $property['title'] }}</h3>
                                <p class="property-location">{{ $property['location'] }}</p>
                            </div>
                            <div class="property-price">
                                <div class="amount">${{ number_format($property['price']) }}</div>
                                <div class="type">{{ $property['price_type'] ?? 'Purchase Price' }}</div>
                            </div>
                        </div>

                        <!-- Property Features -->
                        @if(isset($property['bedrooms']) || isset($property['bathrooms']) || isset($property['sqft']))
                        <div class="property-features">
                            @if(isset($property['bedrooms']))
                            <div class="feature-item">
                                <span class="icon">🛏️</span>
                                <span class="value">{{ $property['bedrooms'] }}</span>
                                <span class="label">Bedrooms</span>
                            </div>
                            @endif
                            
                            @if(isset($property['bathrooms']))
                            <div class="feature-item">
                                <span class="icon">🚿</span>
                                <span class="value">{{ $property['bathrooms'] }}</span>
                                <span class="label">Bathrooms</span>
                            </div>
                            @endif
                            
                            @if(isset($property['sqft']))
                            <div class="feature-item">
                                <span class="icon">📐</span>
                                <span class="value">{{ number_format($property['sqft']) }}</span>
                                <span class="label">Sq Ft</span>
                            </div>
                            @endif
                            
                            @if(isset($property['year_built']))
                            <div class="feature-item">
                                <span class="icon">🗓️</span>
                                <span class="value">{{ $property['year_built'] }}</span>
                                <span class="label">Year Built</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Investment Metrics -->
                        @if(isset($property['roi']) || isset($property['cap_rate']) || isset($property['cash_flow']))
                        <div class="property-metrics">
                            @if(isset($property['roi']))
                            <div class="metric-badge roi">
                                <strong>{{ $property['roi'] }}</strong><br>
                                <small>ROI</small>
                            </div>
                            @endif
                            
                            @if(isset($property['cap_rate']))
                            <div class="metric-badge cap-rate">
                                <strong>{{ $property['cap_rate'] }}</strong><br>
                                <small>Cap Rate</small>
                            </div>
                            @endif
                            
                            @if(isset($property['cash_flow']))
                            <div class="metric-badge cash-flow">
                                <strong>${{ number_format($property['cash_flow']) }}</strong><br>
                                <small>Monthly CF</small>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Property Description -->
                        <div class="property-description">
                            {{ $property['description'] }}
                        </div>

                        <!-- Property Highlights -->
                        @if(isset($property['highlights']) && !empty($property['highlights']))
                        <div class="property-highlights">
                            <h4>Property Highlights</h4>
                            <ul class="highlights-list">
                                @foreach($property['highlights'] as $highlight)
                                <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="property-actions">
                            <a href="{{ $property['details_url'] ?? '#' }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}&property={{ $property['id'] ?? '' }}" 
                               class="btn btn-primary">
                               View Full Details
                            </a>
                            <a href="{{ $property['contact_url'] ?? '#' }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}&property={{ $property['id'] ?? '' }}" 
                               class="btn btn-secondary">
                               Contact Agent
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Market Summary Section -->
            @if(isset($templateData['market_summary']))
            <div class="market-summary">
                <h3>📈 This Week's Market Summary</h3>
                <p>{{ $templateData['market_summary'] }}</p>
            </div>
            @endif

            <!-- Statistics Section -->
            @if(isset($templateData['platform_stats']))
            <div class="stats-section">
                <h3>Why Investors Choose Our Platform</h3>
                <div class="stats-grid">
                    @foreach($templateData['platform_stats'] as $stat)
                    <div class="stat-item">
                        <div class="stat-number">{{ $stat['value'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h3>{{ $templateData['cta_title'] ?? "Don't Miss Out on These Opportunities!" }}</h3>
                <p>{{ $templateData['cta_message'] ?? 'Get access to more exclusive properties and personalized recommendations based on your investment profile.' }}</p>
                <a href="{{ $templateData['cta_url'] ?? route('properties') }}?email={{ $recipientEmail }}&newsletter={{ $newsletter->id }}" 
                   class="cta-button">
                   {{ $templateData['cta_text'] ?? 'Browse All New Properties' }}
                </a>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="footer-content">
                <h4>Questions About These Properties?</h4>
                <p>Our investment specialists are here to help you make informed decisions.</p>
                
                <div class="footer-links">
                    <p>
                        <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> 
                    </p>
                    
                    <p style="margin-top: 20px; font-size: 12px; color: #9CA3AF; line-height: 1.5;">
                        © {{ date('Y') }} {{config('app.name')}}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Pixel -->
    <img src="{{ $trackingPixelUrl }}" width="1" height="1" style="display: none;" alt="">
</body>
</html>
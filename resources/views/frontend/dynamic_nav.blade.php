<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $nav->title }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/frontend/css/style_new.css">
    <link rel="stylesheet" href="/frontend/css/developer.css">

    <style>
        .dynamic-page-wrapper {
            overflow-x: hidden;
        }

        .pb-hero {
            padding: 90px 0;
            text-align: center;
        }

        .pb-hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .pb-hero p {
            font-size: 1.1rem;
            color: #6b7280;
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .pb-text {
            padding: 56px 0;
        }

        .pb-text-inner {
            max-width: 820px;
            margin: 0 auto;
            font-size: 1rem;
            line-height: 1.8;
            color: #374151;
        }

        .pb-image {
            padding: 48px 0;
        }

        .pb-image img {
            border-radius: 14px;
            box-shadow: 0 6px 32px rgba(0, 0, 0, 0.10);
            max-width: 100%;
        }

        .pb-cards-section {
            padding: 70px 0;
        }

        .dynamic-page-wrapper .pb-card,
        .dynamic-page-wrapper .pb-card:hover,
        .dynamic-page-wrapper .pb-card:focus,
        .dynamic-page-wrapper .pb-card:active,
        .dynamic-page-wrapper .pb-card *,
        .dynamic-page-wrapper .pb-card *:hover,
        .dynamic-page-wrapper .pb-card *:focus,
        .dynamic-page-wrapper .pb-card *:active {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            filter: none !important;
        }

        .dynamic-page-wrapper .pb-card {
            border: none !important;
            outline: none !important;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.28s cubic-bezier(.4, 0, .2, 1);
            padding: 0 !important;
        }

        .dynamic-page-wrapper .pb-card:hover {
            transform: translateY(-4px);
        }

        .dynamic-page-wrapper .pb-card-img-wrap {
            width: 100%;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            margin: 0;
        }

        .dynamic-page-wrapper .pb-card-img-wrap img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            border-radius: 0;
        }

        .dynamic-page-wrapper .pb-card:hover .pb-card-img-wrap img {
            transform: scale(1.03);
            transition: transform 0.35s ease;
        }

        .dynamic-page-wrapper .pb-card-body {
            padding: 16px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .pb-card-btn {
            display: inline-block;
            align-self: flex-start;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none !important;
            background: #4361ee;
            color: #fff !important;
            letter-spacing: 0.02em;
            transition: background 0.2s, transform 0.15s;
        }

        .pb-card-btn:hover {
            background: #2d46c7;
            transform: translateX(3px);
        }

        @media (max-width: 768px) {
            .pb-hero {
                padding: 60px 0;
            }

            .pb-cards-section {
                padding: 48px 0;
            }

            .dynamic-page-wrapper .pb-card {
                flex: 0 1 100% !important;
                width: 100% !important;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .dynamic-page-wrapper .pb-card {
                flex: 0 1 calc(50% - 14px) !important;
                width: calc(50% - 14px) !important;
            }
        }
    </style>


</head>

<body>

    @include('partials_new.header')

    @php
    $sections = is_array(json_decode($nav->content, true))
    ? json_decode($nav->content, true)
    : [];
    @endphp

    <div class="dynamic-page-wrapper">

        @foreach($sections as $section)
        @php
        $data = $section['data'] ?? [];
        $alignment = $data['alignment'] ?? 'center';
        $sectionWidth = (int)($data['sectionWidth'] ?? 100);
        $sectionHeight = $data['height'] ?? 'auto';
        $justify = $alignment === 'right' ? 'flex-end' : ($alignment === 'left' ? 'flex-start' : 'center');
        @endphp

        {{-- ── HERO ── --}}
        @if(($section['type'] ?? '') === 'hero')
        <section class="pb-hero" style="background:{{ $data['bg'] ?? '#f8f9fa' }};">
            <div style="width:{{ $sectionWidth }}%; margin:0 auto; min-height:{{ $sectionHeight }}; display:flex; flex-direction:column; justify-content:center; align-items:{{ $justify }}; text-align:{{ $alignment }}; padding:90px 20px;">
                @if(!empty($data['title']))
                <h1 style="color:{{ $data['textColor'] ?? '#1a1a2e' }};">{{ $data['title'] }}</h1>
                @endif
                @if(!empty($data['subtitle']))
                <p style="color:{{ $data['subtitleColor'] ?? '#6b7280' }};">{{ $data['subtitle'] }}</p>
                @endif
                @if(!empty($data['btnText']))
                <a href="{{ $data['btnLink'] ?? '#' }}" class="pb-hero-btn"
                    style="background:{{ $data['btnColor'] ?? '#4361ee' }}; color:{{ $data['btnTextColor'] ?? '#ffffff' }};">
                    {{ $data['btnText'] }}
                </a>
                @endif
            </div>
        </section>
        @endif

        {{-- ── TEXT ── --}}
        @if(($section['type'] ?? '') === 'text')
        <section class="pb-text" style="background:{{ $data['bg'] ?? '#ffffff' }};">
            <div style="width:{{ $sectionWidth }}%; margin:0 auto; min-height:{{ $sectionHeight }}; padding:56px 20px; display:flex; justify-content:{{ $justify }};">
                <div style="max-width:820px; width:100%; text-align:{{ $alignment }}; color:{{ $data['textColor'] ?? '#374151' }}; line-height:1.8;">
                    {!! $data['content'] ?? '' !!}
                </div>
            </div>
        </section>
        @endif

        {{-- ── IMAGE ── --}}
        @if(($section['type'] ?? '') === 'image')
        <section class="pb-image" style="background:{{ $data['bg'] ?? '#ffffff' }};">
            <div style="width:{{ $sectionWidth }}%; margin:0 auto; min-height:{{ $sectionHeight }}; padding:48px 20px; display:flex; flex-direction:column; justify-content:center;">
                @if(!empty($data['image']))
                <div style="width:100%; display:flex; justify-content:{{ $justify }};">
                    <img src="{{ $data['image'] }}" alt="Section image" loading="lazy"
                        style="width:auto; max-width:100%; max-height:{{ $sectionHeight !== 'auto' ? $sectionHeight : '520px' }}; border-radius:14px; box-shadow:0 6px 32px rgba(0,0,0,0.10); object-fit:contain; display:block;">
                </div>
                @endif
                @if(!empty($data['caption']))
                <div style="width:100%; display:flex; justify-content:{{ $justify }};">
                    <p style="margin:12px 0 0; max-width:600px; text-align:{{ $alignment }}; color:{{ $data['captionColor'] ?? '#6b7280' }};">
                        {{ $data['caption'] }}
                    </p>
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- ── CARDS ── --}}
        @if(($section['type'] ?? '') === 'cards')
        @php $cards = is_array($data['cards'] ?? null) ? $data['cards'] : []; @endphp

        <section class="pb-cards-section" style="background:{{ $data['bg'] ?? '#f8f9fa' }};">
            <div style="width:{{ $sectionWidth }}%; margin:0 auto; min-height:{{ $sectionHeight }}; padding:70px 20px;">

                @if(!empty($data['sectionTitle']))
                <h2 style="text-align:{{ $alignment }}; color:{{ $data['sectionTitleColor'] ?? '#1a1a2e' }}; font-size:clamp(1.5rem,3vw,2.2rem); font-weight:800; margin-bottom:40px;">
                    {{ $data['sectionTitle'] }}
                </h2>
                @endif

                <div style="display:flex; flex-wrap:wrap; gap:20px; justify-content:{{ $justify }}; align-items:flex-start;">

                    @foreach($cards as $card)
                    @php
                    $cardType = $card['type'] ?? 'card';
                    $cardAlign = $card['alignment'] ?? 'left';
                    $cardJustify = $cardAlign === 'center' ? 'center' : ($cardAlign === 'right' ? 'flex-end' : 'flex-start');
                    $cardWidth = $card['width'] ?? 33.333;
                    $cardHeight = $card['height'] ?? 'auto';

                    $rawCardBg = strtolower(trim($card['cardBg'] ?? '#ffffff'));
                    $cardBg = in_array($rawCardBg, ['#ffffff', '#fff', 'white', 'transparent', '']) ? 'transparent' : ($card['cardBg'] ?? 'transparent');
                    @endphp

                    <div class="pb-card"
                        style="box-sizing:border-box;
                        width:calc({{ $cardWidth }}% - 14px);
                        flex:0 0 calc({{ $cardWidth }}% - 14px);
                        min-height:{{ $cardHeight }};
                        background:{{ $cardBg }} !important;
                        background-color:{{ $cardBg }} !important;">

                        @if(!empty($card['image']))
                        <div class="pb-card-img-wrap"
                            style="{{ $cardHeight !== 'auto' ? 'height:'.$cardHeight.';' : 'min-height:180px;' }}
                            background:{{ $cardBg }} !important;
                            background-color:{{ $cardBg }} !important;">
                            <img src="{{ $card['image'] }}"
                                alt="{{ $card['title'] ?? 'Card image' }}"
                                loading="lazy">
                        </div>
                        @endif

                        @if($cardType === 'image')
                        @if(!empty($card['caption']))
                        <div style="padding:16px 20px;
                                text-align:{{ $cardAlign }};
                                color:{{ $card['captionColor'] ?? '#6b7280' }};
                                background:{{ $cardBg }} !important;
                                background-color:{{ $cardBg }} !important;">
                            {{ $card['caption'] }}
                        </div>
                        @endif
                        @else
                        <div class="pb-card-body"
                            style="align-items:{{ $cardJustify }};
                            text-align:{{ $cardAlign }};
                            background:{{ $cardBg }} !important;
                            background-color:{{ $cardBg }} !important;">

                            @if(!empty($card['title']))
                            <h3 style="font-size:1.1rem; font-weight:700; margin:0 0 10px; line-height:1.35; color:{{ $card['titleColor'] ?? '#1a1a2e' }};">
                                {{ $card['title'] }}
                            </h3>
                            @endif

                            @if(!empty($card['description']))
                            <div style="width:100%; flex:1; margin:0 0 20px; line-height:1.7; color:{{ $card['descColor'] ?? '#6b7280' }};">
                                {!! $card['description'] !!}
                            </div>
                            @endif

                            @if(!empty($card['btnText']))
                            <a href="{{ $card['btnLink'] ?? '#' }}"
                                style="display:inline-block;
                              align-self:{{ $cardJustify }};
                              padding:10px 22px;
                              border-radius:8px;
                              font-size:.875rem;
                              font-weight:600;
                              text-decoration:none;
                              background:{{ $card['btnColor'] ?? '#4361ee' }};
                              color:{{ $card['btnTextColor'] ?? '#ffffff' }};">
                                {{ $card['btnText'] }}
                            </a>
                            @endif

                        </div>
                        @endif
                    </div>
                    @endforeach

                </div>
            </div>
        </section>
        @endif


        @endforeach

    </div>

    @include('partials_new.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/js/script_new.js"></script>
</body>

</html>
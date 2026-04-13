<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $nav->title }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/frontend/css/style.css">
    <link rel="stylesheet" href="/frontend/css/dynamic-nav.css">
    <link rel="stylesheet" href="/frontend/css/style_new.css">
    <link rel="stylesheet" href="/frontend/css/developer.css">
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
        $sectionWidth = ($data['sectionWidth'] ?? '100') === 'auto' ? 'auto' : ((int)($data['sectionWidth'] ?? 100) . '%');
        $sectionHeight = $data['height'] ?? 'auto';
        $justify = $alignment === 'right' ? 'flex-end' : ($alignment === 'left' ? 'flex-start' : 'center');
        @endphp

        {{-- ── HERO ── --}}
        @if(($section['type'] ?? '') === 'hero')
        <section class="pb-hero dn-section" data-dn-bg="{{ $data['bg'] ?? '#f8f9fa' }}">
            <div class="container dn-section-frame dn-flex dn-flex-col dn-hero-frame"
                data-dn-width="{{ $sectionWidth }}"
                data-dn-min-height="{{ $sectionHeight }}"
                data-dn-align-items="{{ $justify }}"
                data-dn-text-align="{{ $alignment }}">
                @if(!empty($data['title']))
                <h1 data-dn-color="{{ $data['textColor'] ?? '#1a1a2e' }}">{{ $data['title'] }}</h1>
                @endif
                @if(!empty($data['subtitle']))
                <p data-dn-color="{{ $data['subtitleColor'] ?? '#6b7280' }}">{{ $data['subtitle'] }}</p>
                @endif
                @if(!empty($data['btnText']))
                <a href="{{ !empty($data['btnLink']) ? $data['btnLink'] : 'javascript:void(0)' }}" class="pb-hero-btn"
                    data-dn-bg="{{ $data['btnColor'] ?? '#4361ee' }}"
                    data-dn-color="{{ $data['btnTextColor'] ?? '#ffffff' }}">
                    {{ $data['btnText'] }}
                </a>
                @endif
            </div>
        </section>
        @endif

        {{-- ── TEXT ── --}}
        @if(($section['type'] ?? '') === 'text')
        <section class="pb-text dn-section" data-dn-bg="{{ $data['bg'] ?? '#ffffff' }}">
            <div class="container dn-section-frame dn-flex"
                data-dn-width="{{ $sectionWidth }}"
                data-dn-min-height="{{ $sectionHeight }}"
                data-dn-justify="{{ $justify }}">
                <div class="pb-text-inner"
                    data-dn-text-align="{{ $alignment }}"
                    data-dn-color="{{ $data['textColor'] ?? '#374151' }}">
                    {!! $data['content'] ?? '' !!}
                </div>
            </div>
        </section>
        @endif

        {{-- ── IMAGE ── --}}
        @if(($section['type'] ?? '') === 'image')
        <section class="pb-image dn-section" data-dn-bg="{{ $data['bg'] ?? '#ffffff' }}">
            <div class="container dn-section-frame dn-flex dn-flex-col"
                data-dn-width="{{ $sectionWidth }}"
                data-dn-min-height="{{ $sectionHeight }}"
                data-dn-justify="center">
                @if(!empty($data['image']))
                <div class="pb-image-media" data-dn-justify="{{ $justify }}">
                    <img src="{{ $data['image'] }}" alt="Section image" loading="lazy"
                        class="pb-image-img"
                        data-dn-width="{{ (int)($data['sectionWidth'] ?? 100) >= 100 ? '100%' : 'auto' }}"
                        data-dn-max-height="{{ $sectionHeight !== 'auto' ? $sectionHeight : '520px' }}"
                        data-dn-object-fit="{{ (int)($data['sectionWidth'] ?? 100) >= 100 ? 'cover' : 'contain' }}">
                </div>
                @endif
                @if(!empty($data['caption']))
                <div class="pb-image-caption-wrap" data-dn-justify="{{ $justify }}">
                    <p class="pb-image-caption"
                        data-dn-text-align="{{ $alignment }}"
                        data-dn-color="{{ $data['captionColor'] ?? '#6b7280' }}">
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

        <section class="pb-cards-section-new dn-section" data-dn-bg="{{ $data['bg'] ?? '#f8f9fa' }}">
            <div class="container dn-section-frame"
                data-dn-width="{{ $sectionWidth }}"
                data-dn-min-height="{{ $sectionHeight }}">

                @if(!empty($data['sectionTitle']))
                <h2 class="pb-section-title"
                    data-dn-text-align="{{ $alignment }}"
                    data-dn-color="{{ $data['sectionTitleColor'] ?? '#1a1a2e' }}">
                    {{ $data['sectionTitle'] }}
                </h2>
                @endif

                <div class="pb-cards-grid-live" data-dn-justify="{{ $justify }}">

                    @foreach($cards as $card)
                    @php
                    $cardType = $card['type'] ?? 'card';
                    $cardAlign = $card['alignment'] ?? 'left';
                    $cardJustify = $cardAlign === 'center' ? 'center' : ($cardAlign === 'right' ? 'flex-end' : 'flex-start');
                    $cardWidth = $card['width'] ?? '33.333';
                    $cardHeight = $card['height'] ?? 'auto';
                    $isFullWidthImage = $cardType === 'image' && (float) $cardWidth >= 100;
                    $cardWidthStyle = $cardWidth === 'auto' ? 'auto' : ($isFullWidthImage ? '100%' : 'calc('.$cardWidth.'% - 14px)');

                    $rawCardBg = strtolower(trim($card['cardBg'] ?? '#ffffff'));
                    $cardBg = in_array($rawCardBg, ['#ffffff', '#fff', 'white', 'transparent', '']) ? 'transparent' : ($card['cardBg'] ?? 'transparent');
                    @endphp

                    <div class="pb-card"
                        data-dn-width="{{ $cardWidthStyle }}"
                        data-dn-flex-basis="{{ $cardWidthStyle }}"
                        data-dn-min-height="{{ $cardHeight }}"
                        data-dn-bg="{{ $cardBg }}">

                        @if(!empty($card['image']))
                        <div class="pb-card-img-wrap"
                            data-dn-height="{{ $cardHeight !== 'auto' ? $cardHeight : '' }}"
                            data-dn-min-height="{{ $cardHeight === 'auto' ? '180px' : '' }}"
                            data-dn-bg="{{ $cardBg }}">
                            <img src="{{ $card['image'] }}"
                                alt="{{ $card['title'] ?? 'Card image' }}"
                                loading="lazy">
                        </div>
                        @endif

                        @if($cardType === 'image')
                        @if(!empty($card['caption']))
                        <div class="pb-card-caption"
                            data-dn-text-align="{{ $cardAlign }}"
                            data-dn-color="{{ $card['captionColor'] ?? '#6b7280' }}"
                            data-dn-bg="{{ $cardBg }}">
                            {{ $card['caption'] }}
                        </div>
                        @endif
                        @else
                        <div class="pb-card-body"
                            data-dn-align-items="{{ $cardJustify }}"
                            data-dn-text-align="{{ $cardAlign }}"
                            data-dn-bg="{{ $cardBg }}">

                            @if(!empty($card['title']))
                            <h3 class="pb-card-title" data-dn-color="{{ $card['titleColor'] ?? '#1a1a2e' }}">
                                {{ $card['title'] }}
                            </h3>
                            @endif

                            @if(!empty($card['description']))
                            <div class="pb-card-description" data-dn-color="{{ $card['descColor'] ?? '#6b7280' }}">
                                {!! $card['description'] !!}
                            </div>
                            @endif

                            @if(!empty($card['btnText']))
                            <a href="{{ !empty($card['btnLink']) ? $card['btnLink'] : 'javascript:void(0)' }}"
                                class="pb-card-btn"
                                data-dn-align-self="{{ $cardJustify }}"
                                data-dn-bg="{{ $card['btnColor'] ?? '#4361ee' }}"
                                data-dn-color="{{ $card['btnTextColor'] ?? '#ffffff' }}">
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
    <script src="/frontend/js/dynamic-nav.js"></script>
    <script src="/frontend/js/script_new.js"></script>
</body>

</html>

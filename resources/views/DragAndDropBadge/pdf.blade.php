<!DOCTYPE html>
<html>
<head>
<style>
/* 1️⃣ PDF page margins */
@page {
    margin: 0;
    padding: 0;
}

/* 2️⃣ HTML & BODY reset */
html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
}

/* 3️⃣ Badge size exactly */
.badge {
    position: relative;
    width: {{ $badge->width }}in;
    height: {{ $badge->height }}in;
    overflow: hidden;
}

/* Page break control */
.badge-page {
    page-break-after: always;
}

.badge-page:last-child {
    page-break-after: auto;
}

/* Canvas items */
.item {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
    padding: 0;
    margin: 0;
}

.item img {
    display: block;
    max-width: 100%;
    max-height: 100%;
}
</style>
</head>

<body>

@foreach($users as $user)

@php
$data = [
    'name'         => $user->full_name,
    'first_name'   => $user->name,
    'last_name'    => $user->lastname,
    'company_name' => $user->company,
    'qr_code'      => $user->qr_code ? public_path($user->qr_code) : null,
];
@endphp

<div class="badge-page">
    <div class="badge">
        @foreach($layout as $item)
            @if($item['type'] === 'qr_code')
                <div class="item"
                     style="
                        left: {{ $item['x'] }};
                        top: {{ $item['y'] }};
                        width: {{ $item['width'] }}px;
                        height: {{ $item['height'] }}px;
                     ">
                    @if($data['qr_code'] && file_exists($data['qr_code']))
                        <img src="file://{{ $data['qr_code'] }}">
                    @endif
                </div>
            @else
                <div class="item"
                     style="
                        left: {{ $item['x'] }};
                        top: {{ $item['y'] }};
                        width: {{ $item['width'] }}px;
                        height: {{ $item['height'] }}px;
                        font-size: {{ $item['fontSize'] ?? '14px' }};
                     ">
                    {{ $data[$item['type']] ?? '' }}
                </div>
            @endif
        @endforeach
    </div>
</div>

@endforeach

</body>
</html>

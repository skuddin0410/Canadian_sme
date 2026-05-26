@extends('layouts.frontendapp')

@section('title', 'Event Guide')

@section('meta')
    <meta name="description" content="Event guide for {{ $event->title }}">
@endsection

@section('content')
<section class="event-guide-page py-4 py-lg-5">
    <div class="container">
        <div class="guide-shell mx-auto">
            <div class="guide-topbar d-flex align-items-center justify-content-between mb-4">
                <a href="javascript:history.back()" class="guide-icon-btn" aria-label="Back">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="text-center flex-grow-1 px-3">
                    <div class="guide-title">Event Guide</div>
                    <div class="guide-subtitle">{{ $event->title }}</div>
                </div>
                <span class="guide-icon-btn guide-icon-btn-static" aria-hidden="true">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>

            @if($guideSections->isEmpty() && $downloadGuides->isEmpty())
                <div class="guide-empty text-center">
                    <h2 class="h5 mb-2">No event guide items yet</h2>
                    <p class="mb-0">Add event guide entries from the admin panel to show them here.</p>
                </div>
            @endif

            @foreach($guideSections as $sectionTitle => $items)
                <div class="guide-block mb-4">
                    <h2 class="guide-section-title">{{ $sectionTitle }}</h2>
                    <div class="accordion guide-accordion" id="guide-accordion-{{ \Illuminate\Support\Str::slug($sectionTitle) }}">
                        @foreach($items as $guide)
                            @php
                                $collapseId = 'guide-item-' . $guide->id;
                                $hasAnswer = filled($guide->type);
                                $fileUrl = optional($guide->documentFile)->file_path;
                            @endphp
                            <div class="accordion-item guide-card">
                                <h3 class="accordion-header">
                                    @if($hasAnswer)
                                        <button class="accordion-button collapsed guide-question" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            <span class="guide-mark"><i class="fa-solid fa-circle-question"></i></span>
                                            <span>{{ $guide->title }}</span>
                                        </button>
                                    @else
                                        <div class="guide-question guide-question-static">
                                            <span class="guide-mark"><i class="fa-solid fa-circle-question"></i></span>
                                            <span>{{ $guide->title }}</span>
                                        </div>
                                    @endif
                                </h3>
                                @if($hasAnswer || $fileUrl || $guide->weblink)
                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse">
                                        <div class="accordion-body guide-answer">
                                            @if($hasAnswer)
                                                <p class="mb-3">{{ $guide->type }}</p>
                                            @endif
                                            @if($guide->weblink)
                                                <a href="{{ $guide->weblink }}" target="_blank" rel="noopener" class="guide-inline-link">Open Link</a>
                                            @endif
                                            @if($fileUrl)
                                                <div class="mt-2">
                                                    <a href="{{ $fileUrl }}" target="_blank" class="guide-download-chip">
                                                        <i class="fa-regular fa-file-lines"></i>
                                                        <span>{{ basename(parse_url($fileUrl, PHP_URL_PATH)) }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($downloadGuides->isNotEmpty())
                <div class="guide-block">
                    <h2 class="guide-section-title">Files to Download</h2>
                    <div class="guide-download-list">
                        @foreach($downloadGuides as $guide)
                            @php
                                $fileUrl = optional($guide->documentFile)->file_path;
                                $downloadUrl = $guide->weblink ?: $fileUrl;
                                $downloadLabel = $fileUrl ? basename(parse_url($fileUrl, PHP_URL_PATH)) : parse_url($guide->weblink, PHP_URL_HOST);
                            @endphp
                            @if($downloadUrl)
                                <div class="guide-download-card">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="guide-mark guide-mark-download"><i class="fa-solid fa-download"></i></span>
                                        <div>
                                            <div class="guide-download-title">{{ $guide->title }}</div>
                                            @if($guide->type)
                                                <div class="guide-download-text">{{ $guide->type }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="guide-download-arrow" aria-label="Download {{ $guide->title }}">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </div>
                                @if($downloadLabel)
                                    <div class="guide-file-chip-row">
                                        <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="guide-download-chip">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <span>{{ $downloadLabel }}</span>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.event-guide-page {
    background: linear-gradient(180deg, #f6f7fb 0%, #eef2f8 100%);
    min-height: 100vh;
}

.guide-shell {
    max-width: 760px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid rgba(38, 61, 107, 0.08);
    border-radius: 28px;
    box-shadow: 0 22px 60px rgba(31, 55, 102, 0.12);
    padding: 1.25rem;
    backdrop-filter: blur(10px);
}

.guide-topbar {
    border-bottom: 1px solid rgba(38, 61, 107, 0.1);
    padding-bottom: 1rem;
}

.guide-icon-btn {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef3ff;
    color: #1b2b4f;
    text-decoration: none;
    flex-shrink: 0;
}

.guide-icon-btn-static {
    pointer-events: none;
}

.guide-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1b2b4f;
}

.guide-subtitle {
    font-size: 0.88rem;
    color: #6d7890;
}

.guide-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #33415c;
    margin-bottom: 0.9rem;
}

.guide-card,
.guide-download-card {
    background: #fff;
    border: 1px solid rgba(38, 61, 107, 0.08);
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(31, 55, 102, 0.06);
}

.guide-card + .guide-card,
.guide-download-card + .guide-download-card {
    margin-top: 0.85rem;
}

.guide-question,
.guide-question-static {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    width: 100%;
    padding: 1rem 1.1rem;
    color: #24324a;
    font-weight: 600;
    background: transparent;
    box-shadow: none;
}

.guide-accordion .accordion-button:not(.collapsed) {
    background: transparent;
    color: #24324a;
    box-shadow: none;
}

.guide-accordion .accordion-button:focus {
    box-shadow: none;
}

.guide-accordion .accordion-item {
    overflow: hidden;
}

.guide-mark {
    width: 34px;
    height: 34px;
    border-radius: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2f6fff 0%, #69a1ff 100%);
    color: #fff;
    flex-shrink: 0;
}

.guide-mark-download {
    background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%);
}

.guide-answer {
    padding: 0 1.1rem 1.1rem 4rem;
    color: #6a7284;
}

.guide-inline-link {
    color: #315efb;
    text-decoration: none;
    font-weight: 600;
}

.guide-download-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.guide-download-card {
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.guide-download-title {
    font-weight: 700;
    color: #24324a;
}

.guide-download-text {
    margin-top: 0.2rem;
    color: #79839a;
    font-size: 0.92rem;
}

.guide-download-arrow {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f3f6ff;
    color: #315efb;
    text-decoration: none;
}

.guide-file-chip-row {
    padding-left: 1rem;
}

.guide-download-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    margin-top: 0.35rem;
    padding: 0.7rem 0.9rem;
    background: #edf3ff;
    border-radius: 999px;
    color: #315efb;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
}

.guide-empty {
    padding: 3rem 1rem;
    color: #6d7890;
}

@media (max-width: 575.98px) {
    .guide-shell {
        border-radius: 22px;
        padding: 1rem 0.85rem;
    }

    .guide-answer {
        padding-left: 1.1rem;
    }

    .guide-download-card {
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endpush

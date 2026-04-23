@extends('layouts.frontendapp')

@section('title', 'Gallery - ' . ($event->title ?? config('app.name')))

@section('content')
    <!-- Hero Section -->
    <section class="gallery-hero py-5 text-center" style="background: linear-gradient(rgba(0, 79, 184, 0.8), rgba(0, 79, 184, 0.8)), url('{{ $event->photo ? $event->photo->file_path : asset('frontend/images/banner-bg.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ route('user.front.events', $event->slug) }}" class="text-white opacity-75">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Gallery</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-bold text-white mb-3">Event Gallery</h1>
            <p class="lead text-white opacity-75 mx-auto" style="max-width: 700px;">
                Explore the unforgettable moments, industry leaders, and innovative showcases from {{ $event->title }}.
            </p>
        </div>
    </section>

    <section class="gallery-page-content py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <span class="small-heading-blue text-uppercase fw-bold letter-spacing-1">Media Collection</span>
                    <h2 class="h2-black mt-2">All Captured Media Items</h2>
                </div>
                <a href="{{ route('user.front.events', $event->slug) }}" class="heroBtn">
                    <i class="fas fa-chevron-left me-2"></i> Back to Event
                </a>
            </div>

            <div class="gallery-grid-wrapper">
                @if(isset($galleryItems) && count($galleryItems) > 0)
                <div class="row g-4">
                    @foreach($galleryItems as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="gallery-card-wrapper p-0">
                            <a href="{{ $item->file_path }}" target="_blank" class="gallery-main-link">
                                <div class="gallery-media-container shadow-sm" style="height: 240px;">
                                    @if(str_contains($item->file_type, 'video'))
                                    <div class="media-preview video-type">
                                        <video src="{{ $item->file_path }}" muted></video>
                                        <div class="play-overlay">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    </div>
                                    @elseif(str_contains($item->file_type, 'image'))
                                    <div class="media-preview image-type">
                                        <img src="{{ $item->file_path }}" alt="Gallery Item">
                                    </div>
                                    @else
                                    <div class="media-preview document-type">
                                        <div class="doc-icon-box" style="width: 50px; height: 50px; font-size: 24px;">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="doc-details">
                                            <small class="doc-name">{{ $item->file_name ? truncateString($item->file_name, 15) : 'Document' }}</small>
                                            <span class="doc-label text-uppercase" style="font-size: 0.7rem; color: var(--theme); font-weight: 700;">View PDF</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5 pt-4">
                    <div class="pagination-custom">
                        {{ $galleryItems->links() }}
                    </div>
                </div>
                @else
                <div class="py-5 text-center">
                    <div class="no-data-box p-5 rounded-4 bg-light border border-dashed text-secondary">
                        <i class="fas fa-images fa-3x mb-3"></i>
                        <p class="fs-5">No media items found in this collection.</p>
                        <a href="{{ route('user.front.events', $event->slug) }}" class="heroBtn mt-3 px-4 rounded-pill">Return to Event</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <style>
        .letter-spacing-1 { letter-spacing: 2px; }

        /* Card Styles simplified */
        .gallery-media-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #f0f0f0;
        }
        .media-preview img, .media-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Play Overlay */
        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--theme);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Document Placeholder */
        .document-type {
            background: #f0f6ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .doc-icon-box {
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--theme);
            margin-bottom: 10px;
            box-shadow: 0 5px 10px rgba(0, 79, 184, 0.05);
        }

        /* Pagination custom styling */
        .pagination-custom .pagination { gap: 8px; }
        .pagination-custom .page-link {
            border-radius: 10px;
            color: var(--theme);
            border: 1px solid #e0e0e0;
            padding: 8px 16px;
        }
        .pagination-custom .page-item.active .page-link {
            background-color: var(--theme);
            border-color: var(--theme);
            color: #fff;
        }
        
        .no-data-box { border-style: dashed !important; }
    </style>
@endsection

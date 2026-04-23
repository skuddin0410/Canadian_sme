    <!-- gallery -->
    <section class="gallery">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-7">
                    <span class="small-heading-blue text-uppercase fw-bold letter-spacing-1">Gallery</span>
                    <h2 class="h2-black mt-2">
                        Memorable Moments <br>Captured During Our Events
                    </h2>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <!-- Navigation Arrows (Matching Sponsors) -->
                    @if(isset($galleryItems) && count($galleryItems) > 0)
                    <div class="gallery-nav d-flex gap-3 align-items-center justify-content-lg-end mt-4 mt-lg-0">
                        <button class="gallery-prev white-circle">
                            <img src="{{asset('frontend/images/arrow-left.png')}}" alt="">
                        </button>
                        <button class="gallery-next white-circle">
                            <img src="{{asset('frontend/images/arrow-right.png')}}" alt="">
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                @if(isset($galleryItems) && count($galleryItems) > 0)
                <div class="swiper gallery-swiper pb-3">
                    <div class="swiper-wrapper">
                        @foreach($galleryItems as $item)
                        <div class="swiper-slide">
                            <div class="gallery-card-wrapper">
                                <a href="{{ $item->file_path }}" target="_blank" class="gallery-main-link">
                                    <div class="gallery-media-container shadow-sm">
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
                                            <div class="doc-icon-box">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="doc-details">
                                                <small class="doc-name">{{ $item->file_name ? truncateString($item->file_name, 25) : 'Document' }}</small>
                                                <span class="doc-label text-uppercase">View PDF</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="py-5 text-center">
                    <div class="no-data-box p-5 rounded-4 bg-light border border-dashed">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-secondary fs-5">No gallery items available for this event yet.</p>
                    </div>
                </div>
                @endif
            </div>

            @if(isset($galleryItems) && count($galleryItems) > 0)
            <div class="d-flex justify-content-center mt-2">
                 <a class="heroBtn btn-long px-5" href="{{ route('gallery-index', ['slug' => $event->slug]) }}">
                         View All
                 </a>
            </div>
            @endif
        </div>
    </section>

    <style>
        .gallery {
            padding: 100px 0;
            background-color: #f8fbff;
            overflow: hidden;
        }
        .letter-spacing-1 {
            letter-spacing: 2px;
        }

        /* Navigation Buttons Customization */
        .gallery .white-circle {
            border: 1.5px solid var(--theme) !important;
            transition: all 0.4s ease;
            cursor: pointer;
            width: 55px; /* Slightly smaller for elegance on light bg */
            height: 55px;
        }
        .gallery .white-circle img {
            width: 24px;
            transition: all 0.4s ease;
            /* Turns black image into #004fb8 (Theme Blue) */
            filter: invert(20%) sepia(91%) saturate(2283%) hue-rotate(204deg) brightness(97%) contrast(106%);
        }
        .gallery .white-circle:hover {
            background-color: var(--theme) !important;
        }
        .gallery .white-circle:hover img {
            filter: brightness(0) invert(1); /* Turns the blue arrow to white */
        }

        /* Card Wrapper */
        .gallery-card-wrapper {
            padding: 10px;
            height: 100%;
        }
        .gallery-media-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            height: 280px;
            border: 1px solid #f0f0f0;
        }

        /* Media Previews */
        .media-preview {
            width: 100%;
            height: 100%;
            position: relative;
        }
        .media-preview img, .media-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Video Play Icon */
        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--theme);
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Document Styling */
        .document-type {
            background: #f0f6ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
        }
        .doc-icon-box {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--theme);
            font-size: 28px;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .doc-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .doc-name {
            font-weight: 500;
            color: #333;
            display: block;
        }
        .doc-label {
            font-size: 0.8rem;
            color: var(--theme);
            font-weight: 700;
        }

        .no-data-box {
            border-style: dashed !important;
        }

        @media (max-width: 991px) {
            .h2-black { font-size: 32px; }
            .gallery { padding: 60px 0; }
            .white-circle { width: 60px; height: 60px; }
            .white-circle img { width: 30px; }
        }
    </style>
    <!-- gallery end -->

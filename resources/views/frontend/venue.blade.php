@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')
<div class="container py-4 py-lg-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3 p-sm-4">
          <div class="d-flex align-items-center mb-4">
            <div class="text-left mb-2" style="width:100%">

              <div class="venue-location my-4">
                <h4>{{ $title ?? 'Venue Location' }}</h4>

                @if(($section ?? '') === 'event-guide')
                  @if((isset($guideSections) && $guideSections->isNotEmpty()) || (isset($downloadGuides) && $downloadGuides->isNotEmpty()))
                    <div class="guide-content-shell">
                    @if(isset($guideSections))
                      @foreach($guideSections as $sectionTitle => $items)
                        <div class="guide-section-block mt-4">
                          <div class="guide-section-header">
                            <h5 class="guide-section-heading mb-0">{{ $sectionTitle }}</h5>
                          </div>
                          <div class="accordion guide-accordion" id="guide-accordion-{{ \Illuminate\Support\Str::slug($sectionTitle) }}">
                            @foreach($items as $guide)
                              @php
                                $collapseId = 'guide-item-' . $guide->id;
                                $fileUrl = optional($guide->documentFile)->file_path;
                                $isFirstGuide = $loop->first && $loop->parent->first;
                              @endphp
                              <div class="accordion-item guide-accordion-item">
                                <h2 class="accordion-header">
                                  <button class="accordion-button {{ $isFirstGuide ? '' : 'collapsed' }} guide-accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $isFirstGuide ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                    <span class="guide-question-wrap">
                                      <span class="guide-question-icon"><i class="fa-solid fa-circle-question"></i></span>
                                      <span class="guide-question-text">{{ $guide->title }}</span>
                                    </span>
                                  </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $isFirstGuide ? 'show' : '' }}">
                                  <div class="accordion-body">
                                    @if($guide->type)
                                      <div class="text-muted mb-2 mt-2">{{ $guide->type }}</div>
                                    @endif
                                    @if($guide->weblink)
                                      <a href="{{ $guide->weblink }}" target="_blank" rel="noopener" class="guide-link">Open Link</a>
                                    @endif
                                    @if($fileUrl)
                                      <div class="mt-2">
                                        <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="guide-file-link">
                                          View Document
                                        </a>
                                      </div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      @endforeach
                    @endif

                    @if(isset($downloadGuides) && $downloadGuides->isNotEmpty())
                      <div class="guide-section-block mt-4">
                        <div class="guide-section-header">
                          <h5 class="guide-section-heading mb-0">Files to Download</h5>
                        </div>
                        <div class="list-group">
                          @foreach($downloadGuides as $guide)
                            @php
                              $fileUrl = optional($guide->documentFile)->file_path;
                              $downloadUrl = $guide->weblink ?: $fileUrl;
                            @endphp
                            @if($downloadUrl)
                              <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3 mb-2 guide-download-row">
                                <span class="guide-download-wrap">
                                  <span class="guide-download-icon"><i class="fa-solid fa-download"></i></span>
                                  <span>
                                  <strong>{{ $guide->title }}</strong>
                                  @if($guide->type)
                                    <span class="d-block small text-muted mt-1">{{ $guide->type }}</span>
                                  @endif
                                  </span>
                                </span>
                                <i class="fas fa-angle-right text-primary"></i>
                              </a>
                            @endif
                          @endforeach
                        </div>
                      </div>
                    @endif
                    </div>
                  @else
                    <p>Event Guide not available.</p>
                  @endif
                @elseif(!empty($content))
                  @if(($section ?? 'location') === 'location')
                    <p class="mb-2">{{ $content }}</p>
                  @else
                    <div class="text-muted">{!! $content !!}</div>
                  @endif

                  @if(($showMap ?? true) && !empty($mapUrl))
                    <div class="ratio ratio-16x9" style="height:400px;">
                      <iframe
                        src="{{ $mapUrl }}"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                  @elseif(($showMap ?? true))
                    <p class="text-muted">Map not available.</p>
                  @endif


                @else
                  <p>{{ $title ?? 'Content' }} not available.</p>
                @endif
              </div>

            </div>
          </div>
        </div> 
      </div>
    </div>
        <div class="col-lg-4">
      @if(!empty($sessions))
      <div class="list-group shadow-sm rounded-4 mt-3 mt-lg-0">
        <h6 class="list-group-item bg-light fw-bold py-2">Upcoming Sessions</h6>
        @forelse($sessions as $session)
          <div class="list-group-item list-group-item-action d-sm-flex d-lg-block d-xxl-flex justify-content-between align-items-start">
            <div class="me-auto">
              <div class="black-text-18 fw-medium">{{ $session->title ?? 'Untitled Session' }}</div>
              <small class="text-secondary d-block mt-2">
                <i class="fas fa-clock me-1 text-primary"></i>
                {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y h:i A') }}
              </small>
              <small class="text-secondary d-block mt-2">
                <i class="fas fa-location me-1 text-primary"></i>
                {{ $session->location ?? '' }}
              </small>
            </div>
            
          </div>
        @empty
          <div class="list-group-item text-muted">No sessions available</div>
        @endforelse
      </div>
      @endif
      @if($event)
        <div class="card mt-3 shadow-sm rounded-4">
          <div class="card-body text-left">
            <h6 class="fw-bold">{{ $event->title ?? 'Event' }}</h6>
            @if(!empty($event->photo) && $event->photo->file_path)
              <img src="{{ $event->photo->file_path }}" alt="Event" class="img-fluid rounded mt-2">
            @else
               <img src="{{ asset('frontend/images/banner.png') }}" alt="Event" class="img-fluid rounded mt-2">
            @endif
            <p class="fw-bold text-muted mt-2">{{ $event->location ?? '' }}</p>
            <hr>
            <p class="text-muted mt-2">{!! $event->description ?? '' !!}</p>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.guide-section-block {
  margin-top: 1.5rem;
}

.guide-content-shell {
  margin-top: 1.25rem;
  padding: 1.25rem;
  border-radius: 24px;
  background:
    radial-gradient(circle at top left, rgba(67, 97, 238, 0.08), transparent 32%),
    linear-gradient(180deg, #f8fbff 0%, #f3f7fc 100%);
  border: 1px solid rgba(38, 61, 107, 0.08);
}

.guide-section-header {
  display: inline-flex;
  align-items: center;
  min-height: 36px;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  background: #eaf1ff;
  margin-bottom: 0.9rem;
}

.guide-section-heading {
  font-size: 1rem;
  font-weight: 700;
  color: #2a3e66;
  letter-spacing: 0.01em;
}

.guide-accordion-item {
  border: 1px solid rgba(38, 61, 107, 0.08);
  border-radius: 20px !important;
  overflow: hidden;
  margin-bottom: 0.85rem;
  box-shadow: 0 12px 30px rgba(31, 55, 102, 0.08);
  background: #fff;
}

.guide-accordion-button {
  font-weight: 600;
  color: #24324a;
  background: transparent;
  padding: 1rem 1.15rem;
}

.guide-accordion-button:not(.collapsed) {
  color: #24324a;
  background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
  box-shadow: none;
}

.guide-accordion-button:focus {
  box-shadow: none;
}

.guide-accordion-button::after {
  background-size: 1rem;
}

.guide-question-wrap {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding-right: 1rem;
}

.guide-question-icon {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2f6fff 0%, #69a1ff 100%);
  color: #fff;
  flex-shrink: 0;
  box-shadow: 0 10px 22px rgba(49, 94, 251, 0.25);
}

.guide-question-text {
  line-height: 1.45;
}

.guide-accordion .accordion-body {
  color: #6a7284;
  padding: 0 1.15rem 1.15rem 4rem;
}

.guide-link,
.guide-file-link {
  color: #315efb;
  text-decoration: none;
  font-weight: 600;
}

.guide-file-link {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.7rem 0.9rem;
  background: #edf3ff;
  border-radius: 999px;
}

.guide-download-row {
  border: 1px solid rgba(38, 61, 107, 0.08);
  border-radius: 20px !important;
  box-shadow: 0 10px 30px rgba(31, 55, 102, 0.06);
  padding: 1rem 1.15rem;
  background: #fff;
}

.guide-download-wrap {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
}

.guide-download-icon {
  width: 38px;
  height: 38px;
  border-radius: 13px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2f6fff 0%, #69a1ff 100%);
  color: #fff;
  flex-shrink: 0;
  box-shadow: 0 10px 22px rgba(49, 94, 251, 0.2);
}

.guide-download-row strong {
  color: #24324a;
}

.guide-download-row .text-muted {
  color: #79839a !important;
}

@media (max-width: 575.98px) {
  .guide-content-shell {
    padding: 0.9rem;
    border-radius: 20px;
  }

  .guide-accordion .accordion-body {
    padding-left: 1.15rem;
  }
}
</style>
@endpush

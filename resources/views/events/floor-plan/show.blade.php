@extends('layouts.admin')

@section('title', 'View Floor Plan')

@php
    $mapImage = !empty($event->mapImage) && !empty($event->mapImage->file_path) ? asset($event->mapImage->file_path) : null;
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <div>
            <h4 class="mb-1">Floor Plan Preview</h4>
            <div class="text-muted">{{ $event->title }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.floor-plan.edit', $event) }}" class="btn btn-primary">Edit Floor Plan</a>
            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">Back To Event</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($mapImage)
                <div class="floor-plan-preview">
                    <img src="{{ $mapImage }}" alt="Event map" class="floor-plan-preview-image">
                    <div class="floor-plan-preview-layer">
                        @foreach($markers as $marker)
                            <div class="floor-plan-preview-marker"
                                 style="left: {{ $marker->x_percent }}%; top: {{ $marker->y_percent }}%; width: {{ $marker->width_percent }}%; height: {{ $marker->height_percent }}%; background: {{ $marker->color }};">
                                <div class="marker-title">{{ $marker->label }}</div>
                                @if($marker->booth)
                                    <div class="marker-meta">{{ $marker->booth->booth_number }}</div>
                                @endif
                                @if($marker->company)
                                    <div class="marker-meta">{{ $marker->company->name }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">No map image available for this event.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.floor-plan-preview {
    position: relative;
    width: 100%;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #dfe3eb;
}
.floor-plan-preview-image {
    width: 100%;
    display: block;
}
.floor-plan-preview-layer {
    position: absolute;
    inset: 0;
}
.floor-plan-preview-marker {
    position: absolute;
    color: #fff;
    border-radius: 12px;
    padding: 8px 10px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}
.marker-title {
    font-weight: 700;
    font-size: 12px;
}
.marker-meta {
    font-size: 11px;
    opacity: 0.95;
}
</style>
@endsection

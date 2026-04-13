@extends('layouts.admin')

@section('title', 'Edit Floor Plan')

@php
    $mapImage = !empty($event->mapImage) && !empty($event->mapImage->file_path) ? asset($event->mapImage->file_path) : null;
    $markerPayload = $markers->map(function ($marker) {
        return [
            'id' => $marker->id,
            'label' => $marker->label,
            'booth_id' => $marker->booth_id,
            'company_id' => $marker->company_id,
            'x_percent' => (float) $marker->x_percent,
            'y_percent' => (float) $marker->y_percent,
            'width_percent' => (float) $marker->width_percent,
            'height_percent' => (float) $marker->height_percent,
            'color' => $marker->color,
        ];
    })->values();
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <div>
            <h4 class="mb-1">Floor Plan Editor</h4>
            <div class="text-muted">{{ $event->title }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.floor-plan.show', $event) }}" class="btn btn-outline-primary">View Map</a>
            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">Back To Event</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(!$mapImage)
        <div class="alert alert-warning">
            This event does not have a map image yet. Upload the map image in the event edit page first.
        </div>
    @endif

    <form method="POST" action="{{ route('events.floor-plan.update', $event) }}" id="floor-plan-form">
        @csrf
        <input type="hidden" name="markers" id="markers-input">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Map Canvas</h5>
                        <div class="text-muted small">Click on the map to add a booth marker, then drag it into place.</div>
                    </div>
                    <div class="card-body">
                        <div class="floor-plan-stage {{ $mapImage ? '' : 'is-disabled' }}" id="floor-plan-stage">
                            @if($mapImage)
                                <img src="{{ $mapImage }}" alt="Event map" class="floor-plan-image">
                            @else
                                <div class="floor-plan-empty">No map image available</div>
                            @endif
                            <div class="floor-plan-markers" id="floor-plan-markers"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Markers</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="add-marker-btn">Add Marker</button>
                    </div>
                    <div class="card-body">
                        <div id="marker-list" class="marker-list"></div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Save Floor Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stage = document.getElementById('floor-plan-stage');
    const markersLayer = document.getElementById('floor-plan-markers');
    const markerList = document.getElementById('marker-list');
    const markersInput = document.getElementById('markers-input');
    const addMarkerButton = document.getElementById('add-marker-btn');
    const stageImage = stage.querySelector('.floor-plan-image');
    const hasMap = !stage.classList.contains('is-disabled');

    const booths = @json($booths);
    const companies = @json($companies);
    let markers = @json($markerPayload);

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    function getMarkerBounds(marker) {
        return {
            maxX: Math.max(0, 100 - Number(marker.width_percent || 0)),
            maxY: Math.max(0, 100 - Number(marker.height_percent || 0)),
        };
    }

    function normalizeMarkerPosition(marker) {
        const { maxX, maxY } = getMarkerBounds(marker);
        marker.x_percent = clamp(Number(marker.x_percent || 0), 0, maxX);
        marker.y_percent = clamp(Number(marker.y_percent || 0), 0, maxY);
    }

    function getStageRect() {
        return hasMap && stageImage ? stageImage.getBoundingClientRect() : stage.getBoundingClientRect();
    }

    function savePayload() {
        markersInput.value = JSON.stringify(markers.map((marker, index) => ({
            ...marker,
            sort_order: index,
        })));
    }

    function render() {
        markersLayer.innerHTML = '';
        markerList.innerHTML = '';

        markers.forEach((marker, index) => {
            const markerNode = document.createElement('button');
            markerNode.type = 'button';
            markerNode.className = 'map-marker';
            markerNode.style.left = marker.x_percent + '%';
            markerNode.style.top = marker.y_percent + '%';
            markerNode.style.width = marker.width_percent + '%';
            markerNode.style.height = marker.height_percent + '%';
            markerNode.style.background = marker.color || '#4361ee';
            markerNode.textContent = marker.label || ('Booth ' + (index + 1));
            markerNode.dataset.index = index;
            markersLayer.appendChild(markerNode);

            const panel = document.createElement('div');
            panel.className = 'marker-panel';
            panel.innerHTML = `
                <div class="marker-panel-header">
                    <strong>Marker ${index + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-remove="${index}">Remove</button>
                </div>
                <div class="mb-2">
                    <label class="form-label">Label</label>
                    <input type="text" class="form-control" data-field="label" data-index="${index}" value="${marker.label || ''}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Booth</label>
                    <select class="form-select" data-field="booth_id" data-index="${index}">
                        <option value="">Select booth</option>
                        ${booths.map(booth => `<option value="${booth.id}" ${String(marker.booth_id ?? '') === String(booth.id) ? 'selected' : ''}>${booth.booth_number} - ${booth.title ?? 'Booth'}</option>`).join('')}
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Company</label>
                    <select class="form-select" data-field="company_id" data-index="${index}">
                        <option value="">Select company</option>
                        ${companies.map(company => `<option value="${company.id}" ${String(marker.company_id ?? '') === String(company.id) ? 'selected' : ''}>${company.name}</option>`).join('')}
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Width %</label>
                        <input type="number" min="4" max="40" step="0.5" class="form-control" data-field="width_percent" data-index="${index}" value="${marker.width_percent}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Height %</label>
                        <input type="number" min="4" max="40" step="0.5" class="form-control" data-field="height_percent" data-index="${index}" value="${marker.height_percent}">
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label">Color</label>
                    <input type="color" class="form-control form-control-color" data-field="color" data-index="${index}" value="${marker.color || '#4361ee'}">
                </div>
                <div class="mt-2 text-muted small">Position: ${marker.x_percent.toFixed(1)}%, ${marker.y_percent.toFixed(1)}%</div>
            `;
            markerList.appendChild(panel);
        });

        savePayload();
        bindDragging();
        bindPanelEvents();
    }

    function addMarker(x = 10, y = 10) {
        const marker = {
            id: null,
            label: 'Booth ' + (markers.length + 1),
            booth_id: '',
            company_id: '',
            x_percent: Number(x),
            y_percent: Number(y),
            width_percent: 12,
            height_percent: 8,
            color: '#4361ee',
        };
        normalizeMarkerPosition(marker);
        markers.push(marker);
        render();
    }

    function bindPanelEvents() {
        markerList.querySelectorAll('[data-field]').forEach((input) => {
            input.addEventListener('input', function () {
                const index = Number(this.dataset.index);
                const field = this.dataset.field;
                let value = this.value;
                if (field === 'width_percent' || field === 'height_percent') {
                    value = clamp(parseFloat(value || 0), 4, 40);
                }
                markers[index][field] = value;
                if (field === 'width_percent' || field === 'height_percent' || field === 'x_percent' || field === 'y_percent') {
                    normalizeMarkerPosition(markers[index]);
                }
                render();
            });
        });

        markerList.querySelectorAll('[data-remove]').forEach((button) => {
            button.addEventListener('click', function () {
                markers.splice(Number(this.dataset.remove), 1);
                render();
            });
        });
    }

    function bindDragging() {
        markersLayer.querySelectorAll('.map-marker').forEach((node) => {
            let dragging = false;

            node.addEventListener('mousedown', function (event) {
                event.preventDefault();
                dragging = true;
                const index = Number(node.dataset.index);
                const rect = getStageRect();
                const marker = markers[index];
                const offsetX = ((event.clientX - rect.left) / rect.width) * 100 - Number(marker.x_percent);
                const offsetY = ((event.clientY - rect.top) / rect.height) * 100 - Number(marker.y_percent);

                function move(moveEvent) {
                    if (!dragging) return;
                    const x = ((moveEvent.clientX - rect.left) / rect.width) * 100 - offsetX;
                    const y = ((moveEvent.clientY - rect.top) / rect.height) * 100 - offsetY;
                    markers[index].x_percent = x;
                    markers[index].y_percent = y;
                    normalizeMarkerPosition(markers[index]);
                    render();
                }

                function up() {
                    dragging = false;
                    document.removeEventListener('mousemove', move);
                    document.removeEventListener('mouseup', up);
                }

                document.addEventListener('mousemove', move);
                document.addEventListener('mouseup', up);
            });
        });
    }

    if (hasMap) {
        stage.addEventListener('click', function (event) {
            if (event.target.closest('.map-marker') || event.target.closest('#add-marker-btn')) return;
            const rect = getStageRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;
            addMarker(x, y);
        });
    }

    addMarkerButton.addEventListener('click', function () {
        addMarker();
    });

    render();
});
</script>
<style>
.floor-plan-stage {
    position: relative;
    width: 100%;
    border: 1px solid #dfe3eb;
    border-radius: 16px;
    overflow: hidden;
    background: #f8fafc;
}
.floor-plan-stage.is-disabled {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 560px;
}
.floor-plan-image {
    width: 100%;
    display: block;
}
.floor-plan-empty {
    color: #64748b;
    font-weight: 600;
}
.floor-plan-markers {
    position: absolute;
    inset: 0;
}
.map-marker {
    position: absolute;
    border: none;
    color: #fff;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 8px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
    cursor: move;
    overflow: hidden;
}
.marker-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    max-height: 680px;
    overflow: auto;
}
.marker-panel {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
    background: #fff;
}
.marker-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
</style>
@endsection

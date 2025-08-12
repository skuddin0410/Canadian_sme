@extends('layouts.admin')

@section('title', 'Event Calendar')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary  py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="h3 mb-0 mt-2">{{ $event->title }} - Calendar</h1>
                            <p class="mb-0">
                                {{ $event->start_date->format('M j') }} - {{ $event->end_date->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="col-auto">
                             <button class="btn btn-light btn-sm" id="addSessionBtn">
                                <i class="fas fa-plus me-1"></i> Add Session
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Toolbar -->
                    <div class="border-bottom bg-light py-3 px-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="calendarViewBtn">
                                        <i class="fas fa-calendar me-1"></i> Calendar
                                    </button>
                                 <button type="button" class="btn btn-outline-primary" id="gridViewBtn">
                                        <i class="fas fa-th-list me-1"></i> Grid
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="listViewBtn">
                                        <i class="fas fa-list me-1"></i> List
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 no-padding">
                                <div class="row g-2">
                                    <div class="col-auto">
                                     <select class="form-select form-select-sm" id="venueFilter">
                                            <option value="">All Booth</option>
                                            @foreach($booths as $booth)
                                                <option value="{{ $booth->id }}">{{ $booth->title }} {{ $booth->booth_number}}(Size: {{$booth->size}})</option>
                                            @endforeach 
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm" id="statusFilter">
                                            <option value="">All Status</option>
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Track Legend -->
                    {{-- @if($event->tracks->count() > 0)
                    <div class="px-4 py-2 bg-light border-bottom">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($event->tracks as $track)
                                <span class="badge badge-track d-flex align-items-center gap-1">
                                    <span class="track-color-dot" style="background-color: {{ $track->color }}"></span>
                                    {{ $track->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif --}}

                    <!-- Calendar View -->
                    <div id="calendarView" class="p-4">
                        <div id="calendar"></div>
                    </div>

                    <!-- Grid View -->
                    <div id="gridView" class="p-4" style="display: none;">
                        <div id="scheduleGrid"></div>
                    </div>

                    <!-- List View -->
                    <div id="listView" class="p-4" style="display: none;">
                        <div id="sessionsList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Modal -->
<div class="modal fade" id="sessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="sessionForm">
                @csrf
                <input type="hidden" id="sessionId" name="session_id">
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <div class="modal-body">
                    <div id="alertContainer"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="sessionTitle" class="form-label">Session Title *</label>
                            <input type="text" class="form-control" id="sessionTitle" name="title" required>
                        </div>

                        <div class="col-md-6">
                            <label for="startTime" class="form-label">Start Time *</label>
                            <input type="datetime-local" class="form-control" id="startTime" name="start_time" required>
                        </div>

                        <div class="col-md-6">
                            <label for="endTime" class="form-label">End Time *</label>
                            <input type="datetime-local" class="form-control" id="endTime" name="end_time" required>
                        </div>

                        <div class="col-md-6">
                            <label for="sessionType" class="form-label">Session Type *</label>
                            <select class="form-select" id="sessionType" name="type" required>
                                <option value="presentation">Presentation</option>
                                <option value="workshop">Workshop</option>
                                <option value="panel">Panel Discussion</option>
                                <option value="break">Break</option>
                                <option value="networking">Networking</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1">
                        </div>

                         {{-- <div class="col-md-6">
                            <label for="trackSelect" class="form-label">Track</label>
                           <select class="form-select" id="trackSelect" name="track_id">
                                <option value="">No Track</option>
                                @foreach($event->tracks as $track)
                                    <option value="{{ $track->id }}">{{ $track->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-md-6">
                            <label for="venueSelect" class="form-label">Venue</label>
                            <select class="form-select" id="venueSelect" name="booth_id">
                               <option value="">Select Booth</option>
                                    @foreach($booths as $booth)
                                        <option value="{{ $booth->id }}">{{ $booth->title }} {{ $booth->booth_number}}(Size: {{$booth->size}})</option>
                                    @endforeach 
                                </select>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div> 
                        <!-- Speaker Assignment -->
                        <div class="col-12">
                            <label class="form-label">Speakers</label>
                            <div id="speakerSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="speakerSelect">
                                        <option value="">Select a speaker...</option>
                                         @foreach($speakers as $speaker)
                                          <option value="{{ $speaker->id }}">{{ $speaker->full_name }}({{ $speaker->email}}, {{$speaker->mobile ?? ''}})</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addSpeakerBtn">Add</button>
                                </div>
                                 <div id="selectedSpeakers"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn" style="display: none;">Delete</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Session Details Modal -->
<div class="modal fade" id="sessionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailsTitle">Session Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sessionDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editSessionBtn">Edit Session</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Laravel data injection
    window.calendarConfig = {
        eventId: {{ $event->id }},
        eventName: '{{ $event->title }}',
        eventStart: "{{ $event->start_date->format('Y-m-d') }}",
        eventEnd:  "{{ $event->end_date->copy()->addDay()->format('Y-m-d') }}",
        timezone: '{{ config("app.timezone")}}',
        {{-- tracks: @json($event->tracks),
        venues: @json($event->venues), --}}
        apiUrls: {
            sessions: '{{ route('calendar.sessions') }}',
            createSession: '{{ route('calendar.sessions.store') }}',
            updateSession: '{{ route('calendar.sessions.update', ':id') }}',
            deleteSession: '{{ route('calendar.sessions.destroy', ':id') }}',
            speakers: '{{ route('speakers.list') }}'
        },
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection

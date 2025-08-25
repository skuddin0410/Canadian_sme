@extends('layouts.admin')

@section('title')
    Admin | Event Calendar
@endsection


@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<div class="container">
     <h4 class="py-3 mb-4"><span class="text-muted fw-light">Event/</span>Calendar</h4>
    <div class="row">
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
                             <button class="btn btn-primary btn-sm" id="addSessionBtn">
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
                                    <!--<div class="col-auto">
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
                                    </div>-->
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTitle">Add New Session</h5>
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

                        <div class="col-6">
                            <label for="sessionTitle" class="form-label">Session Location *</label>
                            <input type="text" class="form-control" id="sessionTitle" name="location" required>
                        </div>

                        <div class="col-md-6">
                            <label for="venueSelect" class="form-label">Booth </label>
                            <select class="form-select" id="venueSelect" name="booth_id">
                               <option value="">Select Booth</option>
                                    @foreach($booths as $booth)
                                        <option value="{{ $booth->id }}">{{ $booth->title }} {{ $booth->booth_number}}(Size: {{$booth->size}})</option>
                                    @endforeach 
                                </select>
                            </select>
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
                        <div class="col-md-6">
                            <label for="tracks" class="form-label">Tracks *</label>
                            <select class="form-select" id="tracks" name="type" required>
                                <option value="presentation">Presentation</option>
                                <option value="workshop">Workshop</option>
                                <option value="panel">Panel Discussion</option>
                                <option value="break">Break</option>
                                <option value="networking">Networking</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div> 
                        <!-- Speaker Assignment -->
                        <div class="col-6">
                            <label class="form-label">Speakers *</label>
                            <div id="speakerSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="speakerSelect" required>
                                        <option value="">Select a speaker...</option>
                                         @foreach($speakers as $speaker)
                                          <option value="{{ $speaker->id }}">{{ $speaker->full_name }}</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addSpeakerBtn">Add</button>
                                </div>
                                 <div id="selectedSpeakers"></div>
                            </div>
                        </div>

                         <div class="col-6">
                            <label class="form-label">Exhibitors *</label>
                            <div id="exhibitorSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="exhibitorSelect" required>
                                        <option value="">Select a exhibitor...</option>
                                         @foreach($exhibitors as $exhibitor)
                                          <option value="{{ $exhibitor->id }}">{{ $exhibitor->full_name }}</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addExhibitorBtn">Add</button>
                                </div>
                                 <div id="selectedExhibitors"></div>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Sponsors</label>
                            <div id="SponsorSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="sponsorSelect">
                                        <option value="">Select a sponsors...</option>
                                         @foreach($sponsors as $sponsor)
                                          <option value="{{ $sponsor->id }}">{{ $sponsor->full_name }}</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addSponsorBtn">Add</button>
                                </div>
                                 <div id="selectedSponsors"></div>
                            </div>
                        </div>

                        <div class="col-6">
                            @php
                                $calendarColors = [
                                    '#FF5733', // Red-Orange
                                    '#33C1FF', // Sky Blue
                                    '#28A745', // Green
                                    '#FFC107', // Amber
                                    '#6F42C1', // Purple
                                    '#E83E8C', // Pink
                                    '#20C997', // Teal
                                    '#FD7E14', // Orange
                                    '#17A2B8', // Cyan
                                    '#343A40', // Dark Gray
                                ];
                            @endphp
                            <label class="form-label">Color</label>
                            <div>
                                <div class="input-group mb-2">
                                    <select class="form-select" id="sponsorSelect">
                                        <option value="">Select a sponsors...</option>
                                         @foreach($calendarColors as $color)
                                          <option value="{{ $color}}">{{ $color}}</option>
                                         @endforeach 
                                       
                                    </select>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description2" name="description" rows="3"></textarea>
                        </div>

                        <div class="col-6">
                            <label for="keynote" class="form-label">Keynote</label>
                            <textarea class="form-control" id="keynote2" name="keynote" rows="3"></textarea>
                        </div>

                        <div class="col-6">
                            <label for="panels" class="form-label">Panels</label>
                            <textarea class="form-control" id="panels2" name="panels" rows="3"></textarea>
                        </div>

                        <div class="col-6">
                            <label for="demoes" class="form-label">Demoes</label>
                            <textarea class="form-control" id="demoes2" name="demoes" rows="3"></textarea>
                        </div>

                         <div class="col-md-6">
                             <input class="form-check-input" type="checkbox" id="featuredCheck" name="is_featured" value="1">
                            <label class="form-check-label" for="featuredCheck">
                                Marked as Feature
                            </label>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="detailsTitle">Session Details</h5>
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
        {{-- tracks: @json($event->tracks), --}}
        venues: @json($event->venues),
        apiUrls: {
            sessions: '{{ route('calendar.sessions') }}',
            createSession: '{{ route('calendar.sessions.store') }}',
            updateSession: '{{ route('calendar.sessions.update', ':id') }}',
            deleteSession: '{{ route('calendar.sessions.destroy', ':id') }}',
            speakers: '{{ route('speakers.list') }}',
            exhibitors: '{{ route('exhibitors.list') }}',
            sponsors: '{{ route('sponsors.list') }}'
        },
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection

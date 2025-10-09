@extends('layouts.admin')

@section('title')
    Admin | Event Calendar
@endsection


@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<style>
  .dz{
    position:relative; width:100%; height:220px;
    border:2px dashed #c9ced6; border-radius:12px;
    background:#f8f9fb; display:flex; align-items:center; justify-content:center;
    cursor:pointer; overflow:hidden; transition:border-color .2s, background .2s;
  }
  .dz:hover{ border-color:#0d6efd22; background:#f3f7ff; }
  .dz.dz-over{ border-color:#0d6efd; background:#eaf3ff; }

  .dz-placeholder{ text-align:center; color:#6c757d; pointer-events:none; }
  .dz-icon{ font-size:2rem; line-height:1; margin-bottom:.25rem; }
  .dz-text{ font-size:.9rem; }
  .dz-image{
    position:absolute; inset:0; width:100%; height:100%;
    object-fit:cover; border-radius:10px;
  }
  #dz-remove{
    position:absolute; top:.5rem; right:.5rem; z-index:2;
    padding:.25rem .5rem;
    display:none;
  }
  
  .dz img:not(.d-none) ~ #dz-remove{ display:block !important; }
  .dz img:not(.d-none) ~ .dz-placeholder{ display:none; }

  .event-tooltip .tooltip-inner {
    text-align: left;
    font-size: 1rem;       /* Increase text size */
    line-height: 1.6;      /* More spacing between lines */
    padding: 12px 16px;    /* Bigger padding inside tooltip */
    max-width: 300px;      /* Wider tooltip */
}

.event-tooltip .tooltip {
    font-weight: 500;
}
</style>
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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventTrackModal">
                              Add Event Track
                            </button>
                             <button class="btn btn-primary" id="addSessionBtn">
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
                                    
                                </div>
                            </div>
                        </div>
                    </div>

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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTitle">Add New Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="sessionForm" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" id="sessionId" name="session_id">
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <div class="modal-body">
                    <div id="alertContainer"></div>

                    <div class="row g-3">

                    <div class="col-12">
                      <div id="profile-dropzone" class="dz">
                          
                          <div class="dz-placeholder">
                            <div class="dz-text"><strong>Drag & drop</strong> or click to upload</div>
                          </div>

                          <!-- Preview -->
                          <img id="profileImagePreview"
                               src="{{ !empty($user->photo) ? $user->photo->file_path : '' }}"
                               class="dz-image {{ !empty($user->photo) ? '' : 'd-none' }}"
                               alt="Profile preview" />

                          
                              <button type="button" id="dz-remove" class="btn btn-sm btn-danger d-none" aria-label="Remove image" data-photoid="">
                                Remove
                              </button>

                          <!-- Hidden file input (kept for form submission) -->
                          <input type="file" id="profileImageInput" name="image" accept="image/*" value="" class="d-none">
                        </div>

                         <p class="text-muted mt-2">JPG/PNG recommended. Square works best.(<span class="text-danger">1920px (width) x 1081px (height)</span>)</p>
                    </div>

                        <div class="col-6">
                            <label for="sessionTitle" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="sessionTitle" name="title" required>
                        </div>

                        <div class="col-6">
                            <label for="sessionTitle" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="venueSelect" name="location" required>
                        </div>

                        <div class="col-md-6">
                            <label for="tracks" class="form-label">Tracks *</label>
                            <select class="form-select" id="tracks" name="track" required>
                                @if(!empty($tracks))
                                @foreach($tracks as  $track)
                                <option value="{{$track->name}}">{{$track->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            <div class="mt-1">
                             <input class="form-check-input" type="checkbox" id="featuredCheck" name="is_featured" value="1">
                                <label class="form-check-label" for="featuredCheck">
                                    Marked as Feature
                                </label>
                             </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
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


                      {{--   <div class="col-md-6">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1">
                        </div> --}}
                     

                        <!-- Speaker Assignment -->
                        <div class="col-4">
                            <label class="form-label">Choose Speakers *</label>
                            <div id="speakerSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="speakerSelect">
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

                         <div class="col-4">
                            <label class="form-label">Link Exhibitors </label>
                            <div id="exhibitorSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="exhibitorSelect">
                                        <option value="">Select a exhibitor...</option>
                                         @foreach($exhibitors as $exhibitor)
                                          <option value="{{ $exhibitor->id }}">{{ $exhibitor->name }}</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addExhibitorBtn">Add</button>
                                </div>
                                 <div id="selectedExhibitors"></div>
                            </div>
                        </div>

                        <div class="col-4">
                            <label class="form-label">Link Sponsors</label>
                            <div id="SponsorSelection">
                                <div class="input-group mb-2">
                                    <select class="form-select" id="sponsorSelect">
                                        <option value="">Select a sponsors...</option>
                                         @foreach($sponsors as $sponsor)
                                          <option value="{{ $sponsor->id }}">{{ $sponsor->name }}</option>
                                         @endforeach 
                                       
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="addSponsorBtn">Add</button>
                                </div>
                                 <div id="selectedSponsors"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description2" name="description" rows="3" maxlength="1000"></textarea>
                        </div>

                        <div class="col-6 d-none">
                            <label for="keynote" class="form-label">Keynote</label>
                            <textarea class="form-control" id="keynote2" name="keynote" rows="3" maxlength="1000"></textarea>
                        </div>

                        <div class="col-6  d-none">
                            <label for="panels" class="form-label">Panels</label>
                            <textarea class="form-control" id="panels2" name="panels" rows="3" maxlength="1000"></textarea>
                        </div>

                        <div class="col-6 d-none">
                            <label for="demoes" class="form-label">Demoes</label>
                            <textarea class="form-control" id="demoes2" name="demoes" rows="3" maxlength="1000"></textarea>
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


<div class="modal fade" id="eventTrackModal" tabindex="-1" aria-labelledby="eventTrackModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventTrackModalLabel">Add Event Track</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('event-tracks.store') }}" method="POST">
            @csrf
            <div class="modal-body">
              <!-- Event Track Name Field -->
              <div class="mb-3">
                <label for="name" class="form-label">Event Track Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save Track</button>
            </div>
          </form>
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
<script src="{{ asset('js/calendar.js') }}?v={{ time() }}"></script>

<script>
  (function () {
    const dz = document.getElementById('profile-dropzone');
    const input = document.getElementById('profileImageInput');
    const preview = document.getElementById('profileImagePreview');
    const removeBtn = document.getElementById('dz-remove');
    const placeholder = dz.querySelector('.dz-placeholder');

    // Click anywhere to open file picker (except when clicking Remove)
    dz.addEventListener('click', (e) => {
      if (e.target !== removeBtn) input.click();
    });

    // Drag & drop
    dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dz-over'); });
    dz.addEventListener('dragleave', () => dz.classList.remove('dz-over'));
    dz.addEventListener('drop', (e) => {
      e.preventDefault(); dz.classList.remove('dz-over');
      const file = e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) setFile(file);
    });

    // Browse
    input.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) setFile(file);
    });

    // Remove
    removeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      clearSelection();
    });

    function setFile(file){
      if (!file.type.startsWith('image/')) return;
      const dt = new DataTransfer();
      const reader = new FileReader();
      reader.onload = (ev) => {
        preview.src = ev.target.result;
        preview.classList.remove('d-none');
        removeBtn.classList.remove('d-none');
        placeholder.style.display = 'none';

        document.getElementById('profileImageInput').files = dt.files;
      };
      reader.readAsDataURL(file);

      // keep file in input so it submits with the form
      
      dt.items.add(file);
      input.files = dt.files;

    }

    function clearSelection(){
      preview.src = '';
      preview.classList.add('d-none');
      removeBtn.classList.add('d-none');
      placeholder.style.display = '';
      input.value = ''; // clears chosen file (no server call)
    }

    // If there is an initial image, show remove button; otherwise show placeholder
    if (preview.src && preview.src.trim() !== '') {
      removeBtn.classList.remove('d-none');
      placeholder.style.display = 'none';
    }
  })();
</script>
@endsection

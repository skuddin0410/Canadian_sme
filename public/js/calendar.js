class LaravelEventCalendar {
    constructor() {
        this.calendar = null;
        this.currentView = 'calendar';
        this.events = [];
        this.speakers = [];
        this.selectedSpeakers = [];
        this.currentSession = null;
        this.isEditing = false;
        this.config = window.calendarConfig || {};
        this.init();
    }

    init() {
        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar library not loaded');
            return;
        }
        
        this.initializeCalendar();
        this.bindEvents();
        this.loadSpeakers();
        this.loadSessions();
    }

    initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        
        this.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            validRange: {
                start: this.config.eventStart,
                end: this.config.eventEnd
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            height: 'auto',
            slotMinTime: '06:00:00',
            slotMaxTime: '23:00:00',
            slotDuration: '00:15:00',
            snapDuration: '00:15:00',
            editable: true,
            selectable: true,
            selectMirror: true,
            eventResizableFromStart: true,
            nowIndicator: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5, 6, 0],
                startTime: '08:00',
                endTime: '18:00'
            },
            
            eventClick: (info) => {
                this.showSessionDetails(info.event);
            },
            
            select: (info) => {
                this.openSessionModal({
                    start: info.start,
                    end: info.end
                });
                this.calendar.unselect();
            },
            
            eventDrop: (info) => {
                this.updateSessionTime(info.event, info.event.start, info.event.end);
            },
            
            eventResize: (info) => {
                this.updateSessionTime(info.event, info.event.start, info.event.end);
            },
            
            loading: (isLoading) => {
                this.showCalendarLoading(isLoading);
            },
            
            eventDidMount: (info) => {
                // Add custom styling based on session type
                const sessionType = info.event.extendedProps.type;
                info.el.classList.add(`session-type-${sessionType}`);
                
                // Add tooltip
                info.el.title = info.event.extendedProps.description || info.event.title;
            }
        });

        this.calendar.render();
    }

    bindEvents() {
        // View controls
        document.getElementById('calendarViewBtn')?.addEventListener('click', () => {
            this.switchView('calendar');
        });
        
        document.getElementById('gridViewBtn')?.addEventListener('click', () => {
            this.switchView('grid');
        });
        
        document.getElementById('listViewBtn')?.addEventListener('click', () => {
            this.switchView('list');
        });

        // Add session button
        document.getElementById('addSessionBtn')?.addEventListener('click', () => {
            this.openSessionModal();
        });

        // Modal events
        const sessionModal = new bootstrap.Modal(document.getElementById('sessionModal'));
        const detailsModal = new bootstrap.Modal(document.getElementById('sessionDetailsModal'));
        
        document.getElementById('editSessionBtn')?.addEventListener('click', () => {
            if (this.currentSession) {
                detailsModal.hide();
                this.editSession(this.currentSession.id);
            }
        });

        // Form submission
        document.getElementById('sessionForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveSession();
        });

        // Delete button
        document.getElementById('deleteBtn')?.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this session?')) {
                this.deleteSession();
            }
        });

        // Filters
        document.getElementById('trackFilter')?.addEventListener('change', (e) => {
            this.filterByTrack(e.target.value);
        });
        
        document.getElementById('venueFilter')?.addEventListener('change', (e) => {
            this.filterByVenue(e.target.value);
        });
        
        document.getElementById('statusFilter')?.addEventListener('change', (e) => {
            this.filterByStatus(e.target.value);
        });

        // Speaker management
        document.getElementById('addSpeakerBtn')?.addEventListener('click', () => {
            this.addSelectedSpeaker();
        });

        // Time field auto-calculation
        document.getElementById('startTime')?.addEventListener('change', (e) => {
            this.autoCalculateEndTime(e.target.value);
        });
    }

    async loadSessions() {
        try {
            this.showLoading(true);
            
            const response = await this.apiCall('GET', this.config.apiUrls.sessions, {
                event_id: this.config.eventId
            });
            
            this.events = response.data || response;
            this.updateCalendarEvents();
            
            if (this.currentView === 'grid') {
                this.renderGridView();
            } else if (this.currentView === 'list') {
                this.renderListView();
            }
            
        } catch (error) {
            console.error('Error loading sessions:', error);
            this.showAlert('Error loading sessions', 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async loadSpeakers() {
        try {
            const response = await this.apiCall('GET', this.config.apiUrls.speakers);
            this.speakers = response.data || response;
            this.populateSpeakerSelect();
        } catch (error) {
            console.error('Error loading speakers:', error);
        }
    }

    updateCalendarEvents() {
        if (!this.calendar) return;
        this.calendar.removeAllEvents();
        
        const events = this.events.map(item => {
            const session = item; // or item itself if not nested
            return {
                id: session.id,
                title: session.title,
                start: session.start,
                end: session.end,
                backgroundColor: '#6366f1',
                borderColor: '#6366f1',
                textColor: '#fff', // better contrast
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    track: 'Test',
                    track_id: '1',
                    venue: 'Kolkata',
                    venue_id: '2',
                    speakers: session.extendedProps.speakers || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type
                }
            };
        });

        
        console.log(events)
        this.calendar.addEventSource(events); 
    }

    switchView(viewType) {
        this.currentView = viewType;
        
        // Update button states
        document.querySelectorAll('[id$="ViewBtn"]').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(`${viewType}ViewBtn`)?.classList.add('active');

        // Show/hide view containers
        const views = ['calendar', 'grid', 'list'];
        views.forEach(view => {
            const element = document.getElementById(`${view}View`);
            if (element) {
                element.style.display = view === viewType ? 'block' : 'none';
            }
        });

        // Render specific views
        if (viewType === 'grid') {
            this.renderGridView();
        } else if (viewType === 'list') {
            this.renderListView();
        }
    }

    renderGridView() {
        const grid = document.getElementById('scheduleGrid');
        if (!grid) return;
        
        if (this.events.length === 0) {
            grid.innerHTML = '<div class="text-center py-5 text-muted">No sessions found</div>';
            return;
        }

        // Group events by day
        const eventsByDay = {};
        this.events.forEach(session => {
            const day = moment(session.start_time).format('YYYY-MM-DD');
            if (!eventsByDay[day]) {
                eventsByDay[day] = [];
            }
            eventsByDay[day].push(session);
        });

        let gridHTML = '';
        Object.keys(eventsByDay).sort().forEach(day => {
            const sessions = eventsByDay[day].sort((a, b) => 
                moment(a.start_time).diff(moment(b.start_time))
            );

            gridHTML += `
            <div class="day-section mb-5">
                <h4 class="mb-4 text-primary border-bottom pb-2">
                    <i class="fas fa-calendar-day me-2"></i>
                    ${moment(day).format('dddd, MMMM D, YYYY')}
                </h4>
                <div class="row g-4">
            `;

            sessions.forEach(session => {
                const statusBadge = this.getStatusBadge(session.status);
                const typeBadge = this.getTypeBadge(session.type);
                const trackBadge = session.track ? `<span class="badge rounded-pill" style="background-color: ${session.track.color}20; color: ${session.track.color};">${session.track.name}</span>` : '';

                gridHTML += `
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100 session-card" onclick="eventCalendar.showSessionDetailsById('${session.id}')" style="cursor: pointer; transition: transform 0.2s;">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">${session.title}</h6>
                                ${statusBadge}
                            </div>
                            <div class="mb-2">
                                ${typeBadge} ${trackBadge}
                            </div>
                            <div class="text-muted small mb-2">
                                <div><i class="fas fa-clock me-1"></i> ${moment(session.start_time).format('HH:mm')} - ${moment(session.end_time).format('HH:mm')}</div>
                                ${session.venue ? `<div><i class="fas fa-map-marker-alt me-1"></i> ${session.venue.name}</div>` : ''}
                                ${session.capacity ? `<div><i class="fas fa-users me-1"></i> ${session.capacity} capacity</div>` : ''}
                            </div>
                            <div class="mt-auto">
                                ${session.speakers && session.speakers.length ? session.speakers.map(s => `
                                    <span class="badge bg-primary me-1 mb-1">${s.name} (${s.pivot?.role || 'Speaker'})</span>
                                `).join('') : '<span class="text-muted">No speakers assigned</span>'}
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });

            gridHTML += '</div></div>';

        });

        grid.innerHTML = gridHTML;
    }

    renderListView() {
        const list = document.getElementById('sessionsList');
        if (!list) return;
        
        if (this.events.length === 0) {
            list.innerHTML = '<div class="text-center py-5 text-muted">No sessions found</div>';
            return;
        }

        const sortedSessions = [...this.events].sort((a, b) => 
            moment(a.start_time).diff(moment(b.start_time))
        );

            let listHTML = '<div class="list-group">';

            sortedSessions.forEach(session => {
            const statusBadge = this.getStatusBadge(session.status);
            const typeBadge = this.getTypeBadge(session.type);
            const trackBadge = session.track 
            ? `<span class="badge rounded-pill" style="background-color: ${session.track.color}20; color: ${session.track.color};">${session.track.name}</span>` 
            : '';

            const description = session.description 
            ? `<p class="mb-1 mt-2 small text-truncate" style="max-width: 100%;">${session.description.substring(0, 100)}${session.description.length > 100 ? '...' : ''}</p>` 
            : '';

            listHTML += `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" onclick="eventCalendar.showSessionDetailsById('${session.id}')">
            <div class="flex-grow-1">
                <h6 class="mb-1">${session.title}</h6>
                <div class="mb-2">
                    ${typeBadge} ${trackBadge} ${statusBadge}
                </div>
                <div class="small text-muted mb-1">
                    <span class="me-3"><i class="fas fa-calendar me-1"></i> ${moment(session.start_time).format('MMM D, YYYY')}</span>
                    <span class="me-3"><i class="fas fa-clock me-1"></i> ${moment(session.start_time).format('HH:mm')} - ${moment(session.end_time).format('HH:mm')}</span>
                    ${session.venue ? `<span class="me-3"><i class="fas fa-map-marker-alt me-1"></i> ${session.venue.name}</span>` : ''}
                </div>
                ${description}
            </div>
            <div class="text-end ms-3">
                <div class="small text-muted">${this.calculateDuration(session.start_time, session.end_time)} min</div>
            </div>
            </button>
            `;
            });

            listHTML += '</div>';


        list.innerHTML = listHTML;
    }

    openSessionModal(eventData = {}) {
        this.isEditing = !!eventData.id;
        this.selectedSpeakers = [];
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('sessionModal'));
        const title = document.getElementById('modalTitle');
        const deleteBtn = document.getElementById('deleteBtn');

        title.textContent = this.isEditing ? 'Edit Session' : 'Add New Session';
        deleteBtn.style.display = this.isEditing ? 'block' : 'none';

        this.clearForm();
        this.clearAlerts();

        // Populate form if editing
        if (eventData.id) {
            document.getElementById('sessionId').value = eventData.id;
            document.getElementById('sessionTitle').value = eventData.title || '';
            document.getElementById('description').value = eventData.extendedProps?.description || '';
            document.getElementById('sessionType').value = eventData.extendedProps?.type || 'presentation';
            document.getElementById('capacity').value = eventData.extendedProps?.capacity || '';
           // document.getElementById('trackSelect').value = eventData.extendedProps?.track_id || '';
           // document.getElementById('venueSelect').value = eventData.extendedProps?.venue_id || '';
            document.getElementById('status').value = eventData.extendedProps?.status || 'draft';
            
            // Set selected speakers
            this.selectedSpeakers = eventData.extendedProps?.speakers || [];
            this.renderSelectedSpeakers();
        }

        if (eventData.start) {
            document.getElementById('startTime').value = moment(eventData.start).format('YYYY-MM-DDTHH:mm');
        }
        if (eventData.end) {
            document.getElementById('endTime').value = moment(eventData.end).format('YYYY-MM-DDTHH:mm');
        }

        modal.show();
    }

    async showSessionDetails(event) {
        this.currentSession = event;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('sessionDetailsModal'));
        const content = document.getElementById('sessionDetailsContent');
        const title = document.getElementById('detailsTitle');

        title.textContent = event.title;

        const speakers = event.extendedProps?.speakers || [];
        const speakersHTML = event.extendedProps?.speakers?.length
        ? event.extendedProps.speakers.map(speaker => `
            <span class="badge bg-primary me-1 mb-1">
                ${speaker.name} (${speaker.pivot?.role || 'Speaker'})
            </span>
          `).join('')
        : '<span class="text-muted">No speakers assigned</span>';


        content.innerHTML = `
    <div class="session-meta row g-3">
        <div class="col-12 col-md-6">
            <div class="fw-bold">Date & Time</div>
            <div>
                ${moment(event.start).format('dddd, MMMM D, YYYY')}<br>
                ${moment(event.start).format('h:mm A')} - ${moment(event.end).format('h:mm A')}
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Duration</div>
            <div>${event.extendedProps?.duration || 0} minutes</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Type</div>
            <div>${this.getTypeBadge(event.extendedProps?.type)}</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Track</div>
            <div>${event.extendedProps?.track || 'No Track'}</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Venue</div>
            <div>${event.extendedProps?.venue || 'No Venue'}</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Capacity</div>
            <div>${event.extendedProps?.capacity || 'Unlimited'}</div>
        </div>
    </div>

    ${event.extendedProps?.description ? `
        <div class="mt-3">
            <div class="fw-bold">Description</div>
            <div class="mt-2">${event.extendedProps.description}</div>
        </div>
    ` : ''}

    <div class="mt-3">
        <div class="fw-bold">Speakers</div>
        <div class="mt-2">${speakersHTML}</div>
    </div>
`;

        modal.show();
    }

    showSessionDetailsById(sessionId) {
        const session = this.events.find(e => e.id == sessionId);
        if (session) {
            // Convert session to event format for showSessionDetails
            const eventData = {
                id: session.id,
                title: session.title,
                start: session.start,
                end: session.end,
                backgroundColor: '#6366f1',
                borderColor: '#6366f1',
                textColor: '#fff', // better contrast
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    track: 'Test',
                    track_id: '1',
                    venue: 'Kolkata',
                    venue_id: '2',
                    speakers: session.extendedProps.speakers || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    type:session.extendedProps.type
                }
            };
            this.showSessionDetails(eventData);
        }
    }

    async editSession(sessionId) {
        const session = this.events.find(e => e.id == sessionId);
        if (session) {
            const eventData = {
                id: session.id,
                title: session.title,
                start: session.start,
                end: session.end,
                backgroundColor: '#6366f1',
                borderColor: '#6366f1',
                textColor: '#fff', // better contrast
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    track: 'Test',
                    track_id: '1',
                    venue: 'Kolkata',
                    venue_id: '2',
                    speakers: session.extendedProps.speakers || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type
                }
            };
            this.openSessionModal(eventData);
        }
    }

    async saveSession() {
        try {
            const form = document.getElementById('sessionForm');
            const formData = new FormData(form);
            
            // Add selected speakers
            this.selectedSpeakers.forEach((speaker, index) => {
                formData.append(`speaker_ids[${index}]`, speaker.id);
            });

            const sessionData = Object.fromEntries(formData.entries());

            // Validate required fields
            if (!sessionData.title || !sessionData.start_time || !sessionData.end_time) {
                this.showAlert('Please fill in all required fields', 'danger');
                return;
            }

            // Validate time
            if (new Date(sessionData.start_time) >= new Date(sessionData.end_time)) {
                this.showAlert('End time must be after start time', 'danger');
                return;
            }

            this.showLoading(true);

            let response;
            if (this.isEditing) {
                const url = this.config.apiUrls.updateSession.replace(':id', sessionData.session_id);
                response = await this.apiCall('PUT', url, sessionData);
            } else {
                response = await this.apiCall('POST', this.config.apiUrls.createSession, sessionData);
            }

            this.showAlert(
                this.isEditing ? 'Session updated successfully!' : 'Session created successfully!',
                'success'
            );

            await this.loadSessions();
            
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('sessionModal')).hide();
            }, 1500);

        } catch (error) {
            console.error('Error saving session:', error);
            this.showAlert(error.message || 'Error saving session', 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async deleteSession() {
        try {
            const sessionId = document.getElementById('sessionId').value;
            if (!sessionId) return;

            this.showLoading(true);
            
            const url = this.config.apiUrls.deleteSession.replace(':id', sessionId);
            await this.apiCall('DELETE', url);
            
            this.showAlert('Session deleted successfully!', 'success');
            
            await this.loadSessions();
            
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('sessionModal')).hide();
            }, 1500);

        } catch (error) {
            console.error('Error deleting session:', error);
            this.showAlert(error.message || 'Error deleting session', 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    async updateSessionTime(event, newStart, newEnd) {
        try {
            const sessionData = {
                start_time: moment(newStart).format('YYYY-MM-DD HH:mm:ss'),
                end_time: moment(newEnd).format('YYYY-MM-DD HH:mm:ss')
            };

            const url = this.config.apiUrls.updateSession.replace(':id', event.id);
            await this.apiCall('PUT', url, sessionData);
            
            // Update the event in our local array
            const eventIndex = this.events.findIndex(e => e.id == event.id);
            if (eventIndex !== -1) {
                this.events[eventIndex].start_time = sessionData.start_time;
                this.events[eventIndex].end_time = sessionData.end_time;
            }

            this.showAlert('Session time updated successfully!', 'success');

        } catch (error) {
            console.error('Error updating session time:', error);
            this.showAlert('Error updating session time', 'danger');
            
            // Revert the change
            event.setStart(event.extendedProps.originalStart || event.start);
            event.setEnd(event.extendedProps.originalEnd || event.end);
        }
    }

    filterByTrack(trackId) {
        this.applyFilters();
    }

    filterByVenue(venueId) {
        this.applyFilters();
    }

    filterByStatus(status) {
        this.applyFilters();
    }

    applyFilters() {
        const trackFilter = document.getElementById('trackFilter')?.value;
        const venueFilter = document.getElementById('venueFilter')?.value;
        const statusFilter = document.getElementById('statusFilter')?.value;

        let filteredEvents = [...this.events];

        if (trackFilter) {
            filteredEvents = filteredEvents.filter(event => event.track_id == trackFilter);
        }

        if (venueFilter) {
            filteredEvents = filteredEvents.filter(event => event.venue_id == venueFilter);
        }

        if (statusFilter) {
            filteredEvents = filteredEvents.filter(event => event.status === statusFilter);
        }

        // Update calendar
        if (this.calendar) {
            this.calendar.removeAllEvents();
            this.calendar.addEventSource(filteredEvents.map(session => ({
                id: session.id,
                title: session.title,
                start: session.start,
                end: session.end,
                backgroundColor: '#6366f1',
                borderColor: '#6366f1',
                textColor: '#fff', // better contrast
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    track: 'Test',
                    track_id: '1',
                    venue: 'Kolkata',
                    venue_id: '2',
                    speakers: session.extendedProps.speakers || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type
                }
            })));
        }

        // Update other views
        if (this.currentView === 'grid') {
            this.events = filteredEvents;
            this.renderGridView();
        } else if (this.currentView === 'list') {
            this.events = filteredEvents;
            this.renderListView();
        }
    }

    populateSpeakerSelect() {
        const select = document.getElementById('speakerSelect');
        if (!select) return;

        select.innerHTML = '<option value="">Select a speaker...</option>';
        this.speakers.forEach(speaker => {
            const option = document.createElement('option');
            option.value = speaker.id;
            option.textContent = `${speaker.name} - ${speaker.title || 'Speaker'}`;
            select.appendChild(option);
        });
    }

    addSelectedSpeaker() {
        const select = document.getElementById('speakerSelect');
        const speakerId = select.value;
        alert(1)
        if (!speakerId) return;
        
        const speaker = this.speakers.find(s => s.id == speakerId);
        if (!speaker) return;

        // Check if speaker is already selected
        if (this.selectedSpeakers.find(s => s.id == speakerId)) {
            this.showAlert('Speaker is already selected', 'warning');
            return;
        }

        this.selectedSpeakers.push(speaker);
        this.renderSelectedSpeakers();
        select.value = '';
    }

    renderSelectedSpeakers() {
        const container = document.getElementById('selectedSpeakers');
        if (!container) return;

        if (this.selectedSpeakers.length === 0) {
            container.innerHTML = '<div class="text-muted small">No speakers selected</div>';
            return;
        }

        container.innerHTML = this.selectedSpeakers.map((speaker, index) => `
            <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 mb-1">
                <div>
                    <strong>${speaker.name}</strong>
                    ${speaker.title ? `<span class="text-muted">- ${speaker.title}</span>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eventCalendar.removeSpeaker(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    removeSpeaker(index) {
        this.selectedSpeakers.splice(index, 1);
        this.renderSelectedSpeakers();
    }

    autoCalculateEndTime(startTime) {
        if (!startTime) return;

        const endTimeInput = document.getElementById('endTime');
        if (!endTimeInput || endTimeInput.value) return; // Don't override if already set

        // Add 1 hour by default
        const start = moment(startTime);
        const end = start.clone().add(1, 'hour');
        endTimeInput.value = end.format('YYYY-MM-DDTHH:mm');
    }

    calculateDuration(startTime, endTime) {
        return moment(endTime).diff(moment(startTime), 'minutes');
    }

    getStatusBadge(status) {
        const statusMap = {
            'draft': 'secondary',
            'published': 'success',
            'cancelled': 'danger'
        };
        const badgeClass = statusMap[status] || 'secondary';
        return `<span class="badge bg-${badgeClass}">${status || 'Draft'}</span>`;
    }

    getTypeBadge(type) {
        const typeMap = {
            'presentation': { class: 'primary', icon: 'fa-presentation' },
            'workshop': { class: 'info', icon: 'fa-tools' },
            'panel': { class: 'warning', icon: 'fa-users' },
            'break': { class: 'secondary', icon: 'fa-coffee' },
            'networking': { class: 'success', icon: 'fa-handshake' }
        };
        const typeConfig = typeMap[type] || { class: 'secondary', icon: 'fa-calendar' };
        return `<span class="badge bg-${typeConfig.class}"><i class="fas ${typeConfig.icon} me-1"></i>${type || 'Event'}</span>`;
    }

    clearForm() {
        const form = document.getElementById('sessionForm');
        if (form) {
            form.reset();
            document.getElementById('sessionId').value = '';
        }
        this.selectedSpeakers = [];
        this.renderSelectedSpeakers();
    }

    showAlert(message, type = 'success') {
        const container = document.getElementById('alertContainer');
        if (!container) return;

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        container.innerHTML = '';
        container.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    clearAlerts() {
        const container = document.getElementById('alertContainer');
        if (container) {
            container.innerHTML = '';
        }
    }

    showLoading(show) {
        const saveBtn = document.getElementById('saveBtn');
        if (!saveBtn) return;

        if (show) {
            saveBtn.innerHTML = '<span class="loading-spinner me-2"></span>Saving...';
            saveBtn.disabled = true;
        } else {
            saveBtn.innerHTML = 'Save Session';
            saveBtn.disabled = false;
        }
    }

    showCalendarLoading(isLoading) {
        // You can implement a loading overlay for the calendar here
        console.log('Calendar loading:', isLoading);
    }

    async apiCall(method, url, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            }
        };

        if (data && method !== 'GET') {
            if (data instanceof FormData) {
                // For FormData, remove Content-Type header to let browser set it
                delete options.headers['Content-Type'];
                options.body = data;
            } else {
                options.body = JSON.stringify(data);
            }
        } else if (data && method === 'GET') {
            // For GET requests, append data as query parameters
            const params = new URLSearchParams(data);
            url += (url.includes('?') ? '&' : '?') + params.toString();
        }

        const response = await fetch(url, options);
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    // Public methods for external access
    refreshCalendar() {
        this.loadSessions();
    }

    getCurrentView() {
        return this.currentView;
    }

    getSelectedEvents() {
        return this.events;
    }

    // Export functionality
    async exportCalendar(format = 'json') {
        try {
            const data = {
                event: this.config,
                sessions: this.events,
                exported_at: new Date().toISOString()
            };

            if (format === 'json') {
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                this.downloadFile(blob, `calendar-export-${moment().format('YYYY-MM-DD')}.json`);
            } else if (format === 'csv') {
                const csv = this.convertToCSV(this.events);
                const blob = new Blob([csv], { type: 'text/csv' });
                this.downloadFile(blob, `calendar-export-${moment().format('YYYY-MM-DD')}.csv`);
            }

        } catch (error) {
            console.error('Error exporting calendar:', error);
            this.showAlert('Error exporting calendar', 'danger');
        }
    }

    convertToCSV(sessions) {
        const headers = ['ID', 'Title', 'Start Time', 'End Time', 'Type', 'Venue', 'Status', 'Capacity', 'Description'];
        const rows = sessions.map(session => [
            session.id,
            session.title,
            session.start_time,
            session.end_time,
            session.type,
           //session.track?.name || '',
            session.venue?.name || '',
            session.status,
            session.capacity || '',
            session.description || ''
        ]);

        return [headers, ...rows].map(row => 
            row.map(field => `"${(field || '').toString().replace(/"/g, '""')}"`).join(',')
        ).join('\n');
    }

    downloadFile(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}

// Initialize the calendar when the page loads
let eventCalendar;
document.addEventListener('DOMContentLoaded', function() {

    if (typeof window.calendarConfig !== 'undefined') {
        eventCalendar = new LaravelEventCalendar();
        
        // Make it globally accessible
        window.eventCalendar = eventCalendar;
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LaravelEventCalendar;
}
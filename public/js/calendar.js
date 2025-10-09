class LaravelEventCalendar {
    constructor() {
        this.calendar = null;
        this.currentView = 'calendar';
        this.events = [];
        this.speakers = [];
        this.exhibitors = [];
        this.sponsors = [];
        this.selectedSpeakers = [];
        this.selectedExhibitors = [];
        this.selectedSponsors = [];
        this.currentSession = null;
        this.isEditing = false;
        this.image = '';
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
        this.loadExhibitor();
        this.loadSponsors();
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
            slotMaxTime: '23:59:00',
            slotDuration: '00:15:00',
            snapDuration: '00:15:00',
            editable: true,
            selectable: true,
            selectMirror: true,
            eventResizableFromStart: true,
            nowIndicator: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5, 6, 0],
                startTime: '06:00',
                endTime: '23:59'
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
                const track = info.event.extendedProps.track
                info.el.classList.add(`session-type-${sessionType}`);
                
                // Add tooltip
                info.el.title = info.event.extendedProps.description || info.event.title+`(${track})`;

                    // Build stacked list
                    const tooltipContent = `
                        <div>
                            <div><strong>${info.event.title}</strong></div>
                            ${info.event.extendedProps.track ? `<div>Track: ${info.event.extendedProps.track}</div>` : ''}
                            ${info.event.extendedProps.location ? `<div>Location: ${info.event.extendedProps.location}(${info.event.extendedProps.booth})</div>` : ''}
                            ${info.event.extendedProps.keynote ? `<div>Key Note: ${info.event.extendedProps.keynote}</div>` : ''}
                        </div>
                    `;

                    // Initialize Bootstrap tooltip (HTML enabled)
                    if(info.event.title){
                    new bootstrap.Tooltip(info.el, {
                        title: tooltipContent,
                        placement: 'top',
                        html: true,
                        trigger: 'hover',
                        customClass: 'event-tooltip'
                    });
                    }

                const color = info.event.backgroundColor || info.event.extendedProps.color || info.event.extendedProps.backgroundColor;
                  if (color) {
                    // Works with FC v5/v6 CSS variables
                    info.el.style.setProperty('--fc-event-bg-color', color);
                    info.el.style.setProperty('--fc-event-border-color', color);
                    info.el.style.setProperty('--fc-event-text-color', '#000000');
                  }
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

         document.getElementById('addExhibitorBtn')?.addEventListener('click', () => {
            this.addSelectedExhibitor();
        });

          document.getElementById('addSponsorBtn')?.addEventListener('click', () => {
            this.addSelectedSponsor();
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

    async loadExhibitor() {
        try {
            const response = await this.apiCall('GET', this.config.apiUrls.exhibitors);
            this.exhibitors = response.data || response;
            this.populateExhibitorSelect();
        } catch (error) {
            console.error('Error loading exhibitors:', error);
        }
    }

    async loadSponsors() {
        try {
            const response = await this.apiCall('GET', this.config.apiUrls.sponsors);
            this.sponsors = response.data || response;
            this.populateSponsorSelect();
        } catch (error) {
            console.error('Error loading sponsors:', error);
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
                start_time: session.start,
                end_time: session.end,
                backgroundColor: session.backgroundColor,
                borderColor: session.borderColor,
                textColor: session.textColor, // better contrast
                booth: session.booth, // better contrast
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    venue:  session.extendedProps.venue,
                    venue_id: session.extendedProps.venue_id,
                    speakers: session.extendedProps.speakers || [],
                    exhibitors: session.extendedProps.exhibitors || [],
                    sponsors: session.extendedProps.sponsors || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type,
                    backgroundColor: session.borderColor,
                    borderColor: session.borderColor,
                    textColor: session.textColor, // better contrast
                    track: session.track, 
                    location: session.location,
                    keynote: session.keynote,
                    demoes: session.demoes,
                    panels: session.panels,
                    img: session.img,
                    img_id: session.img_id,
                    booth: session.booth,
                }
            };
        });

        

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
        console.log(this.events)
        this.events.forEach(session => {
            const day = moment(session.start).format('YYYY-MM-DD');
            if (!eventsByDay[day]) {
                eventsByDay[day] = [];
            }
            eventsByDay[day].push(session);
        });

        let gridHTML = '';
        Object.keys(eventsByDay).sort().forEach(day => {
            const sessions = eventsByDay[day].sort((a, b) => 
                moment(a.start).diff(moment(b.start))
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
                const typeBadge = this.getTypeBadge(session.track);
                const keyBadge = this.getTypeBadge(session.keynote);

                gridHTML += `
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100 session-card" style="background-color: ${session.backgroundColor}; color: ${session.textColor};" onclick="eventCalendar.showSessionDetailsById('${session.id}')" style="cursor: pointer; transition: transform 0.2s;">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0" style="color: ${session.textColor};">${session.title}</h6>
                                ${statusBadge}
                            </div>
                          
                            <div class="mb-2" style="color: ${session.textColor};">
                                ${typeBadge} 
                            </div>
                            <div class="text-muted small mb-2">
                                <div style="color: ${session.textColor};"><i class="fas fa-clock me-1"></i> ${moment(session.start).format('HH:mm')} - ${moment(session.end).format('HH:mm')}</div>
                                ${session.location ? `<div style="color: ${session.textColor};"><i class="fas fa-map-marker-alt me-1"></i>${session?.location ?? ''} (${session.booth})</div>` : ''}
                                ${session.capacity ? `<div style="color: ${session.textColor};"><i class="fas fa-users me-1"></i> ${session.capacity} capacity</div>` : ''}
                            </div>

                    

                              <div class="mb-2" style="color: ${session.textColor};">
                              <i class="fas fa-pen-square me-1" title="Description"></i>Description:  ${session.description} 
                            </div>   
                            <div class="mt-auto">
                                ${session.extendedProps.speakers && session.extendedProps.speakers.length ? session.extendedProps.speakers.map(s => `
                                    <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: #fff;">${s.name} (${s.pivot?.role || 'Speaker'})</span>
                                `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No speakers assigned</span>'}
                            </div>

                            <!--<div class="mt-auto">
                                ${session.extendedProps.exhibitors && session.extendedProps.exhibitors.length ? session.extendedProps.exhibitors.map(s => `
                                    <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: ${session.textColor};">${s.name} (${s.pivot?.role || 'Exhibitor'})</span>
                                `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No exhibitors assigned</span>'}
                            </div> 

                            <div class="mt-auto">
                                ${session.extendedProps.sponsors && session.extendedProps.sponsors.length ? session.extendedProps.sponsors.map(s => `
                                    <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: ${session.textColor};">${s.name} (${s.pivot?.role || 'Sponsor'})</span>
                                `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No sponsors assigned</span>'}
                            </div>  -->   

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
            moment(a.start).diff(moment(b.start))
        );

            let listHTML = '<div class="list-group">';

            sortedSessions.forEach(session => {
            const statusBadge = this.getStatusBadge(session.status);
            const typeBadge = this.getTypeBadge(session.track);
           
            const description = session.description 
            ? `<p class="mb-1 mt-2 small text-truncate" style="max-width: 100%;">${session.description.substring(0, 100)}${session.description.length > 100 ? '...' : ''}</p>` 
            : '';

            listHTML += `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start mt-2" style="background-color: ${session.backgroundColor}; color: ${session.textColor};" onclick="eventCalendar.showSessionDetailsById('${session.id}')">
            <div class="flex-grow-1">
                <h6 class="mb-1" style="color: ${session.textColor};">${session.title}</h6>
                <div class="mb-2" style="color: ${session.textColor};">
                    ${typeBadge} ${statusBadge}
                </div>
                <div class="small text-muted mb-1">
                    <span class="me-3" style="color: ${session.textColor};"><i class="fas fa-calendar me-1"></i> ${moment(session.start).format('MMM D, YYYY')}</span>
                    <span class="me-3" style="color: ${session.textColor};"><i class="fas fa-clock me-1"></i> ${moment(session.start).format('HH:mm')} - ${moment(session.end).format('HH:mm')}</span>
                    ${session.location ? `<span class="me-3" style="color: ${session.textColor};"><i class="fas fa-map-marker-alt me-1"></i> ${session?.location ?? ''}</span>` : ''}
                </div>

                
                    <div class="mt-2">
                        ${session.extendedProps.speakers && session.extendedProps.speakers.length ? session.extendedProps.speakers.map(s => `
                            <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: #fff;">${s.name} (${s.pivot?.role || 'Speaker'})</span>
                        `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No speakers assigned</span>'}
                    </div>

                    <!--<div class="mt-2">
                        ${session.extendedProps.exhibitors && session.extendedProps.exhibitors.length ? session.extendedProps.exhibitors.map(s => `
                            <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: ${session.textColor};">${s.name} (${s.pivot?.role || 'Exhibitor'})</span>
                        `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No exhibitors assigned</span>'}
                    </div> 

                    <div class="mt-2">
                        ${session.extendedProps.sponsors && session.extendedProps.sponsors.length ? session.extendedProps.sponsors.map(s => `
                            <span class="badge rounded-pill bg-primary me-1 mb-1 small" style="color: ${session.textColor};">${s.name} (${s.pivot?.role || 'Sponsor'})</span>
                        `).join('') : '<span class="text-muted" style="color: ${session.textColor};">No sponsors assigned</span>'}
                    </div>  -->  
            </div>


            <div class="text-end ms-3">
                <div class="small text-muted" style="color: ${session.textColor};">${this.calculateDuration(session.start, session.end)} min</div>
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
            document.getElementById('description2').value = eventData.extendedProps?.description || '';
           // document.getElementById('sessionType').value = presentation;
            //document.getElementById('capacity').value =  '';
            document.getElementById('venueSelect').value = eventData.extendedProps?.location || '';
            document.getElementById('status').value = eventData.extendedProps?.status || 'draft';

            //document.getElementById('keynote2').value = eventData.extendedProps?.keynote || '';
            //document.getElementById('panels2').value = eventData.extendedProps?.demoes || '';
            //document.getElementById('demoes2').value = eventData.extendedProps?.panels || '';

            document.getElementById('profileImagePreview').src = eventData.extendedProps?.img || '';
            document.getElementById('dz-remove').setAttribute('data-photoid', eventData.extendedProps?.img_id);

            const preview = document.getElementById('profileImagePreview');
            preview.classList.remove('d-none');  // Optionally, hide the preview image
            // Set selected speakers
            this.selectedSpeakers = eventData.extendedProps?.speakers || [];
            this.renderSelectedSpeakers();
            
            this.selectedSponsors = eventData.extendedProps?.sponsors || [];
            this.renderSelectedSponsors();

            this.selectedExhibitors = eventData.extendedProps?.exhibitors || [];
            this.renderSelectedExhibitors();
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

         const exhibitorsHTML = event.extendedProps?.exhibitors?.length
        ? event.extendedProps.exhibitors.map(exhibitor => `
            <span class="badge bg-primary me-1 mb-1">
                ${exhibitor.name} (${exhibitor.pivot?.role || 'Exhibitor'})
            </span>
          `).join('')
        : '<span class="text-muted">No exhibitors assigned</span>';

        const sponsorHTML = event.extendedProps?.sponsors?.length
        ? event.extendedProps.sponsors.map(sponsor => `
            <span class="badge bg-primary me-1 mb-1">
                ${sponsor.name} (${sponsor.pivot?.role || 'Exhibitor'})
            </span>
          `).join('')
        : '<span class="text-muted">No sponsors assigned</span>';


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
            <div class="fw-bold">Track</div>
            <div>${this.getTypeBadge(event.extendedProps?.track)}</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Venue</div>
            <div>${event?.location?? ''}</div>
            <div>${event.extendedProps?.venue || 'No Venue'}</div>
        </div>
        <div class="col-12 col-md-6">
            <div class="fw-bold">Capacity</div>
            <div>${event.extendedProps?.capacity || 'Unlimited'}</div>
         </div>
    </div>

    ${event.extendedProps?.img ? `
        <div class="mt-3" style="width:100%;">
            <div class="fw-bold">Description</div>
            <div class="mt-2"><img src="${event.extendedProps.img}" width="100%"/></div>
        </div>
    ` : ''}        

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

     <div class="mt-3">
        <div class="fw-bold">Exhibitors</div>
        <div class="mt-2">${exhibitorsHTML}</div>
    </div>

     <div class="mt-3">
        <div class="fw-bold">Sponsors</div>
        <div class="mt-2">${sponsorHTML}</div>
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
                backgroundColor: session.color,
                borderColor: session.color,
                textColor: session.textColor, // better contrast
                booth: session.booth,
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    venue:  session.extendedProps.venue,
                    venue_id: session.extendedProps.venue_id,
                    speakers: session.extendedProps.speakers || [],
                    exhibitors: session.extendedProps.exhibitors || [],
                    sponsors: session.extendedProps.sponsors || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type,
                    backgroundColor: session.color,
                    borderColor: session.color,
                    textColor: session.textColor, // better contrast
                    track: session.track, 
                    location: session.location,
                    keynote: session.keynote,
                    demoes: session.demoes,
                    panels: session.panels,
                    img: session.img,
                    img_id: session.img_id,
                    booth: session.booth,
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
                backgroundColor: session.color,
                borderColor: session.color,
                textColor: session.textColor, // better contrast
                description: session.description,
                 booth: session.booth,
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    venue:  session.extendedProps.venue,
                    venue_id: session.extendedProps.venue_id,
                    speakers: session.extendedProps.speakers || [],
                    exhibitors: session.extendedProps.exhibitors || [],
                    sponsors: session.extendedProps.sponsors || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type,
                    backgroundColor: session.color,
                    borderColor: session.color,
                    textColor: session.textColor, // better contrast
                    track: session.track, 
                    location: session.location,
                    keynote: session.keynote,
                    demoes: session.demoes,
                    panels: session.panels,
                    img: session.img,
                    img_id: session.img_id,
                     booth: session.booth,
                }
            };
            this.openSessionModal(eventData);
        }
    }

    async saveSession() {
        try {
            const form = document.getElementById('sessionForm');
            let formData = new FormData(form)
            const base64Image = document.getElementById('profileImagePreview').src;
            if (base64Image) {
               formData.append('session_image',base64Image)
            }
            this.selectedSpeakers.forEach((speaker, index) => {
                formData.append(`speaker_ids[${index}]`, speaker.id);
            });
          
            this.selectedExhibitors.forEach((exhibitor, index) => {
                formData.append(`exhibitor_ids[${index}]`, exhibitor.id);
            });

            this.selectedSponsors.forEach((sponsor, index) => {
                formData.append(`sponsor_ids[${index}]`, sponsor.id);
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
            }, 1000);

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
        //const trackFilter = document.getElementById('trackFilter')?.value;
        const venueFilter = document.getElementById('venueFilter')?.value;
        const statusFilter = document.getElementById('statusFilter')?.value;

        let filteredEvents = [...this.events];

        // if (trackFilter) {
        //     filteredEvents = filteredEvents.filter(event => event.track_id == trackFilter);
        // }

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
                backgroundColor: session.color,
                borderColor: session.color,
                textColor: session.textColor, // better contrast
                color: session.color,
                 booth: session.booth,
                extendedProps: {
                    description: session.extendedProps.description,
                    status: session.extendedProps.status,
                    venue:  session.extendedProps.venue,
                    venue_id: session.extendedProps.venue_id,
                    speakers: session.extendedProps.speakers || [],
                    exhibitors: session.extendedProps.exhibitors || [],
                    sponsors: session.extendedProps.sponsors || [],
                    capacity: session.extendedProps.capacity,
                    duration: this.calculateDuration(session.start, session.end),
                    //duration: session.extendedProps.duration,
                    type:session.extendedProps.type,
                    backgroundColor: session.color,
                    borderColor: session.color,
                    textColor: session.textColor, // better contrast
                    track: session.track, 
                    location: session.location,
                    keynote: session.keynote,
                    demoes: session.demoes,
                    panels: session.panels,
                    img: session.img,
                    img_id: session.img_id,
                     booth: session.booth,
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
            option.textContent = `${speaker.full_name} - ${speaker.email || 'Speaker'}`;
            select.appendChild(option);
        });
    }

    populateExhibitorSelect() {
      
        const select = document.getElementById('exhibitorSelect');
        if (!select) return;

        select.innerHTML = '<option value="">Select a exibitor...</option>';
        this.exhibitors.forEach(exibitor => {
            const option = document.createElement('option');
            option.value = exibitor.id;
            option.textContent = `${exibitor.name}`;
            select.appendChild(option);
        });
    }

     populateSponsorSelect() {
        const select = document.getElementById('sponsorSelect');
        if (!select) return;

        select.innerHTML = '<option value="">Select a sponsor...</option>';
        this.sponsors.forEach(sponsor => {
            const option = document.createElement('option');
            option.value = sponsor.id;
            option.textContent = `${sponsor.name}`;
            select.appendChild(option);
        });
    }

    addSelectedSpeaker() {
        const select = document.getElementById('speakerSelect');
        const speakerId = select.value;
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

    addSelectedExhibitor() {
        const select = document.getElementById('exhibitorSelect');
        const exibitorId = select.value;
        if (!exibitorId) return;
        
        const exhibitor = this.exhibitors.find(s => s.id == exibitorId);
        if (!exhibitor) return;

        // Check if speaker is already selected
        if (this.selectedSpeakers.find(s => s.id == exibitorId)) {
            this.showAlert('Exhibitor is already selected', 'warning');
            return;
        }

        this.selectedExhibitors.push(exhibitor);
        this.renderSelectedExhibitors();
        select.value = '';
    }

      addSelectedSponsor() {
        const select = document.getElementById('sponsorSelect');
        const sponsorId = select.value;
        if (!sponsorId) return;
        
        const sponsor = this.sponsors.find(s => s.id == sponsorId);
        if (!sponsor) return;

        // Check if speaker is already selected
        if (this.selectedSponsors.find(s => s.id == sponsorId)) {
            this.showAlert('Sponsors is already selected', 'warning');
            return;
        }

        this.selectedSponsors.push(sponsor);
        this.renderSelectedSponsors();
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

    renderSelectedExhibitors() {
        const container = document.getElementById('selectedExhibitors');
        if (!container) return;

        if (this.selectedExhibitors.length === 0) {
            container.innerHTML = '<div class="text-muted small">No exhibitors selected</div>';
            return;
        }

        container.innerHTML = this.selectedExhibitors.map((speaker, index) => `
            <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 mb-1">
                <div>
                    <strong>${speaker.name}</strong>
                    ${speaker.title ? `<span class="text-muted">- ${speaker.title}</span>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eventCalendar.removeExhibitor(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    renderSelectedSponsors() {
        const container = document.getElementById('selectedSponsors');
        if (!container) return;

        if (this.selectedSponsors.length === 0) {
            container.innerHTML = '<div class="text-muted small">No exhibitors selected</div>';
            return;
        }

        container.innerHTML = this.selectedSponsors.map((sponsor, index) => `
            <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 mb-1">
                <div>
                    <strong>${sponsor.name}</strong>
                    ${sponsor.title ? `<span class="text-muted">- ${sponsor.title}</span>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eventCalendar.removeSponsor(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    removeSpeaker(index) {
        this.selectedSpeakers.splice(index, 1);
        this.renderSelectedSpeakers();
    }

    removeSponsor(index) {
        this.selectedSponsors.splice(index, 1);
        this.renderSelectedSponsors();
    }
    removeExhibitor(index) {
        this.selectedExhibitors.splice(index, 1);
        this.renderSelectedExhibitors();
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
        return `<span class="badge bg-${typeConfig.class}" ><i class="fas ${typeConfig.icon} me-1"></i> ${ (type || 'Event').replace(/,/g, '<br>') }</span>`;
    }

    clearForm() {
        const form = document.getElementById('sessionForm');
        if (form) {
            form.reset();
            document.getElementById('sessionId').value = '';
       
            const preview = document.getElementById('profileImagePreview');
            preview.src = '';  // Clear the base64 image
            preview.classList.add('d-none');  // Optionally, hide the preview image
          
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

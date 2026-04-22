@extends('layouts.admin')

{{-- Keep libs only if you actually use them --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>

@section('content')
<style>
/* Card motion */
.card { transition: transform .2s ease, box-shadow .2s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.08); }

/* KPI cards */
.kpi-card { color:#fff; border:0; }
.kpi-label { font-size:.85rem; opacity:.9; letter-spacing:.2px; }
.kpi-value { font-weight:700; color:#fff}
.kpi-icon { font-size:2rem; opacity:.9; }

.gradient-primary   { background: linear-gradient(135deg,#4e54c8,#8f94fb); }
.gradient-success   { background: linear-gradient(135deg,#11998e,#38ef7d); }
.gradient-info      { background: linear-gradient(135deg,#00c6ff,#0072ff); }
.gradient-warning   { background: linear-gradient(135deg,#f7971e,#ffd200); color:#fff; }
.gradient-secondary { background: linear-gradient(135deg,#6a11cb,#2575fc); }
.gradient-dark      { background: linear-gradient(135deg,#434343,#000000); }

/* Quick actions */
.quick-action-card { border:2px solid transparent; transition: all .25s ease; }
.quick-action-card:hover { border-color: var(--bs-primary); transform: translateY(-4px); }

/* Lists */
.list-group-item { border-left: none; border-right: none; }
.list-group-item:first-child { border-top: none; }
.list-group-item:last-child { border-bottom: none; }


</style>
<div class="container align-items-center grey-bg">
  <section id="minimal-statistics">
    <div class="row">
      <div class="col-12 my-3">
        <h3 class="mb-0">Dashboard</h3>
        <p class="text-muted">Quick overview of your events and recent activity</p>
      </div>
    </div>

    
    @if(Auth::user()->hasRole('Admin'))
    @php
      $fmt = fn($n) => number_format((int)($n ?? 0));
    @endphp
<div class="row g-3">
  <!-- Total Events Card -->
  <!--<div class="col-12 col-sm-6 col-md-4 col-xl-2">
    <div class="card kpi-card gradient-primary">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <p class="kpi-label mb-1">Total Events</p>
          <h4 class="kpi-value mb-0">{{ $fmt($eventCount ?? $evntCount ?? 0) }}</h4>
        </div>
        <i class="bi bi-calendar-event kpi-icon" aria-hidden="true"></i>
      </div>
    </div>
  </div>-->

  <!-- Total Attendees Card -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="card kpi-card gradient-success">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <p class="kpi-label mb-1">Total Attendees</p>
          <h4 class="kpi-value mb-0">{{ $fmt($attendeeCount ?? 0) }}</h4>
        </div>
        <i class="bi bi-people-fill kpi-icon" aria-hidden="true"></i>
      </div>
    </div>
  </div>

  <!-- Total Speakers Card -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="card kpi-card gradient-info">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <p class="kpi-label mb-1">Total Speakers</p>
          <h4 class="kpi-value mb-0">{{ $fmt($speakerCount ?? 0) }}</h4>
        </div>
        <i class="bi bi-mic-fill kpi-icon" aria-hidden="true"></i>
      </div>
    </div>
  </div>

  <!-- Total Sponsors Card -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="card kpi-card gradient-warning">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <p class="kpi-label mb-1">Total Sponsors</p>
          <h4 class="kpi-value mb-0">{{ $fmt($sponsorCount ?? 0) }}</h4>
        </div>
        <i class="bi bi-briefcase-fill kpi-icon" aria-hidden="true"></i>
      </div>
    </div>
  </div>

  <!-- Total Exhibitors Card -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="card kpi-card gradient-secondary">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <p class="kpi-label mb-1">Total Exhibitors</p>
          <h4 class="kpi-value mb-0">{{ $fmt($exhibitorCount ?? 0) }}</h4>
        </div>
        <i class="bi bi-shop kpi-icon" aria-hidden="true"></i>
      </div>
    </div>
  </div>
</div>

    {{-- Membership Status Section for Event Admins --}}
    @if(!isSuperAdmin())
    <div class="row mt-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-dark font-weight-bold">
               <i class="bi bi-shield-check text-primary me-2"></i>Membership Status
            </h5>
          </div>
          @if(isset($subscription) && ($subscription->status == 'active') && (!$subscription->expired_at || !$subscription->expired_at->isPast()))
          <div class="card-body bg-light">
            <div class="row align-items-center">
              <div class="col-md-3 border-end py-2">
                <p class="text-muted small text-uppercase mb-1 fw-semibold">Current Plan</p>
                <h4 class="mb-1 text-primary fw-bold">{{ $subscription->pricing->name ?? 'Individual Plan' }}</h4>
                <span class="badge {{ ($subscription->status ?? '') == 'active' ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill">
                  <i class="bi bi-dot me-1"></i>{{ ucfirst($subscription->status ?? 'Inactive') }}
                </span>
              </div>
              
              <div class="col-md-3 border-end py-2 px-md-4">
                <p class="text-muted small text-uppercase mb-1 fw-semibold">Attendee Capacity</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="mb-0 fw-bold">{{ $attendeeCount }}</h4>
                  <span class="text-muted ms-1">/ {{ $subscription->attendee_count ?? 0 }}</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; background-color: #e0e0e0;">
                  @php
                    $attendeePercent = ($subscription->attendee_count ?? 0) > 0 ? ($attendeeCount / $subscription->attendee_count) * 100 : 0;
                  @endphp
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $attendeePercent) }}%" aria-valuenow="{{ $attendeePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="small text-muted mt-2 mb-0">{{ round($attendeePercent, 1) }}% space utilized</p>
              </div>

              <div class="col-md-3 border-end py-2 px-md-4">
                <p class="text-muted small text-uppercase mb-1 fw-semibold">Event Slots</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="mb-0 fw-bold">{{ $totalEventCount }}</h4>
                  <span class="text-muted ms-1">/ {{ $subscription->event_count ?? 0 }}</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; background-color: #e0e0e0;">
                  @php
                    $eventPercent = ($subscription->event_count ?? 0) > 0 ? ($totalEventCount / $subscription->event_count) * 100 : 0;
                  @endphp
                  <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $eventPercent) }}%" aria-valuenow="{{ $eventPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="small text-muted mt-2 mb-0">{{ round($eventPercent, 1) }}% slots active</p>
              </div>

              <div class="col-md-3 py-2 px-md-4 text-md-end">
                <div class="mb-3">
                  <p class="text-muted small text-uppercase mb-1 fw-semibold">Start Date</p>
                  <h6 class="mb-0 text-dark fw-bold">
                    {{ $subscription->created_at ? $subscription->created_at->format('M d, Y') : 'N/A' }}
                  </h6>
                </div>
                <div>
                  <p class="text-muted small text-uppercase mb-1 fw-semibold">End Date</p>
                  <h6 class="mb-0 text-dark fw-bold">
                    {{ $subscription->expired_at ? $subscription->expired_at->format('M d, Y') : 'N/A' }}
                  </h6>
                  @if($subscription->expired_at)
                  <p class="small {{ $subscription->expired_at->isPast() ? 'text-danger' : 'text-muted' }} mb-0">
                    <i class="bi bi-clock me-1"></i>{{ $subscription->expired_at->diffForHumans() }}
                  </p>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="card-body bg-light">
            <div class="d-flex flex-column align-items-center justify-content-center py-4">
              <div class="rounded-circle bg-warning bg-opacity-10 p-3 mb-3">
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
              </div>
              <h5 class="text-dark fw-bold mb-1">No Active Subscription Found</h5>
              <p class="text-muted text-center mb-3">You are currently not on any plan. Please contact the administrator to assign a subscription plan to your account.</p>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
    @endif


    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h4>
            <div class="text-muted small">Create & manage in a click</div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-xl-6 col-lg-6">
                <div class="card quick-action-card border-primary h-100">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      <i class="bi bi-calendar-plus text-primary" style="font-size:3rem;"></i>
                    </div>
                    <h5 class="card-title">Create New Event</h5>
                    <p class="card-text text-muted">Set up a new event with details and configurations.</p>
                    <div class="d-grid gap-2">
                      <a href="{{ route('calendar.index') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Event
                      </a>
                    
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-6 col-lg-6">
                <div class="card quick-action-card border-success h-100">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      <i class="bi bi-person-plus text-success" style="font-size:3rem;"></i>
                    </div>
                    <h5 class="card-title">Manage Registrations</h5>
                    <p class="card-text text-muted">Handle registrations and participant management.</p>
                    <div class="d-grid gap-2">
                      <a href="{{ route('attendee-users.index') }}" class="btn btn-success">
                        <i class="bi bi-person-add me-2"></i>New Registration
                      </a>
                     
                    </div>
                  </div>
                </div>
              </div>

            </div> {{-- /row --}}
          </div>
        </div>
      </div>
    </div>
    @endif


    <div class="row mt-4">
      <div class="col-12 col-lg-6">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h4>
            <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              @forelse($logs ?? [] as $log)
                @php
                  $action = match($log->event) {
                    'created' => 'New '.class_basename($log->auditable_type),
                    'updated' => 'Updated '.class_basename($log->auditable_type),
                    'deleted' => 'Deleted '.class_basename($log->auditable_type),
                    default => ucfirst($log->event),
                  };
                @endphp
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <i class="bi bi-info-circle text-primary me-2" aria-hidden="true"></i>
                    <strong>#{{ $log->auditable_id }} {{ $action }}:</strong>
                    “{{ $log->user?->full_name ?? 'System' }}”
                    on {{ $log->created_at->format('M d, Y') }},
                    {{ $log->created_at->format('h:i A') }}
                  </div>
                  <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>
              @empty
                <div class="text-muted">No recent activity.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><i class="bi bi-box-arrow-in-right"></i> Login Activity</h4>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              @forelse($loginlogs ?? [] as $log)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <i class="bi bi-person-check text-success me-2" aria-hidden="true"></i>
                    <strong>{{ $log->user?->full_name ?? 'System' }} logged in</strong>
                    on {{ $log->created_at->format('M d, Y') }},
                    {{ $log->created_at->format('h:i A') }}
                  </div>
                  <small class="text-muted">
                    <i class="bi bi-alarm me-1" aria-hidden="true"></i>{{ $log->created_at->diffForHumans() }}
                  </small>
                </div>
              @empty
                <div class="text-muted">No login activity yet.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Button loading states for any form with [data-loading]
  document.querySelectorAll('form[data-loading]').forEach(form => {
    form.addEventListener('submit', function(e){
      const btn = this.querySelector('button[type="submit"]');
      if(btn){
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        btn.disabled = true;
      }
    });
  });

  // Optional: Bulk import validation if modal exists
  const importForm = document.querySelector('#bulkImportModal form');
  if (importForm) {
    importForm.addEventListener('submit', function(e) {
      const fileInput = document.getElementById('importFile');
      const importType = document.getElementById('importType');
      if (!fileInput?.files?.length) {
        e.preventDefault(); alert('Please select a file to import.'); return;
      }
      if (!importType?.value) {
        e.preventDefault(); alert('Please select an import type.'); return;
      }
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Importing...';
        submitBtn.disabled = true;
      }
    });
  }
});
</script>
@endsection

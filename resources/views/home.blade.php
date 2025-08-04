@extends('layouts.admin')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@section('content')
<div class="container align-items-center grey-bg">
  <section id="minimal-statistics">
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        
      </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
      <div class="col-xl-4 col-sm-6 col-12">
        <div class="card text-bg-primary">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                <div class="emdia-body text-left">
                  <h6 class="success text-white">Total Events: {{$evntCount}} </h6><br>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-6 col-12">
        <div class="card text-bg-info">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                <div class="media-body text-left">
                  <h6 class="success text-white">Total users : {{$userCount}} </h6><br>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-6 col-12"> 
        <div class="card text-bg-secondary"> 
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                <div class="media-body text-left">
                  <h6 class="success text-white">Revenue: 0</h6><br>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">
              <i class="bi bi-lightning-charge"></i> Quick Actions
            </h4>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- Event Creation Quick Action -->
              <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                <div class="card border-primary">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      <i class="bi bi-calendar-plus text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Create New Event</h5>
                    <p class="card-text text-muted">Set up a new event with all details and configurations</p>
                    <div class="d-grid gap-2">
                      <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Event
                      </a>
                      <a href="{{ route('events.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list me-2"></i>View All Events
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Event Registration Quick Action -->
              <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                <div class="card border-success">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      <i class="bi bi-person-plus text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Manage Registrations</h5>
                    <p class="card-text text-muted">Handle event registrations and participant management</p>
                    <div class="d-grid gap-2">
                      <a href="{{route('users.create')}}" class="btn btn-success">
                        <i class="bi bi-person-add me-2"></i>New Registration
                      </a>
                      <a href="{{route('users.index')}}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-people me-2"></i>View All Registrations
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Quick Actions Row -->
            <!--<div class="row mt-3">
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-2">
                <div class="d-grid">
                  <a href="#" class="btn btn-outline-info">
                    <i class="bi bi-people me-2"></i>Manage Users
                  </a>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-2">
                <div class="d-grid">
                  <a href="#" class="btn btn-outline-warning">
                    <i class="bi bi-bar-chart me-2"></i>View Reports
                  </a>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-2">
                <div class="d-grid">
                  <a href="#" class="btn btn-outline-secondary">
                    <i class="bi bi-gear me-2"></i>Settings
                  </a>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-2">
                <div class="d-grid">
                  <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                    <i class="bi bi-upload me-2"></i>Bulk Import
                  </button>
                </div>
              </div>
            </div>--->
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row mt-4">
      <div class="col-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
              <i class="bi bi-clock-history"></i> Recent Activity
            </h4>
            <a href="{{route('audit.index')}}" class="btn btn-sm btn-outline-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              @if(!empty($logs))
                @foreach($logs as $log)

                @php
                  $string = '';
                  if($log->event == 'created'){
                    $string = "New ".  class_basename($log->auditable_type);
                  }elseif($log->event == 'updated'){
                    $string = "Updated ".  class_basename($log->auditable_type); 
                  }elseif($log->event == 'deleted'){
                    $string = "Deleted ".  class_basename($log->auditable_type); 
                  }else{
                    $string =  ucfirst($log->event) ;
                  }
                @endphp
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <strong>#{{ $log->auditable_id }} {{$string}}:</strong> "{{ $log->user ? $log->user->full_name : 'System' }}" 
                   On  {{ $log->created_at->format('M d, Y') }}, {{ $log->created_at->format('h:i A') }}
                  </div>
                  <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
              <i class="bi bi-box-arrow-in-right"></i> Login Activity
            </h4>
            
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              @if(!empty($loginlogs))
                @foreach($loginlogs as $log)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <i class="bi bi-alarm text-primary me-2"></i>
                    <strong>{{ $log->user ? $log->user->full_name : 'System' }} Logged</strong>
                   On  {{ $log->created_at->format('M d, Y') }}, {{ $log->created_at->format('h:i A') }}
                  </div>
                  <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<style>
.card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quick-action-card {
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.quick-action-card:hover {
  border-color: var(--bs-primary);
  transform: translateY(-5px);
}

.list-group-item {
  border-left: none;
  border-right: none;
}

.list-group-item:first-child {
  border-top: none;
}

.list-group-item:last-child {
  border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action buttons
    const actionCards = document.querySelectorAll('.card');
    
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Handle bulk import form validation
    const importForm = document.querySelector('#bulkImportModal form');
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('importFile');
            const importType = document.getElementById('importType');
            
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Please select a file to import.');
                return;
            }
            
            if (!importType.value) {
                e.preventDefault();
                alert('Please select an import type.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Importing...';
            submitBtn.disabled = true;
        });
    }
});
</script>

@endsection
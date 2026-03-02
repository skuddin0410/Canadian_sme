@extends('layouts.admin')

@section('title', 'Session Analytics')

@section('content')

<div class="analytics-page">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1"><i class="fas fa-chart-line me-2" style="color:#696cff;"></i>Session Analytics</h4>
            <p class="text-muted mb-0">Track session attendance and popularity metrics</p>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label">Total Sessions</div>
                    <div class="kpi-value" id="kpi-sessions">--</div>
                </div>
                <i class="bi bi-calendar-event kpi-icon" style="color:#696cff;"></i>
            </div>
        </div>
        <div class="kpi-card kpi-favorites">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label">Total Favorites</div>
                    <div class="kpi-value" id="kpi-favorites">--</div>
                </div>
                <i class="bi bi-heart-fill kpi-icon" style="color:#ff3e1d;"></i>
            </div>
        </div>
        <div class="kpi-card kpi-agenda">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label">Total Agendas</div>
                    <div class="kpi-value" id="kpi-agendas">--</div>
                </div>
                <i class="bi bi-journal-check kpi-icon" style="color:#71dd37;"></i>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form id="analyticsFilterForm" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Event</label>
                <select class="form-select" id="filter-event" name="event_id">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Track</label>
                <select class="form-select" id="filter-track" name="track">
                    <option value="">All Tracks</option>
                    @foreach($tracks as $track)
                        <option value="{{ $track }}">{{ $track }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        {{-- Attendance Bar Chart --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5><i class="bi bi-bar-chart me-2"></i>Attendance (User Agenda)</h5>
                    <span class="text-muted" style="font-size:.8rem;">Users who added session to agenda</span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-loading" id="attendanceLoading">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Popularity Bar Chart --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5><i class="bi bi-heart me-2"></i>Popularity (Favorites)</h5>
                    <span class="text-muted" style="font-size:.8rem;">Users who favorited (heart click)</span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-loading" id="popularityLoading">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                    <canvas id="popularityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataUrl = "{{ route('admin.analytics.session.data') }}";

    // Chart instances
    let attendanceChartInstance = null;
    let popularityChartInstance = null;

    // Color palette
    const colors = {
        primary: 'rgba(105, 108, 255, 0.85)',
        info: 'rgba(3, 195, 236, 0.85)',
        success: 'rgba(113, 221, 55, 0.85)',
        danger: 'rgba(255, 62, 29, 0.85)',
    };

    function showLoading(show) {
        ['attendanceLoading', 'popularityLoading'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = show ? 'flex' : 'none';
        });
    }

    function destroyCharts() {
        if (attendanceChartInstance) attendanceChartInstance.destroy();
        if (popularityChartInstance) popularityChartInstance.destroy();
    }

    function fetchData() {
        showLoading(true);

        const params = new URLSearchParams();
        params.append('event_id', document.getElementById('filter-event').value);
        params.append('track', document.getElementById('filter-track').value);

        fetch(dataUrl + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            destroyCharts();
            renderKPIs(data.kpis);
            renderAttendanceChart(data.attendance);
            renderPopularityChart(data.popularity);
            showLoading(false);
        })
        .catch(err => {
            console.error('Error:', err);
            showLoading(false);
        });
    }

    function renderKPIs(kpis) {
        document.getElementById('kpi-sessions').textContent = kpis.total_sessions;
        document.getElementById('kpi-favorites').textContent = kpis.total_favorites;
        document.getElementById('kpi-agendas').textContent = kpis.total_agendas;
    }

    function renderAttendanceChart(data) {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const sorted = data.slice(0, 15);
        attendanceChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sorted.map(s => s.label),
                datasets: [{
                    label: 'Added to Agenda',
                    data: sorted.map(s => s.count),
                    backgroundColor: colors.success,
                    borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
        });
    }

    function renderPopularityChart(data) {
        const ctx = document.getElementById('popularityChart').getContext('2d');
        const sorted = data.slice(0, 15);
        popularityChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sorted.map(s => s.label),
                datasets: [{
                    label: 'Favorites (Hearts)',
                    data: sorted.map(s => s.count),
                    backgroundColor: colors.danger,
                    borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
        });
    }

    document.getElementById('analyticsFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchData();
    });

    fetchData();
});
</script>
@endsection

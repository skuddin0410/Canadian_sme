@extends('layouts.admin')

@section('title', 'Speaker Analytics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            <span class="text-muted fw-light">Analytics /</span> Speaker
        </h4>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Speakers</h6>
                    <h3 class="fw-bold mb-0">
                        {{ $speakerAnalytics->count() ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Attendees</h6>
                    <h3 class="fw-bold mb-0">
                        {{ $speakerAnalytics->sum('total_attendees') ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Top Speaker</h6>
                    <h5 class="fw-bold mb-0">
                        {{ optional($speakerAnalytics->sortByDesc('total_attendees')->first())->name ?? 'N/A' }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Speaker Attendance Overview</h5>
        </div>
        <div class="card-body">
            <canvas id="speakerChart" height="100"></canvas>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Detailed Speaker Data</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Speaker Name</th>
                        <th>Total Attendees</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($speakerAnalytics as $index => $speaker)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $speaker->name }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $speaker->total_attendees }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                No speaker data available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const speakerNames = @json($speakerAnalytics->pluck('name'));
        const attendeeCounts = @json($speakerAnalytics->pluck('total_attendees'));

        const ctx = document.getElementById('speakerChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: speakerNames,
                datasets: [{
                    label: 'Total Attendees',
                    data: attendeeCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

    });
</script>
@endsection
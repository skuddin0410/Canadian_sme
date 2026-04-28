@extends('layouts.admin')

@section('title', 'Speaker Analytics')

@section('content')



<div class="sa-wrap">


    {{-- Header --}}
    <div class="sa-header-card">

        <div class="sa-header-top">
            <div class="sa-header-left">
                <div class="sa-breadcrumb">Analytics</div>
                <div class="sa-title">Speakers</div>
            </div>

            <div class="sa-header-right">

                <form method="GET" action="{{ route('admin.analytics.speaker') }}" class="sa-filter-form">
                    <select name="event_id" onchange="this.form.submit()" class="sa-filter-select">
                        <option value="">All Events</option>

                        @foreach($events as $event)
                        <option value="{{ $event->id }}"
                            {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                        @endforeach
                    </select>
                </form>

                <div class="sa-date" id="sa-date-display"></div>

            </div>
        </div>

    </div>

    {{-- Summary Cards --}}
    <div class="sa-cards">
        <div class="sa-card accent-blue">
            <div class="sa-card-label">Total Speakers</div>
            <div class="sa-card-value">{{ $speakerAnalytics->count() ?? 0 }}</div>
            <div class="sa-card-icon">🎤</div>
        </div>

        <div class="sa-card accent-green">
            <div class="sa-card-label">Total Attendees</div>
            <div class="sa-card-value">{{ number_format($speakerAnalytics->sum('total_attendees') ?? 0) }}</div>
            <div class="sa-card-icon">👥</div>
        </div>

        <div class="sa-card accent-amber">
            <div class="sa-card-label">Top Speaker</div>
            <div class="sa-card-value name">{{ optional($speakerAnalytics->sortByDesc('total_attendees')->first())->name ?? 'N/A' }}</div>
            <div class="sa-card-icon">🏆</div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="sa-chart-card">
        <div class="sa-card-head">
            <span class="sa-card-head-title">Attendance by Speaker</span>
            <span>
                <span class="sa-legend-dot"></span>
                <span class="sa-legend-label">Total Attendees</span>
            </span>
        </div>
        <div class="sa-chart-wrap">
            <canvas id="speakerChart"></canvas>
            <div id="speakerChartNoData" class="no-data-placeholder" style="display: none;">
                <i class="bx bx-bar-chart-alt-2 fs-1 mb-2"></i>
                <p class="mb-0">No speaker data available</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-card">
        <div class="sa-card-head">
            <span class="sa-card-head-title">Detailed Speaker Data</span>
            <span class="sa-legend-label">{{ $speakerAnalytics->count() }} records</span>
        </div>
        <table class="sa-table">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Speaker</th>
                    <th>Attendees</th>
                </tr>
            </thead>
            <tbody>
                @php
                $maxAttendees = $speakerAnalytics->max('total_attendees') ?: 1;
                @endphp
                @forelse($speakerAnalytics as $index => $speaker)
                <tr>
                    <td><span class="sa-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span></td>
                    <td>
                        <span class="sa-speaker-name">{{ $speaker->name }}</span>
                        <span class="sa-bar-mini" style="width: {{ round(($speaker->total_attendees / $maxAttendees) * 100) }}%"></span>
                    </td>
                    <td>
                        <span class="sa-badge">{{ number_format($speaker->total_attendees) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="sa-empty">No speaker data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</script>
<style>
.sa-chart-wrap { position: relative; min-height: 400px; }
.no-data-placeholder { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #a1acb8; border: 1px dashed #d9dee3; border-radius: 8px; background: rgba(255, 255, 255, 0.05); }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Date display
        const d = new Date();
        document.getElementById('sa-date-display').textContent =
            d.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

        const speakerNames = @json($speakerAnalytics -> pluck('name'));
        const attendeeCounts = @json($speakerAnalytics -> pluck('total_attendees'));

        if (speakerNames.length === 0) {
            document.getElementById('speakerChart').style.display = 'none';
            document.getElementById('speakerChartNoData').style.display = 'flex';
            return;
        }

        const ctx = document.getElementById('speakerChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: speakerNames,
                datasets: [{
                    label: 'Total Attendees',
                    data: attendeeCounts,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {
                            ctx: c,
                            chartArea
                        } = chart;
                        if (!chartArea) return 'rgba(116, 127, 224, 0.7)';
                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(87, 112, 224, 0.85');
                        gradient.addColorStop(1, 'rgba(96, 165, 250, 0.35)');
                        return gradient;
                    },
                    borderColor: 'rgba(59, 130, 246, 0.9)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(59, 130, 246, 0.95)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#f9fafb',
                        bodyColor: '#9ca3af',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: {
                            family: 'DM Sans',
                            weight: '600',
                            size: 13
                        },
                        bodyFont: {
                            family: 'DM Sans',
                            size: 12
                        },
                        callbacks: {
                            label: function(ctx) {
                                return '  ' + ctx.parsed.y.toLocaleString() + ' attendees';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#9ca3af',
                            font: {
                                family: 'DM Sans',
                                size: 12,
                                weight: '500'
                            },
                            maxRotation: 35,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        border: {
                            display: false,
                            dash: [4, 4]
                        },
                        ticks: {
                            precision: 0,
                            color: '#9ca3af',
                            font: {
                                family: 'DM Mono',
                                size: 11
                            },
                            callback: function(val) {
                                return val >= 1000 ? (val / 1000).toFixed(1) + 'k' : val;
                            }
                        }
                    }
                },
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                }
            }
        });
    });
</script>
@endsection
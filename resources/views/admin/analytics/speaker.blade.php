@extends('layouts.admin')

@section('title', 'Speaker Analytics')

@section('content')

<style>
    .sa-wrap * {
        font-family: 'DM Sans', sans-serif;
        box-sizing: border-box;
    }

    .sa-wrap {
        background: #f0f2f5;
        min-height: 100vh;
        padding: 2rem 2.5rem;
    }

    /* Header */
    .sa-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2.5rem;
    }

    .sa-breadcrumb {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 0.25rem;
    }

    .sa-title {
        font-size: 1.85rem;
        font-weight: 700;
        color: #111827;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .sa-date {
        font-size: 0.8rem;
        color: #9ca3af;
        font-family: 'DM Mono', monospace;
    }

    /* Stat Cards */
    .sa-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .sa-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        position: relative;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .sa-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
    }

    .sa-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .sa-card.accent-blue::before {
        background: linear-gradient(90deg, #696cff, #696cff);
    }

    .sa-card.accent-green::before {
        background: linear-gradient(90deg, #696cff, #696cff);
    }

    .sa-card.accent-amber::before {
        background: linear-gradient(90deg, #696cff, #696cff);
    }

    .sa-card-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 0.75rem;
    }

    .sa-card-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: #111827;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .sa-card-value.name {
        font-size: 1.35rem;
        letter-spacing: -0.02em;
    }

    .sa-card-icon {
        position: absolute;
        bottom: 1rem;
        right: 1.25rem;
        font-size: 2rem;
        opacity: 0.07;
    }

    /* Chart Card */
    .sa-chart-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.75rem;
        overflow: hidden;
    }

    .sa-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .sa-card-head-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        letter-spacing: -0.01em;
    }

    .sa-legend-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #696cff;
        margin-right: 6px;
    }

    .sa-legend-label {
        font-size: 0.75rem;
        color: #9ca3af;
        font-weight: 500;
    }

    .sa-chart-wrap {
        padding: 1.5rem 1.75rem 1.75rem;
        /* Fixed height for the chart container */
        height: 320px;
        position: relative;
    }

    .sa-chart-wrap canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* Table Card */
    .sa-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .sa-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sa-table thead tr {
        background: #f9fafb;
    }

    .sa-table thead th {
        padding: 0.85rem 1.5rem;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #9ca3af;
        text-align: left;
        border-bottom: 1px solid #f3f4f6;
    }

    .sa-table tbody tr {
        border-bottom: 1px solid #f9fafb;
        transition: background 0.15s ease;
    }

    .sa-table tbody tr:last-child {
        border-bottom: none;
    }

    .sa-table tbody tr:hover {
        background: #f9fafb;
    }

    .sa-table tbody td {
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
    }

    .sa-index {
        font-family: 'DM Mono', monospace;
        font-size: 0.75rem;
        color: #d1d5db;
        font-weight: 500;
    }

    .sa-speaker-name {
        font-weight: 500;
        color: #111827;
    }

    .sa-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        color: #696cff;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 100px;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.02em;
    }

    .sa-bar-mini {
        display: block;
        height: 4px;
        background: linear-gradient(90deg, #696cff, #696cff);
        border-radius: 2px;
        margin-top: 6px;
        transition: width 0.6s ease;
    }

    .sa-empty {
        padding: 3rem;
        text-align: center;
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Header Card */
    .sa-header-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 24px;
        border: 1px solid #eef0f4;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
    }

    /* Header Layout */
    .sa-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Left */
    .sa-header-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sa-breadcrumb {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
        letter-spacing: .04em;
    }

    .sa-title {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    /* Right side */
    .sa-header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Filter */
    .sa-filter-select {
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        background: #fff;
        color: #374151;
        cursor: pointer;
    }

    /* Date */
    .sa-date {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .sa-cards {
            grid-template-columns: 1fr;
        }

        .sa-wrap {
            padding: 1.25rem;
        }
    }
</style>

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
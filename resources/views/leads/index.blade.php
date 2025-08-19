@extends('layouts.admin')

@section('title', 'Leads')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Lead Management</h1>
            <p class="text-muted mb-0">Manage and track your event leads</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="fa fa-plus me-2"></i> Add New Lead
            </a>
        </div>
        
    </div>
    <div class="mb-3">
    <a href="{{ route('leads.export', 'xlsx') }}" class="btn btn-success btn-sm">
        <i class="fa fa-file-excel me-1"></i> Export Excel
    </a>
    <a href="{{ route('leads.export', 'csv') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-file-csv me-1"></i> Export CSV
    </a>
    
</div>


    <!-- Filters + Advanced Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('leads.index') }}" class="row g-3 align-items-center">
                <!-- Search by Lead Name -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search leads..." class="form-control">
                    </div>
                </div>

                <!-- Search by Company -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-building"></i></span>
                        <input type="text" name="company" value="{{ request('company') }}" placeholder="Search by company..." class="form-control">
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>

                <!-- Source -->
                <div class="col-md-2">
                    <select name="source" class="form-select">
                        <option value="">All Sources</option>
                        <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="social_media" {{ request('source') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="walk_in" {{ request('source') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                        <option value="phone" {{ request('source') == 'phone' ? 'selected' : '' }}>Phone</option>
                    </select>
                </div>

                <!-- Filter & Clear -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fa fa-filter me-2"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','company','status','source']))
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lead</th>
                        <th>Contact</th>
                        <th>Interest</th>
                        <th>Budget</th>
                        <th>Desired Amenities</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Date</th>
                        <th>Owner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <!-- Lead Info -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3"
                                         style="width:40px;height:40px;background-color:{{ ['#3b82f6','#8b5cf6','#10b981','#f97316','#ef4444'][array_rand(['#3b82f6','#8b5cf6','#10b981','#f97316','#ef4444'])] }}">
                                        {{ strtoupper(substr($lead->first_name,0,1) . substr($lead->last_name,0,1)) }}
                                    </div>
                                    <div>
                                        <a href="#" class="fw-semibold text-decoration-none text-dark">{{ $lead->first_name }} {{ $lead->last_name }}</a>
                                        <div class="text-muted small">Priority: {{ ucfirst($lead->priority) }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <div>{{ $lead->email }}</div>
                                <div class="text-muted small">{{ $lead->phone }}</div>
                            </td>

                            <!-- Interest -->
                            <td>
                                <div>{{ $lead->desired_bedrooms ? $lead->desired_bedrooms . 'BR Apartment' : 'Unspecified' }}</div>
                                <div class="text-muted small">{{ $lead->move_in_date ? 'Move-in: ' . $lead->move_in_date->format('M j, Y') : 'Flexible' }}</div>
                            </td>

                            <!-- Budget -->
                            <td>
                                @if($lead->budget_min || $lead->budget_max)
                                    ${{ number_format($lead->budget_min ?? 0) }} - ${{ number_format($lead->budget_max ?? 0) }}
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Desired Amenities -->
                            <td>
                                @php
                                    $amenities = $lead->desired_amenities ? json_decode($lead->desired_amenities, true) : [];
                                @endphp
                                @if(!empty($amenities))
                                    {{ implode(', ', $amenities) }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="badge 
                                    @if($lead->status=='new') bg-primary
                                    @elseif($lead->status=='contacted') bg-warning text-dark
                                    @elseif($lead->status=='qualified') bg-success
                                    @elseif($lead->status=='converted') bg-info text-dark
                                    @elseif($lead->status=='lost') bg-danger
                                    @endif">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>

                            <!-- Source -->
                            <td>{{ ucfirst(str_replace('_',' ',$lead->source)) }}</td>

                            <!-- Date -->
                            <td class="text-muted small">{{ $lead->created_at->diffForHumans() }}</td>

                            <!-- Owner -->
                            <td>
                                @if($lead->company)
                                    <a href="#" class="fw-semibold text-decoration-none text-dark">{{ $lead->company->name }}</a>
                                    <div class="text-muted small">(Company)</div>
                                @elseif($lead->user)
                                    <a href="#" class="fw-semibold text-decoration-none text-dark">{{ $lead->user->full_name }}</a>
                                    <div class="text-muted small">(User)</div>
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>
                                <a href="{{ route('leads.show',$lead) }}" class="text-primary me-2"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('leads.edit',$lead) }}" class="text-success me-2"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('leads.destroy',$lead) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No leads found. 
                                <a href="{{ route('leads.create') }}" class="text-primary fw-bold">Create your first lead</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="card-footer">
                {{ $leads->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

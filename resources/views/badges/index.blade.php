@extends('layouts.admin')

@section('title', 'All Badges')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0 mt-3">
    <div class="container">
     <div class="row">
      <div class="col-xl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-id-badge me-2"></i>Generated Badges</h2>
        <a href="{{ route('badges.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Badge
        </a>
    </div>

        @if($badges->count() > 0)
            <div class="row">
                @foreach($badges as $badge)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            @if($badge->badge_path)
                                <img src="{{ Storage::url($badge->badge_path) }}" class="card-img-top" alt="Badge" style="height: 200px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $badge->badge_name ?? 'Badge #' . $badge->id }}
                                </h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Fields: {{ implode(', ', $badge->selected_fields) }}
                                    </small>
                                </p>
                                <div class="btn-group w-100">
                                    <a href="{{ route('badges.show', $badge) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('badges.download', $badge) }}" class="btn btn-outline-success">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $badges->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-id-badge fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No badges generated yet</h4>
                <p class="text-muted">Create your first badge to get started!</p>
                <a href="{{ route('badges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Badge
                </a>
            </div>
        @endif
    </div>
    </div>

    </div>
</div>
@endsection
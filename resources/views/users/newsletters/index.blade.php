@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Newsletter Management</h2>
                <a href="{{ route('newsletters.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Newsletter
                </a>
            </div>

            <!-- Stats -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded text-center ">
                        <h4 class="fw-bold text-white">{{ $stats['total_newsletters'] }}</h4>
                        <small class="text-white">Total Newsletters</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-secondary  rounded text-center">
                        <h4 class="fw-bold text-white">{{ $stats['sent_newsletters'] }}</h4>
                        <small class="text-white">Sent</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded text-center">
                        <h4 class="fw-bold text-white">{{ $stats['total_subscribers'] }}</h4>
                        <small class="text-white">Subscribers</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-secondary  rounded text-center">
                        <h4 class="fw-bold text-white">{{ $stats['draft_newsletters'] }}</h4>
                        <small class="text-white">Drafts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletters Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Newsletters</h5>
            <a href="{{route('newsletter-subscribers.index')}}" class="text-decoration-none">Manage Subscribers</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Recipients</th>
                        <th>Open Rate</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsletters as $newsletter)
                    <tr>
                        <td>
                            <strong>{{ $newsletter->subject }}</strong><br>
                            <small class="text-muted">{{ $newsletter->template_name }}</small>
                        </td>
                        <td>
                            <span class="badge 
                                @if($newsletter->status === 'sent') bg-success
                                @elseif($newsletter->status === 'sending') bg-primary
                                @elseif($newsletter->status === 'scheduled') bg-warning text-dark
                                @elseif($newsletter->status === 'failed') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($newsletter->status) }}
                            </span>
                        </td>
                        <td>
                            {{ number_format($newsletter->total_recipients) }}
                            @if($newsletter->sent_count > 0)
                                <div class="small text-muted">{{ $newsletter->sent_count }} sent</div>
                            @endif
                        </td>
                        <td>
                            @if($newsletter->status === 'sent')
                                {{ $newsletter->open_rate }}%
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            {{ $newsletter->created_at->format('M j, Y') }}<br>
                            <small class="text-muted">{{ $newsletter->creator->name }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('newsletters.show', $newsletter) }}" class="btn btn-sm btn-outline-primary">View</a>
                                
                                @if($newsletter->canBeSent())
                                <a href="{{ route('newsletters.edit', $newsletter) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @endif
                                
                                @if($newsletter->status === 'draft')
                                <form action="" method="POST" onsubmit="return confirm('Send this newsletter now?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Send</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No newsletters found. 
                            <a href="{{ route('newsletters.create') }}" class="text-primary">Create your first newsletter</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($newsletters->hasPages())
        <div class="card-footer">
            {{ $newsletters->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

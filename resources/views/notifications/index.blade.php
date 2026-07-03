@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bx bx-bell me-2"></i>Notifications
                </h5>
                <small class="text-muted">Complete notification history for your admin account</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">Total: {{ $notifications->total() }}</span>
                <button type="button" class="btn btn-sm btn-outline-primary notifications-mark-all-page" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all notifications as read">
                    <i class="bx bx-envelope-open me-1"></i>Mark all as read
                </button>
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Back to dashboard">
                    Back to Dashboard
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item notification-row {{ $notification->is_read ? '' : 'unread-notification bg-light' }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    @if($notification->related_type == 'failed_login')
                                        <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-error"></i></span>
                                    @elseif(str_contains($notification->related_type, 'gallery'))
                                        <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-image"></i></span>
                                    @elseif($notification->related_type == 'attendee_import')
                                        <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-import"></i></span>
                                    @elseif($notification->related_type == 'attendee_registration')
                                        <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-user-plus"></i></span>
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-bell"></i></span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <p class="mb-1">{{ $notification->body }}</p>
                                    </div>
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $notification->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                    @if($notification->created_at)
                                        <span class="ms-2">{{ $notification->created_at->diffForHumans() }}</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No notifications found.</div>
                @endforelse
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-white">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        new bootstrap.Tooltip(element);
    });

    const markAllButton = document.querySelector('.notifications-mark-all-page');
    if (!markAllButton) {
        return;
    }

    markAllButton.addEventListener('click', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("notifications.markAllAsRead") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-row.unread-notification').forEach(function (row) {
                    row.classList.remove('unread-notification', 'bg-light');

                    const badge = row.querySelector('.badge.bg-primary');
                    if (badge) {
                        badge.remove();
                    }
                });

                markAllButton.disabled = true;
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
@endsection

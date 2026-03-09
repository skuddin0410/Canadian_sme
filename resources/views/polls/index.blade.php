@extends('layouts.admin')

@section('content')

<style>
    .polls-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 2rem;
        background: #f4f6fb;
        min-height: 100vh;
    }

    .polls-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 2rem;
    }

    .polls-header h4 {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1.75rem;
        color: #727275;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .polls-header p {
        margin: 0.25rem 0 0;
        color: #64748b;
        font-size: 0.875rem;
    }

    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 8px rgba(30, 41, 59, 0.18);
    }

    .btn-create:hover {
        background: #4f46e5;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 41, 59, 0.22);
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        border: 1px solid #e8edf4;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #eef2ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #4f46e5;
        flex-shrink: 0;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
        margin-bottom: 0.15rem;
    }

    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #4f46e5;
        line-height: 1;
    }
</style>

<div class="polls-wrapper">

    {{-- Header --}}
    <div class="polls-header">
        <div>
            <h4 class="text-muted fw-light">Poll Management</h4>
            <p>Create and manage polls for your events</p>
        </div>
        <a href="{{ route('polls.create') }}" class="btn-create">
            <i class="fa-solid fa-plus"></i> Create Poll
        </a>
    </div>

    {{-- Stat Card --}}
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-chart-bar"></i>
        </div>
        <div>
            <div class="stat-label">Total Polls Created</div>
            <div class="stat-value">{{ $totalPolls }}</div>
        </div>
    </div>
    

    {{-- Table --}}
    @include('polls.table', ['polls' => $polls])

</div>

@endsection
@section('scripts')

<!-- SweetAlert2 CDN (if not already added in layout) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ============================
           STATUS TOGGLE CONFIRMATION
        ============================ */
        document.querySelectorAll('.status-toggle').forEach(toggle => {

            toggle.addEventListener('change', function(e) {

                let checkbox = this;
                let pollId = this.dataset.id;
                let newState = this.checked;

                // Immediately revert UI change until confirmed
                checkbox.checked = !newState;

                Swal.fire({
                    title: 'Are you sure?',
                    text: newState ?
                        "This poll will be activated." :
                        "This poll will be deactivated.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, update it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (result.isConfirmed) {

                        fetch(`/admin/polls/${pollId}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error();
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {

                                    // Apply actual state
                                    checkbox.checked = newState;

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: 'Poll status updated successfully.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                } else {
                                    Swal.fire('Error', 'Failed to update status.', 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Error', 'Something went wrong.', 'error');
                            });

                    }

                });

            });

        });


        /* ============================
           DELETE CONFIRMATION
        ============================ */
        document.querySelectorAll('.delete-form').forEach(form => {

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let currentForm = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This poll will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentForm.submit();
                    }
                });

            });

        });

    });
</script>
@endsection
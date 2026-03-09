<style>
    .table-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        border: 1px solid #e8edf4;
    }

    .table-card .table {
        margin-bottom: 0;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
    }

    .table-card .table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-card .table tbody tr {
        transition: background 0.15s ease;
    }

    .table-card .table tbody tr:hover {
        background: #f8fafc;
    }

    .table-card .table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        color: #334155;
        border-color: #f1f5f9;
    }

    .table-card .table tbody td:first-child {
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .poll-title {
        font-weight: 600;
        color: #1e293b;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .badge-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .badge-active {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge-active::before {
        background: #16a34a;
    }

    .badge-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .badge-inactive::before {
        background: #94a3b8;
    }

    .badge-upcoming {
        background: #fef9c3;
        color: #a16207;
    }

    .badge-upcoming::before {
        background: #eab308;
    }

    .badge-expired {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-expired::before {
        background: #dc2626;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .action-btn-view {
        background: #e0f2fe;
        color: #4f46e5;
    }

    .action-btn-view:hover {
        background: #4f46e5;
        color: #fff;
    }

    .action-btn-edit {
        background: #ede9fe;
        color: #7c3aed;
    }

    .action-btn-edit:hover {
        background: #7c3aed;
        color: #fff;
    }

    .action-btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .action-btn-delete:hover {
        background: #dc2626;
        color: #fff;
    }

    .table-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
    }

    .empty-state {
        text-align: center;
        padding: 3.5rem 1rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        color: #cbd5e1;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        background-color: #e2e8f0;
        border-radius: 24px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        transition: .3s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .3s;
    }

    input:checked+.slider {
        background-color: #16a34a;
    }

    input:checked+.slider:before {
        transform: translateX(22px);
    }
</style>

<div class="table-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Event</th>
                    <th>Session</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="110">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($polls as $poll)
                <tr>
                    <td>{{ $poll->id }}</td>
                    <td class="poll-title">{{ $poll->title }}</td>
                    <td>{{ $poll->event->title ?? '—' }}</td>
                    <td>{{ $poll->eventSession->name ?? 'All Sessions' }}</td>

                    <td>{{ $poll->start_date ? $poll->start_date->format('d M Y, H:i') : '—' }}</td>
                    <td>{{ $poll->end_date ? $poll->end_date->format('d M Y, H:i') : '—' }}</td>

                    <td>
                        <label class="switch">
                            <input type="checkbox"
                                class="status-toggle"
                                data-id="{{ $poll->id }}"
                                {{ $poll->is_active ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td>{{ $poll->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('polls.show', $poll->id) }}" class="action-btn action-btn-view" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('polls.edit', $poll->id) }}" class="action-btn action-btn-edit" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="{{ route('polls.responses', $poll->id) }}"
                                class="action-btn action-btn-response"
                                title="Responses">
                                <i class="fa-solid fa-chart-simple"></i>
                            </a>
                            <form action="{{ route('polls.destroy', $poll->id) }}"
                                method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fa-regular fa-chart-bar"></i>
                            <p>No polls found. Create your first poll to get started.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($polls->hasPages())
    <div class="table-footer">
        {{ $polls->links() }}
    </div>
    @endif
</div>
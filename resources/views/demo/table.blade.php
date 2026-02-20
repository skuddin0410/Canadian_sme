<div class="table-responsive">
    @if($demos->count() > 0)

    <table class="demo-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Requester</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Timezone</th>

                <th> Slot</th>
                <th>Status</th>
                <th>Booked At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demos as $demo)
            <tr>
                <td>
                    <span class="ticket-id">{{ $loop->iteration + ($demos->currentPage() - 1) * $demos->perPage() }}</span>
                </td>

                <td>
                    <div class="name-cell">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($demo->name ?? optional($demo->user)->name ?? '?', 0, 1)) }}
                        </div>
                        <span class="name-text">{{ $demo->name ?? optional($demo->user)->name }}</span>
                    </div>
                </td>

                <td>
                    <span class="email-text">{{ $demo->email ?? optional($demo->user)->email }}</span>
                </td>

                <td>
                    <span class="meta-text">{{ $demo->phone ?? optional($demo->user)->phone ?? '—' }}</span>
                </td>

                <td>
                    <span class="meta-text">{{ $demo->timezone }}</span>
                </td>



                <td>
                    <span class="date-cell">{{ \Carbon\Carbon::parse($demo->booking_date)->format('d M Y') }}</span>
                    <span class="time-badge">{{ $demo->time_slot }}</span>
                </td>

                <td>
                    <form action="{{ route('demo.updateStatus', $demo->id) }}"
                        method="POST"
                        class="status-form">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                            class="status-{{ $demo->status }}"
                            onchange="this.form.submit()">
                            <option value="pending" {{ $demo->status === 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="confirm" {{ $demo->status === 'confirm'    ? 'selected' : '' }}>✅ Confirm</option>
                            <option value="reschedule" {{ $demo->status === 'reschedule' ? 'selected' : '' }}>🔄 Reschedule</option>
                            <option value="cancel" {{ $demo->status === 'cancel'     ? 'selected' : '' }}>❌ Cancel</option>
                            <option value="completed" {{ $demo->status === 'completed'  ? 'selected' : '' }}>🎉 Completed</option>
                        </select>
                    </form>
                </td>

                <td>
                    <span class="date-cell">{{ $demo->created_at->format('d M Y H:i') }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="demo-pagination-wrap custom_pagination">
        {!! $demos->links() !!}
    </div>

    @else

    <div class="demo-empty">
        <div class="demo-empty-icon">📭</div>
        <p style="font-weight:600; color:#3c4165; margin-bottom:4px;">No demo requests found</p>
        <p style="font-size:0.85rem;">Bookings will appear here once submitted.</p>
    </div>

    @endif
</div>
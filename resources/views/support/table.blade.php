<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

    .support-wrapper {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Table Card ── */
    .support-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8eaf0;
        box-shadow: 0 2px 12px rgba(15, 20, 50, 0.06);
        overflow: hidden;
    }

    /* ── Table ── */
    .support-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .support-table thead tr {
        background: #f5f6fa;
        border-bottom: 2px solid #e8eaf0;
    }

    .support-table thead th {
        padding: 14px 18px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8b91a7;
        white-space: nowrap;
    }

    .support-table tbody tr {
        border-bottom: 1px solid #f0f1f6;
        transition: background 0.15s ease;
    }

    .support-table tbody tr:last-child {
        border-bottom: none;
    }

    .support-table tbody tr:hover {
        background: #fafbff;
    }

    .support-table td {
        padding: 14px 18px;
        vertical-align: middle;
        color: #2c3050;
    }

    /* ── ID Badge ── */
    .ticket-id {
        font-family: 'DM Mono', monospace;
        font-size: 0.78rem;
        color: #8b91a7;
        background: #f0f1f6;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* ── Name / Avatar Row ── */
    .name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .name-text {
        font-weight: 500;
        color: #1a1d30;
    }

    /* ── Email Link ── */
    .email-link {
        color: #4f6ef7;
        text-decoration: none;
        font-size: 0.83rem;
    }

    .email-link:hover {
        text-decoration: underline;
    }

    /* ── Subject ── */
    .subject-text {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #3c4165;
        font-weight: 500;
    }

    /* ── Status Select ── */
    .status-form .form-select {
        border: none;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 5px 28px 5px 12px;
        cursor: pointer;
        appearance: auto;
        min-width: 110px;
        outline: none;
        transition: box-shadow 0.2s;
    }

    .status-form .form-select:focus {
        box-shadow: 0 0 0 3px rgba(79, 110, 247, 0.18);
    }

    .status-pending {
        background: #fff8e6;
        color: #b97b00;
    }

    .status-inprogress {
        background: #e8f0ff;
        color: #3558d6;
    }

    .status-completed {
        background: #e6faf2;
        color: #1a8a5c;
    }

    /* ── Added By ── */
    .added-by {
        font-size: 0.82rem;
        color: #5e6388;
    }

    /* ── Date ── */
    .date-cell {
        font-family: 'DM Mono', monospace;
        font-size: 0.78rem;
        color: #8b91a7;
        white-space: nowrap;
    }

    /* ── View Button ── */
    .btn-view {
        background: #4f6ef7;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s, transform 0.12s;
        letter-spacing: 0.02em;
    }

    .btn-view:hover {
        background: #3558d6;
        transform: translateY(-1px);
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 64px 24px;
        color: #8b91a7;
    }

    .empty-state-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
        opacity: 0.4;
    }

    /* ── Pagination ── */
    .pagination-wrapper {
        padding: 18px 24px;
        border-top: 1px solid #f0f1f6;
        display: flex;
        justify-content: center;
    }

    /* ── Modal ── */
    #supportModal .modal-content {
        border: none;
        border-radius: 18px;
        box-shadow: 0 24px 64px rgba(15, 20, 50, 0.18);
        overflow: hidden;
    }

    #supportModal .modal-header {
        background: linear-gradient(135deg, #4f6ef7 0%, #764ba2 100%);
        border: none;
        padding: 22px 28px;
    }

    #supportModal .modal-title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        color: #fff;
        letter-spacing: 0.01em;
    }

    #supportModal .btn-close {
        filter: invert(1) brightness(2);
    }

    #supportModal .modal-body {
        padding: 28px;
        font-family: 'DM Sans', sans-serif;
    }

    .modal-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .modal-info-item {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 12px 16px;
        border: 1px solid #eef0f7;
    }

    .modal-info-item .label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8b91a7;
        margin-bottom: 4px;
    }

    .modal-info-item .value {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1a1d30;
        word-break: break-word;
    }

    .modal-description-block {
        background: #f5f6fa;
        border-left: 4px solid #4f6ef7;
        border-radius: 0 10px 10px 0;
        padding: 16px 18px;
        font-size: 0.88rem;
        color: #3c4165;
        line-height: 1.7;
    }

    .modal-section-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8b91a7;
        margin-bottom: 10px;
    }
</style>

<div class="support-wrapper">

@if($supports->count() > 0)

<div class="support-table-card">
    <div class="table-responsive">
        <table class="support-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Requester</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                   
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($supports as $support)
                <tr>
                    <td>
                        <span class="ticket-id">{{ $loop->iteration + ($supports->currentPage() - 1) * $supports->perPage() }}</span>
                    </td>

                    <td>
                        <div class="name-cell">
                            <div class="avatar-circle">{{ strtoupper(substr($support->name, 0, 1)) }}</div>
                            <span class="name-text">{{ $support->name }}</span>
                        </div>
                    </td>

                    <td>
                        <a class="email-link" href="mailto:{{ $support->email }}?subject=Re: {{ urlencode($support->subject) }}">
                            {{ $support->email }}
                        </a>
                    </td>

                    <td>
                        <span class="subject-text" title="{{ $support->subject }}">{{ $support->subject }}</span>
                    </td>

                    <td>
                        <form class="status-form" action="{{ route('support.updateStatus', $support->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status"
                                class="form-select status-{{ $support->status }}"
                                onchange="this.form.submit()">
                                <option value="pending"    {{ $support->status === 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="inprogress" {{ $support->status === 'inprogress' ? 'selected' : '' }}>🔄 In Progress</option>
                                <option value="completed"  {{ $support->status === 'completed'  ? 'selected' : '' }}>✅ Completed</option>
                            </select>
                        </form>
                    </td>

                   

                    <td>
                        <span class="date-cell">{{ $support->created_at->format('d M, Y H:i') }}</span>
                    </td>

                    <td>
                        <button class="btn-view viewSupportBtn"
                            data-name="{{ $support->name }}"
                            data-email="{{ $support->email }}"
                            data-phone="{{ $support->phone }}"
                            data-location="{{ $support->location }}"
                            data-subject="{{ $support->subject }}"
                            data-description="{{ $support->description }}"
                            data-date="{{ $support->created_at->format('d M Y H:i') }}">
                            View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {!! $supports->links() !!}
    </div>
</div>

@else

<div class="support-table-card">
    <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <p style="font-weight:500; color:#3c4165; margin-bottom:4px;">No support tickets found</p>
        <p style="font-size:0.85rem;">New tickets will appear here once submitted.</p>
    </div>
</div>

@endif

</div>

<!-- Support Details Modal -->
<div class="modal fade" id="supportModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">🎫 Support Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <div class="label">Name</div>
                        <div class="value" id="modal_name"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">Email</div>
                        <div class="value" id="modal_email"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">Phone</div>
                        <div class="value" id="modal_phone"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">Location</div>
                        <div class="value" id="modal_location"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">Subject</div>
                        <div class="value" id="modal_subject"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">Date</div>
                        <div class="value" id="modal_date"></div>
                    </div>
                </div>

                <div class="modal-section-label">Description</div>
                <div class="modal-description-block" id="modal_description"></div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("viewSupportBtn")) {
            document.getElementById("modal_name").innerText        = e.target.dataset.name;
            document.getElementById("modal_email").innerText       = e.target.dataset.email;
            document.getElementById("modal_phone").innerText       = e.target.dataset.phone || '—';
            document.getElementById("modal_location").innerText    = e.target.dataset.location || '—';
            document.getElementById("modal_subject").innerText     = e.target.dataset.subject;
            document.getElementById("modal_description").innerText = e.target.dataset.description;
            document.getElementById("modal_date").innerText        = e.target.dataset.date;
            new bootstrap.Modal(document.getElementById('supportModal')).show();
        }
    });
</script>
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
                <th>Slot</th>
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
                    @php
                    $email = $demo->email ?? optional($demo->user)->email;
                    @endphp

                    @if($email)
                    <a href="mailto:{{ $email }}" class="email-text">
                        {{ $email }}
                    </a>
                    @else
                    <span class="email-text">—</span>
                    @endif
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
                        id="status-form-{{ $demo->id }}"
                        class="status-form">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="note" class="status-note">

                        <select name="status"
                            class="status-dropdown form-select"
                            data-id="{{ $demo->id }}"
                            data-original="{{ $demo->status }}">
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

            {{-- ── NOTE ROW ── --}}
            <!-- <tr class="note-row" id="note-row-{{ $demo->id }}" style="display:none;">
                <td colspan="8" class="note-row-cell">
                    <div class="note-panel">
                        <div class="note-panel-header">
                            <div class="note-panel-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <div>
                                <p class="note-panel-title">Reason Required</p>
                                <p class="note-panel-subtitle">Please provide a reason before updating the status</p>
                            </div>
                        </div>

                        <div class="note-textarea-wrap">
                            <textarea
                                class="note-textarea inline-note-text"
                                rows="3"
                                placeholder="Describe the reason for this status change…"
                            ></textarea>
                            <div class="note-char-hint">Be specific — this will be logged against the booking.</div>
                        </div>

                        <div class="note-panel-actions">
                            <button type="button"
                                class="btn-note-cancel cancel-note-btn"
                                data-id="{{ $demo->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Discard
                            </button>
                            <button type="button"
                                class="btn-note-save save-note-btn"
                                data-id="{{ $demo->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Save & Submit
                            </button>
                        </div>
                    </div>
                </td>
            </tr> -->
            @endforeach
        </tbody>
    </table>
    {{-- ── STATUS REASON MODAL ── --}}
    <div class="modal fade" id="statusReasonModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">

                <div class="modal-header">
                    <h5 class="modal-title">Provide Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted small mb-2">
                        Please provide a reason before updating the status.
                    </p>

                    <textarea
                        id="statusReasonText"
                        class="form-control"
                        rows="4"
                        placeholder="Describe the reason..."></textarea>

                    <div class="invalid-feedback">
                        Reason is required.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button"
                        class="btn btn-primary"
                        id="saveStatusReasonBtn">
                        Save & Submit
                    </button>
                </div>

            </div>
        </div>
    </div>
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

{{-- ── STYLES ── --}}
<style>
    /* ── Note row cell ── */
    .note-row-cell {
        padding: 0 !important;
        border-top: none !important;
    }

    .note-row td {
        background: transparent;
    }

    /* ── Panel wrapper ── */
    .note-panel {
        margin: 4px 12px 12px 12px;
        background: #ffffff;
        border: 1.5px solid #e0e7ff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(99, 102, 241, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04);
        animation: notePanelIn 0.22s cubic-bezier(.34, 1.3, .64, 1) both;
    }

    @keyframes notePanelIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ── Header ── */
    .note-panel-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px 12px;
        background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
        border-bottom: 1px solid #e0e7ff;
    }

    .note-panel-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #6366f1;
        color: #fff;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .note-panel-title {
        margin: 0 0 2px;
        font-size: 0.875rem;
        font-weight: 700;
        color: #3730a3;
        letter-spacing: -0.01em;
    }

    .note-panel-subtitle {
        margin: 0;
        font-size: 0.775rem;
        color: #6b7280;
    }

    /* ── Textarea area ── */
    .note-textarea-wrap {
        padding: 14px 18px 10px;
    }

    .note-textarea {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 13px;
        font-size: 0.855rem;
        color: #1f2937;
        background: #fafafa;
        resize: vertical;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        font-family: inherit;
        line-height: 1.55;
        box-sizing: border-box;
    }

    .note-textarea:focus {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .note-textarea::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .note-textarea.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
    }

    .note-char-hint {
        margin-top: 6px;
        font-size: 0.73rem;
        color: #9ca3af;
    }

    /* ── Actions ── */
    .note-panel-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 10px 18px 14px;
        border-top: 1px solid #f3f4f6;
        background: #fafafa;
    }

    /* Discard button */
    .btn-note-cancel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 7px;
        border: 1.5px solid #e5e7eb;
        background: #ffffff;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-note-cancel:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: #374151;
    }

    /* Save button */
    .btn-note-save {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 18px;
        border-radius: 7px;
        border: none;
        background: #6366f1;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
    }

    .btn-note-save:hover {
        background: #4f46e5;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        transform: translateY(-1px);
    }

    .btn-note-save:active {
        transform: translateY(0);
        box-shadow: 0 1px 4px rgba(99, 102, 241, 0.2);
    }
</style>

{{-- ── SCRIPT ── --}}

<!-- <script>
    // Set data-original FIRST before attaching any listeners
    document.querySelectorAll('.status-dropdown').forEach(function(select) {
        select.setAttribute('data-original', select.value);
    });

    document.querySelectorAll('.status-dropdown').forEach(function(select) {
        select.addEventListener('change', function() {
            let demoId = this.dataset.id;
            let selectedStatus = this.value;
            let noteRow = document.getElementById('note-row-' + demoId);

            if (selectedStatus === 'reschedule' || selectedStatus === 'cancel') {
                noteRow.style.display = '';
                // Reset state
                noteRow.querySelector('.inline-note-text').value = '';
                noteRow.querySelector('.inline-note-text').classList.remove('is-invalid');
            } else {
                document.getElementById('status-form-' + demoId).submit();
            }
        });
    });

    document.querySelectorAll('.save-note-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            let demoId = this.dataset.id;
            let noteRow = document.getElementById('note-row-' + demoId);
            let textarea = noteRow.querySelector('.inline-note-text');
            let note = textarea.value.trim();

            if (!note) {
                textarea.classList.add('is-invalid');
                textarea.focus();
                return;
            }
            textarea.classList.remove('is-invalid');

            let form = document.getElementById('status-form-' + demoId);
            form.querySelector('.status-note').value = note;
            form.submit();
        });
    });

    document.querySelectorAll('.cancel-note-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            let demoId = this.dataset.id;
            let noteRow = document.getElementById('note-row-' + demoId);
            noteRow.style.display = 'none';

            let select = document.getElementById('status-form-' + demoId)
                .querySelector('.status-dropdown');
            select.value = select.getAttribute('data-original');
        });
    });
</script> -->
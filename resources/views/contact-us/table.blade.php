{{-- ============================================================
     EVENT SUPPORT PARTIAL  (loaded via AJAX into #support-table)
     Routes, method names, variable names — all unchanged.
     Only the presentation layer is modernized.
     ============================================================ --}}

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

:root {
    --brand:       #4f6ef7;
    --brand-dark:  #3558d6;
    --brand-soft:  #eef1fe;
    --purple:      #764ba2;
    --surface:     #ffffff;
    --surface-2:   #f7f8fc;
    --surface-3:   #f0f1f8;
    --border:      #e6e9f4;
    --border-soft: #f0f1f8;
    --text-primary:#1a1d30;
    --text-secondary:#5e6388;
    --text-muted:  #9097b8;
    --mono:        'JetBrains Mono', monospace;
    --sans:        'Plus Jakarta Sans', sans-serif;
    --radius-sm:   6px;
    --radius-md:   10px;
    --radius-lg:   16px;
    --radius-xl:   22px;
    --shadow-sm:   0 1px 3px rgba(15,20,60,.06), 0 1px 2px rgba(15,20,60,.04);
    --shadow-md:   0 4px 16px rgba(15,20,60,.08), 0 1px 4px rgba(15,20,60,.05);
    --shadow-lg:   0 12px 48px rgba(15,20,60,.13), 0 4px 16px rgba(15,20,60,.07);
}

/* ─── Wrapper ─────────────────────────────────────────── */
.es-wrapper {
    font-family: var(--sans);
    color: var(--text-primary);
}

/* ─── Card ─────────────────────────────────────────────── */
.es-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

/* ─── Table ─────────────────────────────────────────────── */
.es-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.855rem;
}

.es-table thead tr {
    background: var(--surface-2);
    border-bottom: 2px solid var(--border);
}

.es-table thead th {
    padding: 13px 20px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--text-muted);
    white-space: nowrap;
}

.es-table tbody tr {
    border-bottom: 1px solid var(--border-soft);
    transition: background 0.13s;
    animation: rowFadeIn 0.3s ease both;
}

@keyframes rowFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.es-table tbody tr:nth-child(1)  { animation-delay: .03s; }
.es-table tbody tr:nth-child(2)  { animation-delay: .06s; }
.es-table tbody tr:nth-child(3)  { animation-delay: .09s; }
.es-table tbody tr:nth-child(4)  { animation-delay: .12s; }
.es-table tbody tr:nth-child(5)  { animation-delay: .15s; }
.es-table tbody tr:nth-child(6)  { animation-delay: .18s; }
.es-table tbody tr:nth-child(7)  { animation-delay: .21s; }
.es-table tbody tr:nth-child(8)  { animation-delay: .24s; }
.es-table tbody tr:nth-child(9)  { animation-delay: .27s; }
.es-table tbody tr:nth-child(10) { animation-delay: .30s; }

.es-table tbody tr:last-child { border-bottom: none; }

.es-table tbody tr:hover { background: #fafbff; }

.es-table td {
    padding: 14px 20px;
    vertical-align: middle;
    color: var(--text-primary);
}

/* ─── ID badge ─────────────────────────────────────────── */
.es-id {
    font-family: var(--mono);
    font-size: 0.72rem;
    color: var(--text-muted);
    background: var(--surface-3);
    border: 1px solid var(--border);
    padding: 3px 9px;
    border-radius: var(--radius-sm);
    display: inline-block;
    letter-spacing: .03em;
}

/* ─── Event chip ────────────────────────────────────────── */
.es-event-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.es-event-chip svg { flex-shrink: 0; }

/* ─── Name cell ─────────────────────────────────────────── */
.es-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.es-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
    color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--brand) 0%, var(--purple) 100%);
    box-shadow: 0 2px 8px rgba(79,110,247,.28);
}

.es-name { font-weight: 600; font-size: 0.875rem; color: var(--text-primary); }
.es-email-link {
    color: var(--brand);
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 500;
    transition: color 0.15s;
}
.es-email-link:hover { color: var(--brand-dark); text-decoration: underline; }

.es-phone {
    font-family: var(--mono);
    font-size: 0.78rem;
    color: var(--text-secondary);
}

/* ─── Message snippet ───────────────────────────────────── */
.es-message {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: var(--text-secondary);
    font-size: 0.82rem;
}

/* ─── Status select ─────────────────────────────────────── */
.es-status-form { display: inline-block; }

.es-status-select {
    appearance: none;
    border: 1.5px solid transparent;
    border-radius: 20px;
    font-family: var(--sans);
    font-size: 0.76rem;
    font-weight: 700;
    padding: 5px 14px 5px 10px;
    cursor: pointer;
    outline: none;
    transition: box-shadow 0.18s, transform 0.12s;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%238b91a7' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    padding-right: 26px;
}

.es-status-select:focus {
    box-shadow: 0 0 0 3px rgba(79,110,247,.18);
    transform: scale(1.02);
}

.es-status-select.status-pending    { background-color:#fff8e6; color:#b97b00; border-color:#f5d97a; }
.es-status-select.status-inprogress { background-color:#e8f0ff; color:#3558d6; border-color:#b3c4f8; }
.es-status-select.status-completed  { background-color:#e6faf2; color:#1a8a5c; border-color:#82d9b6; }

/* ─── Date ──────────────────────────────────────────────── */
.es-date {
    font-family: var(--mono);
    font-size: 0.75rem;
    color: var(--text-muted);
    white-space: nowrap;
}

/* ─── View button ───────────────────────────────────────── */
.es-btn-view {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--brand-soft);
    color: var(--brand);
    border: 1.5px solid #c5d0fb;
    border-radius: var(--radius-md);
    padding: 6px 13px;
    font-family: var(--sans);
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s;
    letter-spacing: .02em;
    white-space: nowrap;
}

.es-btn-view:hover {
    background: var(--brand);
    color: #fff;
    border-color: var(--brand);
    box-shadow: 0 4px 14px rgba(79,110,247,.3);
    transform: translateY(-1px);
}

.es-btn-view:active { transform: translateY(0); }

/* ─── Empty state ───────────────────────────────────────── */
.es-empty {
    padding: 72px 24px;
    text-align: center;
}

.es-empty-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: var(--surface-3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.es-empty h6 { font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
.es-empty p  { font-size: 0.83rem; color: var(--text-muted); margin: 0; }

/* ─── Pagination wrapper ────────────────────────────────── */
.es-pagination {
    padding: 16px 22px;
    border-top: 1px solid var(--border-soft);
    display: flex;
    justify-content: center;
}

/* ─── Modal ─────────────────────────────────────────────── */
#supportModal .modal-content {
    border: none;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    font-family: var(--sans);
}

#supportModal .modal-header {
    background: linear-gradient(135deg, var(--brand) 0%, var(--purple) 100%);
    border: none;
    padding: 22px 28px;
    position: relative;
}

#supportModal .modal-title {
    font-family: var(--sans);
    font-weight: 700;
    font-size: 1rem;
    color: #fff;
    letter-spacing: .01em;
    display: flex;
    align-items: center;
    gap: 9px;
}

.modal-title-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,.18);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#supportModal .btn-close {
    position: absolute;
    top: 50%;
    right: 22px;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.18) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z' fill='white'/%3E%3C/svg%3E") center/10px no-repeat;
    opacity: 1;
    transition: background 0.18s;
    border: none;
}

#supportModal .btn-close:hover { background-color: rgba(255,255,255,.3); }

#supportModal .modal-body {
    padding: 26px 28px 28px;
}

.modal-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 22px;
}

.modal-info-item {
    background: var(--surface-2);
    border-radius: var(--radius-md);
    padding: 12px 16px;
    border: 1px solid var(--border);
    transition: border-color 0.15s;
}

.modal-info-item:hover { border-color: var(--brand); }

.modal-info-item .label {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 5px;
}

.modal-info-item .value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    word-break: break-word;
}

.modal-section-label {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 10px;
}

.modal-description-block {
    background: var(--surface-2);
    border-left: 3px solid var(--brand);
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    padding: 16px 18px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.75;
    border: 1px solid var(--border);
    border-left: 3px solid var(--brand);
}
</style>

<div class="es-wrapper">

    @if($contactUs->count() > 0)

    <div class="es-card">
        <div class="table-responsive">
            <table class="es-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Requester</th>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contactUs as $contact)
                    <tr>
                        {{-- ID --}}
                        <td>
                            <span class="es-id">
                                {{ ($contactUs->currentPage() - 1) * $contactUs->perPage() + $loop->iteration }}
                            </span>
                        </td>

                        {{-- Event --}}
                        <td>
                            <span class="es-event-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $contact->event->title ?? '—' }}
                            </span>
                        </td>

                        {{-- Name + Email stacked --}}
                        <td>
                            <div class="es-name-cell">
                                <div class="es-avatar">
                                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="es-name">{{ $contact->name }}</div>
                                    <a href="mailto:{{ $contact->email }}" class="es-email-link">
                                        {{ $contact->email }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        {{-- Phone --}}
                        <td>
                            <span class="es-phone">{{ $contact->phone ?? '—' }}</span>
                        </td>

                        {{-- Message snippet --}}
                        <td>
                            <span class="es-message" title="{{ $contact->message }}">
                                {{ \Illuminate\Support\Str::limit($contact->message, 55) }}
                            </span>
                        </td>

                        {{-- Status dropdown --}}
                        <td>
                            <form action="{{ route('event-support.update', $contact->id) }}"
                                  method="POST"
                                  class="es-status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status"
                                    class="es-status-select status-{{ $contact->status }}"
                                    onchange="this.form.submit()">
                                    <option value="pending"    {{ $contact->status == 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="inprogress" {{ $contact->status == 'inprogress' ? 'selected' : '' }}>🔄 In Progress</option>
                                    <option value="completed"  {{ $contact->status == 'completed'  ? 'selected' : '' }}>✅ Completed</option>
                                </select>
                            </form>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span class="es-date">{{ $contact->created_at->format('d M Y') }}<br>
                            <span style="opacity:.7;">{{ $contact->created_at->format('H:i') }}</span></span>
                        </td>

                        {{-- View --}}
                        <td>
                            <button class="es-btn-view viewSupportBtn"
                                data-name="{{ $contact->name }}"
                                data-email="{{ $contact->email }}"
                                data-phone="{{ $contact->phone }}"
                                data-message="{{ $contact->message }}"
                                data-event="{{ $contact->event->title ?? '—' }}"
                                data-date="{{ $contact->created_at->format('d M Y H:i') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="es-pagination custom_pagination">
            {!! $contactUs->links() !!}
        </div>
    </div>

    @else

    <div class="es-card">
        <div class="es-empty">
            <div class="es-empty-icon">📭</div>
            <h6>No support tickets yet</h6>
            <p>Contact submissions from events will appear here.</p>
        </div>
    </div>

    @endif

</div>

{{-- ── MODAL ── --}}
<div class="modal fade" id="supportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <div class="modal-title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.02 1.18a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 6.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    </div>
                    Event Support Ticket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="modal-info-grid">
                    <div class="modal-info-item">
                        <div class="label">🗓 Event</div>
                        <div class="value" id="modal_event"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">👤 Name</div>
                        <div class="value" id="modal_name"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">✉️ Email</div>
                        <div class="value" id="modal_email"></div>
                    </div>
                    <div class="modal-info-item">
                        <div class="label">📞 Phone</div>
                        <div class="value" id="modal_phone"></div>
                    </div>
                    <div class="modal-info-item" style="grid-column: 1 / -1;">
                        <div class="label">🕐 Submitted</div>
                        <div class="value" id="modal_date"></div>
                    </div>
                </div>

                <div class="modal-section-label">Message</div>
                <div class="modal-description-block" id="modal_description"></div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("click", function(e) {
    const btn = e.target.closest(".viewSupportBtn");
    if (!btn) return;

    document.getElementById("modal_event").innerText       = btn.dataset.event;
    document.getElementById("modal_name").innerText        = btn.dataset.name;
    document.getElementById("modal_email").innerText       = btn.dataset.email;
    document.getElementById("modal_phone").innerText       = btn.dataset.phone || '—';
    document.getElementById("modal_description").innerText = btn.dataset.message;
    document.getElementById("modal_date").innerText        = btn.dataset.date;

    new bootstrap.Modal(document.getElementById('supportModal')).show();
});
</script>
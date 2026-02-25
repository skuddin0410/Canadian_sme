@extends('layouts.admin')

@section('title')
Admin | Demo Requests
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

    .demo-wrapper {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Page Header ── */
    .demo-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .demo-page-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f1530;
    }

    .demo-page-title span {
        color: #8b91a7;
        font-weight: 400;
        font-size: 1rem;
    }

    /* ── Card ── */
    .demo-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8eaf0;
        box-shadow: 0 2px 12px rgba(15, 20, 50, 0.06);
        overflow: hidden;
    }

    /* ── Card Top Bar ── */
    .demo-card-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #f0f1f6;
        flex-wrap: wrap;
        gap: 12px;
    }

    .demo-card-topbar h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f1530;
    }

    /* ── Search Bar ── */
    .demo-search-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .demo-search-input {
        border: 1.5px solid #e0e3ef;
        border-radius: 10px;
        padding: 8px 14px 8px 36px;
        font-size: 0.85rem;
        font-family: 'DM Sans', sans-serif;
        color: #1a1d30;
        background: #f8f9fc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%238b91a7' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 11px center;
        min-width: 240px;
        transition: border-color 0.18s, box-shadow 0.18s;
        outline: none;
    }

    .demo-search-input:focus {
        border-color: #4f6ef7;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(79, 110, 247, 0.12);
    }

    .demo-filter-btn {
        background: #4f6ef7;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.18s, transform 0.12s;
    }

    .demo-filter-btn:hover {
        background: #3558d6;
        transform: translateY(-1px);
    }

    /* ── Spinner ── */
    .demo-spinner-wrap {
        padding: 40px;
        text-align: center;
    }

    .demo-spinner {
        width: 28px;
        height: 28px;
        border: 3px solid #e8eaf0;
        border-top-color: #4f6ef7;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        margin: 0 auto;
        display: none;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ── Table ── */
    .demo-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .demo-table thead tr {
        background: #f5f6fa;
        border-bottom: 2px solid #e8eaf0;
    }

    .demo-table thead th {
        padding: 13px 18px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8b91a7;
        white-space: nowrap;
    }

    .demo-table tbody tr {
        border-bottom: 1px solid #f0f1f6;
        transition: background 0.15s;
    }

    .demo-table tbody tr:last-child {
        border-bottom: none;
    }

    .demo-table tbody tr:hover {
        background: #fafbff;
    }

    .demo-table td {
        padding: 13px 18px;
        vertical-align: middle;
        color: #2c3050;
    }

    /* ── ID ── */
    .ticket-id {
        font-family: 'DM Mono', monospace;
        font-size: 0.78rem;
        color: #8b91a7;
        background: #f0f1f6;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* ── Name cell ── */
    .name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f6ef7, #764ba2);
        color: #fff;
        font-weight: 700;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .name-text {
        font-weight: 500;
        color: #1a1d30;
    }

    /* ── Email ── */
    .email-text {
        font-size: 0.83rem;
        color: #4f6ef7;
    }

    /* ── Timezone / Phone ── */
    .meta-text {
        font-size: 0.82rem;
        color: #5e6388;
    }

    /* ── Date / Time ── */
    .date-cell {
        font-family: 'DM Mono', monospace;
        font-size: 0.78rem;
        color: #5e6388;
        white-space: nowrap;
    }

    .time-badge {
        background: #f0f4ff;
        color: #3558d6;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    /* ── Status Select ── */
    .status-form select {
        border: none;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 26px 5px 11px;
        cursor: pointer;
        min-width: 126px;
        outline: none;
        transition: box-shadow 0.18s;
        font-family: 'DM Sans', sans-serif;
    }

    .status-form select:focus {
        box-shadow: 0 0 0 3px rgba(79, 110, 247, 0.15);
    }

    .status-pending {
        background: #fff8e6;
        color: #b97b00;
    }

    .status-confirm {
        background: #e8f0ff;
        color: #3558d6;
    }

    .status-reschedule {
        background: #e0f7fa;
        color: #00796b;
    }

    .status-cancel {
        background: #fdecea;
        color: #d32f2f;
    }

    .status-completed {
        background: #e6faf2;
        color: #1a8a5c;
    }

    /* ── Empty State ── */
    .demo-empty {
        text-align: center;
        padding: 64px 24px;
        color: #8b91a7;
    }

    .demo-empty-icon {
        font-size: 2.4rem;
        margin-bottom: 12px;
        opacity: 0.45;
    }

    /* ── Pagination ── */
    .demo-pagination-wrap {
        padding: 16px 22px;
        border-top: 1px solid #f0f1f6;
        display: flex;
        justify-content: center;
    }
</style>

<div class="container flex-grow-1 container-p-y pt-0 demo-wrapper">

    <div class="demo-page-header">
        <h4 class="demo-page-title">
            Demo Requests <span>/ All Bookings</span>
        </h4>
    </div>

    <div class="demo-card">

        <!-- Top Bar: Title + Search -->
        <div class="demo-card-topbar">
            <h5>📋 All Demo Bookings</h5>

            <form method="GET" id="demo-filter">
                <div class="demo-search-row">

                    <input type="text"
                        class="demo-search-input"
                        name="search"
                        id="search"
                        placeholder="Search name or email…">

                    <!-- <input type="date"
               name="start_date"
               id="start_date"
               class="demo-search-input"
               style="min-width:160px;"> -->

                    <input type="date"
                        name="end_date"
                        id="end_date"
                        class="demo-search-input"
                        style="min-width:160px;">

                    <button type="button" class="demo-filter-btn filter">Search</button>
                </div>
            </form>
        </div>

        <!-- Spinner -->
        <div class="demo-spinner-wrap">
            <div class="demo-spinner" id="demoSpinner"></div>
        </div>

        <!-- Table Target -->
        <div id="demo-table"></div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentDemoId = null;
    let currentSelect = null;
    let originalValue = null;

    // Delegated listener for dynamically loaded dropdowns
    $(document).on("change", ".status-dropdown", function() {

        let selectedStatus = $(this).val();

        if (selectedStatus === 'reschedule' || selectedStatus === 'cancel') {

            currentDemoId = $(this).data("id");
            currentSelect = $(this);
            originalValue = $(this).data("original");

            let modalElement = document.getElementById('statusReasonModal');

            if (!modalElement) {
                console.error("Modal element not found.");
                return;
            }

            let modal = new bootstrap.Modal(modalElement);

            $("#statusReasonText").val('').removeClass("is-invalid");

            modal.show();

        } else {
            $("#status-form-" + $(this).data("id")).submit();
        }
    });


    // Save button click
    $(document).on("click", "#saveStatusReasonBtn", function() {

        let note = $("#statusReasonText").val().trim();

        if (!note) {
            $("#statusReasonText").addClass("is-invalid").focus();
            return;
        }

        let form = $("#status-form-" + currentDemoId);
        form.find(".status-note").val(note);

        let modalElement = document.getElementById('statusReasonModal');
        let modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();

        form.submit();
    });


    // Reset dropdown if modal closed
    $(document).on("hidden.bs.modal", "#statusReasonModal", function() {

        if (currentSelect && originalValue !== null) {
            currentSelect.val(originalValue);
        }

        currentDemoId = null;
        currentSelect = null;
        originalValue = null;
    });
</script>
<script>
    function showSpinner() {
        document.getElementById('demoSpinner').style.display = 'block';
    }

    function hideSpinner() {
        document.getElementById('demoSpinner').style.display = 'none';
    }

    function GetDemoList(url, params) {
        showSpinner();
        $.get(url || "{{ route('demo.index') }}", $.extend({
            ajax_request: true
        }, params), function(data) {
            $("#demo-table").html(data.html);
            hideSpinner();
        });
    }

    $(document).ready(function() {
        GetDemoList();
    });

    $(document).on("click", ".filter", function() {

        GetDemoList("{{ route('demo.index') }}", {
            search: $("#search").val(),
            start_date: $("#start_date").val(),
            end_date: $("#end_date").val()
        });

    });

    $(document).on("keydown", "#search", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            $(".filter").click();
        }
    });

    $(document).on("click", ".custom_pagination .pagination-link", function(e) {
        e.preventDefault();
        showSpinner();
        $.get($(this).attr("href"), {
            ajax_request: true
        }, function(data) {
            $("#demo-table").html(data.html);
            hideSpinner();
        });
    });
</script>
@endsection
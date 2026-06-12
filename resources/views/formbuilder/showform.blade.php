@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')

<style>
    /* ── Tokens ── */
    :root {
        --bg: #f5f6fa;
        --surface: #ffffff;
        --border: #e4e7ee;
        --accent: #0e2caf;
        --accent-dk: #1709af;
        --accent-lt: #eef2ff;
        --text: #111827;
        --muted: #6b7280;
        --subtle: #f9fafb;
        --green-bg: #ecfdf5;
        --green-fg: #059669;
        --green-bdr: #a7f3d0;
        --red-bg: #fff1f2;
        --red-fg: #e11d48;
        --amber-bg: #fffbeb;
        --amber-fg: #d97706;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .07), 0 1px 4px rgba(0, 0, 0, .05);
        --shadow-lg: 0 12px 40px rgba(0, 0, 0, .10), 0 2px 8px rgba(0, 0, 0, .06);
    }

    /* ── Page ── */
    .df-page {
        background: var(--bg);
        min-height: 100vh;
        padding: 3rem 1.25rem 5rem;
    }

    .df-wrap {
        max-width: 680px;
        margin: 0 auto;
    }

    /* ── Page header ── */
    .df-hero {
        text-align: center;
        margin-bottom: 2rem;
    }

    .df-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .9rem;
        border-radius: 999px;
        background: var(--accent-lt);
        color: var(--accent);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .85rem;
    }

    .df-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 .5rem;
        letter-spacing: -.02em;
        line-height: 1.2;
    }

    .df-desc {
        font-size: .92rem;
        color: var(--muted);
        margin: 0;
        line-height: 1.6;
    }

    /* ── Alerts ── */
    #form-alert {
        border-radius: var(--radius-md);
        border: none;
        font-size: .875rem;
        margin-bottom: 1.25rem;
        padding: .85rem 1.1rem;
    }

    .df-alert-success {
        background: var(--green-bg);
        color: var(--green-fg);
        border: 1px solid var(--green-bdr);
        border-radius: var(--radius-md);
        padding: .85rem 1.1rem;
        font-size: .875rem;
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        margin-bottom: 1.25rem;
    }

    .df-alert-danger {
        background: var(--red-bg);
        color: var(--red-fg);
        border: 1px solid #fecdd3;
        border-radius: var(--radius-md);
        padding: .85rem 1.1rem;
        font-size: .875rem;
        margin-bottom: 1.25rem;
    }

    .df-alert-danger ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    /* ── Card ── */
    .df-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .df-card-body {
        padding: 2rem 2rem 1.5rem;
    }

    /* ── Section label ── */
    .df-section-label {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .df-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ── Form controls ── */
    .df-field {
        margin-bottom: 1.25rem;
    }

    .df-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 1rem;
    }

    .df-field-full {
        grid-column: 1 / -1;
    }

    .df-label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: .4rem;
    }

    .df-label .req {
        color: var(--red-fg);
        margin-left: 2px;
    }

    .df-input,
    .df-select,
    .df-textarea {
        width: 100%;
        padding: .65rem .9rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface);
        color: var(--text);
        font-size: .875rem;
        line-height: 1.5;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
        -webkit-appearance: none;
        appearance: none;
    }

    .df-input::placeholder,
    .df-textarea::placeholder {
        color: #b0b7c3;
    }

    .df-input:focus,
    .df-select:focus,
    .df-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        background: #fafafe;
    }

    .df-input:invalid:not(:placeholder-shown),
    .df-select:invalid,
    .df-textarea:invalid:not(:placeholder-shown) {
        border-color: var(--red-fg);
    }

    .df-textarea {
        resize: vertical;
        min-height: 110px;
    }

    /* select arrow */
    .df-select-wrap {
        position: relative;
    }

    .df-select-wrap::after {
        content: '';
        position: absolute;
        right: .85rem;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 5px solid var(--muted);
        pointer-events: none;
    }

    .df-select {
        padding-right: 2.2rem;
    }

    /* hint & error */
    .df-hint {
        font-size: .74rem;
        color: var(--muted);
        margin-top: .3rem;
    }

    .df-invalid {
        font-size: .74rem;
        color: var(--red-fg);
        margin-top: .3rem;
        display: none;
    }

    .was-validated .df-input:invalid~.df-invalid,
    .was-validated .df-select:invalid~.df-invalid,
    .was-validated .df-textarea:invalid~.df-invalid {
        display: block;
    }

    /* radio & checkbox */
    .df-check-group {
        display: flex;
        flex-direction: column;
        gap: .45rem;
        margin-top: .1rem;
    }

    .df-check-item {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .55rem .8rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: border-color .15s, background .15s;
        user-select: none;
    }

    .df-check-item:hover {
        border-color: #c7d2fe;
        background: var(--accent-lt);
    }

    .df-check-item input[type="radio"],
    .df-check-item input[type="checkbox"] {
        accent-color: var(--accent);
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        cursor: pointer;
        margin: 0;
    }

    .df-check-item label {
        font-size: .86rem;
        color: var(--text);
        cursor: pointer;
        margin: 0;
        flex: 1;
    }

    /* checked state */
    .df-check-item:has(input:checked) {
        border-color: var(--accent);
        background: var(--accent-lt);
    }

    /* ── Divider ── */
    .df-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 1.75rem 0;
    }

    /* ── Registration type ── */
    .df-reg-type {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
        margin-top: .6rem;
    }

    .df-mode-type {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
        margin-top: .6rem;
    }

    .df-mode-option {
        position: relative;
    }

    .df-mode-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .df-mode-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: 1rem .75rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: border-color .15s, background .15s, box-shadow .15s;
        text-align: center;
        background: var(--subtle);
    }

    .df-mode-label:hover {
        border-color: #c7d2fe;
        background: var(--accent-lt);
    }

    .df-mode-option input:checked+.df-mode-label {
        border-color: var(--accent);
        background: var(--accent-lt);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
    }

    .df-mode-name {
        font-size: .82rem;
        font-weight: 700;
        color: var(--text);
    }

    .df-mode-hint {
        font-size: .7rem;
        color: var(--muted);
    }

    .df-reg-option {
        position: relative;
    }

    .df-reg-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .df-reg-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: 1rem .75rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: border-color .15s, background .15s, box-shadow .15s;
        text-align: center;
        background: var(--subtle);
    }

    .df-reg-label:hover {
        border-color: #c7d2fe;
        background: var(--accent-lt);
    }

    .df-reg-option input:checked+.df-reg-label {
        border-color: var(--accent);
        background: var(--accent-lt);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .1);
    }

    .df-reg-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .df-reg-icon.free {
        background: var(--green-bg);
        color: var(--green-fg);
    }

    .df-reg-icon.paid {
        background: var(--amber-bg);
        color: var(--amber-fg);
    }

    .df-reg-name {
        font-size: .82rem;
        font-weight: 700;
        color: var(--text);
    }

    .df-reg-hint {
        font-size: .7rem;
        color: var(--muted);
    }

    /* ── Ticket selected indicator ── */
    #ticketRadioContainer .df-ticket-selected {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .75rem 1rem;
        background: var(--green-bg);
        border: 1px solid var(--green-bdr);
        border-radius: var(--radius-sm);
        margin-top: .75rem;
        font-size: .84rem;
        color: var(--green-fg);
        font-weight: 600;
    }

    .df-ticket-selected-main {
        display: flex;
        flex-direction: column;
        gap: .2rem;
        min-width: 0;
    }

    .df-ticket-price-stack {
        display: flex;
        align-items: baseline;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .df-price-final {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -.02em;
    }

    .df-price-base {
        font-size: .85rem;
        color: var(--muted);
        text-decoration: line-through;
        text-decoration-thickness: 1.5px;
    }

    .df-ticket-meta {
        font-size: .74rem;
        color: var(--muted);
        line-height: 1.5;
    }

    .df-ticket-breakup {
        margin-top: .45rem;
        padding-top: .45rem;
        border-top: 1px dashed var(--border);
        display: grid;
        gap: .18rem;
        font-size: .74rem;
        color: var(--muted);
    }

    .df-ticket-breakup-row {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
    }

    .df-ticket-breakup-row strong {
        color: var(--text);
    }

    .df-ticket-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-top: .45rem;
    }

    .df-ticket-badge {
        display: inline-flex;
        align-items: center;
        padding: .22rem .5rem;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .df-ticket-badge.discount {
        background: #e8f7ee;
        color: #0f8a4b;
    }

    .df-ticket-badge.group {
        background: #eef2ff;
        color: #3245b6;
    }

    .df-ticket-badge.early {
        background: #fff4db;
        color: #b76b00;
    }

    .df-promo-shell {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1rem;
        background: #fcfcfe;
        margin-top: .9rem;
        margin-bottom: 1.1rem;
    }

    .df-promo-row {
        display: flex;
        gap: .75rem;
        align-items: flex-end;
    }

    .df-promo-row .df-field {
        flex: 1;
        margin-bottom: 0;
    }

    .df-promo-status {
        margin-top: .65rem;
        font-size: .8rem;
    }

    .df-promo-status.success {
        color: var(--green-fg);
    }

    .df-promo-status.error {
        color: var(--red-fg);
    }

    .df-team-shell {
        margin-top: 1rem;
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--subtle);
    }

    .df-team-shell.d-none {
        display: none;
    }

    .df-team-coordinator-note {
        margin-top: .75rem;
        padding: .8rem .9rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: #fff;
        font-size: .78rem;
        color: var(--muted);
        line-height: 1.55;
    }

    .df-team-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .df-team-title {
        font-size: .88rem;
        font-weight: 700;
        color: var(--text);
    }

    .df-team-hint {
        font-size: .74rem;
        color: var(--muted);
        margin-top: .2rem;
    }

    .df-team-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: #fff;
        padding: 1rem;
    }

    .df-team-card+.df-team-card {
        margin-top: 1rem;
    }

    .df-team-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .df-team-card-title {
        font-size: .82rem;
        font-weight: 700;
        color: var(--text);
    }

    .df-team-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .9rem;
    }

    .df-team-grid .df-field {
        margin-bottom: 0;
    }

    .df-btn-secondary,
    .df-btn-danger {
        appearance: none;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: #fff;
        color: var(--text);
        padding: .55rem .95rem;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
    }

    .df-btn-danger {
        color: var(--red-fg);
        border-color: #fecdd3;
        background: #fff1f2;
    }

    .df-inline-check {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-top: 1rem;
        padding: .8rem .9rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: #fff;
    }

    .df-inline-check input[type="checkbox"] {
        margin-top: .15rem;
        accent-color: var(--accent);
    }

    .df-inline-check label {
        font-size: .82rem;
        color: var(--text);
        line-height: 1.45;
        cursor: pointer;
    }

    .df-waitlist-notice {
        margin-top: 1rem;
        padding: .85rem 1rem;
        border: 1px solid #fed7aa;
        border-radius: var(--radius-sm);
        background: #fff7ed;
        color: #9a3412;
        font-size: .86rem;
        line-height: 1.45;
    }

    /* ── Submit button ── */
    .df-submit {
        width: 100%;
        padding: .8rem 1.5rem;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: .92rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, box-shadow .15s, transform .1s;
        box-shadow: 0 2px 8px rgba(79, 70, 229, .3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        margin-top: 1.5rem;
        letter-spacing: .01em;
    }

    .df-submit:hover {
        background: var(--accent-dk);
        box-shadow: 0 4px 16px rgba(79, 70, 229, .35);
    }

    .df-submit:active {
        transform: scale(.98);
    }

    /* ── Footer link ── */
    .df-footer-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: .86rem;
        color: var(--muted);
    }

    .df-footer-link a {
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
    }

    .df-footer-link a:hover {
        text-decoration: underline;
    }

    /* ── Ticket modal ── */
    .df-modal .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .df-modal .modal-header {
        background: var(--subtle);
        border-bottom: 1px solid var(--border);
        padding: 1.1rem 1.4rem;
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .df-modal-icon {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: var(--accent-lt);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .df-modal .modal-title {
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
        flex: 1;
        margin: 0;
    }

    .df-modal .btn-close {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--bg);
        border: 1px solid var(--border);
        opacity: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        padding: 0;
        font-size: .65rem;
    }

    .df-modal .btn-close:hover {
        background: var(--border);
    }

    .df-modal .modal-body {
        padding: 1.4rem;
        background: var(--bg);
        max-height: 65vh;
        overflow-y: auto;
    }

    /* Ticket card */
    .df-ticket-card {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        height: 100%;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
        display: flex;
        flex-direction: column;
    }

    .df-ticket-card:hover {
        border-color: #c7d2fe;
        box-shadow: 0 4px 16px rgba(79, 70, 229, .1);
    }

    .df-ticket-card.is-selected {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(14, 44, 175, .12);
        background: linear-gradient(180deg, #ffffff 0%, #f6f8ff 100%);
    }

    .df-ticket-card.is-selected .df-ticket-btn {
        background: var(--accent);
    }

    .df-ticket-card.is-selected .df-ticket-btn:hover {
        background: var(--accent-dk);
    }

    .df-ticket-name {
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
        margin-bottom: .3rem;
    }

    .df-ticket-desc {
        font-size: .8rem;
        color: var(--muted);
        flex: 1;
        margin-bottom: .85rem;
        line-height: 1.5;
    }

    .df-ticket-price {
        margin-bottom: .85rem;
    }

    .df-ticket-btn {
        width: 100%;
        padding: .55rem 1rem;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s;
    }

    .df-ticket-btn:hover {
        background: var(--accent-dk);
    }

    /* loading state */
    .df-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .65rem;
        padding: 3rem 1rem;
        color: var(--muted);
        font-size: .85rem;
    }

    .df-spinner {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2.5px solid var(--border);
        border-top-color: var(--accent);
        animation: df-spin .7s linear infinite;
    }

    @keyframes df-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ── Responsive ── */
    @media (max-width: 576px) {
        .df-page {
            padding: 1.5rem .75rem 4rem;
        }

        .df-card-body {
            padding: 1.25rem 1.1rem 1rem;
        }

        .df-title {
            font-size: 1.4rem;
        }

        .df-reg-type,
        .df-mode-type,
        .df-details-grid,
        .df-team-grid {
            grid-template-columns: 1fr;
        }

        .df-promo-row {
            flex-direction: column;
            align-items: stretch;
        }

        .df-team-header,
        .df-team-card-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="df-page">
    <div class="df-wrap">

        {{-- Hero header --}}
        <div class="df-hero">
            <div class="df-hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                Registration Form
            </div>
            <h1 class="df-title">{{ $form->title }}</h1>
            @if(!empty($form->description))
            <p class="df-desc">{{ $form->description }}</p>
            @endif
        </div>

        {{-- Flash alerts --}}
        <div id="form-alert" class="alert d-none" role="alert"></div>

        @if(session('success'))
        <div class="df-alert-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="df-alert-danger">
            <strong style="display:block;margin-bottom:.35rem;">Registration failed</strong>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="df-alert-danger">
            <strong style="display:block;margin-bottom:.35rem;">Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form card --}}
        <div class="df-card">
            <div class="df-card-body">

                <div class="df-section-label">Your Details</div>

                <form id="dynamic-form"
                    method="POST"
                    action="{{ route('forms.submit', $form->id) }}"
                    novalidate>
                    @csrf
                    <div class="df-details-grid">
                    @foreach($form->form_data as $index => $field)
                    @php
                    $type = $field['type'] ?? 'text';
                    $label = $field['label'] ?? ucfirst($type);
                    $name = Str::slug($label, '_');
                    $idBase = "f{$index}_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
                    $oldValue = old($name);
                    $isRequired = in_array('required', $field['validation'] ?? []);
                    $min = $field['min'] ?? null;
                    $max = $field['max'] ?? null;
                    $logic = $field['conditional_logic'] ?? null;
                    $normalizedLabel = Str::lower(trim($label));
                    $isBioField = $name === 'bio' || str_contains($name, 'bio') || str_contains($normalizedLabel, 'bio');
                    $isFullWidthField = in_array($type, ['textarea', 'checkbox', 'radio', 'select']) || $isBioField;
                    $hasLenRule = in_array($type, ['text','email','textarea','number']) && ($min || $max);
                    $isPasswordField = $type === 'password';
                    $displayRequired = $isRequired && !$isPasswordField;
                    $isPhoneField = in_array($name, ['mobile', 'phone', 'phone_number'], true);
                    $inputType = $isPhoneField ? 'tel' : $type;

                    $autocomplete = match($name) {
                    'first_name' => 'given-name',
                    'last_name' => 'family-name',
                    'email' => 'email',
                    'mobile', 'phone', 'phone_number' => 'tel',
                    'company' => 'organization',
                    'designation', 'job_title', 'title' => 'organization-title',
                    default => match($type) {
                        'text' => 'off',
                        'email' => 'email',
                        'number' => 'off',
                        'date' => 'bday',
                        'password' => 'new-password',
                        default => 'off',
                    },
                    };

                    $hintParts = [];
                    if ($isRequired) $hintParts[] = 'Required';
                    if ($hasLenRule) {
                    if ($type === 'number') {
                    if (!is_null($min) && !is_null($max)) $hintParts[] = "Range: {$min}–{$max}";
                    elseif (!is_null($min)) $hintParts[] = "Min: {$min}";
                    elseif (!is_null($max)) $hintParts[] = "Max: {$max}";
                    } else {
                    if ($min && $max) $hintParts[] = "{$min}–{$max} characters";
                    elseif ($min) $hintParts[] = "Min {$min} characters";
                    elseif ($max) $hintParts[] = "Max {$max} characters";
                    }
                    }
                    $hintText = implode(' · ', $hintParts);
                    @endphp

                    <div @class([
                        'df-field',
                        'field-wrapper',
                        'df-field-full' => $isFullWidthField,
                    ])
                        id="field-{{ $index }}"
                        @if(!empty($logic)) data-logic='@json($logic)' @endif
                        data-target-label="{{ $label }}">

                        @switch($type)

                        @case('text')
                        @case('email')
                        @case('number')
                        @case('date')
                        @case('password')
                        <label class="df-label" for="{{ $idBase }}">
                            {{ $label }} @if($displayRequired)<span class="req">*</span>@endif
                        </label>
                        <input
                            type="{{ $inputType }}"
                            id="{{ $idBase }}"
                            name="{{ $name }}"
                            class="df-input"
                            placeholder="Enter {{ strtolower($label) }}"
                            autocomplete="{{ $autocomplete }}"
                            @if($isPhoneField) inputmode="tel" @endif
                            value="{{ $type !== 'password' ? $oldValue : '' }}"
                            @if($displayRequired) required @endif
                            @if($min && in_array($type,['text','email','textarea'])) minlength="{{ $min }}" @endif
                            @if($max && in_array($type,['text','email','textarea'])) maxlength="{{ $max }}" @endif
                            @if(!is_null($min) && $type==='number' ) min="{{ $min }}" @endif
                            @if(!is_null($max) && $type==='number' ) max="{{ $max }}" @endif>
                        @if($hintText)<div class="df-hint">{{ $hintText }}</div>@endif
                        <div class="df-invalid">Please provide a valid {{ strtolower($label) }}.</div>
                        @break

                        @case('textarea')
                        <label class="df-label" for="{{ $idBase }}">
                            {{ $label }} @if($isRequired)<span class="req">*</span>@endif
                        </label>
                        <textarea
                            id="{{ $idBase }}"
                            name="{{ $name }}"
                            class="df-textarea"
                            rows="4"
                            placeholder="Type {{ strtolower($label) }} here"
                            autocomplete="off"
                            @if($isRequired) required @endif
                            @if($min) minlength="{{ $min }}" @endif
                            @if($max) maxlength="{{ $max }}" @endif>{{ $oldValue }}</textarea>
                        @if($hintText)<div class="df-hint">{{ $hintText }}</div>@endif
                        <div class="df-invalid">Please provide a valid {{ strtolower($label) }}.</div>
                        @break

                        @case('select')
                        <label class="df-label" for="{{ $idBase }}">
                            {{ $label }} @if($isRequired)<span class="req">*</span>@endif
                        </label>
                        <div class="df-select-wrap">
                            <select
                                id="{{ $idBase }}"
                                name="{{ $name }}"
                                class="df-select"
                                autocomplete="off"
                                @if($isRequired) required @endif>
                                <option value="" {{ blank($oldValue) ? 'selected' : '' }} disabled>Choose an option</option>
                                @foreach($field['options'] ?? [] as $opt)
                                <option value="{{ $opt }}" {{ (string) $oldValue === (string) $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($hintText)<div class="df-hint">{{ $hintText }}</div>@endif
                        <div class="df-invalid">Please select a {{ strtolower($label) }}.</div>
                        @break

                        @case('radio')
                        <fieldset style="border:none;padding:0;margin:0;">
                            <legend class="df-label">
                                {{ $label }} @if($isRequired)<span class="req">*</span>@endif
                            </legend>
                            <div class="df-check-group">
                                @foreach($field['options'] ?? [] as $i => $opt)
                                @php $rid = "{$idBase}_r{$i}"; @endphp
                                <div class="df-check-item">
                                    <input type="radio"
                                        id="{{ $rid }}"
                                        name="{{ $name }}"
                                        value="{{ $opt }}"
                                        {{ (string) $oldValue === (string) $opt ? 'checked' : '' }}
                                        @if($isRequired) required @endif>
                                    <label for="{{ $rid }}">{{ $opt }}</label>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>
                        @if($hintText)<div class="df-hint">{{ $hintText }}</div>@endif
                        @break

                        @case('checkbox')
                        <fieldset style="border:none;padding:0;margin:0;">
                            <legend class="df-label">
                                {{ $label }} @if($isRequired)<span class="req">*</span>@endif
                            </legend>
                            <div class="df-check-group">
                                @php $oldCheckboxValues = (array) old($name, []); @endphp
                                @foreach($field['options'] ?? [] as $i => $opt)
                                @php $cid = "{$idBase}_c{$i}"; @endphp
                                <div class="df-check-item">
                                    <input type="checkbox"
                                        id="{{ $cid }}"
                                        name="{{ $name }}[]"
                                        value="{{ $opt }}"
                                        {{ in_array($opt, $oldCheckboxValues, true) ? 'checked' : '' }}
                                        @if($isRequired && $i===0) required @endif>
                                    <label for="{{ $cid }}">{{ $opt }}</label>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>
                        @if($hintText)<div class="df-hint">{{ $hintText }}</div>@endif
                        @break

                        @endswitch

                    </div>
                    @endforeach
                    </div>

                    <hr class="df-divider">

                    <div class="df-field">
                        <div class="df-label">Registration Mode</div>
                        <div class="df-mode-type">
                            @php
                                $defaultRegistrationMode = old('registration_mode', $event->enable_team_registration ? 'single' : 'single');
                                $defaultRegistrationType = old('registration_type');
                                if (!$defaultRegistrationType && old('selected_ticket_id')) {
                                    $defaultRegistrationType = 'paid';
                                } elseif (!$defaultRegistrationType && $event->enable_free_registration) {
                                    $defaultRegistrationType = 'free';
                                }
                            @endphp
                            <div class="df-mode-option">
                                <input type="radio" name="registration_mode" id="regModeSingle" value="single" {{ $defaultRegistrationMode === 'single' ? 'checked' : '' }}>
                                <label class="df-mode-label" for="regModeSingle">
                                    <span class="df-mode-name">Single Register</span>
                                    <span class="df-mode-hint">Register one attendee</span>
                                </label>
                            </div>
                            @if($event->enable_team_registration)
                            <div class="df-mode-option">
                                <input type="radio" name="registration_mode" id="regModeTeam" value="team" {{ $defaultRegistrationMode === 'team' ? 'checked' : '' }}>
                                <label class="df-mode-label" for="regModeTeam">
                                    <span class="df-mode-name">Team Register</span>
                                    <span class="df-mode-hint">Add multiple attendees in one submission</span>
                                </label>
                            </div>
                            @endif
                        </div>
                        <div id="coordinatorModeNote" class="df-team-coordinator-note d-none">
                            In team registration, the details above are used for the team coordinator/contact. Team members added below are the actual attendees unless you check "Coordinator is also attending".
                        </div>
                    </div>

                    {{-- Registration type --}}
                    <div class="df-field">
                        <div class="df-label">Registration Type</div>
                        <div class="df-reg-type">
                            @if($event->enable_free_registration)
                            <div class="df-reg-option">
                                <input type="radio" name="registration_type" id="regFree" value="free" {{ $defaultRegistrationType === 'free' ? 'checked' : '' }}>
                                <label class="df-reg-label" for="regFree">
                                    <div class="df-reg-icon free">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                                        </svg>
                                    </div>
                                    <span class="df-reg-name">Free</span>
                                    <span class="df-reg-hint">No payment needed</span>
                                </label>
                            </div>
                            @endif
                            @if($event->enable_paid_registration)
                            <div class="df-reg-option">
                                <input type="radio" name="registration_type" id="regPaid" value="paid" {{ $defaultRegistrationType === 'paid' ? 'checked' : '' }}>
                                <label class="df-reg-label" for="regPaid">
                                    <div class="df-reg-icon paid">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                            <line x1="1" y1="10" x2="23" y2="10" />
                                        </svg>
                                    </div>
                                    <span class="df-reg-name">Paid</span>
                                    <span class="df-reg-hint">Select a ticket</span>
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Selected ticket display --}}
                    <div id="ticketRadioContainer"></div>
                    <div id="promoCodeSection" class="df-promo-shell d-none">
                        <div class="df-promo-row">
                            <div class="df-field">
                                <label class="df-label" for="promo_code">Promo Code</label>
                                <input type="text" name="promo_code" id="promo_code" class="df-input" value="{{ old('promo_code') }}" placeholder="Enter discount code">
                            </div>
                            <button type="button" id="applyPromoBtn" class="df-btn-secondary">Apply Code</button>
                        </div>
                        <div id="promoCodeStatus" class="df-promo-status"></div>
                    </div>

                    <div id="teamMembersSection" class="df-team-shell d-none">
                        <div class="df-team-header">
                            <div>
                                <div class="df-team-title">Team Members</div>
                                <div class="df-team-hint">Primary attendee details stay above. Use Add More to include extra members.</div>
                            </div>
                            <button type="button" id="addTeamMemberBtn" class="df-btn-secondary">+ Add More</button>
                        </div>
                        <div class="df-inline-check">
                            <input type="checkbox" name="coordinator_attending" id="coordinatorAttending" value="1" {{ old('coordinator_attending') ? 'checked' : '' }}>
                            <label for="coordinatorAttending">Coordinator is also attending this event and should get an attendee account.</label>
                        </div>
                        <div id="teamMembersContainer"></div>
                    </div>

                    {{-- Hidden ticket id --}}
                    <input type="hidden" name="selected_ticket_id" id="selected_ticket_id" value="{{ old('selected_ticket_id') }}">
                    <input type="hidden" name="submission_action" id="submission_action" value="{{ old('submission_action', 'register') }}">

                    <div id="waitlistNotice" class="df-waitlist-notice d-none">
                        All spots are currently full for your attendee count. You can submit this form to join the waitlist.
                    </div>

                    <button type="submit" id="submit-btn" class="df-submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        Submit Registration
                    </button>

                </form>
            </div>
        </div>

        <div class="df-footer-link">
            Already have an account? <a href="{{ route('event.user.login', ['event' => $event->slug]) }}">
                Sign in
            </a>
        </div>

    </div>
</div>

{{-- Ticket modal --}}
<div class="modal fade df-modal" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="df-modal-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" />
                        <line x1="1" y1="10" x2="23" y2="10" />
                    </svg>
                </div>
                <h5 class="modal-title" id="ticketModalLabel">Select Your Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="10" height="10" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M1 1l12 12M13 1L1 13" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="ticketContainer">
                <div class="df-loading">
                    <div class="df-spinner"></div>
                    Loading available tickets…
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $teamMemberFields = collect($form->form_data ?? [])
        ->map(function ($field) {
            $type = $field['type'] ?? 'text';
            $label = $field['label'] ?? ucfirst($type);
            $name = \Illuminate\Support\Str::slug($label, '_');
            $normalizedLabel = \Illuminate\Support\Str::lower(trim($label));
            $isBioField = $name === 'bio' || str_contains($name, 'bio') || str_contains($normalizedLabel, 'bio');

            return [
                'type' => $type,
                'label' => $label,
                'name' => $name,
                'options' => array_values($field['options'] ?? []),
                'required' => in_array('required', $field['validation'] ?? []),
                'min' => $field['min'] ?? null,
                'max' => $field['max'] ?? null,
                'is_full_width' => in_array($type, ['textarea', 'checkbox', 'radio', 'select']) || $isBioField,
                'autocomplete' => match ($name) {
                    'first_name' => 'given-name',
                    'last_name' => 'family-name',
                    'email' => 'email',
                    'mobile', 'phone', 'phone_number' => 'tel',
                    'company' => 'organization',
                    'designation', 'job_title', 'title' => 'organization-title',
                    default => 'off',
                },
            ];
        })
        ->reject(fn ($field) => $field['name'] === 'password')
        ->sortBy(function ($field) {
            return match ($field['name']) {
                'company' => 100,
                'designation' => 101,
                'bio' => 102,
                default => 0,
            };
        })
        ->values();
@endphp
<script>
    const EVENT_ID = @json($event->id);
    const OLD_TEAM_MEMBERS = @json(old('team_members', []));
    const OLD_SELECTED_TICKET_ID = @json(old('selected_ticket_id'));
    const TICKETS = @json($tickets->map(fn($ticket) => ['id' => $ticket->id, 'name' => $ticket->name, 'base_price' => $ticket->base_price])->values());
    const HAS_FORM_ERRORS = @json($errors->any());
    const TEAM_MEMBER_FIELDS = @json($teamMemberFields);
    const CURRENCY_SYMBOL = @json(config('tickets.defaults.currency_symbol', '$'));
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const regPaid = document.getElementById('regPaid');
        const regFree = document.getElementById('regFree');
        const regModeSingle = document.getElementById('regModeSingle');
        const regModeTeam = document.getElementById('regModeTeam');
        const coordinatorModeNote = document.getElementById('coordinatorModeNote');
        const coordinatorAttending = document.getElementById('coordinatorAttending');
        const form = document.getElementById('dynamic-form');
        const ticketModalEl = document.getElementById('ticketModal');
        const ticketModal = new bootstrap.Modal(ticketModalEl);
        const ticketContainer = document.getElementById('ticketContainer');
        const hiddenTicketInput = document.getElementById('selected_ticket_id');
        const ticketRadioContainer = document.getElementById('ticketRadioContainer');
        const promoCodeSection = document.getElementById('promoCodeSection');
        const promoCodeInput = document.getElementById('promo_code');
        const promoCodeStatus = document.getElementById('promoCodeStatus');
        const applyPromoBtn = document.getElementById('applyPromoBtn');
        const teamMembersSection = document.getElementById('teamMembersSection');
        const teamMembersContainer = document.getElementById('teamMembersContainer');
        const addTeamMemberBtn = document.getElementById('addTeamMemberBtn');
        const submitButton = document.getElementById('submit-btn');
        const submissionActionInput = document.getElementById('submission_action');
        const waitlistNotice = document.getElementById('waitlistNotice');
        const passwordInput = form.querySelector('[name="password"]');
        const primaryEmailInput = form.querySelector('[name="email"]');
        let teamMemberIndex = 0;
        let isWaitlistMode = submissionActionInput && submissionActionInput.value === 'waitlist';
        let isCapacityWaitlistMode = isWaitlistMode;
        let isTicketWaitlistMode = false;

        const submitIcon = `
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="22" y1="2" x2="11" y2="13" />
                <polygon points="22 2 15 22 11 13 2 9 22 2" />
            </svg>`;
        const waitlistIcon = `
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 00-3-3.87" />
                <path d="M16 3.13a4 4 0 010 7.75" />
            </svg>`;

        function setWaitlistMode(enabled) {
            isWaitlistMode = enabled;

            if (submissionActionInput) {
                submissionActionInput.value = enabled ? 'waitlist' : 'register';
            }

            if (waitlistNotice) {
                waitlistNotice.classList.toggle('d-none', !enabled);
            }

            if (submitButton) {
                submitButton.innerHTML = enabled
                    ? `${waitlistIcon} Join Waitlist`
                    : `${submitIcon} Submit Registration`;
            }

            if (enabled) {
                hiddenTicketInput.value = '';
                ticketRadioContainer.innerHTML = '';
                setPromoStatus('', '');
                if (promoCodeSection) {
                    promoCodeSection.classList.add('d-none');
                }
            }
        }

        function syncWaitlistMode() {
            setWaitlistMode(isCapacityWaitlistMode || isTicketWaitlistMode);
        }

        function refreshRegistrationCapacity() {
            const params = new URLSearchParams({
                attendee_count: String(getAttendeeCount()),
            });

            return fetch(`/events/${EVENT_ID}/registration-capacity?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    isCapacityWaitlistMode = !data.can_register;

                    if (waitlistNotice && isCapacityWaitlistMode) {
                        waitlistNotice.textContent = data.is_unlimited
                            ? 'Registration is available.'
                            : `The attendee limit is full for this event. Requested ${data.requested} attendee(s), remaining ${data.remaining}. You can submit this form to join the waitlist.`;
                    }

                    syncWaitlistMode();
                    return data;
                })
                .catch(() => {
                    // On error, we keep the existing mode instead of resetting to false
                    syncWaitlistMode();
                });
        }

        function openTicketSelectionIfNeeded() {
            if (!regPaid || !regPaid.checked || hiddenTicketInput.value) {
                return;
            }

            loadTickets();
            ticketModal.show();
        }

        function getAttendeeCount() {
            if (!regModeTeam || !regModeTeam.checked) {
                return 1;
            }

            // Count the number of team member cards present in the form
            const teamMemberCount = teamMembersContainer.querySelectorAll('.df-team-card').length;
            const count = teamMemberCount + (coordinatorAttending.checked ? 1 : 0);

            // Ensure we return at least 1 to pass server-side validation
            return Math.max(1, count);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function teamMemberFieldTemplate(field, index, member = {}) {
            const fieldId = `team_${field.name}_${index}`;
            const fieldName = `team_members[${index}][${field.name}]`;
            const value = member[field.name];
            const requiredMarker = field.required ? '<span class="req">*</span>' : '';
            const requiredAttr = field.required ? ' data-team-required="1"' : '';
            const fullWidthClass = field.is_full_width ? ' df-field-full' : '';
            const autocomplete = field.autocomplete && field.autocomplete !== 'off'
                ? `section-team-${index} ${field.autocomplete}`
                : 'off';
            const hintParts = [];

            if (field.required) {
                hintParts.push('Required');
            }

            if (field.type === 'number') {
                if (field.min !== null && field.min !== '') hintParts.push(`Min: ${field.min}`);
                if (field.max !== null && field.max !== '') hintParts.push(`Max: ${field.max}`);
            } else if (['text', 'email', 'textarea'].includes(field.type)) {
                if (field.min !== null && field.min !== '' && field.max !== null && field.max !== '') {
                    hintParts.push(`${field.min}-${field.max} characters`);
                } else if (field.min !== null && field.min !== '') {
                    hintParts.push(`Min ${field.min} characters`);
                } else if (field.max !== null && field.max !== '') {
                    hintParts.push(`Max ${field.max} characters`);
                }
            }

            const hintMarkup = hintParts.length ? `<div class="df-hint">${hintParts.join(' · ')}</div>` : '';

            if (field.type === 'textarea') {
                return `
                    <div class="df-field${fullWidthClass}">
                        <label class="df-label" for="${fieldId}">${escapeHtml(field.label)} ${requiredMarker}</label>
                        <textarea id="${fieldId}" name="${fieldName}" class="df-textarea"${requiredAttr}>${escapeHtml(value ?? '')}</textarea>
                        ${hintMarkup}
                    </div>`;
            }

            if (field.type === 'select') {
                const options = (field.options || []).map(option => {
                    const selected = String(value ?? '') === String(option) ? ' selected' : '';
                    return `<option value="${escapeHtml(option)}"${selected}>${escapeHtml(option)}</option>`;
                }).join('');

                return `
                    <div class="df-field${fullWidthClass}">
                        <label class="df-label" for="${fieldId}">${escapeHtml(field.label)} ${requiredMarker}</label>
                        <div class="df-select-wrap">
                            <select id="${fieldId}" name="${fieldName}" class="df-select"${requiredAttr}>
                                <option value="" disabled ${value ? '' : 'selected'}>Choose an option</option>
                                ${options}
                            </select>
                        </div>
                        ${hintMarkup}
                    </div>`;
            }

            if (field.type === 'radio') {
                const options = (field.options || []).map((option, optionIndex) => {
                    const optionId = `${fieldId}_r${optionIndex}`;
                    const checked = String(value ?? '') === String(option) ? ' checked' : '';
                    const optionRequired = field.required && optionIndex === 0 ? ' data-team-required="1"' : '';

                    return `
                        <div class="df-check-item">
                            <input type="radio" id="${optionId}" name="${fieldName}" value="${escapeHtml(option)}"${checked}${optionRequired}>
                            <label for="${optionId}">${escapeHtml(option)}</label>
                        </div>`;
                }).join('');

                return `
                    <div class="df-field df-field-full">
                        <fieldset style="border:none;padding:0;margin:0;">
                            <legend class="df-label">${escapeHtml(field.label)} ${requiredMarker}</legend>
                            <div class="df-check-group">${options}</div>
                        </fieldset>
                        ${hintMarkup}
                    </div>`;
            }

            if (field.type === 'checkbox') {
                const currentValues = Array.isArray(value) ? value.map(String) : [];
                const options = (field.options || []).map((option, optionIndex) => {
                    const optionId = `${fieldId}_c${optionIndex}`;
                    const checked = currentValues.includes(String(option)) ? ' checked' : '';
                    const optionRequired = field.required && optionIndex === 0 ? ' data-team-required="1"' : '';

                    return `
                        <div class="df-check-item">
                            <input type="checkbox" id="${optionId}" name="${fieldName}[]" value="${escapeHtml(option)}"${checked}${optionRequired}>
                            <label for="${optionId}">${escapeHtml(option)}</label>
                        </div>`;
                }).join('');

                return `
                    <div class="df-field df-field-full">
                        <fieldset style="border:none;padding:0;margin:0;">
                            <legend class="df-label">${escapeHtml(field.label)} ${requiredMarker}</legend>
                            <div class="df-check-group">${options}</div>
                        </fieldset>
                        ${hintMarkup}
                    </div>`;
            }

            const minAttr = field.type === 'number' && field.min !== null && field.min !== '' ? ` min="${escapeHtml(field.min)}"` : '';
            const maxAttr = field.type === 'number' && field.max !== null && field.max !== '' ? ` max="${escapeHtml(field.max)}"` : '';
            const minlengthAttr = ['text', 'email'].includes(field.type) && field.min !== null && field.min !== '' ? ` minlength="${escapeHtml(field.min)}"` : '';
            const maxlengthAttr = ['text', 'email'].includes(field.type) && field.max !== null && field.max !== '' ? ` maxlength="${escapeHtml(field.max)}"` : '';
            const isPhoneField = ['mobile', 'phone', 'phone_number'].includes(field.name);
            const inputType = isPhoneField ? 'tel' : field.type;
            const inputModeAttr = isPhoneField ? ' inputmode="tel"' : '';

            return `
                <div class="df-field${fullWidthClass}">
                    <label class="df-label" for="${fieldId}">${escapeHtml(field.label)} ${requiredMarker}</label>
                    <input
                        type="${escapeHtml(inputType)}"
                        id="${fieldId}"
                        name="${fieldName}"
                        class="df-input"
                        placeholder="Enter ${escapeHtml(String(field.label).toLowerCase())}"
                        autocomplete="${escapeHtml(autocomplete)}"
                        value="${escapeHtml(value ?? '')}"${requiredAttr}${minAttr}${maxAttr}${minlengthAttr}${maxlengthAttr}${inputModeAttr}>
                    ${hintMarkup}
                </div>`;
        }

        function teamMemberTemplate(index, member = {}) {
            const fieldsMarkup = TEAM_MEMBER_FIELDS.map(field => teamMemberFieldTemplate(field, index, member)).join('');

            return `
            <div class="df-team-card">
                <div class="df-team-card-header">
                    <div class="df-team-card-title">Team Member ${index + 1}</div>
                    <button type="button" class="df-btn-danger remove-team-member">Remove</button>
                </div>
                <div class="df-team-grid">
                    ${fieldsMarkup}
                </div>
            </div>`;
        }

        function syncTeamMemberIndexes() {
            const cards = Array.from(teamMembersContainer.querySelectorAll('.df-team-card'));

            cards.forEach((card, index) => {
                const title = card.querySelector('.df-team-card-title');
                if (title) {
                    title.textContent = `Team Member ${index + 1}`;
                }

                card.querySelectorAll('label[for], input[id], textarea[id], select[id]').forEach((element) => {
                    if (element.hasAttribute('for')) {
                        element.setAttribute('for', element.getAttribute('for').replace(/\d+$/, index));
                    }

                    if (element.id) {
                        element.id = element.id.replace(/\d+$/, index);
                    }
                });

                card.querySelectorAll('input[name^="team_members["], textarea[name^="team_members["], select[name^="team_members["]').forEach((field) => {
                    field.name = field.name.replace(/team_members\[\d+\]/, `team_members[${index}]`);
                });
            });

            teamMemberIndex = cards.length;
        }

        function updateTeamSectionState() {
            const isTeamMode = regModeTeam && regModeTeam.checked;
            teamMembersSection.classList.toggle('d-none', !isTeamMode);
            coordinatorModeNote.classList.toggle('d-none', !isTeamMode);
            if (promoCodeSection) {
                promoCodeSection.classList.toggle('d-none', isWaitlistMode || !(regPaid && regPaid.checked));
            }
            teamMembersContainer.querySelectorAll('[data-team-required="1"]').forEach(input => {
                input.required = isTeamMode;
            });

            if (passwordInput) {
                passwordInput.required = false;
            }

            refreshRegistrationCapacity();
            refreshSelectedTicketPreview();
        }

        function ensureTeamMemberExists() {
            if (!teamMembersContainer.children.length) {
                teamMembersContainer.insertAdjacentHTML('beforeend', teamMemberTemplate(teamMemberIndex));
                syncTeamMemberIndexes();
            }
            updateTeamSectionState();
        }

        function restoreOldTeamMembers() {
            if (!Array.isArray(OLD_TEAM_MEMBERS) || !OLD_TEAM_MEMBERS.length) {
                return;
            }

            teamMembersContainer.innerHTML = '';
            OLD_TEAM_MEMBERS.forEach(member => {
                teamMembersContainer.insertAdjacentHTML('beforeend', teamMemberTemplate(teamMemberIndex, member || {}));
            });
            syncTeamMemberIndexes();
        }

        regModeSingle.addEventListener('change', function() {
            updateTeamSectionState();
        });

        if (regModeTeam) {
            regModeTeam.addEventListener('change', function() {
                if (this.checked) {
                    ensureTeamMemberExists();
                }
                updateTeamSectionState();
            });
        }

        addTeamMemberBtn.addEventListener('click', function() {
            teamMembersContainer.insertAdjacentHTML('beforeend', teamMemberTemplate(teamMemberIndex));
            syncTeamMemberIndexes();
            updateTeamSectionState();
        });

        coordinatorAttending.addEventListener('change', function() {
            updateTeamSectionState();
        });

        teamMembersContainer.addEventListener('click', function(event) {
            if (!event.target.classList.contains('remove-team-member')) {
                return;
            }

            const card = event.target.closest('.df-team-card');
            if (card) {
                card.remove();
            }

            if (!teamMembersContainer.children.length) {
                ensureTeamMemberExists();
            }

            syncTeamMemberIndexes();
            updateTeamSectionState();
        });

        teamMembersContainer.addEventListener('input', function(event) {
            if (event.target.name && event.target.name.endsWith('[email]')) {
                refreshSelectedTicketPreview();
            }
        });

        if (primaryEmailInput) {
            primaryEmailInput.addEventListener('input', function() {
                if (promoCodeInput && promoCodeInput.value.trim() !== '') {
                    refreshSelectedTicketPreview();
                }
            });
        }


        /* ── Show selected ticket label inside form ── */
        function showSelectedTicket(ticketName, ticketPrice, pricing = null) {
            const pricingMarkup = buildPricingMarkup(pricing, ticketPrice, true);

            ticketRadioContainer.innerHTML = `
            <div class="df-ticket-selected">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div class="df-ticket-selected-main">
                    <div>Ticket selected: <strong>${ticketName}</strong></div>
                    ${pricingMarkup}
                </div>
                <button type="button" id="changeTicketBtn" style="margin-left:auto;background:none;border:none;color:inherit;font-size:.78rem;font-weight:600;cursor:pointer;text-decoration:underline;padding:0;">
                    Change
                </button>
            </div>`;

            document.getElementById('changeTicketBtn').addEventListener('click', function() {
                loadTickets();
                ticketModal.show();
            });
        }

        function setPromoStatus(message = '', type = '') {
            if (!promoCodeStatus) {
                return;
            }

            promoCodeStatus.textContent = message;
            promoCodeStatus.className = `df-promo-status${type ? ' ' + type : ''}`;
        }

        function fetchSelectedTicketPricing() {
            if (!hiddenTicketInput.value) {
                return Promise.resolve(null);
            }

            const params = new URLSearchParams({
                ticket_id: hiddenTicketInput.value,
                attendee_count: String(getAttendeeCount()),
            });

            if (promoCodeInput && promoCodeInput.value.trim() !== '') {
                params.set('promo_code', promoCodeInput.value.trim());
            }

            if (primaryEmailInput && primaryEmailInput.value.trim() !== '') {
                params.set('email', primaryEmailInput.value.trim());
            }

            return fetch(`/events/${EVENT_ID}/tickets/pricing-summary?${params.toString()}`)
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Unable to refresh pricing.');
                    }

                    return data;
                });
        }

        function restoreSelectedTicket() {
            if (!OLD_SELECTED_TICKET_ID) {
                return;
            }

            const ticket = Array.isArray(TICKETS)
                ? TICKETS.find(item => String(item.id) === String(OLD_SELECTED_TICKET_ID))
                : null;

            if (ticket) {
                if (regPaid) {
                    regPaid.checked = true;
                }
                if (regFree) {
                    regFree.checked = false;
                }
                hiddenTicketInput.value = ticket.id;
                showSelectedTicket(ticket.name, ticket.base_price);
            }
        }

        function refreshSelectedTicketPreview() {
            if (!hiddenTicketInput.value || !regPaid || !regPaid.checked) {
                return;
            }

            fetchSelectedTicketPricing()
                .then(data => {
                    if (!data) {
                        return;
                    }

                    showSelectedTicket(data.ticket_name, data.pricing.total, data.pricing);
                    if (promoCodeInput && promoCodeInput.value.trim() !== '') {
                        setPromoStatus(`Promo code ${data.pricing.promo_code} applied successfully.`, 'success');
                    }
                })
                .catch(error => {
                    if (promoCodeInput && promoCodeInput.value.trim() !== '') {
                        setPromoStatus(error.message, 'error');
                    }
                });
        }

        function formatMoney(value) {
            const amount = Number(value || 0);
            return amount.toFixed(2);
        }

        function buildPricingBadges(pricing) {
            if (!pricing) {
                return '';
            }

            const badges = [];
            const totalSavings = Number(pricing.total_savings ?? pricing.savings ?? 0);
            if (totalSavings > 0) {
                badges.push(`<span class="df-ticket-badge discount">Save ${CURRENCY_SYMBOL}${formatMoney(totalSavings)}</span>`);
            }
            if (pricing.is_group_discount_applied) {
                badges.push(`<span class="df-ticket-badge group">Group discount ${formatMoney(pricing.group_discount_percentage).replace(/\.00$/, '')}%</span>`);
            }
            if (pricing.is_early_bird_applied) {
                badges.push(`<span class="df-ticket-badge early">Early bird applied</span>`);
            }
            if (pricing.promo_code && Number(pricing.promo_discount || 0) > 0) {
                badges.push(`<span class="df-ticket-badge discount">Promo ${pricing.promo_code}</span>`);
            }

            return badges.length ? `<div class="df-ticket-badges">${badges.join('')}</div>` : '';
        }

        function buildPricingBreakdown(pricing) {
            if (!pricing) {
                return '';
            }

            const parts = [];
            if (pricing.is_early_bird_applied && Number(pricing.early_bird_units || 0) > 0) {
                parts.push(`${pricing.early_bird_units} attendee(s) at ${CURRENCY_SYMBOL}${formatMoney(pricing.early_bird_unit_price)}`);
            }
            if (Number(pricing.regular_units || 0) > 0) {
                parts.push(`${pricing.regular_units} attendee(s) at ${CURRENCY_SYMBOL}${formatMoney(pricing.regular_unit_price)}`);
            }
            if (pricing.is_group_discount_applied) {
                parts.push(`group discount applied after ${pricing.quantity} attendee(s)`);
            }

            return parts.length ? `<div class="df-ticket-meta">${parts.join(' · ')}</div>` : '';
        }

        function buildPricingRows(pricing) {
            if (!pricing) {
                return '';
            }

            const rows = [
                `<div class="df-ticket-breakup-row"><span>Base total</span><span>${CURRENCY_SYMBOL}${formatMoney(pricing.base_subtotal)}</span></div>`
            ];

            const ticketDiscountSavings = Number(pricing.ticket_discount_savings || 0);
            if (ticketDiscountSavings > 0) {
                rows.push(`<div class="df-ticket-breakup-row"><span>Ticket Discount</span><span>- ${CURRENCY_SYMBOL}${formatMoney(ticketDiscountSavings)}</span></div>`);
            }

            if (Number(pricing.promo_discount || 0) > 0) {
                rows.push(`<div class="df-ticket-breakup-row"><span>Promo Discount</span><span>- ${CURRENCY_SYMBOL}${formatMoney(pricing.promo_discount)}</span></div>`);
            }

            rows.push(`<div class="df-ticket-breakup-row"><strong>Final payable</strong><strong>${CURRENCY_SYMBOL}${formatMoney(pricing.total)}</strong></div>`);

            return `<div class="df-ticket-breakup">${rows.join('')}</div>`;
        }

        function buildPricingMarkup(pricing, fallbackPrice, compact = false) {
            if (!pricing) {
                return `<div class="df-ticket-price-stack"><span class="df-price-final">${CURRENCY_SYMBOL}${fallbackPrice}</span></div>`;
            }

            const hasDiscount = Number(pricing.savings || 0) > 0;
            const priceStack = `
                <div class="df-ticket-price-stack">
                    <span class="df-price-final">${CURRENCY_SYMBOL}${formatMoney(pricing.total)}</span>
                    ${hasDiscount ? `<span class="df-price-base">${CURRENCY_SYMBOL}${formatMoney(pricing.base_subtotal)}</span>` : ''}
                </div>`;

            const breakdown = buildPricingBreakdown(pricing);
            const badges = buildPricingBadges(pricing);
            const breakupRows = buildPricingRows(pricing);

            return `${priceStack}${breakdown}${breakupRows}${compact ? badges : badges}`;
        }

        /* ── Paid selected → open modal ── */
            if (regPaid) {
                regPaid.addEventListener('change', function() {
                    if (!this.checked) return;
                    isTicketWaitlistMode = false;
                    syncWaitlistMode();
                    hiddenTicketInput.value = '';
                    ticketRadioContainer.innerHTML = '';
                    setPromoStatus('', '');
                openTicketSelectionIfNeeded();
                updateTeamSectionState();
            });
        }

        /* ── Switched back to Free → clear ticket ── */
        if (regFree) {
            regFree.addEventListener('change', function() {
                isTicketWaitlistMode = false;
                syncWaitlistMode();
                hiddenTicketInput.value = '';
                ticketRadioContainer.innerHTML = '';
                setPromoStatus('', '');
                updateTeamSectionState();
            });
        }

        /* ── Modal closed without picking → revert to Free ── */
        ticketModalEl.addEventListener('hidden.bs.modal', function() {
            if (!hiddenTicketInput.value) {
                if (isWaitlistMode) {
                    return;
                }
                if (regFree) {
                    regFree.checked = true;
                    if (regPaid) {
                        regPaid.checked = false;
                    }
                } else if (regPaid) {
                    regPaid.checked = false;
                }
            }
        });

        /* ── Fetch & render tickets ── */
        function loadTickets(refreshSelected = false) {
            ticketContainer.innerHTML = `
            <div class="df-loading">
                <div class="df-spinner"></div>
                Loading available tickets…
            </div>`;

            const attendeeCount = getAttendeeCount();
            fetch(`/events/${EVENT_ID}/tickets/available?attendee_count=${encodeURIComponent(attendeeCount)}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load');
                    return res.json();
                })
                .then(tickets => {

                    if (!Array.isArray(tickets) || !tickets.length) {
                        ticketContainer.innerHTML = `
                        <div style="text-align:center;padding:3rem 1rem;color:var(--muted);font-size:.87rem;">
                            No tickets are available for this attendee count. Close this popup and submit the form to join the waitlist.
                        </div>`;
                        isTicketWaitlistMode = !isCapacityWaitlistMode;
                        if (waitlistNotice && !isCapacityWaitlistMode) {
                            waitlistNotice.textContent = 'No paid tickets are available for this attendee count. You can submit this form to join the waitlist.';
                        }
                        syncWaitlistMode();
                        return;
                    }

                    isTicketWaitlistMode = false;
                    syncWaitlistMode();

                    let html = '<div class="row g-3">';
                    tickets.forEach(ticket => {
                        const priceMarkup = buildPricingMarkup(ticket.pricing || null, ticket.base_price);
                        const isSelected = String(hiddenTicketInput.value || '') === String(ticket.id);
                        html += `
                        <div class="col-md-6">
                            <div class="df-ticket-card ${isSelected ? 'is-selected' : ''}" data-ticket-id="${ticket.id}" data-ticket-name="${ticket.name}" data-ticket-price="${ticket.formatted_total || ticket.base_price}" data-ticket-pricing='${JSON.stringify(ticket.pricing || {}).replace(/'/g, "&apos;")}'>
                                <div class="df-ticket-name">${ticket.name}</div>
                                <div class="df-ticket-desc">${ticket.description || ''}</div>
                                <div class="df-ticket-price">${priceMarkup}</div>
                                <button type="button" class="df-ticket-btn select-ticket">
                                    ${isSelected ? 'Selected' : 'Select Ticket'}
                                </button>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    ticketContainer.innerHTML = html;

                    ticketContainer.querySelectorAll('.select-ticket').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const card = this.closest('.df-ticket-card');
                            const ticketId = card.dataset.ticketId;
                            const ticketName = card.dataset.ticketName;
                            const ticketPrice = card.dataset.ticketPrice;
                            const ticketPricing = JSON.parse(card.dataset.ticketPricing || '{}');

                            if (regPaid) {
                                regPaid.checked = true;
                            }
                            if (regFree) {
                                regFree.checked = false;
                            }
                            hiddenTicketInput.value = ticketId;
                            isTicketWaitlistMode = false;
                            syncWaitlistMode();
                            ticketModal.hide();
                            if (promoCodeInput && promoCodeInput.value.trim() !== '') {
                                refreshSelectedTicketPreview();
                            } else {
                                showSelectedTicket(ticketName, ticketPrice, ticketPricing);
                            }
                            updateTeamSectionState();
                        });
                    });

                    if (refreshSelected && hiddenTicketInput.value) {
                        const selectedTicket = tickets.find(ticket => String(ticket.id) === String(hiddenTicketInput.value));
                        if (selectedTicket) {
                            showSelectedTicket(selectedTicket.name, selectedTicket.formatted_total || selectedTicket.base_price, selectedTicket.pricing || null);
                        } else {
                            hiddenTicketInput.value = '';
                            ticketRadioContainer.innerHTML = '';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    ticketContainer.innerHTML = `
                    <div style="text-align:center;padding:3rem 1rem;color:var(--red-fg);font-size:.87rem;">
                        Error loading tickets. Please try again.
                    </div>`;
                });
        }

        if (applyPromoBtn) {
            applyPromoBtn.addEventListener('click', function() {
                if (!regPaid || !regPaid.checked) {
                    setPromoStatus('Please choose paid registration before applying a promo code.', 'error');
                    return;
                }

                if (!hiddenTicketInput.value) {
                    setPromoStatus('Please select a ticket before applying a promo code.', 'error');
                    loadTickets();
                    ticketModal.show();
                    return;
                }

                if (!promoCodeInput || !promoCodeInput.value.trim()) {
                    setPromoStatus('Enter a promo code to apply.', 'error');
                    return;
                }

                setPromoStatus('Validating promo code...', '');

                fetchSelectedTicketPricing()
                    .then(data => {
                        showSelectedTicket(data.ticket_name, data.pricing.total, data.pricing);
                        setPromoStatus(`Promo code ${data.pricing.promo_code} applied successfully.`, 'success');
                    })
                    .catch(error => {
                        setPromoStatus(error.message, 'error');
                    });
            });
        }

        /* ── Single submit listener ── */
        form.addEventListener('submit', function(e) {
            if (regModeTeam && regModeTeam.checked) {
                const validTeamEmails = Array.from(teamMembersContainer.querySelectorAll('input[name$="[email]"]'))
                    .filter(input => input.value.trim() !== '');

                if (!validTeamEmails.length) {
                    e.preventDefault();
                    alert('Please add at least one team member for team registration.');
                    return;
                }
            }

            if ((!regFree || !regFree.checked) && (!regPaid || !regPaid.checked)) {
                e.preventDefault();
                alert('Please choose a registration type to continue.');
                return;
            }

            if (regPaid && regPaid.checked && !hiddenTicketInput.value && !isWaitlistMode) {
                e.preventDefault();
                alert('Please select a ticket to continue.');
                loadTickets();
                ticketModal.show();
                return;
            }

            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        restoreOldTeamMembers();
        restoreSelectedTicket();
        setWaitlistMode(isWaitlistMode);
        if (regModeTeam && regModeTeam.checked) {
            ensureTeamMemberExists();
        }
        updateTeamSectionState();
        if (HAS_FORM_ERRORS) {
            const errorBox = document.querySelector('.df-alert-danger');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        /* ── Conditional logic ── */
        function evaluateConditionalLogic() {
            document.querySelectorAll('.field-wrapper[data-logic]').forEach(wrapper => {
                let logic;
                try {
                    logic = JSON.parse(wrapper.dataset.logic);
                } catch {
                    return;
                }

                const targetLabel = logic.field;
                const operator = logic.operator ?? 'equals';
                const triggerVal = String(logic.value ?? '');

                const triggerWrapper = Array.from(
                    document.querySelectorAll('.field-wrapper[data-target-label]')
                ).find(w => w.dataset.targetLabel === targetLabel);

                if (!triggerWrapper) return;

                let currentVal = '';
                triggerWrapper.querySelectorAll('input, select, textarea').forEach(input => {
                    if ((input.type === 'radio' || input.type === 'checkbox') && input.checked) {
                        currentVal = input.value;
                    } else if (input.type !== 'radio' && input.type !== 'checkbox') {
                        currentVal = input.value;
                    }
                });

                let show = false;
                if (operator === 'equals') show = currentVal === triggerVal;
                if (operator === 'not_equals') show = currentVal !== triggerVal;
                if (operator === 'contains') show = currentVal.includes(triggerVal);

                wrapper.style.display = show ? '' : 'none';
                wrapper.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = !show;
                });
            });
        }

        form.addEventListener('change', evaluateConditionalLogic);
        evaluateConditionalLogic();

    });
</script>
@endsection

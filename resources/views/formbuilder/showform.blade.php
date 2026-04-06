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
        align-items: center;
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
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--accent);
        margin-bottom: .85rem;
        letter-spacing: -.02em;
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

        .df-reg-type {
            grid-template-columns: 1fr 1fr;
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
                Registration Form for {{ $event->title }}
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

        @if($errors->any())
        <div class="df-alert-danger">
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

                    @foreach($form->form_data as $index => $field)
                    @php
                    $type = $field['type'] ?? 'text';
                    $label = $field['label'] ?? ucfirst($type);
                    $name = Str::slug($label, '_');
                    $idBase = "f{$index}_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
                    $isRequired = in_array('required', $field['validation'] ?? []);
                    $min = $field['min'] ?? null;
                    $max = $field['max'] ?? null;
                    $logic = $field['conditional_logic'] ?? null;
                    $hasLenRule = in_array($type, ['text','email','textarea','number']) && ($min || $max);

                    $autocomplete = match($type) {
                    'text' => 'name',
                    'email' => 'email',
                    'number' => 'off',
                    'date' => 'bday',
                    'password' => 'new-password',
                    default => 'off',
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

                    <div class="df-field field-wrapper"
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
                            {{ $label }} @if($isRequired)<span class="req">*</span>@endif
                        </label>
                        <input
                            type="{{ $type }}"
                            id="{{ $idBase }}"
                            name="{{ $name }}"
                            class="df-input"
                            placeholder="Enter {{ strtolower($label) }}"
                            autocomplete="{{ $autocomplete }}"
                            @if($isRequired) required @endif
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
                            @if($max) maxlength="{{ $max }}" @endif></textarea>
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
                                <option value="" selected disabled>Choose an option</option>
                                @foreach($field['options'] ?? [] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
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
                                @foreach($field['options'] ?? [] as $i => $opt)
                                @php $cid = "{$idBase}_c{$i}"; @endphp
                                <div class="df-check-item">
                                    <input type="checkbox"
                                        id="{{ $cid }}"
                                        name="{{ $name }}[]"
                                        value="{{ $opt }}"
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

                    <hr class="df-divider">

                    {{-- Registration type --}}
                    <div class="df-field">
                        <div class="df-label">Registration Type</div>
                        <div class="df-reg-type">
                            <div class="df-reg-option">
                                <input type="radio" name="registration_type" id="regFree" value="free" checked>
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
                            <div class="df-reg-option">
                                <input type="radio" name="registration_type" id="regPaid" value="paid">
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
                        </div>
                    </div>

                    {{-- Selected ticket display --}}
                    <div id="ticketRadioContainer"></div>

                    {{-- Hidden ticket id --}}
                    <input type="hidden" name="selected_ticket_id" id="selected_ticket_id">

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
            Already have an account? <a href="{{ route('event.user.login', $event->id) }}">
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
<script>
    const EVENT_ID = @json($event->id);
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const regPaid = document.getElementById('regPaid');
        const regFree = document.getElementById('regFree');
        const form = document.getElementById('dynamic-form');
        const ticketModalEl = document.getElementById('ticketModal');
        const ticketModal = new bootstrap.Modal(ticketModalEl);
        const ticketContainer = document.getElementById('ticketContainer');
        const hiddenTicketInput = document.getElementById('selected_ticket_id');
        const ticketRadioContainer = document.getElementById('ticketRadioContainer');


        /* ── Show selected ticket label inside form ── */
        function showSelectedTicket(ticketName, ticketPrice) {
            ticketRadioContainer.innerHTML = `
            <div class="df-ticket-selected">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Ticket selected: <strong>${ticketName}</strong>&nbsp;·&nbsp;₹ ${ticketPrice}
                <button type="button" id="changeTicketBtn" style="margin-left:auto;background:none;border:none;color:inherit;font-size:.78rem;font-weight:600;cursor:pointer;text-decoration:underline;padding:0;">
                    Change
                </button>
            </div>`;

            document.getElementById('changeTicketBtn').addEventListener('click', function() {
                loadTickets();
                ticketModal.show();
            });
        }

        /* ── Paid selected → open modal ── */
        regPaid.addEventListener('change', function() {
            if (!this.checked) return;
            loadTickets();
            ticketModal.show();
        });

        /* ── Switched back to Free → clear ticket ── */
        regFree.addEventListener('change', function() {
            hiddenTicketInput.value = '';
            ticketRadioContainer.innerHTML = '';
        });

        /* ── Modal closed without picking → revert to Free ── */
        ticketModalEl.addEventListener('hidden.bs.modal', function() {
            if (!hiddenTicketInput.value) {
                regFree.checked = true;
                regPaid.checked = false;
            }
        });

        /* ── Fetch & render tickets ── */
        function loadTickets() {
            ticketContainer.innerHTML = `
            <div class="df-loading">
                <div class="df-spinner"></div>
                Loading available tickets…
            </div>`;

            fetch(`/events/${EVENT_ID}/tickets/available`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load');
                    return res.json();
                })
                .then(tickets => {

                    if (!Array.isArray(tickets) || !tickets.length) {
                        ticketContainer.innerHTML = `
                        <div style="text-align:center;padding:3rem 1rem;color:var(--muted);font-size:.87rem;">
                            No tickets are available for this event.
                        </div>`;
                        return;
                    }

                    let html = '<div class="row g-3">';
                    tickets.forEach(ticket => {
                        html += `
                        <div class="col-md-6">
                            <div class="df-ticket-card" data-ticket-id="${ticket.id}" data-ticket-name="${ticket.name}" data-ticket-price="${ticket.base_price}">
                                <div class="df-ticket-name">${ticket.name}</div>
                                <div class="df-ticket-desc">${ticket.description || ''}</div>
                                <div class="df-ticket-price">₹ ${ticket.base_price}</div>
                                <button type="button" class="df-ticket-btn select-ticket">
                                    Select Ticket
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

                            hiddenTicketInput.value = ticketId;
                            showSelectedTicket(ticketName, ticketPrice);
                            ticketModal.hide();
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    ticketContainer.innerHTML = `
                    <div style="text-align:center;padding:3rem 1rem;color:var(--red-fg);font-size:.87rem;">
                        Error loading tickets. Please try again.
                    </div>`;
                });
        }

        /* ── Single submit listener ── */
        form.addEventListener('submit', function(e) {
            if (regPaid.checked && !hiddenTicketInput.value) {
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
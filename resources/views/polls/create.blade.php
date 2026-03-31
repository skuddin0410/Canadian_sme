@extends('layouts.admin')

@section('title', isset($poll) ? 'Edit Poll' : 'Create Poll')

@section('content')
<style>
    .poll-shell {
        width: 100%;
        margin: 0 auto;
    }

    .poll-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1.25rem rgba(67, 89, 113, 0.08);
    }

    .poll-card .card-header {
        border-bottom: 1px solid rgba(67, 89, 113, 0.08);
        background: linear-gradient(135deg, rgba(105, 108, 255, 0.08), rgba(3, 195, 236, 0.08));
        border-radius: 1rem 1rem 0 0;
        padding: 1.25rem 1.5rem;
    }

    .poll-section {
        background: #fff;
        border: 1px solid rgba(67, 89, 113, 0.12);
        border-radius: 0.8rem;
        padding: 1rem;
    }

    .question-item {
        border: 1px solid rgba(67, 89, 113, 0.14);
        border-radius: 0.85rem;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(67, 89, 113, 0.08);
        transform: translateY(-1px);
    }

    .question-meta {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.9rem;
    }

    .question-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #696cff;
        background: rgba(105, 108, 255, 0.14);
    }

    .remove-question {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        padding: 0;
        line-height: 1;
    }

    .rating-wrapper {
        background: rgba(3, 195, 236, 0.08);
        border: 1px dashed rgba(3, 195, 236, 0.35);
        border-radius: 0.7rem;
        padding: 0.75rem;
    }

    @media (max-width: 576px) {

        .poll-card .card-header,
        .poll-card .card-body {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="poll-shell">
        <div class="card poll-card">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h4 class="mb-1">{{ isset($poll) ? 'Edit Poll' : 'Create Poll' }}</h4>
                    <p class="text-muted mb-0">Configure poll details and build question flow.</p>
                </div>
                <span class="badge bg-label-primary">{{ isset($poll) ? 'Edit' : 'New Poll' }}</span>
            </div>

            <div class="card-body">
                <form action="{{ isset($poll)
                        ? route('polls.update', $poll->id)
                        : route('polls.store') }}"
                    method="POST">

                    @csrf
                    @if(isset($poll))
                        @method('PUT')
                    @endif

                    {{-- Validation errors --}}
                    @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="poll-section mb-4">
                        <h6 class="mb-3">Poll Details</h6>

                        {{-- Event --}}
                        <div class="mb-3">
                            <label class="form-label">Event <span class="text-danger">*</span></label>
                            <select name="event_id" class="form-select" required>
                                <option value="">Select Event</option>
                                @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ (isset($poll) && $poll->event_id == $event->id) || old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{--
                            FIX: store() accepts event_session_id (nullable|exists:sessions,id)
                            — field was missing from the blade entirely
                        --}}
                        <div class="mb-3">
                            <label class="form-label">Session <span class="text-muted">(optional)</span></label>
                            <select name="event_session_id" class="form-select">
                                <option value="">Select Session</option>
                                @foreach($sessions ?? [] as $session)
                                <option value="{{ $session->id }}"
                                    {{ (isset($poll) && $poll->event_session_id == $session->id) || old('event_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Poll Title --}}
                        <div class="mb-3">
                            <label class="form-label">Poll Title <span class="text-danger">*</span></label>
                            <input type="text"
                                name="title"
                                class="form-control"
                                placeholder="Enter a clear poll title"
                                value="{{ old('title', isset($poll) ? $poll->title : '') }}"
                                required>
                        </div>

                        {{-- Dates --}}
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local"
                                    name="start_date"
                                    class="form-control"
                                    value="{{ old('start_date', isset($poll) && $poll->start_date ? $poll->start_date->format('Y-m-d\TH:i') : '') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local"
                                    name="end_date"
                                    class="form-control"
                                    value="{{ old('end_date', isset($poll) && $poll->end_date ? $poll->end_date->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="poll-section">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Questions <span class="text-danger">*</span></h6>
                            <button type="button" id="add-question" class="btn btn-secondary">
                                <i class="fas fa-plus me-1"></i>Add Question
                            </button>
                        </div>

                        <div id="questions-wrapper">
                            @if(isset($poll) && $poll->questions->count())

                                @foreach($poll->questions as $index => $question)
                                <div class="question-item card mb-3 p-3" data-index="{{ $index }}">

                                    <div class="question-meta">
                                        <span class="question-badge question-number">{{ $index + 1 }}</span>
                                        <button type="button" class="btn btn-sm btn-danger remove-question ms-auto">&times;</button>
                                    </div>

                                    {{-- FIX: preserve question id for update --}}
                                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">

                                    <div class="mb-2">
                                        <label class="form-label">Question</label>
                                        <input type="text"
                                            class="form-control question-input"
                                            name="questions[{{ $index }}][question]"
                                            value="{{ old('questions.'.$index.'.question', $question->question) }}"
                                            required>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Type</label>
                                        <select class="form-select type-select"
                                            name="questions[{{ $index }}][type]">
                                            <option value="text"    {{ $question->type == 'text'    ? 'selected' : '' }}>Text</option>
                                            <option value="yes_no"  {{ $question->type == 'yes_no'  ? 'selected' : '' }}>Yes / No</option>
                                            <option value="option"  {{ $question->type == 'option'  ? 'selected' : '' }}>MCQ</option>
                                            <option value="rating"  {{ $question->type == 'rating'  ? 'selected' : '' }}>Rating</option>
                                        </select>
                                    </div>

                                    {{--
                                        FIX: store() validates rating_scale as integer|in:5
                                        — value locked to 5, input is readonly to match validation
                                    --}}
                                    <div class="rating-wrapper mb-2" style="{{ $question->type == 'rating' ? '' : 'display:none' }}">
                                        <label class="form-label">Rating Scale</label>
                                        <input type="number"
                                            class="form-control rating-input"
                                            name="questions[{{ $index }}][rating_scale]"
                                            value="5"
                                            readonly>
                                        <small class="text-muted">Scale is fixed at 5 stars.</small>
                                    </div>

                                    {{--
                                        FIX: options validated as array|min:2
                                        — at least 2 options must be present for MCQ
                                    --}}
                                    <div class="option-wrapper" style="{{ $question->type == 'option' ? '' : 'display:none' }}">
                                        <label class="form-label">Options <span class="text-danger">*</span> <small class="text-muted">(min 2)</small></label>
                                        <div class="options-container">
                                            @foreach($question->options as $opt)
                                            <div class="input-group mb-2 option-input">
                                                <input type="text"
                                                    class="form-control option-text"
                                                    name="questions[{{ $index }}][options][]"
                                                    value="{{ $opt->option_text }}"
                                                    placeholder="Option text"
                                                    required>
                                                <button type="button" class="btn btn-danger remove-option">&times;</button>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-option mt-2">
                                            + Add Option
                                        </button>
                                    </div>

                                </div>
                                @endforeach

                            @else
                            {{--
                                FIX: default first question now has proper name attributes
                                so form submission works even without JS adding extra questions
                            --}}
                            <div class="question-item card mb-3 p-3" data-index="0">
                                <div class="question-meta">
                                    <span class="question-badge question-number">1</span>
                                    <button type="button" class="btn btn-sm btn-danger remove-question ms-auto">&times;</button>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Question</label>
                                    <input type="text"
                                        class="form-control question-input"
                                        name="questions[0][question]"
                                        required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Type</label>
                                    <select class="form-select type-select" name="questions[0][type]">
                                        <option value="text">Text</option>
                                        <option value="yes_no">Yes / No</option>
                                        <option value="option">MCQ</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                </div>

                                <div class="rating-wrapper mb-2" style="display:none">
                                    <label class="form-label">Rating Scale</label>
                                    <input type="number"
                                        class="form-control rating-input"
                                        name="questions[0][rating_scale]"
                                        value="5"
                                        readonly>
                                    <small class="text-muted">Scale is fixed at 5 stars.</small>
                                </div>

                                <div class="option-wrapper" style="display:none">
                                    <label class="form-label">Options <span class="text-danger">*</span> <small class="text-muted">(min 2)</small></label>
                                    <div class="options-container"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-option mt-2">
                                        + Add Option
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('polls.index') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($poll) ? 'Update Poll' : 'Create Poll' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const wrapper = document.getElementById('questions-wrapper');
    const addBtn  = document.getElementById('add-question');

    // ---------- Helpers ----------

    function questionItems() {
        return wrapper.querySelectorAll('.question-item');
    }

    function refreshNumbers() {
        questionItems().forEach((q, i) => {
            q.querySelector('.question-number').innerText = i + 1;
        });
    }

    function toggleUI(item, type) {
        item.querySelector('.rating-wrapper').style.display =
            type === 'rating' ? 'block' : 'none';
        item.querySelector('.option-wrapper').style.display =
            type === 'option' ? 'block' : 'none';
    }

    // ---------- Question Template ----------

    function questionHTML(index) {
        return `
        <div class="question-item card mb-3 p-3" data-index="${index}">
            <div class="question-meta d-flex align-items-center">
                <span class="question-badge question-number"></span>
                <button type="button" class="btn btn-sm btn-danger remove-question ms-auto">&times;</button>
            </div>

            <div class="mb-2">
                <label class="form-label">Question</label>
                <input type="text"
                       class="form-control question-input"
                       name="questions[${index}][question]"
                       required>
            </div>

            <div class="mb-2">
                <label class="form-label">Type</label>
                <select class="form-select type-select"
                        name="questions[${index}][type]">
                    <option value="text">Text</option>
                    <option value="yes_no">Yes / No</option>
                    <option value="option">MCQ</option>
                    <option value="rating">Rating</option>
                </select>
            </div>

            <div class="rating-wrapper mb-2" style="display:none">
                <label class="form-label">Rating Scale</label>
                <input type="number"
                       class="form-control rating-input"
                       name="questions[${index}][rating_scale]"
                       value="5"
                       readonly>
                <small class="text-muted">Scale is fixed at 5 stars.</small>
            </div>

            <div class="option-wrapper" style="display:none">
                <label class="form-label">Options <span class="text-danger">*</span> <small class="text-muted">(min 2)</small></label>
                <div class="options-container"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-option mt-2">
                    + Add Option
                </button>
            </div>
        </div>`;
    }

    // ---------- Reindex All ----------

    function reIndexAllQuestions() {
        questionItems().forEach((item, i) => {

            item.dataset.index = i;

            item.querySelector('.question-input')
                .setAttribute('name', `questions[${i}][question]`);

            item.querySelector('.type-select')
                .setAttribute('name', `questions[${i}][type]`);

            item.querySelector('.rating-input')
                .setAttribute('name', `questions[${i}][rating_scale]`);

            item.querySelectorAll('.option-text').forEach(opt => {
                opt.setAttribute('name', `questions[${i}][options][]`);
            });

            // preserve hidden id field if present (edit mode)
            const idField = item.querySelector('input[name^="questions"][name$="[id]"]');
            if (idField) {
                idField.setAttribute('name', `questions[${i}][id]`);
            }
        });

        refreshNumbers();
    }

    // ---------- Add Question ----------

    addBtn.addEventListener('click', () => {
        const index = questionItems().length;
        wrapper.insertAdjacentHTML('beforeend', questionHTML(index));
        refreshNumbers();
    });

    // ---------- Remove Question ----------

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-question')) {

            if (questionItems().length <= 1) {
                alert('At least one question is required.');
                return;
            }

            e.target.closest('.question-item').remove();
            reIndexAllQuestions();
        }
    });

    // ---------- Type Change ----------

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('type-select')) {
            toggleUI(
                e.target.closest('.question-item'),
                e.target.value
            );
        }
    });

    // ---------- Add / Remove Option ----------

    document.addEventListener('click', function (e) {

        if (e.target.classList.contains('add-option')) {
            const questionItem = e.target.closest('.question-item');
            const index        = questionItem.dataset.index;
            const box          = questionItem.querySelector('.options-container');

            box.insertAdjacentHTML('beforeend', `
                <div class="input-group mb-2 option-input">
                    <input type="text"
                           class="form-control option-text"
                           name="questions[${index}][options][]"
                           placeholder="Option text"
                           required>
                    <button type="button" class="btn btn-danger remove-option">&times;</button>
                </div>
            `);
        }

        if (e.target.classList.contains('remove-option')) {
            const container = e.target.closest('.option-wrapper')
                                      .querySelector('.options-container');
            // FIX: prevent removing below min:2 for MCQ
            if (container.querySelectorAll('.option-input').length <= 2) {
                alert('MCQ requires at least 2 options.');
                return;
            }
            e.target.closest('.option-input').remove();
        }
    });

    // ---------- Initial Load ----------

    refreshNumbers();

});
</script>
@endsection
@extends('layouts.admin')

@section('title', isset($poll) ? 'Edit Poll' : 'Create Poll')

@section('content')
<style>
    .poll-shell {
        width:100%;
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

                    <div class="poll-section mb-4">
                        <h6 class="mb-3">Poll Details</h6>

                        {{-- Event --}}
                        <div class="mb-3">
                            <label class="form-label">Event</label>
                            <select name="event_id" class="form-select" required>
                                <option value="">Select Event</option>
                                @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ isset($poll) && $poll->event_id == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Poll Title --}}
                        <div class="mb-3">
                            <label class="form-label">Poll Title</label>
                            <input type="text"
                                name="title"
                                class="form-control"
                                placeholder="Enter a clear poll title"
                                value="{{ isset($poll) ? $poll->title : '' }}"
                                required>
                        </div>

                        {{-- Dates --}}
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local"
                                    name="start_date"
                                    class="form-control"
                                    value="{{ isset($poll) && $poll->start_date
                                            ? $poll->start_date->format('Y-m-d\TH:i')
                                            : '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local"
                                    name="end_date"
                                    class="form-control"
                                    value="{{ isset($poll) && $poll->end_date
                                            ? $poll->end_date->format('Y-m-d\TH:i')
                                            : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="poll-section">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Questions</h6>
                            <button type="button" id="add-question" class="btn btn-secondary">
                                <i class="fas fa-plus me-1"></i>Add Question
                            </button>
                        </div>

                        <div id="questions-wrapper">
                            @if(isset($poll) && $poll->questions->count())

                            @foreach($poll->questions as $index => $question)
                            <div class="question-item card mb-3 p-3">
                                <div class="question-meta">
                                    <span class="question-badge question-number">{{ $index + 1 }}</span>
                                    <span class="text-muted small fw-semibold">Question Block</span>
                                    <button type="button"
                                        class="btn btn-sm btn-danger remove-question ms-auto"
                                        aria-label="Remove question">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <input type="hidden"
                                    name="questions[{{ $index }}][id]"
                                    value="{{ $question->id }}">

                                <div class="mb-2">
                                    <label class="form-label">Question</label>
                                    <input type="text"
                                        name="questions[{{ $index }}][question]"
                                        value="{{ $question->question }}"
                                        class="form-control"
                                        required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Type</label>
                                    <select name="questions[{{ $index }}][type]"
                                        class="form-select type-select">
                                        <option value="text" {{ $question->type=='text'?'selected':'' }}>Text</option>
                                        <option value="yes_no" {{ $question->type=='yes_no'?'selected':'' }}>Yes / No</option>
                                        <option value="rating" {{ $question->type=='rating'?'selected':'' }}>Rating</option>
                                    </select>
                                </div>

                                <div class="mb-0 rating-wrapper"
                                    style="{{ $question->type == 'rating' ? '' : 'display:none;' }}">
                                    <label class="form-label">Rating Scale</label>
                                    <input type="number"
                                        name="questions[{{ $index }}][rating_scale]"
                                        value="{{ $question->rating_scale }}"
                                        class="form-control"
                                        placeholder="e.g. 5">
                                </div>
                            </div>
                            @endforeach

                            @else

                            {{-- Default First Question --}}
                            <div class="question-item card mb-3 p-3">
                                <div class="question-meta">
                                    <span class="question-badge question-number">1</span>
                                    <span class="text-muted small fw-semibold">Question Block</span>
                                    <button type="button"
                                        class="btn btn-sm btn-danger remove-question ms-auto"
                                        aria-label="Remove question">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Question</label>
                                    <input type="text"
                                        name="questions[0][question]"
                                        class="form-control"
                                        required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Type</label>
                                    <select name="questions[0][type]"
                                        class="form-select type-select">
                                        <option value="text">Text</option>
                                        <option value="yes_no">Yes / No</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                </div>

                                <div class="mb-0 rating-wrapper" style="display:none;">
                                    <label class="form-label">Rating Scale</label>
                                    <input type="number"
                                        name="questions[0][rating_scale]"
                                        class="form-control"
                                        placeholder="e.g. 5">
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
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('questions-wrapper');
        const addBtn = document.getElementById('add-question');
        let questionIndex = wrapper.querySelectorAll('.question-item').length;

        function refreshQuestionNumbers() {
            const items = wrapper.querySelectorAll('.question-item');
            items.forEach((item, idx) => {
                const number = item.querySelector('.question-number');
                if (number) {
                    number.textContent = idx + 1;
                }
            });
        }

        // Add Question
        addBtn.addEventListener('click', function() {
            const html = `
            <div class="question-item card mb-3 p-3">
                <div class="question-meta">
                    <span class="question-badge question-number">${wrapper.querySelectorAll('.question-item').length + 1}</span>
                    <span class="text-muted small fw-semibold">Question Block</span>
                    <button type="button"
                        class="btn btn-sm btn-danger remove-question ms-auto"
                        aria-label="Remove question">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-2">
                    <label class="form-label">Question</label>
                    <input type="text"
                        name="questions[${questionIndex}][question]"
                        class="form-control"
                        required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Type</label>
                    <select name="questions[${questionIndex}][type]"
                        class="form-select type-select">
                        <option value="text">Text</option>
                        <option value="yes_no">Yes / No</option>
                        <option value="rating">Rating</option>
                    </select>
                </div>

                <div class="mb-0 rating-wrapper" style="display:none;">
                    <label class="form-label">Rating Scale</label>
                    <input type="number"
                        name="questions[${questionIndex}][rating_scale]"
                        class="form-control"
                        placeholder="e.g. 5">
                </div>
            </div>`;

            wrapper.insertAdjacentHTML('beforeend', html);
            questionIndex++;
            refreshQuestionNumbers();
        });

        // Remove Question
        document.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-question');
            if (removeBtn) {
                const items = wrapper.querySelectorAll('.question-item');
                if (items.length <= 1) {
                    alert('At least one question is required.');
                    return;
                }
                removeBtn.closest('.question-item').remove();
                refreshQuestionNumbers();
            }
        });

        // Toggle rating field
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('type-select')) {
                const ratingWrapper = e.target
                    .closest('.question-item')
                    .querySelector('.rating-wrapper');

                ratingWrapper.style.display =
                    e.target.value === 'rating' ? 'block' : 'none';
            }
        });

        refreshQuestionNumbers();
    });
</script>
@endsection

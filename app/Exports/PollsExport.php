<?php

namespace App\Exports;

use App\Models\PollAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PollsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $managedEventIds = isSuperAdmin() ? null : getEventIds();

        $query = PollAnswer::with([
            'user',
            'option',
            'question.poll.event',
            'question.options',
        ])->whereHas('question.poll', function ($pollQuery) use ($managedEventIds) {
            if (!isSuperAdmin()) {
                $pollQuery->whereIn('event_id', $managedEventIds);
            }

            if (!empty($this->filters['event_id'])) {
                if (!isSuperAdmin() && !in_array((int) $this->filters['event_id'], $managedEventIds, true)) {
                    $pollQuery->whereRaw('1 = 0');
                } else {
                    $pollQuery->where('event_id', $this->filters['event_id']);
                }
            }

            if (!empty($this->filters['poll_id'])) {
                $pollQuery->where('id', $this->filters['poll_id']);
            }
        });

        $query->when(!empty($this->filters['question_id']), function ($answerQuery) {
            $answerQuery->where('poll_question_id', $this->filters['question_id']);
        });

        $query->when(!empty($this->filters['user_query']), function ($answerQuery) {
            $search = trim($this->filters['user_query']);

            $answerQuery->where(function ($wrappedQuery) use ($search) {
                $wrappedQuery->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('lastname', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });

                if (strcasecmp($search, 'guest') === 0) {
                    $wrappedQuery->orWhereNull('user_id');
                }
            });
        });

        $query->when(!empty($this->filters['submitted_from']), function ($answerQuery) {
            $answerQuery->whereDate('created_at', '>=', $this->filters['submitted_from']);
        });

        $query->when(!empty($this->filters['submitted_to']), function ($answerQuery) {
            $answerQuery->whereDate('created_at', '<=', $this->filters['submitted_to']);
        });

        $selectedQuestion = null;
        if (!empty($this->filters['question_id'])) {
            $selectedQuestion = \App\Models\PollQuestion::find($this->filters['question_id']);
        }

        $query->when($selectedQuestion && $selectedQuestion->type === 'text' && !empty($this->filters['answer_text']), function ($answerQuery) {
            $answerQuery->where('text_answer', 'like', '%' . trim($this->filters['answer_text']) . '%');
        });

        $query->when($selectedQuestion && $selectedQuestion->type === 'yes_no' && isset($this->filters['answer_value']) && $this->filters['answer_value'] !== '', function ($answerQuery) {
            $answerQuery->where('yes_no_answer', (int) $this->filters['answer_value']);
        });

        $query->when($selectedQuestion && $selectedQuestion->type === 'rating' && !empty($this->filters['answer_value']), function ($answerQuery) {
            $answerQuery->where('rating_answer', (int) $this->filters['answer_value']);
        });

        $query->when($selectedQuestion && $selectedQuestion->type === 'option' && !empty($this->filters['answer_value']), function ($answerQuery) {
            $answerQuery->where('option_id', (int) $this->filters['answer_value']);
        });

        $answers = $query->latest()->get();

        $rows = collect();

        foreach ($answers as $answer) {
            $question = $answer->question;
            $poll = $question?->poll;

            $rows->push([
                $poll?->id ?? '-',
                $poll?->title ?? '-',
                $poll?->event?->title ?? '-',
                $question?->question ?? '-',
                ucfirst($question?->type ?? '-'),
                trim(($answer->user->name ?? '') . ' ' . ($answer->user->lastname ?? '')) ?: ($answer->user->email ?? 'Guest'),
                $this->formatAnswer($answer),
                $answer->created_at?->format('d-m-Y H:i') ?? '-',
            ]);
        }

        return $rows;
    }

    private function formatAnswer($answer)
    {
        if ($answer->option) {
            return $answer->option->option_text;
        }

        if (!empty($answer->text_answer)) {
            return $answer->text_answer;
        }

        if ($answer->yes_no_answer !== null) {
            return $answer->yes_no_answer ? 'Yes' : 'No';
        }

        if (!empty($answer->rating_answer)) {
            return 'Rating: ' . $answer->rating_answer;
        }

        return '-';
    }

    public function headings(): array
    {
        return [
            'Poll ID',
            'Poll Title',
            'Event',
            'Question',
            'Question Type',
            'User Name',
            'Answer',
            'Submitted At',
        ];
    }
}

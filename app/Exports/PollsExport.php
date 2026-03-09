<?php

namespace App\Exports;

use App\Models\Poll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class PollsExport implements FromCollection, WithHeadings
{
    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function collection(): Collection
    {
        $polls = Poll::with([
                'event',
                'questions.answers.user'
            ])
            ->whereHas('questions.answers') // only polls with responses
            ->when($this->eventId, function ($query) {
                $query->where('event_id', $this->eventId);
            })
            ->get();

        $rows = collect();

        foreach ($polls as $poll) {
            foreach ($poll->questions as $question) {
                foreach ($question->answers as $answer) {

                    $rows->push([
                        $poll->id,
                        $poll->title,
                        $poll->event->title ?? '-',
                        $question->question,
                        ucfirst($question->type),
                        $answer->user->name ?? 'Guest',
                        $this->formatAnswer($answer),
                        $answer->created_at->format('d-m-Y H:i'),
                    ]);
                }
            }
        }

        return $rows;
    }

    private function formatAnswer($answer)
    {
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


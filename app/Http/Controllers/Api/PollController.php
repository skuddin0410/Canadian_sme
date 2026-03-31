<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\Event;
use App\Models\Session;
use App\Models\PollAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PollController extends Controller
{
    public function submit(Request $request, Poll $poll)
    {
        // if (!auth()->check()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'User not authenticated.'
        //     ], 401);
        // }
        $user = $request->user();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }


        if (!$poll->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This poll is not active.'
            ], 403);
        }


        if ($poll->start_date && now()->lt($poll->start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Poll has not started yet.'
            ], 403);
        }

        if ($poll->end_date && now()->gt($poll->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Poll has expired.'
            ], 403);
        }

        $request->validate([
            'answers' => 'required|array|min:1',

            'answers.*.question_id' => [
                'required',
                Rule::exists('poll_questions', 'id')
                    ->where(fn($q) => $q->where('poll_id', $poll->id)),
            ],

            'answers.*.text_answer' => 'nullable|string',
            'answers.*.yes_no_answer' => 'nullable|boolean',
            'answers.*.rating_answer' => 'nullable|integer|min:1|max:5',

            'answers.*.option_id' => [
                'nullable',
                Rule::exists('poll_question_options', 'id')
                    ->where(
                        fn($q) =>
                        $q->whereIn(
                            'poll_question_id',
                            $poll->questions()->pluck('id')
                        )
                    ),
            ],
        ]);

        DB::beginTransaction();

        try {

            $already = PollAnswer::where('user_id', $user->id)
                ->whereHas('question', fn($q) => $q->where('poll_id', $poll->id))
                ->exists();

            if ($already) {
                return response()->json(['success' => false, 'message' => 'Already submitted'], 409);
            }

            foreach ($request->answers as $ans) {

                $question = $poll->questions()->findOrFail($ans['question_id']);

                // ✅ TYPE BASED VALIDATION
                match ($question->type) {
                    'text'   => throw_if(empty($ans['text_answer']), new \Exception('Text answer required')),
                    'yes_no' => throw_if(!isset($ans['yes_no_answer']), new \Exception('Yes/No required')),
                    'rating' => throw_if(empty($ans['rating_answer']), new \Exception('Rating required')),
                    'option' => throw_if(empty($ans['option_id']), new \Exception('Option selection required')),
                };

                // ✅ SAVE CORRECTLY
                PollAnswer::create([
                    'poll_question_id' => $question->id,
                    'user_id' => $user->id,
                    'text_answer' => $ans['text_answer'] ?? null,
                    'yes_no_answer' => $ans['yes_no_answer'] ?? null,
                    'rating_answer' => $ans['rating_answer'] ?? null,
                    'option_id' => $ans['option_id'] ?? null,
                ]);
            }

            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Poll submitted successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function latestSubmittedPollByEvent(Request $request, Event $event)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $poll = Poll::where('event_id', $event->id)
            ->with(['questions.options']) // eager load
            ->latest()
            ->first();

        if (!$poll) {
            return response()->json(['success' => false, 'message' => 'No poll found.'], 404);
        }

        $questionIds = $poll->questions->pluck('id');

        /*
    |--------------------------------------------------------------------------
    | Preload all stats in 3 SMALL queries
    |--------------------------------------------------------------------------
    */

        // MCQ vote stats
        $mcqStats = PollAnswer::selectRaw('poll_question_id, option_id, COUNT(*) as votes')
            ->whereIn('poll_question_id', $questionIds)
            ->whereNotNull('option_id')
            ->groupBy('poll_question_id', 'option_id')
            ->get()
            ->groupBy('poll_question_id');

        // Yes/No stats
        $yesNoStats = PollAnswer::selectRaw('poll_question_id, yes_no_answer, COUNT(*) as votes')
        ->whereIn('poll_question_id', $questionIds)
        ->groupBy('poll_question_id', 'yes_no_answer')
        ->get()
        ->groupBy('poll_question_id');

        // User answers
        $userAnswers = PollAnswer::where('user_id', $user->id)
            ->whereIn('poll_question_id', $questionIds)
            ->get()
            ->keyBy('poll_question_id');

        /*
    |--------------------------------------------------------------------------
    | Build payload
    |--------------------------------------------------------------------------
    */

        $questions = $poll->questions->map(function ($q) use ($mcqStats, $yesNoStats, $userAnswers) {

            $payload = [
                'question_id' => $q->id,
                'question' => $q->question,
                'type' => $q->type,
                'rating_scale' => $q->rating_scale,
                'options' => null,
                'yes_no_votes' => null,
                'your_answer' => null,
            ];

            /*
        |--------------------------------------------------------------------------
        | MCQ Handling
        |--------------------------------------------------------------------------
        */
            if ($q->type === 'option') {

                $stats = collect($mcqStats[$q->id] ?? []);

                $payload['options'] = $q->options->map(function ($opt) use ($stats) {
                    $voteRow = $stats->firstWhere('option_id', $opt->id);

                    return [
                        'option_id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'votes' => $voteRow->votes ?? 0,
                    ];
                })->values();
            }

            /*
        |--------------------------------------------------------------------------
        | YES / NO Handling
        |--------------------------------------------------------------------------
        */
            if ($q->type === 'yes_no') {

                $stats = collect($yesNoStats[$q->id] ?? []);

                $yesVotes = optional($stats->firstWhere('yes_no_answer', 1))->votes ?? 0;
                $noVotes  = optional($stats->firstWhere('yes_no_answer', 0))->votes ?? 0;

                $payload['yes_no_votes'] = [
                    'yes' => $yesVotes,
                    'no' => $noVotes,
                    'total_votes' => $yesVotes + $noVotes
                ];
            }

            /*
        |--------------------------------------------------------------------------
        | USER ANSWER
        |--------------------------------------------------------------------------
        */
            if (isset($userAnswers[$q->id])) {
                $ans = $userAnswers[$q->id];

                $payload['your_answer'] = [
                    'text_answer' => $ans->text_answer,
                    'yes_no_answer' => $ans->yes_no_answer,
                    'rating_answer' => $ans->rating_answer,
                    'option_id' => $ans->option_id,
                ];
            }

            return $payload;
        });

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
            ],
            'poll' => [
                'id' => $poll->id,
                'title' => $poll->title,
                'start_date' => $poll->start_date,
                'end_date' => $poll->end_date,
                'is_active' => $poll->is_active,
            ],
            'questions' => $questions
            
        ]);
    }
    // public function latestSubmittedPollByEvent(Request $request, Event $event)
    // {
    //     $user = $request->user();

    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthenticated.'
    //         ], 401);
    //     }


    //     $latestAnswer = PollAnswer::with('question.poll')
    //         ->where('user_id', $user->id)
    //         ->whereHas('question.poll', function ($q) use ($event) {
    //             $q->where('event_id', $event->id);
    //         })
    //         ->latest()
    //         ->first();

    //     if (!$latestAnswer) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No poll submitted for this event.'
    //         ], 404);
    //     }

    //     $poll = $latestAnswer->question->poll;


    //     $answers = PollAnswer::with('question')
    //         ->where('user_id', $user->id)
    //         ->whereHas('question', function ($q) use ($poll) {
    //             $q->where('poll_id', $poll->id);
    //         })
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'event' => [
    //             'id' => $event->id,
    //             'title' => $event->title,
    //         ],
    //         'poll' => [
    //             'id' => $poll->id,
    //             'title' => $poll->title,
    //             'start_date' => $poll->start_date,
    //             'end_date' => $poll->end_date,
    //             'is_active' => $poll->is_active,
    //         ],
    //         'answers' => $answers->map(function ($answer) {
    //             return [
    //                 'question_id' => $answer->question->id,
    //                 'question' => $answer->question->question,
    //                 'text_answer' => $answer->text_answer,
    //                 'yes_no_answer' => $answer->yes_no_answer,
    //                 'rating_answer' => $answer->rating_answer,
    //             ];
    //         })
    //     ]);
    // }
}

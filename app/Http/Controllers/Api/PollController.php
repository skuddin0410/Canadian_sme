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

class PollController extends Controller
{
    public function submit(Request $request, Poll $poll)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        //  Check if poll is active
        if (!$poll->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This poll is not active.'
            ], 403);
        }

        // Check date validity 
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
            'answers.*.question_id' => 'required|exists:poll_questions,id',
            'answers.*.text_answer' => 'nullable|string',
            'answers.*.yes_no_answer' => 'nullable|boolean',
            'answers.*.rating_answer' => 'nullable|integer|min:1|max:10',
        ]);

        DB::beginTransaction();

        try {

            //  Prevent duplicate submission
            $alreadySubmitted = PollAnswer::whereHas('question', function ($q) use ($poll) {
                $q->where('poll_id', $poll->id);
            })
                ->where('user_id', auth()->id())
                ->exists();

            if ($alreadySubmitted) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already submitted this poll.'
                ], 409);
            }

            foreach ($request->answers as $answerData) {

                $question = $poll->questions()
                    ->where('id', $answerData['question_id'])
                    ->first();

                if (!$question) {
                    throw new \Exception('Invalid question for this poll.');
                }

                PollAnswer::create([
                    'poll_question_id' => $question->id,
                    'user_id' => auth()->id(),
                    'text_answer' => $answerData['text_answer'] ?? null,
                    'yes_no_answer' => $answerData['yes_no_answer'] ?? null,
                    'rating_answer' => $answerData['rating_answer'] ?? null,
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
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Get latest poll submitted by user for this event
        $latestAnswer = PollAnswer::with('question.poll')
            ->where('user_id', $user->id)
            ->whereHas('question.poll', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->latest()
            ->first();

        if (!$latestAnswer) {
            return response()->json([
                'success' => false,
                'message' => 'No poll submitted for this event.'
            ], 404);
        }

        $poll = $latestAnswer->question->poll;

        // Get all answers of this poll by this user
        $answers = PollAnswer::with('question')
            ->where('user_id', $user->id)
            ->whereHas('question', function ($q) use ($poll) {
                $q->where('poll_id', $poll->id);
            })
            ->get();

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
            'answers' => $answers->map(function ($answer) {
                return [
                    'question_id' => $answer->question->id,
                    'question' => $answer->question->question,
                    'text_answer' => $answer->text_answer,
                    'yes_no_answer' => $answer->yes_no_answer,
                    'rating_answer' => $answer->rating_answer,
                ];
            })
        ]);
    }
}

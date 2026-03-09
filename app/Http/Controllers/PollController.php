<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\Event;
use App\Models\Session;
use App\Models\PollAnswer;
use Illuminate\Support\Facades\DB;
use App\Exports\PollsExport;
use Maatwebsite\Excel\Facades\Excel;

class PollController extends Controller
{
    public function index()
    {

        $totalPolls = Poll::count();


        $polls = Poll::with(['event', 'eventSession'])
            ->latest()
            ->paginate(10);

        return view('polls.index', compact('polls', 'totalPolls'));
    }
    public function create()
    {
        $events = Event::all();
        $sessions = Session::all();

        return view('polls.create', compact('events', 'sessions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,yes_no,rating',
            'questions.*.rating_scale' => 'nullable|integer|min:2|max:10',
        ]);

        DB::beginTransaction();

        try {

            $poll = Poll::create([
                'event_id' => $request->event_id,
                'event_session_id' => $request->event_session_id,
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => true,
            ]);

            foreach ($request->questions as $question) {

                PollQuestion::create([
                    'poll_id' => $poll->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'rating_scale' => $question['type'] === 'rating'
                        ? $question['rating_scale']
                        : null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('polls.index')
                ->with('success', 'Poll created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage());
        }
    }
    public function show($id)
    {
        $poll = Poll::with(['event', 'eventSession', 'questions'])->findOrFail($id);

        return view('polls.show', compact('poll'));
    }
    public function edit($id)
    {
        $poll = Poll::with('questions')->findOrFail($id);
        $events = Event::all();

        return view('polls.create', compact('poll', 'events'));
    }

    public function update(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',

            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:poll_questions,id',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,yes_no,rating',
            'questions.*.rating_scale' => 'nullable|integer|min:2|max:10',
        ]);

        DB::beginTransaction();

        try {


            $poll->update([
                'event_id' => $request->event_id,
                'event_session_id' => $request->event_session_id,
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $existingQuestionIds = [];

            foreach ($request->questions as $questionData) {


                if (!empty($questionData['id'])) {

                    $question = PollQuestion::where('id', $questionData['id'])
                        ->where('poll_id', $poll->id)
                        ->first();

                    if ($question) {

                        $question->update([
                            'question' => $questionData['question'],
                            'type' => $questionData['type'],
                            'rating_scale' => $questionData['type'] === 'rating'
                                ? $questionData['rating_scale']
                                : null,
                        ]);

                        $existingQuestionIds[] = $question->id;
                    }
                } else {

                    $newQuestion = PollQuestion::create([
                        'poll_id' => $poll->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'rating_scale' => $questionData['type'] === 'rating'
                            ? $questionData['rating_scale']
                            : null,
                    ]);

                    $existingQuestionIds[] = $newQuestion->id;
                }
            }


            PollQuestion::where('poll_id', $poll->id)
                ->whereNotIn('id', $existingQuestionIds)
                ->delete();

            DB::commit();

            return redirect()
                ->route('polls.index')
                ->with('success', 'Poll updated successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage());
        }
    }
    public function destroy($id)
    {
        $poll = Poll::findOrFail($id);
        $poll->delete();

        return redirect()->route('polls.index')
            ->with('success', 'Poll deleted successfully.');
    }
    public function toggleStatus(Poll $poll)
    {
        $poll->is_active = !$poll->is_active;
        $poll->save();


        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $poll->is_active,
            ]);
        }


        return redirect()->route('polls.index')
            ->with('success', 'Poll status updated successfully.');
    }
    public function responses(Request $request, Poll $poll)
    {
        $query = PollAnswer::with(['question.poll', 'user'])
            ->whereHas('question.poll', function ($q) use ($poll) {
                $q->where('id', $poll->id);
            });


        if ($request->event_id) {
            $query->whereHas('question.poll', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $answers = $query->latest()->paginate(10);

        return view('polls.response', compact('poll', 'answers'));
    }


    // public function allResponses()
    // {
    //     $polls = Poll::withCount([
    //         'questions',
    //         'answers'
    //     ])
    //         ->with('event')
    //         ->whereHas('questions.answers')
    //         ->latest()
    //         ->paginate(10);

    //     return view('polls.response-index', compact('polls'));
    // }
    public function allResponses(Request $request)
    {
        $events = Event::orderBy('title')->get();

        $polls = Poll::with([
            'event',
            'questions.answers.user'
        ])
            ->whereHas('questions.answers')
            ->when($request->event_id, function ($query) use ($request) {
                $query->where('event_id', $request->event_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('polls.response-index', compact('polls', 'events'));
    }

    public function getPollResponses(Poll $poll)
    {
        $poll->load([
            'event',
            'questions.answers.user'
        ]);

        return response()->json([
            'success' => true,
            'poll' => $poll
        ]);
    }
    public function getQuestionAnswers(PollQuestion $question)
    {
        $question->load('answers.user');

        return response()->json([
            'success' => true,
            'question' => $question
        ]);
    }
    public function export(Request $request)
    {
        return Excel::download(
            new PollsExport($request->event_id),
            'poll_responses.xlsx'
        );
    }
}

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
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => ['required', Rule::in(['text', 'yes_no', 'rating', 'option'])],

            // rating
            'questions.*.rating_scale' => 'nullable|required_if:questions.*.type,rating|integer|in:5',

            // options (MCQ)
            'questions.*.options' => 'nullable|required_if:questions.*.type,option|array|min:2',
            'questions.*.options.*' => 'required_if:questions.*.type,option|string',
        ]);

        DB::beginTransaction();

        try {
            $poll = Poll::create([
                'event_id' => $validated['event_id'],
                'event_session_id' => $validated['event_session_id'],
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => true,
            ]);

            foreach ($validated['questions'] as $question) {

                $q = PollQuestion::create([
                    'poll_id' => $poll->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'rating_scale' => $question['type'] === 'rating'
                        ? $question['rating_scale']
                        : null,
                ]);

                // ✅ MCQ options safely saved (already validated)
                if ($question['type'] === 'option') {
                    foreach ($question['options'] as $opt) {
                        $q->options()->create([
                            'option_text' => $opt
                        ]);
                    }
                }
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

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:poll_questions,id',
            'questions.*.question' => 'required|string',
            'questions.*.type' => ['required', Rule::in(['text', 'yes_no', 'rating', 'option'])],

            // rating
            'questions.*.rating_scale' => 'nullable|required_if:questions.*.type,rating|integer|in:5',

            // mcq
            'questions.*.options' => 'nullable|required_if:questions.*.type,option|array|min:2',
            'questions.*.options.*' => 'required_if:questions.*.type,option|string',
        ]);

        DB::beginTransaction();

        try {

            // ---------------- Update poll ----------------
            $poll->update([
                'event_id' => $validated['event_id'],
                'event_session_id' => $validated['event_session_id'],
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            $keptQuestionIds = [];

            foreach ($validated['questions'] as $qData) {

                // -------- Update or Create Question --------
                $question = isset($qData['id'])
                    ? PollQuestion::where('id', $qData['id'])
                    ->where('poll_id', $poll->id)
                    ->first()
                    : new PollQuestion(['poll_id' => $poll->id]);

                $question->question = $qData['question'];
                $question->type = $qData['type'];
                $question->rating_scale = $qData['type'] === 'rating'
                    ? $qData['rating_scale']
                    : null;

                $question->save();

                $keptQuestionIds[] = $question->id;

                // -------- Handle MCQ Options --------
                if ($qData['type'] === 'option') {

                    $existingOptionIds = [];

                    foreach ($qData['options'] as $optText) {
                        $opt = $question->options()->create([
                            'option_text' => $optText
                        ]);
                        $existingOptionIds[] = $opt->id;
                    }

                    // remove old options not in new list
                    $question->options()
                        ->whereNotIn('id', $existingOptionIds)
                        ->delete();
                } else {
                    // if type changed from option → others, delete old options
                    $question->options()->delete();
                }
            }

            // -------- Delete removed questions --------
            PollQuestion::where('poll_id', $poll->id)
                ->whereNotIn('id', $keptQuestionIds)
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
        $query = PollAnswer::with(['question.poll', 'question.options', 'user'])
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
            'questions.options',
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
            'questions.answers.user',
            'questions.options'
        ]);

        return response()->json([
            'success' => true,
            'poll' => $poll
        ]);
    }
    public function getQuestionAnswers(PollQuestion $question)
    {
        $question->load([
            'answers.user',
            'answers.option'
        ]);

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

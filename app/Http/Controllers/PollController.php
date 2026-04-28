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
        $query = Poll::with(['event', 'eventSession']);

        if (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        $totalPolls = (clone $query)->count();

        $polls = $query->latest()
            ->paginate(10);

        return view('polls.index', compact('polls', 'totalPolls'));
    }
    public function create()
    {
        $eventIds = isSuperAdmin() ? null : getEventIds();

        $events = isSuperAdmin()
            ? Event::all()
            : Event::whereIn('id', $eventIds)->get();

        $sessions = isSuperAdmin()
            ? Session::all()
            : Session::whereIn('event_id', $eventIds)->get();

        return view('polls.create', compact('events', 'sessions'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:event_sessions,id',
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

        if (!isSuperAdmin() && !in_array($poll->event_id, getEventIds())) {
            abort(403, 'Unauthorized action.');
        }

        $eventIds = isSuperAdmin() ? null : getEventIds();

        $events = isSuperAdmin()
            ? Event::all()
            : Event::whereIn('id', $eventIds)->get();

        $sessions = isSuperAdmin()
            ? Session::all()
            : Session::whereIn('event_id', $eventIds)->get();

        return view('polls.create', compact('poll', 'events', 'sessions'));
    }

    public function update(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);

        if (!isSuperAdmin() && !in_array($poll->event_id, getEventIds())) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:event_sessions,id',
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

        if (!isSuperAdmin() && !in_array($poll->event_id, getEventIds())) {
            abort(403, 'Unauthorized action.');
        }
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
        $events = isSuperAdmin()
            ? Event::orderBy('title')->get()
            : Event::whereIn('id', getEventIds())->orderBy('title')->get();

        $query = Poll::with([
            'event',
            'questions.options',
            'questions.answers.user'
        ])
            ->whereHas('questions.answers');

        if (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        $polls = $query->when($request->event_id, function ($q) use ($request) {
                // Ensure filtered event is within their managed list
                if (!isSuperAdmin() && !in_array($request->event_id, getEventIds())) {
                    $q->where('event_id', 0); // No results
                } else {
                    $q->where('event_id', $request->event_id);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('polls.response-index', compact('polls', 'events'));
    }

    public function getPollResponses(Poll $poll)
    {
        if (!isSuperAdmin() && !in_array($poll->event_id, getEventIds())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        $poll = $question->poll;
        if (!isSuperAdmin() && !in_array($poll->event_id, getEventIds())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        $eventId = $request->event_id;

        // Security: Ensure they can only export data they are allowed to see
        if (!isSuperAdmin()) {
            $managedIds = getEventIds();
            if ($eventId && !in_array($eventId, $managedIds)) {
                $eventId = -1; // Force no results
            } elseif (!$eventId) {
                $eventId = $managedIds; // Export all managed events if no specific ID provided
            }
        }

        return Excel::download(
            new PollsExport($eventId),
            'poll_responses.xlsx'
        );
    }
}

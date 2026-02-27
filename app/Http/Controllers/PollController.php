<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index()
    {
        //  Total polls count
        $totalPolls = Poll::count();

        //  Fetch polls with related data
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

    //  STORE POLL + QUESTIONS
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'event_session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
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

        $poll->update([
            'event_id' => $request->event_id,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        foreach ($request->questions as $questionData) {

            $question = PollQuestion::find($questionData['id']);

            if ($question) {
                $question->update([
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'rating_scale' => $questionData['type'] === 'rating'
                        ? $questionData['rating_scale']
                        : null,
                ]);
            }
        }

        return redirect()->route('polls.index')
            ->with('success', 'Poll updated successfully.');
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

        // Check if the request expects JSON (AJAX)
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $poll->is_active,
            ]);
        }

        // Fallback for normal requests
        return redirect()->route('polls.index')
            ->with('success', 'Poll status updated successfully.');
    }
}
